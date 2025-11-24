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
 * ==========================================================================
 * AMAZON BOUNTY PROGRAM SHORTCODES
 * These generate flat-rate bounty commissions ($3-$5 each)
 * ==========================================================================
 */

/**
 * Amazon Bounty Links Configuration
 */
function sp_get_bounty_programs() {
    return array(
        'prime' => array(
            'name' => 'Amazon Prime',
            'url' => 'https://www.amazon.com/amazonprime?tag=' . SP_AFFILIATE_TAG,
            'bounty' => '$3.00',
            'description' => 'Free 2-day shipping, Prime Video, Prime Music & more',
            'cta' => 'Try Prime FREE',
            'icon' => '⚡',
        ),
        'audible' => array(
            'name' => 'Audible',
            'url' => 'https://www.amazon.com/hz/audible/mlp?tag=' . SP_AFFILIATE_TAG,
            'bounty' => '$5.00',
            'description' => 'Get 2 free audiobooks with your trial',
            'cta' => 'Try Audible FREE',
            'icon' => '🎧',
        ),
        'kindle_unlimited' => array(
            'name' => 'Kindle Unlimited',
            'url' => 'https://www.amazon.com/kindle-dbs/hz/subscribe/ku?tag=' . SP_AFFILIATE_TAG,
            'bounty' => '$3.00',
            'description' => 'Unlimited reading on any device',
            'cta' => 'Try Kindle Unlimited',
            'icon' => '📚',
        ),
        'amazon_music' => array(
            'name' => 'Amazon Music Unlimited',
            'url' => 'https://www.amazon.com/music/unlimited?tag=' . SP_AFFILIATE_TAG,
            'bounty' => '$3.00',
            'description' => '100 million songs, ad-free',
            'cta' => 'Try Music Unlimited',
            'icon' => '🎵',
        ),
        'amazon_fresh' => array(
            'name' => 'Amazon Fresh',
            'url' => 'https://www.amazon.com/alm/storefront?tag=' . SP_AFFILIATE_TAG,
            'bounty' => '$3.00',
            'description' => 'Grocery delivery in as fast as 2 hours',
            'cta' => 'Try Fresh',
            'icon' => '🥑',
        ),
        'amazon_business' => array(
            'name' => 'Amazon Business',
            'url' => 'https://www.amazon.com/business/register?tag=' . SP_AFFILIATE_TAG,
            'bounty' => '$15.00',
            'description' => 'Business pricing & bulk discounts',
            'cta' => 'Start Business Account',
            'icon' => '💼',
        ),
    );
}

/**
 * Shortcode: Amazon Bounty Card
 * Usage: [bounty_card program="prime"]
 */
function sp_bounty_card_shortcode($atts) {
    $atts = shortcode_atts(array(
        'program' => 'prime',
        'style' => 'card', // card, inline, banner
    ), $atts);

    $programs = sp_get_bounty_programs();

    if (!isset($programs[$atts['program']])) {
        return '';
    }

    $program = $programs[$atts['program']];

    ob_start();

    if ($atts['style'] === 'banner') {
        ?>
        <div class="sp-bounty-banner sp-bounty-banner--<?php echo esc_attr($atts['program']); ?>">
            <div class="sp-bounty-banner__content">
                <span class="sp-bounty-banner__icon"><?php echo $program['icon']; ?></span>
                <div class="sp-bounty-banner__text">
                    <strong><?php echo esc_html($program['name']); ?></strong>
                    <span><?php echo esc_html($program['description']); ?></span>
                </div>
                <a href="<?php echo esc_url($program['url']); ?>" class="sp-btn sp-btn--amazon sp-btn--sm" target="_blank" rel="nofollow sponsored noopener">
                    <?php echo esc_html($program['cta']); ?>
                </a>
            </div>
        </div>
        <?php
    } elseif ($atts['style'] === 'inline') {
        ?>
        <a href="<?php echo esc_url($program['url']); ?>" class="sp-bounty-inline" target="_blank" rel="nofollow sponsored noopener">
            <?php echo $program['icon']; ?> <?php echo esc_html($program['cta']); ?>
        </a>
        <?php
    } else {
        ?>
        <div class="sp-bounty-card sp-bounty-card--<?php echo esc_attr($atts['program']); ?>">
            <div class="sp-bounty-card__icon"><?php echo $program['icon']; ?></div>
            <h4 class="sp-bounty-card__title"><?php echo esc_html($program['name']); ?></h4>
            <p class="sp-bounty-card__description"><?php echo esc_html($program['description']); ?></p>
            <a href="<?php echo esc_url($program['url']); ?>" class="sp-btn sp-btn--amazon" target="_blank" rel="nofollow sponsored noopener">
                <?php echo esc_html($program['cta']); ?>
            </a>
        </div>
        <?php
    }

    return ob_get_clean();
}
add_shortcode('bounty_card', 'sp_bounty_card_shortcode');

/**
 * Shortcode: All Bounty Programs Grid
 * Usage: [bounty_grid columns="3"]
 */
function sp_bounty_grid_shortcode($atts) {
    $atts = shortcode_atts(array(
        'columns' => '3',
        'exclude' => '', // comma-separated list of programs to exclude
    ), $atts);

    $programs = sp_get_bounty_programs();
    $exclude = array_filter(array_map('trim', explode(',', $atts['exclude'])));

    ob_start();
    ?>
    <div class="sp-bounty-grid sp-bounty-grid--cols-<?php echo esc_attr($atts['columns']); ?>">
        <?php foreach ($programs as $key => $program): ?>
            <?php if (in_array($key, $exclude)) continue; ?>
            <div class="sp-bounty-card sp-bounty-card--<?php echo esc_attr($key); ?>">
                <div class="sp-bounty-card__icon"><?php echo $program['icon']; ?></div>
                <h4 class="sp-bounty-card__title"><?php echo esc_html($program['name']); ?></h4>
                <p class="sp-bounty-card__description"><?php echo esc_html($program['description']); ?></p>
                <a href="<?php echo esc_url($program['url']); ?>" class="sp-btn sp-btn--amazon sp-btn--sm" target="_blank" rel="nofollow sponsored noopener">
                    <?php echo esc_html($program['cta']); ?>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('bounty_grid', 'sp_bounty_grid_shortcode');

/**
 * ==========================================================================
 * ENHANCED PRODUCT COMPONENTS
 * ==========================================================================
 */

/**
 * Shortcode: Product Card with Price History
 * Usage: [product_card_enhanced asin="..." price="..." lowest_price="..." highest_price="..."]
 */
function sp_product_card_enhanced_shortcode($atts) {
    $atts = shortcode_atts(array(
        'asin'          => '',
        'title'         => 'Product',
        'price'         => '',
        'original_price' => '',
        'lowest_price'  => '',
        'highest_price' => '',
        'image'         => '',
        'badge'         => '',
        'rating'        => '',
        'reviews'       => '',
    ), $atts);

    if (empty($atts['asin'])) {
        return '';
    }

    $url = "https://www.amazon.com/dp/{$atts['asin']}?tag=" . SP_AFFILIATE_TAG;

    // Calculate price position
    $price_indicator = '';
    if (!empty($atts['lowest_price']) && !empty($atts['highest_price']) && !empty($atts['price'])) {
        $current = floatval($atts['price']);
        $lowest = floatval($atts['lowest_price']);
        $highest = floatval($atts['highest_price']);

        if ($current <= $lowest * 1.05) {
            $price_indicator = 'lowest';
        } elseif ($current <= $lowest + ($highest - $lowest) * 0.33) {
            $price_indicator = 'good';
        } elseif ($current <= $lowest + ($highest - $lowest) * 0.66) {
            $price_indicator = 'average';
        } else {
            $price_indicator = 'high';
        }
    }

    ob_start();
    ?>
    <div class="sp-card sp-product-card sp-product-card--enhanced" data-asin="<?php echo esc_attr($atts['asin']); ?>">
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

            <?php if (!empty($atts['rating'])): ?>
            <div class="sp-card__rating">
                <span class="sp-stars"><?php echo str_repeat('★', floor(floatval($atts['rating']))); ?><?php echo (floatval($atts['rating']) - floor(floatval($atts['rating']))) >= 0.5 ? '½' : ''; ?></span>
                <span class="sp-rating-text"><?php echo esc_html($atts['rating']); ?></span>
                <?php if (!empty($atts['reviews'])): ?>
                <span class="sp-reviews">(<?php echo esc_html(number_format(intval($atts['reviews']))); ?> reviews)</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="sp-card__price">
                <?php if (!empty($atts['price'])): ?>
                <span class="sp-card__price-current">$<?php echo esc_html($atts['price']); ?></span>
                <?php endif; ?>
                <?php if (!empty($atts['original_price']) && floatval($atts['original_price']) > floatval($atts['price'])): ?>
                <span class="sp-card__price-original">$<?php echo esc_html($atts['original_price']); ?></span>
                <?php endif; ?>
            </div>

            <?php if ($price_indicator): ?>
            <div class="sp-price-history">
                <div class="sp-price-history__bar">
                    <div class="sp-price-history__indicator sp-price-history__indicator--<?php echo esc_attr($price_indicator); ?>"></div>
                </div>
                <div class="sp-price-history__labels">
                    <span>$<?php echo esc_html($atts['lowest_price']); ?></span>
                    <span class="sp-price-history__status sp-price-history__status--<?php echo esc_attr($price_indicator); ?>">
                        <?php
                        switch ($price_indicator) {
                            case 'lowest': echo '🔥 Near Lowest!'; break;
                            case 'good': echo '✓ Good Price'; break;
                            case 'average': echo '— Average'; break;
                            case 'high': echo '⚠ Wait for Drop'; break;
                        }
                        ?>
                    </span>
                    <span>$<?php echo esc_html($atts['highest_price']); ?></span>
                </div>
            </div>
            <?php endif; ?>

            <a href="<?php echo esc_url($url); ?>" class="sp-btn sp-btn--amazon" target="_blank" rel="nofollow sponsored noopener">
                View on Amazon
            </a>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('product_card_enhanced', 'sp_product_card_enhanced_shortcode');

/**
 * Shortcode: Quick Deal Alert Box
 * Usage: [deal_alert title="Flash Sale!" expires="2024-12-25" asin="..."]
 */
function sp_deal_alert_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title'       => 'Limited Time Deal!',
        'description' => '',
        'asin'        => '',
        'expires'     => '',
        'type'        => 'default', // default, flash, lightning, prime
    ), $atts);

    $url = '';
    if (!empty($atts['asin'])) {
        $url = "https://www.amazon.com/dp/{$atts['asin']}?tag=" . SP_AFFILIATE_TAG;
    }

    ob_start();
    ?>
    <div class="sp-deal-alert sp-deal-alert--<?php echo esc_attr($atts['type']); ?>" <?php if (!empty($atts['expires'])): ?>data-expires="<?php echo esc_attr($atts['expires']); ?>"<?php endif; ?>>
        <div class="sp-deal-alert__header">
            <?php if ($atts['type'] === 'flash'): ?>
            <span class="sp-deal-alert__icon">⚡</span>
            <?php elseif ($atts['type'] === 'lightning'): ?>
            <span class="sp-deal-alert__icon">🔥</span>
            <?php elseif ($atts['type'] === 'prime'): ?>
            <span class="sp-deal-alert__icon">✨</span>
            <?php endif; ?>
            <h3 class="sp-deal-alert__title"><?php echo esc_html($atts['title']); ?></h3>
        </div>
        <?php if (!empty($atts['description'])): ?>
        <p class="sp-deal-alert__description"><?php echo esc_html($atts['description']); ?></p>
        <?php endif; ?>
        <?php if (!empty($atts['expires'])): ?>
        <div class="sp-deal-alert__countdown" data-countdown="<?php echo esc_attr($atts['expires']); ?>">
            <span class="sp-countdown-label">Ends in:</span>
            <span class="sp-countdown-timer">Loading...</span>
        </div>
        <?php endif; ?>
        <?php if (!empty($url)): ?>
        <a href="<?php echo esc_url($url); ?>" class="sp-btn sp-btn--amazon" target="_blank" rel="nofollow sponsored noopener">
            Grab This Deal
        </a>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('deal_alert', 'sp_deal_alert_shortcode');

/**
 * Shortcode: Seasonal Gift Guide CTA
 * Usage: [seasonal_cta season="christmas" category="tech"]
 */
function sp_seasonal_cta_shortcode($atts) {
    $atts = shortcode_atts(array(
        'season'   => 'holiday',
        'category' => '',
        'title'    => '',
        'subtitle' => '',
    ), $atts);

    $seasons = array(
        'christmas' => array(
            'title' => '🎄 Christmas Gift Guide',
            'subtitle' => 'Find the perfect gift for everyone on your list',
            'class' => 'christmas',
        ),
        'holiday' => array(
            'title' => '🎁 Holiday Gift Guide',
            'subtitle' => 'Curated gifts they\'ll actually love',
            'class' => 'holiday',
        ),
        'valentines' => array(
            'title' => '💕 Valentine\'s Day Gifts',
            'subtitle' => 'Show them you care',
            'class' => 'valentines',
        ),
        'mothers_day' => array(
            'title' => '💐 Mother\'s Day Gifts',
            'subtitle' => 'Thoughtful gifts for Mom',
            'class' => 'mothers-day',
        ),
        'fathers_day' => array(
            'title' => '👔 Father\'s Day Gifts',
            'subtitle' => 'Gifts Dad will actually use',
            'class' => 'fathers-day',
        ),
        'back_to_school' => array(
            'title' => '📚 Back to School Essentials',
            'subtitle' => 'Everything for a successful school year',
            'class' => 'back-to-school',
        ),
        'summer' => array(
            'title' => '☀️ Summer Must-Haves',
            'subtitle' => 'Beat the heat with these finds',
            'class' => 'summer',
        ),
        'black_friday' => array(
            'title' => '🛒 Black Friday Deals',
            'subtitle' => 'The best deals of the year',
            'class' => 'black-friday',
        ),
        'prime_day' => array(
            'title' => '⚡ Prime Day Deals',
            'subtitle' => 'Exclusive savings for Prime members',
            'class' => 'prime-day',
        ),
    );

    $season_data = isset($seasons[$atts['season']]) ? $seasons[$atts['season']] : $seasons['holiday'];

    // Override with custom title/subtitle if provided
    if (!empty($atts['title'])) {
        $season_data['title'] = $atts['title'];
    }
    if (!empty($atts['subtitle'])) {
        $season_data['subtitle'] = $atts['subtitle'];
    }

    $url = home_url('/gift-guides/');
    if (!empty($atts['category'])) {
        $url = home_url('/category/' . sanitize_title($atts['category']) . '/');
    }

    ob_start();
    ?>
    <div class="sp-seasonal-cta sp-seasonal-cta--<?php echo esc_attr($season_data['class']); ?>">
        <div class="sp-seasonal-cta__content">
            <h3 class="sp-seasonal-cta__title"><?php echo esc_html($season_data['title']); ?></h3>
            <p class="sp-seasonal-cta__subtitle"><?php echo esc_html($season_data['subtitle']); ?></p>
            <a href="<?php echo esc_url($url); ?>" class="sp-btn sp-btn--white">
                Browse Gift Ideas →
            </a>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('seasonal_cta', 'sp_seasonal_cta_shortcode');

/**
 * Shortcode: Above-the-Fold Quick Buy
 * Usage: [quick_buy asin="..." title="..." price="..."]
 */
function sp_quick_buy_shortcode($atts) {
    $atts = shortcode_atts(array(
        'asin'  => '',
        'title' => '',
        'price' => '',
        'image' => '',
        'badge' => 'Featured Pick',
    ), $atts);

    if (empty($atts['asin'])) {
        return '';
    }

    $url = "https://www.amazon.com/dp/{$atts['asin']}?tag=" . SP_AFFILIATE_TAG;

    ob_start();
    ?>
    <div class="sp-quick-buy">
        <span class="sp-quick-buy__badge"><?php echo esc_html($atts['badge']); ?></span>
        <div class="sp-quick-buy__content">
            <?php if (!empty($atts['image'])): ?>
            <img src="<?php echo esc_url($atts['image']); ?>" alt="<?php echo esc_attr($atts['title']); ?>" class="sp-quick-buy__image">
            <?php endif; ?>
            <div class="sp-quick-buy__info">
                <h4 class="sp-quick-buy__title"><?php echo esc_html($atts['title']); ?></h4>
                <?php if (!empty($atts['price'])): ?>
                <span class="sp-quick-buy__price">$<?php echo esc_html($atts['price']); ?></span>
                <?php endif; ?>
            </div>
            <a href="<?php echo esc_url($url); ?>" class="sp-btn sp-btn--amazon sp-btn--lg" target="_blank" rel="nofollow sponsored noopener">
                Buy on Amazon
            </a>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('quick_buy', 'sp_quick_buy_shortcode');

/**
 * ==========================================================================
 * EXIT INTENT POPUP
 * ==========================================================================
 */

/**
 * Add exit intent popup to footer
 */
function sp_exit_intent_popup() {
    if (is_admin()) return;
    ?>
    <div id="sp-exit-popup" class="sp-exit-popup" role="dialog" aria-modal="true" aria-labelledby="exit-popup-title">
        <div class="sp-exit-popup__overlay"></div>
        <div class="sp-exit-popup__content">
            <button class="sp-exit-popup__close" aria-label="Close popup">&times;</button>
            <div class="sp-exit-popup__icon">🛒</div>
            <h2 id="exit-popup-title" class="sp-exit-popup__title">Wait! Don't Miss Out!</h2>
            <p class="sp-exit-popup__text">Get my best Amazon deals delivered straight to your inbox. Join 10,000+ smart shoppers!</p>
            <form class="sp-exit-popup__form" id="exit-popup-form">
                <input type="email" name="email" placeholder="Your email address" required>
                <button type="submit" class="sp-btn sp-btn--primary">Get the Deals</button>
            </form>
            <p class="sp-exit-popup__disclaimer">No spam, ever. Unsubscribe anytime.</p>
            <div class="sp-exit-popup__bonus">
                <span>🎁 BONUS:</span> Get my Top 10 Hidden Amazon Gems guide instantly!
            </div>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'sp_exit_intent_popup');

/**
 * Add inline CTA after specific paragraph
 * Auto-inserts bounty CTA in long posts
 */
function sp_auto_insert_ctas($content) {
    if (!is_single() || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    // Count paragraphs
    $paragraphs = explode('</p>', $content);
    $count = count($paragraphs);

    // Only insert in posts with 5+ paragraphs
    if ($count < 5) {
        return $content;
    }

    // Insert Prime bounty card after 3rd paragraph
    $prime_cta = do_shortcode('[bounty_card program="prime" style="banner"]');

    // Insert after 3rd paragraph
    $insert_at = 3;
    if ($count > $insert_at) {
        $paragraphs[$insert_at] .= $prime_cta;
    }

    // Insert Audible bounty card after 6th paragraph if post is long enough
    if ($count > 7) {
        $audible_cta = do_shortcode('[bounty_card program="audible" style="banner"]');
        $paragraphs[6] .= $audible_cta;
    }

    return implode('</p>', $paragraphs);
}
add_filter('the_content', 'sp_auto_insert_ctas', 20);
