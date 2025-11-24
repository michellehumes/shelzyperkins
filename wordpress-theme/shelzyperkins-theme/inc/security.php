<?php
/**
 * Security Improvements
 *
 * @package ShelzyPerkins
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Disable XML-RPC
 */
add_filter('xmlrpc_enabled', '__return_false');

/**
 * Remove XML-RPC methods
 */
add_filter('xmlrpc_methods', function($methods) {
    return array();
});

/**
 * Remove WordPress version from head
 */
remove_action('wp_head', 'wp_generator');

/**
 * Remove version from scripts and styles
 */
function sp_remove_version_query($src) {
    if (strpos($src, 'ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('style_loader_src', 'sp_remove_version_query', 9999);
add_filter('script_loader_src', 'sp_remove_version_query', 9999);

/**
 * Disable file editing in admin
 */
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}

/**
 * Remove emoji scripts and styles
 */
function sp_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

    add_filter('tiny_mce_plugins', 'sp_disable_emojis_tinymce');
    add_filter('wp_resource_hints', 'sp_disable_emojis_dns_prefetch', 10, 2);
}
add_action('init', 'sp_disable_emojis');

/**
 * Remove emoji TinyMCE plugin
 */
function sp_disable_emojis_tinymce($plugins) {
    if (is_array($plugins)) {
        return array_diff($plugins, array('wpemoji'));
    }
    return array();
}

/**
 * Remove emoji DNS prefetch
 */
function sp_disable_emojis_dns_prefetch($urls, $relation_type) {
    if ('dns-prefetch' === $relation_type) {
        $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/');
        $urls = array_filter($urls, function($url) use ($emoji_svg_url) {
            return strpos($url, $emoji_svg_url) === false;
        });
    }
    return $urls;
}

/**
 * Disable REST API for non-authenticated users (selective)
 */
function sp_restrict_rest_api($result) {
    if (!is_user_logged_in()) {
        // Allow certain endpoints for non-authenticated users
        $allowed_endpoints = array(
            '/wp/v2/posts',
            '/wp/v2/pages',
            '/wp/v2/categories',
            '/wp/v2/tags',
        );

        $current_route = $GLOBALS['wp']->query_vars['rest_route'] ?? '';

        foreach ($allowed_endpoints as $endpoint) {
            if (strpos($current_route, $endpoint) === 0) {
                return $result;
            }
        }
    }
    return $result;
}
// Uncomment to enable REST API restrictions
// add_filter('rest_authentication_errors', 'sp_restrict_rest_api');

/**
 * Add security headers
 */
function sp_security_headers() {
    if (!is_admin()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
    }
}
add_action('send_headers', 'sp_security_headers');

/**
 * Disable author archives to prevent user enumeration
 */
function sp_disable_author_archives() {
    if (is_author()) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
    }
}
// Uncomment to disable author archives
// add_action('template_redirect', 'sp_disable_author_archives');

/**
 * Remove unnecessary header links
 */
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'rest_output_link_wp_head');
remove_action('wp_head', 'wp_oembed_add_discovery_links');

/**
 * Limit login attempts (basic implementation)
 */
function sp_check_login_attempts($user, $username, $password) {
    $max_attempts = 5;
    $lockout_time = 15 * MINUTE_IN_SECONDS;

    $ip = $_SERVER['REMOTE_ADDR'];
    $transient_key = 'login_attempts_' . md5($ip);
    $attempts = get_transient($transient_key);

    if ($attempts === false) {
        $attempts = 0;
    }

    if ($attempts >= $max_attempts) {
        return new WP_Error('too_many_attempts',
            sprintf(__('Too many failed login attempts. Please try again in %d minutes.', 'shelzyperkins'),
            ceil($lockout_time / MINUTE_IN_SECONDS))
        );
    }

    return $user;
}
add_filter('authenticate', 'sp_check_login_attempts', 30, 3);

/**
 * Track failed login attempts
 */
function sp_track_failed_login($username) {
    $lockout_time = 15 * MINUTE_IN_SECONDS;
    $ip = $_SERVER['REMOTE_ADDR'];
    $transient_key = 'login_attempts_' . md5($ip);
    $attempts = get_transient($transient_key);

    if ($attempts === false) {
        $attempts = 0;
    }

    $attempts++;
    set_transient($transient_key, $attempts, $lockout_time);
}
add_action('wp_login_failed', 'sp_track_failed_login');

/**
 * Clear login attempts on successful login
 */
function sp_clear_login_attempts($user_login, $user) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $transient_key = 'login_attempts_' . md5($ip);
    delete_transient($transient_key);
}
add_action('wp_login', 'sp_clear_login_attempts', 10, 2);

/**
 * Hide login error details
 */
function sp_hide_login_errors($error) {
    return __('Invalid login credentials. Please try again.', 'shelzyperkins');
}
add_filter('login_errors', 'sp_hide_login_errors');
