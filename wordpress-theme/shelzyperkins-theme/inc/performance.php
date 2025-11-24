<?php
/**
 * Performance Optimizations
 *
 * @package ShelzyPerkins
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add preload hints for critical resources
 */
function sp_resource_hints($urls, $relation_type) {
    if ('preconnect' === $relation_type) {
        $urls[] = array(
            'href' => 'https://fonts.googleapis.com',
        );
        $urls[] = array(
            'href' => 'https://fonts.gstatic.com',
            'crossorigin' => 'anonymous',
        );
        $urls[] = array(
            'href' => 'https://www.amazon.com',
        );
        $urls[] = array(
            'href' => 'https://m.media-amazon.com',
        );
    }

    if ('dns-prefetch' === $relation_type) {
        $urls[] = 'https://ws-na.amazon-adsystem.com';
        $urls[] = 'https://images-na.ssl-images-amazon.com';
    }

    return $urls;
}
add_filter('wp_resource_hints', 'sp_resource_hints', 10, 2);

/**
 * Defer non-critical JavaScript
 */
function sp_defer_scripts($tag, $handle, $src) {
    $defer_scripts = array('sp-main', 'sp-affiliate', 'comment-reply');

    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }

    return $tag;
}
add_filter('script_loader_tag', 'sp_defer_scripts', 10, 3);

/**
 * Add loading="lazy" to images in content
 */
function sp_lazy_load_images($content) {
    if (is_admin() || is_feed()) {
        return $content;
    }

    // Add loading="lazy" to images without it
    $content = preg_replace(
        '/<img((?!.*loading=)[^>]*)>/i',
        '<img$1 loading="lazy">',
        $content
    );

    return $content;
}
add_filter('the_content', 'sp_lazy_load_images', 99);

/**
 * Add fetchpriority="high" to featured images
 */
function sp_prioritize_featured_image($html, $post_id, $post_thumbnail_id, $size, $attr) {
    if (is_singular() && in_the_loop() && is_main_query()) {
        $html = str_replace('<img', '<img fetchpriority="high"', $html);
        // Remove lazy loading from above-the-fold images
        $html = str_replace(' loading="lazy"', '', $html);
    }
    return $html;
}
add_filter('post_thumbnail_html', 'sp_prioritize_featured_image', 10, 5);

/**
 * Remove jQuery migrate
 */
function sp_remove_jquery_migrate($scripts) {
    if (!is_admin() && isset($scripts->registered['jquery'])) {
        $script = $scripts->registered['jquery'];
        if ($script->deps) {
            $script->deps = array_diff($script->deps, array('jquery-migrate'));
        }
    }
}
add_action('wp_default_scripts', 'sp_remove_jquery_migrate');

/**
 * Disable self-pingbacks
 */
function sp_disable_self_pingback(&$links) {
    $home = get_option('home');
    foreach ($links as $l => $link) {
        if (0 === strpos($link, $home)) {
            unset($links[$l]);
        }
    }
}
add_action('pre_ping', 'sp_disable_self_pingback');

/**
 * Limit post revisions
 */
if (!defined('WP_POST_REVISIONS')) {
    define('WP_POST_REVISIONS', 5);
}

/**
 * Remove query strings from static resources
 */
function sp_remove_query_strings($src) {
    if (strpos($src, '?ver=') !== false) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
// Uncomment to enable (may cause issues with some plugins)
// add_filter('style_loader_src', 'sp_remove_query_strings', 10, 2);
// add_filter('script_loader_src', 'sp_remove_query_strings', 10, 2);

/**
 * Disable embeds
 */
function sp_disable_embeds_init() {
    // Remove the REST API endpoint
    remove_action('rest_api_init', 'wp_oembed_register_route');

    // Turn off oEmbed auto discovery
    remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);

    // Remove oEmbed discovery links
    remove_action('wp_head', 'wp_oembed_add_discovery_links');

    // Remove oEmbed-specific JavaScript from the front-end and back-end
    remove_action('wp_head', 'wp_oembed_add_host_js');
}
add_action('init', 'sp_disable_embeds_init', 9999);

/**
 * Optimize heartbeat
 */
function sp_heartbeat_settings($settings) {
    $settings['interval'] = 60; // Default is 15 seconds
    return $settings;
}
add_filter('heartbeat_settings', 'sp_heartbeat_settings');

/**
 * Disable heartbeat on front-end
 */
function sp_disable_frontend_heartbeat() {
    if (!is_admin()) {
        wp_deregister_script('heartbeat');
    }
}
add_action('init', 'sp_disable_frontend_heartbeat', 1);

/**
 * Preload critical fonts
 */
function sp_preload_fonts() {
    ?>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"></noscript>
    <?php
}
add_action('wp_head', 'sp_preload_fonts', 1);

/**
 * Add will-change hint for animated elements
 */
function sp_performance_css() {
    ?>
    <style id="sp-performance-hints">
        .sp-card,
        .sp-btn,
        .sp-sticky-bar,
        .sp-back-to-top {
            will-change: transform;
        }
    </style>
    <?php
}
add_action('wp_head', 'sp_performance_css', 99);

/**
 * Optimize database queries
 */
function sp_optimize_queries($query) {
    if (!is_admin() && $query->is_main_query()) {
        // Disable counting total rows for better performance on archives
        if ($query->is_archive() || $query->is_search()) {
            $query->set('no_found_rows', false); // Keep pagination working
        }
    }
}
add_action('pre_get_posts', 'sp_optimize_queries');
