<?php
/**
 * Custom template tags for this theme
 *
 * @package ShelzyPerkins
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Calculate reading time for a post
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
 * Get post views count
 */
function sp_get_post_views($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $count = get_post_meta($post_id, 'post_views_count', true);
    return $count ? $count : 0;
}

/**
 * Set post views count
 */
function sp_set_post_views($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    // Don't count admin views
    if (current_user_can('edit_posts')) {
        return;
    }

    $count = sp_get_post_views($post_id);
    $count++;
    update_post_meta($post_id, 'post_views_count', $count);
}

/**
 * Track post views
 */
function sp_track_post_views() {
    if (is_single()) {
        sp_set_post_views(get_the_ID());
    }
}
add_action('wp_head', 'sp_track_post_views');

/**
 * Fallback menu if no menu is set
 */
function sp_fallback_menu() {
    echo '<ul class="sp-nav__list">';
    echo '<li><a href="' . esc_url(home_url('/')) . '" class="sp-nav__link">Home</a></li>';
    echo '<li><a href="' . esc_url(home_url('/deals/')) . '" class="sp-nav__link">Deals</a></li>';
    echo '<li><a href="' . esc_url(home_url('/best-of/')) . '" class="sp-nav__link">Best Of</a></li>';
    echo '<li><a href="' . esc_url(home_url('/gift-guides/')) . '" class="sp-nav__link">Gift Guides</a></li>';
    echo '<li><a href="' . esc_url(home_url('/about/')) . '" class="sp-nav__link">About</a></li>';
    echo '</ul>';
}

/**
 * Custom nav walker for primary menu
 */
class SP_Nav_Walker extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item));

        $output .= '<li class="' . esc_attr($class_names) . '">';

        $attributes = '';
        if (!empty($item->url)) {
            $attributes .= ' href="' . esc_attr($item->url) . '"';
        }
        if (!empty($item->target)) {
            $attributes .= ' target="' . esc_attr($item->target) . '"';
        }
        if (!empty($item->attr_title)) {
            $attributes .= ' title="' . esc_attr($item->attr_title) . '"';
        }

        $item_output = $args->before;
        $item_output .= '<a class="sp-nav__link"' . $attributes . '>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}

/**
 * Get Amazon affiliate link
 */
function sp_get_amazon_url($asin) {
    $tag = get_theme_mod('sp_affiliate_tag', SP_AFFILIATE_TAG);
    return "https://www.amazon.com/dp/{$asin}?tag={$tag}";
}

/**
 * Display affiliate disclosure
 */
function sp_affiliate_disclosure($short = false) {
    if ($short) {
        echo '<p class="sp-affiliate-disclosure"><strong>Disclosure:</strong> This post contains affiliate links. I may earn a commission at no extra cost to you.</p>';
    } else {
        echo '<div class="sp-affiliate-disclosure"><strong>Disclosure:</strong> This post contains affiliate links. If you purchase through these links, I may earn a small commission at no extra cost to you. I only recommend products I genuinely love and use!</div>';
    }
}

/**
 * Get category badge color class
 */
function sp_get_category_color($category_slug) {
    $colors = array(
        'deals'        => 'coral',
        'beauty'       => 'pink',
        'home'         => 'blue',
        'tech'         => 'purple',
        'fashion'      => 'yellow',
        'kitchen'      => 'green',
        'gift-guides'  => 'gold',
        'budget-hacks' => 'teal',
    );

    return isset($colors[$category_slug]) ? $colors[$category_slug] : 'default';
}

/**
 * Custom pagination
 */
function sp_pagination($query = null) {
    global $wp_query;
    $query = $query ?: $wp_query;

    $big = 999999999;

    echo '<nav class="sp-pagination">';
    echo paginate_links(array(
        'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format'    => '?paged=%#%',
        'current'   => max(1, get_query_var('paged')),
        'total'     => $query->max_num_pages,
        'prev_text' => '&larr; Previous',
        'next_text' => 'Next &rarr;',
        'type'      => 'list',
    ));
    echo '</nav>';
}

/**
 * Format price display
 */
function sp_format_price($price, $currency = '$') {
    return $currency . number_format((float)$price, 2);
}

/**
 * Calculate savings percentage
 */
function sp_calculate_savings($original, $current) {
    if ($original <= 0) return 0;
    return round((($original - $current) / $original) * 100);
}

/**
 * Get related posts
 */
function sp_get_related_posts($post_id, $count = 3) {
    $categories = wp_get_post_categories($post_id);
    $tags = wp_get_post_tags($post_id, array('fields' => 'ids'));

    $args = array(
        'posts_per_page' => $count,
        'post__not_in'   => array($post_id),
        'orderby'        => 'rand',
    );

    if ($categories) {
        $args['category__in'] = $categories;
    }

    if ($tags) {
        $args['tag__in'] = $tags;
    }

    return new WP_Query($args);
}

/**
 * Breadcrumbs
 */
function sp_breadcrumbs() {
    if (is_front_page()) return;

    echo '<nav class="sp-breadcrumbs" aria-label="Breadcrumb">';
    echo '<ol class="sp-breadcrumbs__list" itemscope itemtype="https://schema.org/BreadcrumbList">';

    // Home
    echo '<li class="sp-breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
    echo '<a href="' . esc_url(home_url('/')) . '" itemprop="item"><span itemprop="name">Home</span></a>';
    echo '<meta itemprop="position" content="1">';
    echo '</li>';

    $position = 2;

    if (is_category()) {
        $category = get_queried_object();
        echo '<li class="sp-breadcrumbs__item sp-breadcrumbs__item--current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        echo '<span itemprop="name">' . esc_html($category->name) . '</span>';
        echo '<meta itemprop="position" content="' . $position . '">';
        echo '</li>';
    } elseif (is_single()) {
        $categories = get_the_category();
        if ($categories) {
            $cat = $categories[0];
            echo '<li class="sp-breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            echo '<a href="' . esc_url(get_category_link($cat->term_id)) . '" itemprop="item"><span itemprop="name">' . esc_html($cat->name) . '</span></a>';
            echo '<meta itemprop="position" content="' . $position . '">';
            echo '</li>';
            $position++;
        }
        echo '<li class="sp-breadcrumbs__item sp-breadcrumbs__item--current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        echo '<span itemprop="name">' . esc_html(get_the_title()) . '</span>';
        echo '<meta itemprop="position" content="' . $position . '">';
        echo '</li>';
    } elseif (is_page()) {
        echo '<li class="sp-breadcrumbs__item sp-breadcrumbs__item--current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        echo '<span itemprop="name">' . esc_html(get_the_title()) . '</span>';
        echo '<meta itemprop="position" content="' . $position . '">';
        echo '</li>';
    }

    echo '</ol>';
    echo '</nav>';
}
