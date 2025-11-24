<?php
/**
 * Analytics Tracking Code for ShelzyPerkins
 *
 * Add to WordPress via Customizer or functions.php
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Output Google Analytics 4 tracking code
 */
function sp_output_ga4_tracking() {
    $ga_id = get_theme_mod('sp_ga_id', '');

    if (empty($ga_id)) {
        return;
    }

    // Don't track admin users
    if (current_user_can('manage_options')) {
        return;
    }
    ?>
    <!-- Google Analytics 4 - ShelzyPerkins -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($ga_id); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo esc_js($ga_id); ?>', {
            'cookie_flags': 'SameSite=None;Secure',
            'link_attribution': true,
            'anonymize_ip': true
        });

        // Custom dimensions
        gtag('set', 'user_properties', {
            'site_version': '<?php echo esc_js(SP_THEME_VERSION); ?>'
        });
    </script>
    <?php
}
add_action('wp_head', 'sp_output_ga4_tracking', 1);

/**
 * Output Hotjar tracking code
 */
function sp_output_hotjar_tracking() {
    $hotjar_id = get_theme_mod('sp_hotjar_id', '');

    if (empty($hotjar_id)) {
        return;
    }

    // Don't track admin users
    if (current_user_can('manage_options')) {
        return;
    }
    ?>
    <!-- Hotjar Tracking Code - ShelzyPerkins -->
    <script>
        (function(h,o,t,j,a,r){
            h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
            h._hjSettings={hjid:<?php echo esc_js($hotjar_id); ?>,hjsv:6};
            a=o.getElementsByTagName('head')[0];
            r=o.createElement('script');r.async=1;
            r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
            a.appendChild(r);
        })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
    </script>
    <?php
}
add_action('wp_head', 'sp_output_hotjar_tracking', 2);

/**
 * Track affiliate link clicks via AJAX
 */
function sp_track_affiliate_click() {
    check_ajax_referer('sp_nonce', 'nonce');

    $asin = sanitize_text_field($_POST['asin'] ?? '');
    $url = esc_url_raw($_POST['url'] ?? '');
    $link_type = sanitize_text_field($_POST['link_type'] ?? 'unknown');

    // Log to custom table or external service
    $click_data = array(
        'asin' => $asin,
        'url' => $url,
        'link_type' => $link_type,
        'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? ''),
        'referrer' => wp_get_referer(),
        'timestamp' => current_time('mysql'),
    );

    // Store click (implement your preferred storage method)
    // Option 1: Custom database table
    // Option 2: Log file
    // Option 3: External analytics service

    // For now, log to error log for debugging
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Affiliate Click: ' . json_encode($click_data));
    }

    wp_send_json_success(array('tracked' => true));
}
add_action('wp_ajax_sp_track_click', 'sp_track_affiliate_click');
add_action('wp_ajax_nopriv_sp_track_click', 'sp_track_affiliate_click');

/**
 * Add meta pixel (Facebook) if configured
 */
function sp_output_meta_pixel() {
    $pixel_id = get_theme_mod('sp_meta_pixel_id', '');

    if (empty($pixel_id)) {
        return;
    }
    ?>
    <!-- Meta Pixel Code - ShelzyPerkins -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '<?php echo esc_js($pixel_id); ?>');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none"
             src="https://www.facebook.com/tr?id=<?php echo esc_attr($pixel_id); ?>&ev=PageView&noscript=1"/>
    </noscript>
    <?php
}
add_action('wp_head', 'sp_output_meta_pixel', 3);

/**
 * Pinterest tag
 */
function sp_output_pinterest_tag() {
    $pinterest_tag_id = get_theme_mod('sp_pinterest_tag_id', '');

    if (empty($pinterest_tag_id)) {
        return;
    }
    ?>
    <!-- Pinterest Tag - ShelzyPerkins -->
    <script>
        !function(e){if(!window.pintrk){window.pintrk=function(){
        window.pintrk.queue.push(Array.prototype.slice.call(arguments))};var
        n=window.pintrk;n.queue=[],n.version="3.0";var
        t=document.createElement("script");t.async=!0,t.src=e;var
        r=document.getElementsByTagName("script")[0];
        r.parentNode.insertBefore(t,r)}}("https://s.pinimg.com/ct/core.js");
        pintrk('load', '<?php echo esc_js($pinterest_tag_id); ?>');
        pintrk('page');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none;" alt=""
             src="https://ct.pinterest.com/v3/?event=init&tid=<?php echo esc_attr($pinterest_tag_id); ?>&noscript=1" />
    </noscript>
    <?php
}
add_action('wp_head', 'sp_output_pinterest_tag', 4);
