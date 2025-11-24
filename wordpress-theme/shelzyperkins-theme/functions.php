<?php
/**
 * ShelzyPerkins Theme Functions
 *
 * @package ShelzyPerkins
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Theme Constants
define('SP_THEME_VERSION', '1.0.0');
define('SP_THEME_DIR', get_template_directory());
define('SP_THEME_URI', get_template_directory_uri());
define('SP_AFFILIATE_TAG', 'shelzysdesigns-20');

/**
 * Theme Setup
 */
function sp_theme_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height'      => 80,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));
    add_theme_support('automatic-feed-links');
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    add_theme_support('wp-block-styles');
    add_theme_support('editor-styles');

    // Add image sizes
    add_image_size('sp-card', 400, 400, true);
    add_image_size('sp-featured', 1200, 630, true);
    add_image_size('sp-pinterest', 735, 1102, true);

    // Register nav menus
    register_nav_menus(array(
        'primary'   => __('Primary Menu', 'shelzyperkins'),
        'footer'    => __('Footer Menu', 'shelzyperkins'),
        'mobile'    => __('Mobile Menu', 'shelzyperkins'),
        'categories' => __('Categories Menu', 'shelzyperkins'),
    ));

    // Load text domain
    load_theme_textdomain('shelzyperkins', SP_THEME_DIR . '/languages');
}
add_action('after_setup_theme', 'sp_theme_setup');

/**
 * Enqueue Scripts and Styles
 */
function sp_enqueue_assets() {
    // Styles
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap', array(), null);
    wp_enqueue_style('sp-style', get_stylesheet_uri(), array('google-fonts'), SP_THEME_VERSION);
    wp_enqueue_style('sp-custom', SP_THEME_URI . '/assets/css/custom.css', array('sp-style'), SP_THEME_VERSION);

    // Scripts
    wp_enqueue_script('sp-main', SP_THEME_URI . '/assets/js/main.js', array(), SP_THEME_VERSION, true);
    wp_enqueue_script('sp-affiliate', SP_THEME_URI . '/assets/js/affiliate-tracking.js', array('sp-main'), SP_THEME_VERSION, true);

    // Localize script with data
    wp_localize_script('sp-main', 'spData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('sp_nonce'),
        'affiliateTag' => SP_AFFILIATE_TAG,
    ));
}
add_action('wp_enqueue_scripts', 'sp_enqueue_assets');

/**
 * Register Sidebars/Widget Areas
 */
function sp_widgets_init() {
    register_sidebar(array(
        'name'          => __('Blog Sidebar', 'shelzyperkins'),
        'id'            => 'sidebar-blog',
        'description'   => __('Widgets in this area will be shown on blog posts and archives.', 'shelzyperkins'),
        'before_widget' => '<div id="%1$s" class="sp-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="sp-widget__title">',
        'after_title'   => '</h4>',
    ));

    register_sidebar(array(
        'name'          => __('Footer Column 1', 'shelzyperkins'),
        'id'            => 'footer-1',
        'before_widget' => '<div id="%1$s" class="sp-footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5 class="sp-footer__title">',
        'after_title'   => '</h5>',
    ));

    register_sidebar(array(
        'name'          => __('Footer Column 2', 'shelzyperkins'),
        'id'            => 'footer-2',
        'before_widget' => '<div id="%1$s" class="sp-footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5 class="sp-footer__title">',
        'after_title'   => '</h5>',
    ));

    register_sidebar(array(
        'name'          => __('Footer Column 3', 'shelzyperkins'),
        'id'            => 'footer-3',
        'before_widget' => '<div id="%1$s" class="sp-footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5 class="sp-footer__title">',
        'after_title'   => '</h5>',
    ));
}
add_action('widgets_init', 'sp_widgets_init');

/**
 * Amazon Affiliate Link Generator
 */
function sp_amazon_link($asin, $text = 'View on Amazon', $class = 'sp-btn sp-btn--amazon') {
    $affiliate_tag = SP_AFFILIATE_TAG;
    $url = "https://www.amazon.com/dp/{$asin}?tag={$affiliate_tag}";

    return sprintf(
        '<a href="%s" class="%s" target="_blank" rel="nofollow sponsored noopener" data-asin="%s">%s</a>',
        esc_url($url),
        esc_attr($class),
        esc_attr($asin),
        esc_html($text)
    );
}

/**
 * Shortcode: Amazon Product Link
 * Usage: [amazon_link asin="B08N5WRWNW" text="Buy Now"]
 */
function sp_amazon_link_shortcode($atts) {
    $atts = shortcode_atts(array(
        'asin' => '',
        'text' => 'View on Amazon',
        'class' => 'sp-btn sp-btn--amazon',
    ), $atts);

    if (empty($atts['asin'])) {
        return '';
    }

    return sp_amazon_link($atts['asin'], $atts['text'], $atts['class']);
}
add_shortcode('amazon_link', 'sp_amazon_link_shortcode');

/**
 * Shortcode: Product Card
 * Usage: [product_card asin="B08N5WRWNW" title="Product Name" price="29.99" original_price="49.99" image="url"]
 */
function sp_product_card_shortcode($atts) {
    $atts = shortcode_atts(array(
        'asin'           => '',
        'title'          => 'Product',
        'price'          => '',
        'original_price' => '',
        'image'          => '',
        'badge'          => '',
    ), $atts);

    if (empty($atts['asin'])) {
        return '';
    }

    $affiliate_tag = SP_AFFILIATE_TAG;
    $url = "https://www.amazon.com/dp/{$atts['asin']}?tag={$affiliate_tag}";

    $savings = '';
    if (!empty($atts['original_price']) && !empty($atts['price'])) {
        $saved = floatval($atts['original_price']) - floatval($atts['price']);
        $percent = round(($saved / floatval($atts['original_price'])) * 100);
        $savings = "Save {$percent}%";
    }

    ob_start();
    ?>
    <div class="sp-card sp-product-card" data-asin="<?php echo esc_attr($atts['asin']); ?>">
        <?php if (!empty($atts['image'])): ?>
        <div class="sp-card__image">
            <img src="<?php echo esc_url($atts['image']); ?>" alt="<?php echo esc_attr($atts['title']); ?>" loading="lazy">
            <?php if (!empty($atts['badge'])): ?>
            <span class="sp-card__badge"><?php echo esc_html($atts['badge']); ?></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <div class="sp-card__content">
            <h4 class="sp-card__title"><?php echo esc_html($atts['title']); ?></h4>
            <div class="sp-card__price">
                <?php if (!empty($atts['price'])): ?>
                <span class="sp-card__price-current">$<?php echo esc_html($atts['price']); ?></span>
                <?php endif; ?>
                <?php if (!empty($atts['original_price'])): ?>
                <span class="sp-card__price-original">$<?php echo esc_html($atts['original_price']); ?></span>
                <?php endif; ?>
            </div>
            <?php if ($savings): ?>
            <span class="sp-card__savings"><?php echo esc_html($savings); ?></span>
            <?php endif; ?>
            <a href="<?php echo esc_url($url); ?>" class="sp-btn sp-btn--amazon sp-btn--sm" target="_blank" rel="nofollow sponsored noopener">
                View on Amazon
            </a>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('product_card', 'sp_product_card_shortcode');

/**
 * Shortcode: Product Comparison Table
 * Usage: [comparison_table products="asin1,asin2,asin3"]
 */
function sp_comparison_table_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'products' => '',
    ), $atts);

    if ($content) {
        ob_start();
        ?>
        <div class="sp-product-table">
            <?php echo do_shortcode($content); ?>
        </div>
        <?php
        return ob_get_clean();
    }

    return '';
}
add_shortcode('comparison_table', 'sp_comparison_table_shortcode');

/**
 * Shortcode: Product Table Row
 */
function sp_product_row_shortcode($atts) {
    $atts = shortcode_atts(array(
        'asin'  => '',
        'title' => '',
        'price' => '',
        'image' => '',
        'note'  => '',
    ), $atts);

    if (empty($atts['asin'])) {
        return '';
    }

    $url = "https://www.amazon.com/dp/{$atts['asin']}?tag=" . SP_AFFILIATE_TAG;

    ob_start();
    ?>
    <div class="sp-product-table__row">
        <?php if (!empty($atts['image'])): ?>
        <img src="<?php echo esc_url($atts['image']); ?>" alt="<?php echo esc_attr($atts['title']); ?>" class="sp-product-table__image" loading="lazy">
        <?php endif; ?>
        <div class="sp-product-table__info">
            <h4><?php echo esc_html($atts['title']); ?></h4>
            <?php if (!empty($atts['note'])): ?>
            <p><?php echo esc_html($atts['note']); ?></p>
            <?php endif; ?>
        </div>
        <div class="sp-product-table__price">
            <?php if (!empty($atts['price'])): ?>
            <span class="sp-card__price-current">$<?php echo esc_html($atts['price']); ?></span>
            <?php endif; ?>
        </div>
        <div class="sp-product-table__cta">
            <a href="<?php echo esc_url($url); ?>" class="sp-btn sp-btn--amazon sp-btn--sm" target="_blank" rel="nofollow sponsored noopener">
                View Deal
            </a>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('product_row', 'sp_product_row_shortcode');

/**
 * Shortcode: Affiliate Disclosure
 */
function sp_disclosure_shortcode($atts) {
    $atts = shortcode_atts(array(
        'short' => false,
    ), $atts);

    if ($atts['short']) {
        return '<p class="sp-affiliate-disclosure"><strong>Disclosure:</strong> This post contains affiliate links. I may earn a commission at no extra cost to you.</p>';
    }

    return '<div class="sp-affiliate-disclosure"><strong>Disclosure:</strong> This post contains affiliate links. If you purchase through these links, I may earn a small commission at no extra cost to you. I only recommend products I genuinely love and use!</div>';
}
add_shortcode('affiliate_disclosure', 'sp_disclosure_shortcode');

/**
 * Shortcode: Deal Badge
 */
function sp_deal_badge_shortcode($atts) {
    $atts = shortcode_atts(array(
        'text' => 'HOT DEAL',
        'type' => 'default', // default, hot, limited
    ), $atts);

    $class = 'sp-deal-badge';
    if ($atts['type'] === 'hot') {
        $class .= ' sp-deal-badge--hot';
    } elseif ($atts['type'] === 'limited') {
        $class .= ' sp-deal-badge--limited';
    }

    return '<span class="' . esc_attr($class) . '">' . esc_html($atts['text']) . '</span>';
}
add_shortcode('deal_badge', 'sp_deal_badge_shortcode');

/**
 * Shortcode: Email Signup Box
 */
function sp_email_signup_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title'       => 'Never Miss a Deal!',
        'description' => 'Subscribe to get the hottest deals delivered straight to your inbox.',
        'button_text' => 'Subscribe',
        'form_id'     => '',
    ), $atts);

    ob_start();
    ?>
    <div class="sp-email-signup">
        <h3 class="sp-email-signup__title"><?php echo esc_html($atts['title']); ?></h3>
        <p class="sp-email-signup__text"><?php echo esc_html($atts['description']); ?></p>
        <form class="sp-email-signup__form" action="#" method="post" data-form-id="<?php echo esc_attr($atts['form_id']); ?>">
            <input type="email" name="email" class="sp-email-signup__input" placeholder="Enter your email" required>
            <button type="submit" class="sp-btn sp-btn--primary"><?php echo esc_html($atts['button_text']); ?></button>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('email_signup', 'sp_email_signup_shortcode');

/**
 * Auto-insert affiliate disclosure before content
 */
function sp_auto_affiliate_disclosure($content) {
    if (is_single() && in_the_loop() && is_main_query()) {
        // Check if post has affiliate links
        if (has_shortcode($content, 'amazon_link') ||
            has_shortcode($content, 'product_card') ||
            has_shortcode($content, 'product_row') ||
            strpos($content, 'amazon.com') !== false) {
            $disclosure = '<div class="sp-affiliate-disclosure"><strong>Disclosure:</strong> This post contains affiliate links. If you purchase through these links, I may earn a small commission at no extra cost to you. I only recommend products I genuinely love!</div>';
            $content = $disclosure . $content;
        }
    }
    return $content;
}
add_filter('the_content', 'sp_auto_affiliate_disclosure');

/**
 * Add structured data for products
 */
function sp_product_schema($content) {
    if (is_single()) {
        // Schema markup is added via JavaScript based on product data
        return $content;
    }
    return $content;
}
add_filter('the_content', 'sp_product_schema');

/**
 * Custom excerpt length
 */
function sp_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'sp_excerpt_length');

/**
 * Custom excerpt more
 */
function sp_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'sp_excerpt_more');

/**
 * Add defer/async to scripts
 */
function sp_defer_scripts($tag, $handle, $src) {
    $defer_scripts = array('sp-main', 'sp-affiliate');

    if (in_array($handle, $defer_scripts)) {
        return '<script src="' . esc_url($src) . '" defer></script>';
    }

    return $tag;
}
add_filter('script_loader_tag', 'sp_defer_scripts', 10, 3);

/**
 * Preload critical assets
 */
function sp_preload_assets() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
    echo '<link rel="preload" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" as="style">';
    echo '<link rel="dns-prefetch" href="//www.amazon.com">';
    echo '<link rel="dns-prefetch" href="//m.media-amazon.com">';
}
add_action('wp_head', 'sp_preload_assets', 1);

/**
 * Add custom body classes
 */
function sp_body_classes($classes) {
    if (is_single()) {
        $classes[] = 'sp-single-post';
    }
    if (is_page()) {
        $classes[] = 'sp-page';
    }
    if (is_front_page()) {
        $classes[] = 'sp-front-page';
    }
    return $classes;
}
add_filter('body_class', 'sp_body_classes');

/**
 * Register Block Patterns
 */
function sp_register_block_patterns() {
    register_block_pattern_category('shelzyperkins', array(
        'label' => __('ShelzyPerkins', 'shelzyperkins'),
    ));
}
add_action('init', 'sp_register_block_patterns');

/**
 * Include additional files
 */
require_once SP_THEME_DIR . '/inc/customizer.php';
require_once SP_THEME_DIR . '/inc/template-tags.php';
require_once SP_THEME_DIR . '/inc/block-patterns.php';
require_once SP_THEME_DIR . '/inc/security.php';
require_once SP_THEME_DIR . '/inc/seo.php';
require_once SP_THEME_DIR . '/inc/breadcrumbs.php';
require_once SP_THEME_DIR . '/inc/performance.php';

/**
 * Theme activation
 */
function sp_theme_activation() {
    // Flush rewrite rules
    flush_rewrite_rules();

    // Set default options
    update_option('sp_affiliate_tag', SP_AFFILIATE_TAG);
}
add_action('after_switch_theme', 'sp_theme_activation');

/**
 * Enhanced Product Card Shortcode with Prime Badge and Star Ratings
 * Usage: [product_card_pro asin="B08N5WRWNW" title="Product Name" price="29.99" original_price="49.99" image="url" rating="4.5" reviews="1234" prime="true"]
 */
function sp_product_card_pro_shortcode($atts) {
    $atts = shortcode_atts(array(
        'asin'           => '',
        'title'          => 'Product',
        'price'          => '',
        'original_price' => '',
        'image'          => '',
        'badge'          => '',
        'rating'         => '',
        'reviews'        => '',
        'prime'          => 'true',
        'brand'          => '',
    ), $atts);

    if (empty($atts['asin'])) {
        return '';
    }

    $affiliate_tag = SP_AFFILIATE_TAG;
    $url = "https://www.amazon.com/dp/{$atts['asin']}?tag={$affiliate_tag}";

    $savings = '';
    $savings_percent = 0;
    if (!empty($atts['original_price']) && !empty($atts['price'])) {
        $saved = floatval($atts['original_price']) - floatval($atts['price']);
        $savings_percent = round(($saved / floatval($atts['original_price'])) * 100);
        $savings = "Save {$savings_percent}%";
    }

    ob_start();
    ?>
    <div class="sp-card sp-product-card sp-product-card--pro" data-asin="<?php echo esc_attr($atts['asin']); ?>">
        <?php if (!empty($atts['image'])): ?>
        <div class="sp-card__image">
            <img src="<?php echo esc_url($atts['image']); ?>" alt="<?php echo esc_attr($atts['title']); ?>" loading="lazy">
            <?php if (!empty($atts['badge'])): ?>
            <span class="sp-card__badge"><?php echo esc_html($atts['badge']); ?></span>
            <?php elseif ($savings_percent >= 30): ?>
            <span class="sp-card__badge sp-card__badge--hot"><?php echo esc_html($savings); ?></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <div class="sp-card__content">
            <?php if (!empty($atts['brand'])): ?>
            <span class="sp-card__brand"><?php echo esc_html($atts['brand']); ?></span>
            <?php endif; ?>
            <h4 class="sp-card__title"><?php echo esc_html($atts['title']); ?></h4>

            <?php if (!empty($atts['rating'])): ?>
            <div class="sp-card__rating">
                <div class="sp-stars" style="--rating: <?php echo esc_attr($atts['rating']); ?>">
                    <span class="sp-stars__filled"></span>
                </div>
                <span class="sp-card__rating-value"><?php echo esc_html($atts['rating']); ?></span>
                <?php if (!empty($atts['reviews'])): ?>
                <span class="sp-card__reviews">(<?php echo esc_html(number_format(intval($atts['reviews']))); ?>)</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="sp-card__price">
                <?php if (!empty($atts['price'])): ?>
                <span class="sp-card__price-current">$<?php echo esc_html($atts['price']); ?></span>
                <?php endif; ?>
                <?php if (!empty($atts['original_price'])): ?>
                <span class="sp-card__price-original">$<?php echo esc_html($atts['original_price']); ?></span>
                <?php endif; ?>
            </div>

            <?php if ($atts['prime'] === 'true'): ?>
            <div class="sp-card__prime">
                <svg class="sp-prime-icon" width="53" height="15" viewBox="0 0 53 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 12.5h3.3V9.4c0-.7.1-1.3.3-1.8.2-.5.5-.9.9-1.2.4-.3.8-.5 1.4-.5.5 0 1 .1 1.3.4.4.3.6.6.8 1.1.2.5.2 1 .2 1.6v4.5h3.3V8.4c0-1-.1-1.9-.4-2.7-.3-.8-.7-1.4-1.4-1.9-.6-.5-1.5-.7-2.6-.7-.8 0-1.4.2-2 .5-.5.3-1 .7-1.3 1.2h-.1V3.4H0v9.1z" fill="#00A8E1"/>
                    <path d="M18.8 12.7c-.9 0-1.6-.3-2.1-.8-.5-.5-.7-1.2-.7-2.1V6.3c0-.9.2-1.6.7-2.1.5-.5 1.2-.8 2.1-.8.9 0 1.6.3 2.1.8.5.5.7 1.2.7 2.1v3.5c0 .9-.2 1.6-.7 2.1-.5.5-1.2.8-2.1.8zm0-2.4c.3 0 .5-.1.6-.3.1-.2.2-.4.2-.7V6.5c0-.3-.1-.5-.2-.7-.1-.2-.3-.3-.6-.3-.3 0-.5.1-.6.3-.1.2-.2.4-.2.7v2.8c0 .3.1.5.2.7.1.2.3.3.6.3z" fill="#00A8E1"/>
                    <path d="M25.2 12.5h3.3V7c0-.5.1-.9.3-1.2.2-.3.5-.5 1-.5.4 0 .7.1.9.4.2.3.3.6.3 1.1v5.7h3.3V6.9c0-.9-.2-1.6-.5-2.2-.3-.6-.8-1-1.4-1.3-.6-.3-1.3-.4-2-.4-.8 0-1.4.2-2 .5-.5.3-1 .7-1.3 1.2h-.1V.2h-3.3v12.3z" fill="#00A8E1"/>
                    <path d="M42.7 12.7c-1 0-1.8-.2-2.5-.5-.7-.4-1.2-.9-1.6-1.5-.4-.7-.5-1.4-.5-2.3V7c0-.9.2-1.6.5-2.3.4-.7.9-1.2 1.6-1.5.7-.4 1.5-.5 2.5-.5 1 0 1.8.2 2.5.5.7.4 1.2.9 1.6 1.5.4.7.5 1.4.5 2.3v1.4c0 .9-.2 1.6-.5 2.3-.4.7-.9 1.2-1.6 1.5-.7.4-1.5.5-2.5.5zm0-2.4c.4 0 .8-.1 1-.4.3-.3.4-.6.4-1.1V6.5c0-.5-.1-.8-.4-1.1-.3-.3-.6-.4-1-.4-.4 0-.8.1-1 .4-.3.3-.4.6-.4 1.1v2.3c0 .5.1.8.4 1.1.2.3.6.4 1 .4z" fill="#00A8E1"/>
                    <path d="M51.7 3.4h-3.3v9.1h3.3V8.3c0-.5.1-.9.3-1.2.2-.3.5-.5.9-.5.4 0 .7.1.8.4.2.2.3.5.3.9v4.6H53c0-1.7 0-3.4 0-5.1 0-.6-.1-1.2-.3-1.6-.2-.5-.5-.8-.9-1.1-.4-.3-.9-.4-1.5-.4-.6 0-1.1.2-1.5.5-.4.3-.7.6-.9 1.1h-.1V3.4z" fill="#00A8E1"/>
                </svg>
                <span class="sp-prime-text">FREE Delivery</span>
            </div>
            <?php endif; ?>

            <?php if ($savings): ?>
            <span class="sp-card__savings"><?php echo esc_html($savings); ?></span>
            <?php endif; ?>

            <a href="<?php echo esc_url($url); ?>" class="sp-btn sp-btn--amazon sp-btn--sm" target="_blank" rel="nofollow sponsored noopener">
                View on Amazon
            </a>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('product_card_pro', 'sp_product_card_pro_shortcode');

/**
 * Reading Time Calculator
 */
function sp_reading_time($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $content = get_post_field('post_content', $post_id);
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // Average reading speed

    return max(1, $reading_time);
}

/**
 * Hide comments count if zero
 */
function sp_hide_zero_comments($count) {
    if ($count == 0) {
        return '';
    }
    return $count;
}
// add_filter('get_comments_number', 'sp_hide_zero_comments');

/**
 * Enhanced Author Bio Shortcode
 */
function sp_author_bio_shortcode($atts) {
    $atts = shortcode_atts(array(
        'show_social' => 'true',
        'show_posts'  => 'true',
    ), $atts);

    $author_id = get_the_author_meta('ID');
    $author_name = get_the_author();
    $author_bio = get_the_author_meta('description');
    $author_avatar = get_avatar($author_id, 120);
    $author_posts_url = get_author_posts_url($author_id);
    $author_posts_count = count_user_posts($author_id);

    ob_start();
    ?>
    <div class="sp-author-bio-enhanced">
        <div class="sp-author-bio__avatar">
            <?php echo $author_avatar; ?>
        </div>
        <div class="sp-author-bio__content">
            <h3 class="sp-author-bio__name">
                <a href="<?php echo esc_url($author_posts_url); ?>"><?php echo esc_html($author_name); ?></a>
            </h3>
            <?php if ($author_bio): ?>
            <p class="sp-author-bio__description"><?php echo wp_kses_post($author_bio); ?></p>
            <?php endif; ?>

            <?php if ($atts['show_posts'] === 'true'): ?>
            <p class="sp-author-bio__posts-count">
                <a href="<?php echo esc_url($author_posts_url); ?>">
                    <?php printf(_n('%d article', '%d articles', $author_posts_count, 'shelzyperkins'), $author_posts_count); ?>
                </a>
            </p>
            <?php endif; ?>

            <?php if ($atts['show_social'] === 'true'): ?>
            <div class="sp-author-bio__social">
                <a href="https://pinterest.com/shelzyperkins" target="_blank" rel="noopener" class="sp-author-bio__social-link">Pinterest</a>
                <a href="https://instagram.com/shelzyperkins" target="_blank" rel="noopener" class="sp-author-bio__social-link">Instagram</a>
                <a href="https://tiktok.com/@shelzyperkins" target="_blank" rel="noopener" class="sp-author-bio__social-link">TikTok</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('author_bio', 'sp_author_bio_shortcode');

/**
 * Related Posts Shortcode
 */
function sp_related_posts_shortcode($atts) {
    $atts = shortcode_atts(array(
        'count' => 3,
    ), $atts);

    $categories = wp_get_post_categories(get_the_ID());

    if (empty($categories)) {
        return '';
    }

    $related_query = new WP_Query(array(
        'posts_per_page' => intval($atts['count']),
        'post__not_in'   => array(get_the_ID()),
        'category__in'   => $categories,
        'orderby'        => 'rand',
    ));

    if (!$related_query->have_posts()) {
        return '';
    }

    ob_start();
    ?>
    <div class="sp-related-posts-widget">
        <h3 class="sp-related-posts__title">You Might Also Like</h3>
        <div class="sp-grid sp-grid--<?php echo esc_attr($atts['count']); ?>">
            <?php
            while ($related_query->have_posts()): $related_query->the_post();
                get_template_part('template-parts/content', 'card');
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('related_posts', 'sp_related_posts_shortcode');

/**
 * Exit Intent Popup HTML
 */
function sp_exit_intent_popup() {
    if (is_admin()) {
        return;
    }

    // Only show on posts and pages
    if (!is_singular()) {
        return;
    }

    // Check if user has already subscribed (cookie check)
    if (isset($_COOKIE['sp_subscribed'])) {
        return;
    }
    ?>
    <div class="sp-popup-overlay" id="exit-popup" aria-hidden="true">
        <div class="sp-popup" role="dialog" aria-labelledby="popup-title" aria-modal="true">
            <button class="sp-popup__close" aria-label="Close popup" id="popup-close">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6L6 18M6 6l12 12"></path>
                </svg>
            </button>
            <div class="sp-popup__content">
                <div class="sp-popup__icon">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"></path>
                    </svg>
                </div>
                <h2 class="sp-popup__title" id="popup-title">Wait! Don't Miss Out!</h2>
                <p class="sp-popup__text">Get my exclusive "Top 10 Hidden Amazon Deals" guide delivered to your inbox - FREE!</p>
                <form class="sp-popup__form" id="popup-form">
                    <input type="email" class="sp-popup__input" placeholder="Enter your email" required>
                    <button type="submit" class="sp-btn sp-btn--primary sp-btn--lg">Get Free Guide</button>
                </form>
                <p class="sp-popup__disclaimer">No spam, ever. Unsubscribe anytime.</p>
            </div>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'sp_exit_intent_popup');

/**
 * Back to Top Button
 */
function sp_back_to_top_button() {
    ?>
    <button class="sp-back-to-top" aria-label="<?php esc_attr_e('Back to top', 'shelzyperkins'); ?>">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="18 15 12 9 6 15"></polyline>
        </svg>
    </button>
    <?php
}
add_action('wp_footer', 'sp_back_to_top_button');

/**
 * Mobile Bottom Navigation
 */
function sp_mobile_bottom_nav() {
    if (is_admin()) {
        return;
    }
    ?>
    <nav class="sp-mobile-nav" aria-label="<?php esc_attr_e('Mobile Navigation', 'shelzyperkins'); ?>">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="sp-mobile-nav__item <?php echo is_front_page() ? 'is-active' : ''; ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
            </svg>
            <span>Home</span>
        </a>
        <a href="<?php echo esc_url(home_url('/deals/')); ?>" class="sp-mobile-nav__item">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"></path>
                <line x1="7" y1="7" x2="7.01" y2="7"></line>
            </svg>
            <span>Deals</span>
        </a>
        <a href="https://www.amazon.com/shop/shelzyperkins?tag=<?php echo esc_attr(SP_AFFILIATE_TAG); ?>" class="sp-mobile-nav__item sp-mobile-nav__item--amazon" target="_blank" rel="nofollow sponsored noopener">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M21.5 15.5c-.2 0-.4 0-.6.1-.2.1-.4.2-.6.4-.9.8-2.3 1.4-3.9 1.9-1.6.4-3.4.7-5.4.7s-3.8-.2-5.4-.7c-1.6-.4-3-1.1-3.9-1.9-.2-.2-.4-.3-.6-.4-.2-.1-.4-.1-.6-.1-.3 0-.5.1-.7.3-.2.2-.3.5-.3.8 0 .2.1.5.2.7.1.2.3.4.5.5 1.1 1 2.7 1.8 4.6 2.3 1.9.5 4 .8 6.2.8s4.3-.3 6.2-.8c1.9-.5 3.5-1.3 4.6-2.3.2-.2.4-.3.5-.5.1-.2.2-.4.2-.7 0-.3-.1-.6-.3-.8-.2-.2-.4-.3-.7-.3z"/>
            </svg>
            <span>Shop</span>
        </a>
        <a href="<?php echo esc_url(home_url('/category/')); ?>" class="sp-mobile-nav__item">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
            </svg>
            <span>Categories</span>
        </a>
        <button class="sp-mobile-nav__item" id="mobile-search-toggle">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="M21 21l-4.35-4.35"></path>
            </svg>
            <span>Search</span>
        </button>
    </nav>
    <?php
}
add_action('wp_footer', 'sp_mobile_bottom_nav');

/**
 * Ensure all Amazon links have affiliate tag
 */
function sp_ensure_affiliate_tag($content) {
    // Match Amazon links without the affiliate tag
    $pattern = '/(https?:\/\/(?:www\.)?amazon\.com\/[^\s"\'<>]*?)(?=["\'\s<>]|$)/i';

    $content = preg_replace_callback($pattern, function($matches) {
        $url = $matches[1];

        // Check if tag parameter already exists
        if (strpos($url, 'tag=') !== false) {
            // Replace existing tag with our tag
            $url = preg_replace('/tag=[^&]+/', 'tag=' . SP_AFFILIATE_TAG, $url);
        } else {
            // Add tag parameter
            $separator = (strpos($url, '?') !== false) ? '&' : '?';
            $url .= $separator . 'tag=' . SP_AFFILIATE_TAG;
        }

        return $url;
    }, $content);

    return $content;
}
add_filter('the_content', 'sp_ensure_affiliate_tag', 100);
add_filter('widget_text_content', 'sp_ensure_affiliate_tag', 100);
