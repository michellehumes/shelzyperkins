<?php
/**
 * Breadcrumb Navigation
 *
 * @package ShelzyPerkins
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Display breadcrumb navigation
 */
function sp_breadcrumbs() {
    if (is_front_page()) {
        return;
    }

    $separator = '<svg class="sp-breadcrumb__separator" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>';

    echo '<nav class="sp-breadcrumbs" aria-label="' . esc_attr__('Breadcrumb', 'shelzyperkins') . '">';
    echo '<ol class="sp-breadcrumbs__list" itemscope itemtype="https://schema.org/BreadcrumbList">';

    // Home
    echo '<li class="sp-breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
    echo '<a href="' . esc_url(home_url('/')) . '" class="sp-breadcrumbs__link" itemprop="item">';
    echo '<svg class="sp-breadcrumbs__home-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>';
    echo '<span itemprop="name">' . esc_html__('Home', 'shelzyperkins') . '</span>';
    echo '</a>';
    echo '<meta itemprop="position" content="1">';
    echo '</li>';

    $position = 2;

    if (is_category()) {
        $category = get_queried_object();
        $ancestors = get_ancestors($category->term_id, 'category');
        $ancestors = array_reverse($ancestors);

        foreach ($ancestors as $ancestor_id) {
            $ancestor = get_category($ancestor_id);
            echo $separator;
            echo '<li class="sp-breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            echo '<a href="' . esc_url(get_category_link($ancestor_id)) . '" class="sp-breadcrumbs__link" itemprop="item">';
            echo '<span itemprop="name">' . esc_html($ancestor->name) . '</span>';
            echo '</a>';
            echo '<meta itemprop="position" content="' . $position++ . '">';
            echo '</li>';
        }

        echo $separator;
        echo '<li class="sp-breadcrumbs__item sp-breadcrumbs__item--current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        echo '<span itemprop="name">' . esc_html($category->name) . '</span>';
        echo '<meta itemprop="position" content="' . $position . '">';
        echo '</li>';

    } elseif (is_tag()) {
        echo $separator;
        echo '<li class="sp-breadcrumbs__item sp-breadcrumbs__item--current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        echo '<span itemprop="name">' . esc_html__('Tag: ', 'shelzyperkins') . single_tag_title('', false) . '</span>';
        echo '<meta itemprop="position" content="' . $position . '">';
        echo '</li>';

    } elseif (is_singular('post')) {
        $categories = get_the_category();
        if ($categories) {
            $category = $categories[0];

            // Get parent categories
            $ancestors = get_ancestors($category->term_id, 'category');
            $ancestors = array_reverse($ancestors);

            foreach ($ancestors as $ancestor_id) {
                $ancestor = get_category($ancestor_id);
                echo $separator;
                echo '<li class="sp-breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
                echo '<a href="' . esc_url(get_category_link($ancestor_id)) . '" class="sp-breadcrumbs__link" itemprop="item">';
                echo '<span itemprop="name">' . esc_html($ancestor->name) . '</span>';
                echo '</a>';
                echo '<meta itemprop="position" content="' . $position++ . '">';
                echo '</li>';
            }

            echo $separator;
            echo '<li class="sp-breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="sp-breadcrumbs__link" itemprop="item">';
            echo '<span itemprop="name">' . esc_html($category->name) . '</span>';
            echo '</a>';
            echo '<meta itemprop="position" content="' . $position++ . '">';
            echo '</li>';
        }

        echo $separator;
        echo '<li class="sp-breadcrumbs__item sp-breadcrumbs__item--current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        echo '<span itemprop="name">' . esc_html(get_the_title()) . '</span>';
        echo '<meta itemprop="position" content="' . $position . '">';
        echo '</li>';

    } elseif (is_page()) {
        global $post;
        $ancestors = get_post_ancestors($post);
        $ancestors = array_reverse($ancestors);

        foreach ($ancestors as $ancestor_id) {
            echo $separator;
            echo '<li class="sp-breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            echo '<a href="' . esc_url(get_permalink($ancestor_id)) . '" class="sp-breadcrumbs__link" itemprop="item">';
            echo '<span itemprop="name">' . esc_html(get_the_title($ancestor_id)) . '</span>';
            echo '</a>';
            echo '<meta itemprop="position" content="' . $position++ . '">';
            echo '</li>';
        }

        echo $separator;
        echo '<li class="sp-breadcrumbs__item sp-breadcrumbs__item--current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        echo '<span itemprop="name">' . esc_html(get_the_title()) . '</span>';
        echo '<meta itemprop="position" content="' . $position . '">';
        echo '</li>';

    } elseif (is_search()) {
        echo $separator;
        echo '<li class="sp-breadcrumbs__item sp-breadcrumbs__item--current">';
        echo '<span>' . esc_html__('Search Results', 'shelzyperkins') . '</span>';
        echo '</li>';

    } elseif (is_404()) {
        echo $separator;
        echo '<li class="sp-breadcrumbs__item sp-breadcrumbs__item--current">';
        echo '<span>' . esc_html__('Page Not Found', 'shelzyperkins') . '</span>';
        echo '</li>';

    } elseif (is_archive()) {
        echo $separator;
        echo '<li class="sp-breadcrumbs__item sp-breadcrumbs__item--current">';
        echo '<span>' . esc_html(get_the_archive_title()) . '</span>';
        echo '</li>';
    }

    echo '</ol>';
    echo '</nav>';
}

/**
 * Get category icon
 */
function sp_get_category_icon($category_id) {
    $icons = array(
        'beauty'    => '&#128132;',
        'home'      => '&#127968;',
        'tech'      => '&#128187;',
        'fashion'   => '&#128087;',
        'kitchen'   => '&#127860;',
        'deals'     => '&#127873;',
        'travel'    => '&#9992;',
        'health'    => '&#128138;',
        'kids'      => '&#128118;',
        'pets'      => '&#128054;',
    );

    $category = get_category($category_id);
    if ($category) {
        $slug = strtolower($category->slug);
        if (isset($icons[$slug])) {
            return $icons[$slug];
        }
    }

    return '&#128176;'; // Default: money bag
}
