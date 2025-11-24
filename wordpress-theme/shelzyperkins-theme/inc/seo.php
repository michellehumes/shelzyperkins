<?php
/**
 * SEO Improvements - Open Graph, Twitter Cards, Schema Markup
 *
 * @package ShelzyPerkins
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Open Graph and Twitter Card meta tags
 */
function sp_add_social_meta_tags() {
    if (is_admin()) {
        return;
    }

    $og_title = '';
    $og_description = '';
    $og_image = '';
    $og_url = '';
    $og_type = 'website';

    if (is_singular()) {
        global $post;
        $og_title = get_the_title();
        $og_description = has_excerpt() ? get_the_excerpt() : wp_trim_words(strip_tags($post->post_content), 30);
        $og_url = get_permalink();
        $og_type = 'article';

        if (has_post_thumbnail()) {
            $og_image = get_the_post_thumbnail_url(null, 'sp-featured');
        }
    } elseif (is_front_page()) {
        $og_title = get_bloginfo('name');
        $og_description = get_bloginfo('description');
        $og_url = home_url('/');
    } elseif (is_archive()) {
        $og_title = get_the_archive_title();
        $og_description = get_the_archive_description();
        $og_url = get_pagenum_link();
    } elseif (is_search()) {
        $og_title = sprintf(__('Search Results for: %s', 'shelzyperkins'), get_search_query());
        $og_description = sprintf(__('Search results for %s on %s', 'shelzyperkins'), get_search_query(), get_bloginfo('name'));
        $og_url = get_search_link();
    }

    // Fallback image
    if (empty($og_image)) {
        $og_image = get_template_directory_uri() . '/assets/images/og-default.jpg';
    }

    // Clean up description
    $og_description = wp_strip_all_tags($og_description);
    $og_description = str_replace(array("\r", "\n"), ' ', $og_description);
    $og_description = substr($og_description, 0, 160);

    // Output Open Graph tags
    ?>
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo esc_attr($og_title); ?>">
    <meta property="og:description" content="<?php echo esc_attr($og_description); ?>">
    <meta property="og:url" content="<?php echo esc_url($og_url); ?>">
    <meta property="og:type" content="<?php echo esc_attr($og_type); ?>">
    <meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
    <?php if ($og_image): ?>
    <meta property="og:image" content="<?php echo esc_url($og_image); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <?php endif; ?>
    <meta property="og:locale" content="<?php echo esc_attr(get_locale()); ?>">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr($og_title); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($og_description); ?>">
    <?php if ($og_image): ?>
    <meta name="twitter:image" content="<?php echo esc_url($og_image); ?>">
    <?php endif; ?>
    <meta name="twitter:site" content="@shelzyperkins">
    <meta name="twitter:creator" content="@shelzyperkins">

    <?php if (is_singular('post')): ?>
    <!-- Article Specific -->
    <meta property="article:published_time" content="<?php echo esc_attr(get_the_date('c')); ?>">
    <meta property="article:modified_time" content="<?php echo esc_attr(get_the_modified_date('c')); ?>">
    <meta property="article:author" content="<?php echo esc_attr(get_the_author()); ?>">
    <?php
    $categories = get_the_category();
    if ($categories):
        foreach ($categories as $category):
    ?>
    <meta property="article:section" content="<?php echo esc_attr($category->name); ?>">
    <?php
        endforeach;
    endif;

    $tags = get_the_tags();
    if ($tags):
        foreach ($tags as $tag):
    ?>
    <meta property="article:tag" content="<?php echo esc_attr($tag->name); ?>">
    <?php
        endforeach;
    endif;
    endif;
}
add_action('wp_head', 'sp_add_social_meta_tags', 5);

/**
 * Add canonical URL
 */
function sp_add_canonical_url() {
    if (is_singular()) {
        echo '<link rel="canonical" href="' . esc_url(get_permalink()) . '">' . "\n";
    } elseif (is_front_page()) {
        echo '<link rel="canonical" href="' . esc_url(home_url('/')) . '">' . "\n";
    } elseif (is_archive()) {
        echo '<link rel="canonical" href="' . esc_url(get_pagenum_link()) . '">' . "\n";
    }
}
add_action('wp_head', 'sp_add_canonical_url', 1);

/**
 * Add JSON-LD Schema markup for Website
 */
function sp_website_schema() {
    if (!is_front_page()) {
        return;
    }

    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => get_bloginfo('name'),
        'description' => get_bloginfo('description'),
        'url' => home_url('/'),
        'potentialAction' => array(
            '@type' => 'SearchAction',
            'target' => array(
                '@type' => 'EntryPoint',
                'urlTemplate' => home_url('/?s={search_term_string}'),
            ),
            'query-input' => 'required name=search_term_string',
        ),
    );

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}
add_action('wp_head', 'sp_website_schema');

/**
 * Add JSON-LD Schema markup for Organization
 */
function sp_organization_schema() {
    if (!is_front_page()) {
        return;
    }

    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'ShelzyPerkins',
        'url' => home_url('/'),
        'logo' => get_template_directory_uri() . '/assets/images/logo.png',
        'sameAs' => array(
            'https://pinterest.com/shelzyperkins',
            'https://instagram.com/shelzyperkins',
            'https://tiktok.com/@shelzyperkins',
            'https://youtube.com/@shelzyperkins',
        ),
    );

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}
add_action('wp_head', 'sp_organization_schema');

/**
 * Add JSON-LD Schema markup for Articles
 */
function sp_article_schema() {
    if (!is_singular('post')) {
        return;
    }

    global $post;

    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => get_the_title(),
        'description' => has_excerpt() ? get_the_excerpt() : wp_trim_words(strip_tags($post->post_content), 30),
        'author' => array(
            '@type' => 'Person',
            'name' => get_the_author(),
            'url' => get_author_posts_url(get_the_author_meta('ID')),
        ),
        'publisher' => array(
            '@type' => 'Organization',
            'name' => 'ShelzyPerkins',
            'logo' => array(
                '@type' => 'ImageObject',
                'url' => get_template_directory_uri() . '/assets/images/logo.png',
            ),
        ),
        'datePublished' => get_the_date('c'),
        'dateModified' => get_the_modified_date('c'),
        'mainEntityOfPage' => array(
            '@type' => 'WebPage',
            '@id' => get_permalink(),
        ),
    );

    if (has_post_thumbnail()) {
        $schema['image'] = array(
            '@type' => 'ImageObject',
            'url' => get_the_post_thumbnail_url(null, 'sp-featured'),
            'width' => 1200,
            'height' => 630,
        );
    }

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}
add_action('wp_head', 'sp_article_schema');

/**
 * Add JSON-LD Schema markup for Breadcrumbs
 */
function sp_breadcrumb_schema() {
    if (is_front_page()) {
        return;
    }

    $items = array();
    $position = 1;

    // Home
    $items[] = array(
        '@type' => 'ListItem',
        'position' => $position++,
        'name' => 'Home',
        'item' => home_url('/'),
    );

    if (is_category()) {
        $category = get_queried_object();
        $ancestors = get_ancestors($category->term_id, 'category');
        $ancestors = array_reverse($ancestors);

        foreach ($ancestors as $ancestor_id) {
            $ancestor = get_category($ancestor_id);
            $items[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $ancestor->name,
                'item' => get_category_link($ancestor_id),
            );
        }

        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => $category->name,
            'item' => get_category_link($category->term_id),
        );
    } elseif (is_singular('post')) {
        $categories = get_the_category();
        if ($categories) {
            $category = $categories[0];
            $items[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $category->name,
                'item' => get_category_link($category->term_id),
            );
        }

        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => get_the_title(),
            'item' => get_permalink(),
        );
    } elseif (is_page()) {
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => get_the_title(),
            'item' => get_permalink(),
        );
    }

    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $items,
    );

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}
add_action('wp_head', 'sp_breadcrumb_schema');

/**
 * Add JSON-LD Schema markup for Products (affiliate products)
 */
function sp_product_schema_shortcode($atts) {
    $atts = shortcode_atts(array(
        'name' => '',
        'description' => '',
        'price' => '',
        'currency' => 'USD',
        'image' => '',
        'url' => '',
        'rating' => '',
        'review_count' => '',
        'brand' => '',
        'availability' => 'InStock',
    ), $atts);

    if (empty($atts['name']) || empty($atts['price'])) {
        return '';
    }

    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $atts['name'],
        'offers' => array(
            '@type' => 'Offer',
            'price' => $atts['price'],
            'priceCurrency' => $atts['currency'],
            'availability' => 'https://schema.org/' . $atts['availability'],
            'url' => $atts['url'],
        ),
    );

    if (!empty($atts['description'])) {
        $schema['description'] = $atts['description'];
    }

    if (!empty($atts['image'])) {
        $schema['image'] = $atts['image'];
    }

    if (!empty($atts['brand'])) {
        $schema['brand'] = array(
            '@type' => 'Brand',
            'name' => $atts['brand'],
        );
    }

    if (!empty($atts['rating']) && !empty($atts['review_count'])) {
        $schema['aggregateRating'] = array(
            '@type' => 'AggregateRating',
            'ratingValue' => $atts['rating'],
            'reviewCount' => $atts['review_count'],
        );
    }

    return '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>';
}
add_shortcode('product_schema', 'sp_product_schema_shortcode');
