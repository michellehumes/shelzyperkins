<?php
/**
 * Theme Customizer Settings
 *
 * @package ShelzyPerkins
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register customizer settings
 */
function sp_customize_register($wp_customize) {

    // ShelzyPerkins Panel
    $wp_customize->add_panel('sp_theme_options', array(
        'title'       => __('ShelzyPerkins Settings', 'shelzyperkins'),
        'priority'    => 30,
        'description' => __('Customize your ShelzyPerkins theme settings.', 'shelzyperkins'),
    ));

    // ============================================
    // General Settings Section
    // ============================================
    $wp_customize->add_section('sp_general_settings', array(
        'title'    => __('General Settings', 'shelzyperkins'),
        'panel'    => 'sp_theme_options',
        'priority' => 10,
    ));

    // Affiliate Tag
    $wp_customize->add_setting('sp_affiliate_tag', array(
        'default'           => 'shelzysdesigns-20',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('sp_affiliate_tag', array(
        'label'       => __('Amazon Affiliate Tag', 'shelzyperkins'),
        'description' => __('Your Amazon Associates tracking ID.', 'shelzyperkins'),
        'section'     => 'sp_general_settings',
        'type'        => 'text',
    ));

    // Amazon Storefront URL
    $wp_customize->add_setting('sp_amazon_storefront', array(
        'default'           => 'https://www.amazon.com/shop/shelzyperkins',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('sp_amazon_storefront', array(
        'label'       => __('Amazon Storefront URL', 'shelzyperkins'),
        'description' => __('Link to your Amazon Storefront.', 'shelzyperkins'),
        'section'     => 'sp_general_settings',
        'type'        => 'url',
    ));

    // ============================================
    // Header Settings Section
    // ============================================
    $wp_customize->add_section('sp_header_settings', array(
        'title'    => __('Header Settings', 'shelzyperkins'),
        'panel'    => 'sp_theme_options',
        'priority' => 20,
    ));

    // Show Announcement Bar
    $wp_customize->add_setting('sp_show_announcement', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('sp_show_announcement', array(
        'label'   => __('Show Announcement Bar', 'shelzyperkins'),
        'section' => 'sp_header_settings',
        'type'    => 'checkbox',
    ));

    // Announcement Text
    $wp_customize->add_setting('sp_announcement_text', array(
        'default'           => 'Free shipping on orders over $35!',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('sp_announcement_text', array(
        'label'       => __('Announcement Text', 'shelzyperkins'),
        'section'     => 'sp_header_settings',
        'type'        => 'text',
        'active_callback' => function() {
            return get_theme_mod('sp_show_announcement', false);
        },
    ));

    // ============================================
    // Social Media Section
    // ============================================
    $wp_customize->add_section('sp_social_settings', array(
        'title'    => __('Social Media Links', 'shelzyperkins'),
        'panel'    => 'sp_theme_options',
        'priority' => 30,
    ));

    $social_networks = array(
        'pinterest'  => 'Pinterest URL',
        'instagram'  => 'Instagram URL',
        'tiktok'     => 'TikTok URL',
        'youtube'    => 'YouTube URL',
        'facebook'   => 'Facebook URL',
        'twitter'    => 'Twitter/X URL',
    );

    foreach ($social_networks as $network => $label) {
        $wp_customize->add_setting("sp_social_{$network}", array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control("sp_social_{$network}", array(
            'label'   => __($label, 'shelzyperkins'),
            'section' => 'sp_social_settings',
            'type'    => 'url',
        ));
    }

    // ============================================
    // Homepage Settings Section
    // ============================================
    $wp_customize->add_section('sp_homepage_settings', array(
        'title'    => __('Homepage Settings', 'shelzyperkins'),
        'panel'    => 'sp_theme_options',
        'priority' => 40,
    ));

    // Hero Title
    $wp_customize->add_setting('sp_hero_title', array(
        'default'           => 'Smart Deals. Real Savings.',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('sp_hero_title', array(
        'label'   => __('Hero Title', 'shelzyperkins'),
        'section' => 'sp_homepage_settings',
        'type'    => 'text',
    ));

    // Hero Subtitle
    $wp_customize->add_setting('sp_hero_subtitle', array(
        'default'           => 'Discover the best Amazon finds, curated product recommendations, and money-saving tips.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('sp_hero_subtitle', array(
        'label'   => __('Hero Subtitle', 'shelzyperkins'),
        'section' => 'sp_homepage_settings',
        'type'    => 'textarea',
    ));

    // ============================================
    // Footer Settings Section
    // ============================================
    $wp_customize->add_section('sp_footer_settings', array(
        'title'    => __('Footer Settings', 'shelzyperkins'),
        'panel'    => 'sp_theme_options',
        'priority' => 50,
    ));

    // Footer About Text
    $wp_customize->add_setting('sp_footer_about', array(
        'default'           => 'Your trusted source for the best Amazon deals, product recommendations, and money-saving tips.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('sp_footer_about', array(
        'label'   => __('Footer About Text', 'shelzyperkins'),
        'section' => 'sp_footer_settings',
        'type'    => 'textarea',
    ));

    // Copyright Text
    $wp_customize->add_setting('sp_copyright_text', array(
        'default'           => 'ShelzyPerkins. All rights reserved.',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('sp_copyright_text', array(
        'label'   => __('Copyright Text', 'shelzyperkins'),
        'section' => 'sp_footer_settings',
        'type'    => 'text',
    ));

    // ============================================
    // Analytics Section
    // ============================================
    $wp_customize->add_section('sp_analytics_settings', array(
        'title'    => __('Analytics & Tracking', 'shelzyperkins'),
        'panel'    => 'sp_theme_options',
        'priority' => 60,
    ));

    // Google Analytics ID
    $wp_customize->add_setting('sp_ga_id', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('sp_ga_id', array(
        'label'       => __('Google Analytics 4 Measurement ID', 'shelzyperkins'),
        'description' => __('Enter your GA4 ID (e.g., G-XXXXXXXXXX)', 'shelzyperkins'),
        'section'     => 'sp_analytics_settings',
        'type'        => 'text',
    ));

    // Hotjar Site ID
    $wp_customize->add_setting('sp_hotjar_id', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('sp_hotjar_id', array(
        'label'       => __('Hotjar Site ID', 'shelzyperkins'),
        'description' => __('Enter your Hotjar Site ID for heatmaps', 'shelzyperkins'),
        'section'     => 'sp_analytics_settings',
        'type'        => 'text',
    ));
}
add_action('customize_register', 'sp_customize_register');

/**
 * Selective refresh support
 */
function sp_customize_preview_js() {
    wp_enqueue_script(
        'sp-customizer',
        get_template_directory_uri() . '/assets/js/customizer.js',
        array('customize-preview'),
        SP_THEME_VERSION,
        true
    );
}
add_action('customize_preview_init', 'sp_customize_preview_js');
