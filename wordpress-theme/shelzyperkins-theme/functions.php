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

/* ==========================================================================
   NEW BLOG IMPROVEMENTS - P0 Priority Features
   ========================================================================== */

/**
 * Auto-generate Table of Contents for long posts
 */
function sp_auto_toc($content) {
    if (!is_single() || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    // Only add TOC for posts with 1500+ words
    if (str_word_count(strip_tags($content)) < 1500) {
        return $content;
    }

    // Find all H2 and H3 headings
    preg_match_all('/<h([23])([^>]*)>(.*?)<\/h[23]>/i', $content, $matches, PREG_SET_ORDER);

    if (count($matches) < 3) {
        return $content;
    }

    $toc = '<nav class="sp-toc" aria-label="Table of Contents">';
    $toc .= '<div class="sp-toc__header"><h4 class="sp-toc__title">Quick Navigation</h4>';
    $toc .= '<button class="sp-toc__toggle" aria-expanded="true">−</button></div>';
    $toc .= '<ol class="sp-toc__list">';

    $counter = 0;
    foreach ($matches as $match) {
        $level = $match[1];
        $heading_text = strip_tags($match[3]);
        $slug = sanitize_title($heading_text);

        // Add ID to heading in content
        $new_heading = sprintf(
            '<h%s%s id="%s">%s</h%s>',
            $level,
            $match[2],
            $slug,
            $match[3],
            $level
        );
        $content = str_replace($match[0], $new_heading, $content);

        // Add to TOC
        $indent_class = ($level == '3') ? ' sp-toc__item--indent' : '';
        $toc .= sprintf(
            '<li class="sp-toc__item%s"><a href="#%s">%s</a></li>',
            $indent_class,
            $slug,
            esc_html($heading_text)
        );
        $counter++;
    }

    $toc .= '</ol></nav>';

    // Insert TOC after first paragraph
    $first_p_pos = strpos($content, '</p>');
    if ($first_p_pos !== false) {
        $content = substr_replace($content, '</p>' . $toc, $first_p_pos, 4);
    } else {
        $content = $toc . $content;
    }

    return $content;
}
add_filter('the_content', 'sp_auto_toc', 5);

/**
 * Shortcode: Quick Picks Summary Box
 * Usage: [quick_picks best="Product A|ASIN|$29" budget="Product B|ASIN|$15" premium="Product C|ASIN|$99"]
 */
function sp_quick_picks_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'title'   => "TL;DR - Our Top Picks",
        'best'    => '',
        'budget'  => '',
        'premium' => '',
        'runner'  => '',
    ), $atts);

    $picks = array();

    if (!empty($atts['best'])) {
        $parts = explode('|', $atts['best']);
        $picks[] = array('label' => 'Best Overall', 'name' => $parts[0], 'asin' => $parts[1] ?? '', 'price' => $parts[2] ?? '');
    }
    if (!empty($atts['budget'])) {
        $parts = explode('|', $atts['budget']);
        $picks[] = array('label' => 'Budget Pick', 'name' => $parts[0], 'asin' => $parts[1] ?? '', 'price' => $parts[2] ?? '');
    }
    if (!empty($atts['premium'])) {
        $parts = explode('|', $atts['premium']);
        $picks[] = array('label' => 'Premium Choice', 'name' => $parts[0], 'asin' => $parts[1] ?? '', 'price' => $parts[2] ?? '');
    }
    if (!empty($atts['runner'])) {
        $parts = explode('|', $atts['runner']);
        $picks[] = array('label' => 'Runner Up', 'name' => $parts[0], 'asin' => $parts[1] ?? '', 'price' => $parts[2] ?? '');
    }

    if (empty($picks) && empty($content)) {
        return '';
    }

    ob_start();
    ?>
    <div class="sp-quick-picks">
        <div class="sp-quick-picks__header">
            <svg class="sp-quick-picks__icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
            </svg>
            <h3 class="sp-quick-picks__title"><?php echo esc_html($atts['title']); ?></h3>
        </div>
        <?php if (!empty($picks)): ?>
        <ol class="sp-quick-picks__list">
            <?php foreach ($picks as $pick): ?>
            <li class="sp-quick-picks__item">
                <span class="sp-quick-picks__label"><?php echo esc_html($pick['label']); ?>:</span>
                <?php if (!empty($pick['asin'])): ?>
                <a href="https://www.amazon.com/dp/<?php echo esc_attr($pick['asin']); ?>?tag=<?php echo SP_AFFILIATE_TAG; ?>"
                   class="sp-quick-picks__link" target="_blank" rel="nofollow sponsored noopener">
                    <?php echo esc_html($pick['name']); ?>
                </a>
                <?php else: ?>
                <span class="sp-quick-picks__name"><?php echo esc_html($pick['name']); ?></span>
                <?php endif; ?>
                <?php if (!empty($pick['price'])): ?>
                <span class="sp-quick-picks__price"><?php echo esc_html($pick['price']); ?></span>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ol>
        <?php endif; ?>
        <?php if ($content): ?>
        <div class="sp-quick-picks__content"><?php echo do_shortcode($content); ?></div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('quick_picks', 'sp_quick_picks_shortcode');

/**
 * Shortcode: FAQ Section with Schema
 * Usage: [faq_section][faq question="Question here?"]Answer here[/faq][/faq_section]
 */
function sp_faq_section_shortcode($atts, $content = null) {
    if (!$content) return '';

    ob_start();
    ?>
    <section class="sp-faq-section" itemscope itemtype="https://schema.org/FAQPage">
        <h2 class="sp-faq-section__title">Frequently Asked Questions</h2>
        <div class="sp-faq-section__list">
            <?php echo do_shortcode($content); ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('faq_section', 'sp_faq_section_shortcode');

function sp_faq_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'question' => '',
    ), $atts);

    if (empty($atts['question']) || !$content) return '';

    ob_start();
    ?>
    <div class="sp-faq" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
        <button class="sp-faq__question" aria-expanded="false" itemprop="name">
            <?php echo esc_html($atts['question']); ?>
            <svg class="sp-faq__icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
        </button>
        <div class="sp-faq__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer" hidden>
            <div itemprop="text">
                <?php echo wp_kses_post($content); ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('faq', 'sp_faq_shortcode');

/**
 * Shortcode: Price History Chart
 * Usage: [price_history asin="B08N5WRWNW" current="29.99" high="49.99" low="24.99" avg="35.99"]
 */
function sp_price_history_shortcode($atts) {
    $atts = shortcode_atts(array(
        'asin'    => '',
        'current' => '',
        'high'    => '',
        'low'     => '',
        'avg'     => '',
        'title'   => 'Price History (Last 30 Days)',
    ), $atts);

    if (empty($atts['current'])) return '';

    $current = floatval($atts['current']);
    $avg = floatval($atts['avg']) ?: $current;
    $high = floatval($atts['high']) ?: $current;
    $low = floatval($atts['low']) ?: $current;

    // Calculate if it's a good deal
    $diff_from_avg = (($avg - $current) / $avg) * 100;
    $verdict_class = '';
    $verdict_text = '';

    if ($diff_from_avg >= 15) {
        $verdict_class = 'sp-price-history__verdict--great';
        $verdict_text = sprintf('Great deal! %.0f%% below average', $diff_from_avg);
    } elseif ($diff_from_avg >= 5) {
        $verdict_class = 'sp-price-history__verdict--good';
        $verdict_text = sprintf('Good price - %.0f%% below average', $diff_from_avg);
    } elseif ($diff_from_avg >= -5) {
        $verdict_class = 'sp-price-history__verdict--fair';
        $verdict_text = 'Fair price - near average';
    } else {
        $verdict_class = 'sp-price-history__verdict--wait';
        $verdict_text = sprintf('Consider waiting - %.0f%% above average', abs($diff_from_avg));
    }

    ob_start();
    ?>
    <div class="sp-price-history" data-asin="<?php echo esc_attr($atts['asin']); ?>">
        <h4 class="sp-price-history__title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                <polyline points="17 6 23 6 23 12"></polyline>
            </svg>
            <?php echo esc_html($atts['title']); ?>
        </h4>
        <div class="sp-price-history__chart">
            <div class="sp-price-history__bar">
                <div class="sp-price-history__range" style="--low: <?php echo esc_attr($low); ?>; --high: <?php echo esc_attr($high); ?>; --current: <?php echo esc_attr($current); ?>; --avg: <?php echo esc_attr($avg); ?>;">
                    <span class="sp-price-history__marker sp-price-history__marker--low" title="30-day low">$<?php echo esc_html(number_format($low, 2)); ?></span>
                    <span class="sp-price-history__marker sp-price-history__marker--avg" title="30-day average">Avg: $<?php echo esc_html(number_format($avg, 2)); ?></span>
                    <span class="sp-price-history__marker sp-price-history__marker--high" title="30-day high">$<?php echo esc_html(number_format($high, 2)); ?></span>
                    <span class="sp-price-history__marker sp-price-history__marker--current" title="Current price">Now: $<?php echo esc_html(number_format($current, 2)); ?></span>
                </div>
            </div>
        </div>
        <div class="sp-price-history__stats">
            <div class="sp-price-history__stat">
                <span class="sp-price-history__stat-label">Low</span>
                <span class="sp-price-history__stat-value sp-price-history__stat-value--low">$<?php echo esc_html(number_format($low, 2)); ?></span>
            </div>
            <div class="sp-price-history__stat">
                <span class="sp-price-history__stat-label">Average</span>
                <span class="sp-price-history__stat-value">$<?php echo esc_html(number_format($avg, 2)); ?></span>
            </div>
            <div class="sp-price-history__stat">
                <span class="sp-price-history__stat-label">High</span>
                <span class="sp-price-history__stat-value sp-price-history__stat-value--high">$<?php echo esc_html(number_format($high, 2)); ?></span>
            </div>
        </div>
        <p class="sp-price-history__verdict <?php echo esc_attr($verdict_class); ?>">
            <?php echo esc_html($verdict_text); ?>
        </p>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('price_history', 'sp_price_history_shortcode');

/**
 * Shortcode: Price Alert Signup
 * Usage: [price_alert asin="B08N5WRWNW" product="Product Name" current_price="29.99"]
 */
function sp_price_alert_shortcode($atts) {
    $atts = shortcode_atts(array(
        'asin'          => '',
        'product'       => 'this product',
        'current_price' => '',
    ), $atts);

    ob_start();
    ?>
    <div class="sp-price-alert" data-asin="<?php echo esc_attr($atts['asin']); ?>">
        <div class="sp-price-alert__icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
        </div>
        <div class="sp-price-alert__content">
            <h4 class="sp-price-alert__title">Get Price Drop Alerts</h4>
            <p class="sp-price-alert__text">Be notified when <?php echo esc_html($atts['product']); ?> drops in price.</p>
            <form class="sp-price-alert__form" data-product="<?php echo esc_attr($atts['product']); ?>" data-price="<?php echo esc_attr($atts['current_price']); ?>">
                <input type="email" name="email" placeholder="Enter your email" required class="sp-price-alert__input">
                <button type="submit" class="sp-btn sp-btn--primary sp-btn--sm">Alert Me</button>
            </form>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('price_alert', 'sp_price_alert_shortcode');

/**
 * Shortcode: Pros and Cons Box
 * Usage: [pros_cons pros="Pro 1|Pro 2|Pro 3" cons="Con 1|Con 2"]
 */
function sp_pros_cons_shortcode($atts) {
    $atts = shortcode_atts(array(
        'pros' => '',
        'cons' => '',
        'title' => '',
    ), $atts);

    $pros = array_filter(explode('|', $atts['pros']));
    $cons = array_filter(explode('|', $atts['cons']));

    if (empty($pros) && empty($cons)) return '';

    ob_start();
    ?>
    <div class="sp-pros-cons">
        <?php if (!empty($atts['title'])): ?>
        <h4 class="sp-pros-cons__title"><?php echo esc_html($atts['title']); ?></h4>
        <?php endif; ?>
        <div class="sp-pros-cons__grid">
            <?php if (!empty($pros)): ?>
            <div class="sp-pros-cons__column sp-pros-cons__column--pros">
                <h5 class="sp-pros-cons__heading">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Pros
                </h5>
                <ul class="sp-pros-cons__list">
                    <?php foreach ($pros as $pro): ?>
                    <li><?php echo esc_html(trim($pro)); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            <?php if (!empty($cons)): ?>
            <div class="sp-pros-cons__column sp-pros-cons__column--cons">
                <h5 class="sp-pros-cons__heading">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    Cons
                </h5>
                <ul class="sp-pros-cons__list">
                    <?php foreach ($cons as $con): ?>
                    <li><?php echo esc_html(trim($con)); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('pros_cons', 'sp_pros_cons_shortcode');

/**
 * Shortcode: Product Comparison Table
 * Usage: [compare_products][compare_product name="Product A" asin="XXX" price="29.99" rating="4.5" features="Feature 1|Feature 2"][/compare_products]
 */
function sp_compare_products_shortcode($atts, $content = null) {
    if (!$content) return '';

    ob_start();
    ?>
    <div class="sp-compare-table">
        <div class="sp-compare-table__header">
            <h4>Product Comparison</h4>
            <button class="sp-compare-table__clear">Clear All</button>
        </div>
        <div class="sp-compare-table__grid">
            <?php echo do_shortcode($content); ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('compare_products', 'sp_compare_products_shortcode');

function sp_compare_product_shortcode($atts) {
    $atts = shortcode_atts(array(
        'name'     => '',
        'asin'     => '',
        'price'    => '',
        'image'    => '',
        'rating'   => '',
        'features' => '',
        'badge'    => '',
    ), $atts);

    if (empty($atts['name'])) return '';

    $features = array_filter(explode('|', $atts['features']));
    $url = "https://www.amazon.com/dp/{$atts['asin']}?tag=" . SP_AFFILIATE_TAG;

    ob_start();
    ?>
    <div class="sp-compare-product" data-asin="<?php echo esc_attr($atts['asin']); ?>">
        <?php if (!empty($atts['badge'])): ?>
        <span class="sp-compare-product__badge"><?php echo esc_html($atts['badge']); ?></span>
        <?php endif; ?>
        <?php if (!empty($atts['image'])): ?>
        <img src="<?php echo esc_url($atts['image']); ?>" alt="<?php echo esc_attr($atts['name']); ?>" class="sp-compare-product__image" loading="lazy">
        <?php endif; ?>
        <h5 class="sp-compare-product__name"><?php echo esc_html($atts['name']); ?></h5>
        <?php if (!empty($atts['rating'])): ?>
        <div class="sp-compare-product__rating">
            <span class="sp-compare-product__stars" style="--rating: <?php echo esc_attr($atts['rating']); ?>"></span>
            <span class="sp-compare-product__rating-num"><?php echo esc_html($atts['rating']); ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($atts['price'])): ?>
        <div class="sp-compare-product__price">$<?php echo esc_html($atts['price']); ?></div>
        <?php endif; ?>
        <?php if (!empty($features)): ?>
        <ul class="sp-compare-product__features">
            <?php foreach ($features as $feature): ?>
            <li><?php echo esc_html(trim($feature)); ?></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <?php if (!empty($atts['asin'])): ?>
        <a href="<?php echo esc_url($url); ?>" class="sp-btn sp-btn--amazon sp-btn--sm" target="_blank" rel="nofollow sponsored noopener">
            View on Amazon
        </a>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('compare_product', 'sp_compare_product_shortcode');

/**
 * AJAX Handler: Price Alert Signup
 */
function sp_handle_price_alert() {
    check_ajax_referer('sp_nonce', 'nonce');

    $email = sanitize_email($_POST['email'] ?? '');
    $asin = sanitize_text_field($_POST['asin'] ?? '');
    $product = sanitize_text_field($_POST['product'] ?? '');
    $price = floatval($_POST['price'] ?? 0);

    if (!is_email($email)) {
        wp_send_json_error(array('message' => 'Please enter a valid email address.'));
    }

    // Store the alert (in production, this would go to a database or email service)
    $alerts = get_option('sp_price_alerts', array());
    $alerts[] = array(
        'email'   => $email,
        'asin'    => $asin,
        'product' => $product,
        'price'   => $price,
        'created' => current_time('mysql'),
    );
    update_option('sp_price_alerts', $alerts);

    wp_send_json_success(array('message' => "You'll be notified when the price drops!"));
}
add_action('wp_ajax_sp_price_alert', 'sp_handle_price_alert');
add_action('wp_ajax_nopriv_sp_price_alert', 'sp_handle_price_alert');

/**
 * AJAX Handler: Newsletter Signup
 */
function sp_handle_newsletter_signup() {
    check_ajax_referer('sp_nonce', 'nonce');

    $email = sanitize_email($_POST['email'] ?? '');

    if (!is_email($email)) {
        wp_send_json_error(array('message' => 'Please enter a valid email address.'));
    }

    // Store subscriber (in production, integrate with email service)
    $subscribers = get_option('sp_newsletter_subscribers', array());
    if (!in_array($email, $subscribers)) {
        $subscribers[] = $email;
        update_option('sp_newsletter_subscribers', $subscribers);
    }

    wp_send_json_success(array('message' => 'Thanks for subscribing!'));
}
add_action('wp_ajax_sp_newsletter', 'sp_handle_newsletter_signup');
add_action('wp_ajax_nopriv_sp_newsletter', 'sp_handle_newsletter_signup');

/**
 * Add Dark Mode toggle to header
 */
function sp_add_dark_mode_support() {
    // Add body class based on cookie/preference
    add_filter('body_class', function($classes) {
        if (isset($_COOKIE['sp_dark_mode']) && $_COOKIE['sp_dark_mode'] === 'true') {
            $classes[] = 'sp-dark-mode';
        }
        return $classes;
    });
}
add_action('init', 'sp_add_dark_mode_support');

/**
 * Shortcode: Stock Status Indicator
 * Usage: [stock_status status="low" count="3"]
 */
function sp_stock_status_shortcode($atts) {
    $atts = shortcode_atts(array(
        'status' => 'in_stock', // in_stock, low, out_of_stock
        'count'  => '',
    ), $atts);

    $class = 'sp-stock-status sp-stock-status--' . $atts['status'];
    $text = '';
    $icon = '';

    switch ($atts['status']) {
        case 'low':
            $text = !empty($atts['count'])
                ? sprintf('Only %d left in stock - order soon!', intval($atts['count']))
                : 'Limited stock - order soon!';
            $icon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
            break;
        case 'out_of_stock':
            $text = 'Currently out of stock';
            $icon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
            break;
        default:
            $text = 'In Stock';
            $icon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>';
    }

    return sprintf('<div class="%s">%s <span>%s</span></div>', esc_attr($class), $icon, esc_html($text));
}
add_shortcode('stock_status', 'sp_stock_status_shortcode');

/**
 * Add last verified date custom field
 */
function sp_register_meta_boxes() {
    add_meta_box(
        'sp_price_verification',
        'Price Verification',
        'sp_price_verification_callback',
        'post',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'sp_register_meta_boxes');

function sp_price_verification_callback($post) {
    $verified_date = get_post_meta($post->ID, '_sp_prices_verified', true);
    wp_nonce_field('sp_price_verification', 'sp_price_verification_nonce');
    ?>
    <p>
        <label for="sp_prices_verified">Prices Last Verified:</label><br>
        <input type="date" id="sp_prices_verified" name="sp_prices_verified"
               value="<?php echo esc_attr($verified_date); ?>" style="width:100%;">
    </p>
    <?php
}

function sp_save_price_verification($post_id) {
    if (!isset($_POST['sp_price_verification_nonce']) ||
        !wp_verify_nonce($_POST['sp_price_verification_nonce'], 'sp_price_verification')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST['sp_prices_verified'])) {
        update_post_meta($post_id, '_sp_prices_verified', sanitize_text_field($_POST['sp_prices_verified']));
    }
}
add_action('save_post', 'sp_save_price_verification');

/**
 * Enqueue new improvement scripts
 */
function sp_enqueue_improvement_assets() {
    wp_enqueue_script('sp-improvements', SP_THEME_URI . '/assets/js/improvements.js', array('sp-main'), SP_THEME_VERSION, true);
}
add_action('wp_enqueue_scripts', 'sp_enqueue_improvement_assets');
