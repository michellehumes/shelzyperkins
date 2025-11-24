<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main-content"><?php esc_html_e('Skip to content', 'shelzyperkins'); ?></a>

<header class="sp-header" id="site-header">
    <div class="sp-header__inner">
        <!-- Logo -->
        <div class="sp-logo">
            <?php if (has_custom_logo()): ?>
                <?php the_custom_logo(); ?>
            <?php else: ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="sp-logo__link">
                    <svg width="200" height="50" viewBox="0 0 300 80" class="sp-logo__svg">
                        <defs>
                            <linearGradient id="headerGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" style="stop-color:#FF6B6B"/>
                                <stop offset="100%" style="stop-color:#FFE66D"/>
                            </linearGradient>
                        </defs>
                        <g transform="translate(10, 10)">
                            <path d="M10 20 L10 55 Q10 60 15 60 L45 60 Q50 60 50 55 L50 20 Q30 25 10 20Z" fill="url(#headerGradient)"/>
                            <path d="M15 20 Q15 8 30 8 Q45 8 45 20" stroke="#2D3436" stroke-width="3" fill="none"/>
                            <path d="M22 35 Q30 50 38 35 Q30 42 22 35Z" fill="#FFFFFF"/>
                        </g>
                        <text x="70" y="45" font-family="Poppins, sans-serif" font-size="26" font-weight="700" fill="#2D3436">
                            Shelzy<tspan fill="#FF6B6B">Perkins</tspan>
                        </text>
                    </svg>
                </a>
            <?php endif; ?>
        </div>

        <!-- Navigation -->
        <nav class="sp-nav" role="navigation" aria-label="<?php esc_attr_e('Main Navigation', 'shelzyperkins'); ?>">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'sp-nav__list',
                'menu_id'        => 'primary-menu',
                'fallback_cb'    => 'sp_fallback_menu',
                'link_before'    => '<span class="sp-nav__link-text">',
                'link_after'     => '</span>',
            ));
            ?>

            <!-- Search Toggle -->
            <button class="sp-search-toggle" aria-label="<?php esc_attr_e('Search', 'shelzyperkins'); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="M21 21l-4.35-4.35"></path>
                </svg>
            </button>

            <!-- Mobile Menu Toggle -->
            <button class="sp-menu-toggle" aria-label="<?php esc_attr_e('Toggle Menu', 'shelzyperkins'); ?>" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </nav>
    </div>

    <!-- Search Form (Hidden by default) -->
    <div class="sp-search-overlay" id="search-overlay">
        <div class="sp-container">
            <form role="search" method="get" class="sp-search-form" action="<?php echo esc_url(home_url('/')); ?>">
                <input type="search" class="sp-search-form__input" placeholder="<?php esc_attr_e('Search for deals...', 'shelzyperkins'); ?>" value="<?php echo get_search_query(); ?>" name="s">
                <button type="submit" class="sp-btn sp-btn--primary"><?php esc_html_e('Search', 'shelzyperkins'); ?></button>
            </form>
            <button class="sp-search-close" aria-label="<?php esc_attr_e('Close Search', 'shelzyperkins'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6L6 18M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
</header>

<!-- Announcement Bar (Optional) -->
<?php if (get_theme_mod('sp_show_announcement', false)): ?>
<div class="sp-announcement-bar">
    <div class="sp-container">
        <p><?php echo esc_html(get_theme_mod('sp_announcement_text', 'Free shipping on orders over $35!')); ?></p>
    </div>
</div>
<?php endif; ?>

<main id="main-content" class="sp-main">
