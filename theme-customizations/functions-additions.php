<?php
/**
 * ShelzyPerkins.com Theme Customizations
 * Add these to your child theme's functions.php file
 */

// ============================================================================
// 1. STICKY HEADER
// ============================================================================

function shelzy_sticky_header_script() {
    ?>
    <script>
    jQuery(document).ready(function($) {
        var header = $('.site-header');
        var headerOffset = header.offset().top;

        $(window).scroll(function() {
            if ($(window).scrollTop() > headerOffset) {
                header.addClass('sticky-header');
            } else {
                header.removeClass('sticky-header');
            }
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'shelzy_sticky_header_script');


// ============================================================================
// 2. BACK TO TOP BUTTON
// ============================================================================

function shelzy_back_to_top_button() {
    ?>
    <button id="back-to-top" aria-label="Back to top">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 15l-6-6-6 6"/>
        </svg>
    </button>
    <script>
    jQuery(document).ready(function($) {
        var btn = $('#back-to-top');

        $(window).scroll(function() {
            if ($(window).scrollTop() > 500) {
                btn.addClass('show');
            } else {
                btn.removeClass('show');
            }
        });

        btn.on('click', function(e) {
            e.preventDefault();
            $('html, body').animate({scrollTop: 0}, 600);
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'shelzy_back_to_top_button');


// ============================================================================
// 3. BREADCRUMBS
// ============================================================================

function shelzy_breadcrumbs() {
    if (is_front_page()) return;

    echo '<nav class="breadcrumbs" aria-label="Breadcrumb">';
    echo '<a href="' . home_url() . '">Home</a>';

    if (is_category() || is_single()) {
        echo ' &raquo; ';
        the_category(' &raquo; ');
        if (is_single()) {
            echo ' &raquo; ';
            the_title();
        }
    } elseif (is_page()) {
        echo ' &raquo; ';
        the_title();
    } elseif (is_search()) {
        echo ' &raquo; Search Results for: ' . get_search_query();
    } elseif (is_404()) {
        echo ' &raquo; 404 Not Found';
    }

    echo '</nav>';
}


// ============================================================================
// 4. AUTHOR BIO BOX
// ============================================================================

function shelzy_author_bio_box() {
    if (!is_single()) return;
    ?>
    <div class="author-bio-box">
        <img src="<?php echo get_avatar_url(get_the_author_meta('ID'), array('size' => 120)); ?>"
             alt="<?php the_author(); ?>"
             class="author-avatar">
        <div class="author-info">
            <h3 class="author-name">Written by <?php the_author(); ?></h3>
            <p class="author-description">
                <?php
                $bio = get_the_author_meta('description');
                if (empty($bio)) {
                    echo "Deal hunter, budget optimizer, and Amazon aficionado. I test products so you don't have to.";
                } else {
                    echo esc_html($bio);
                }
                ?>
            </p>
            <div class="author-social">
                <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" class="author-link">
                    View all posts →
                </a>
            </div>
        </div>
    </div>
    <?php
}
add_action('the_content', 'shelzy_author_bio_box');


// ============================================================================
// 5. RELATED POSTS
// ============================================================================

function shelzy_related_posts() {
    if (!is_single()) return;

    global $post;
    $categories = get_the_category($post->ID);

    if ($categories) {
        $category_ids = array();
        foreach($categories as $category) {
            $category_ids[] = $category->term_id;
        }

        $args = array(
            'category__in' => $category_ids,
            'post__not_in' => array($post->ID),
            'posts_per_page' => 6,
            'orderby' => 'rand'
        );

        $related_posts = new WP_Query($args);

        if ($related_posts->have_posts()) {
            echo '<div class="related-posts">';
            echo '<h3>You Might Also Like</h3>';
            echo '<div class="related-posts-grid">';

            while ($related_posts->have_posts()) {
                $related_posts->the_post();
                ?>
                <article class="related-post-card">
                    <a href="<?php the_permalink(); ?>">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium'); ?>
                        <?php endif; ?>
                        <h4><?php the_title(); ?></h4>
                    </a>
                </article>
                <?php
            }

            echo '</div></div>';
            wp_reset_postdata();
        }
    }
}
add_action('the_content', 'shelzy_related_posts');


// ============================================================================
// 6. SCHEMA MARKUP - PRODUCT
// ============================================================================

function shelzy_add_product_schema() {
    if (!is_single()) return;

    global $post;
    $content = $post->post_content;

    // Extract products from content
    preg_match_all('/<h3>([^<]+)<\/h3>.*?<strong>(\$[\d.,]+)<\/strong>.*?amazon\.com\/dp\/([A-Z0-9]{10})/s', $content, $matches, PREG_SET_ORDER);

    if (!empty($matches)) {
        foreach ($matches as $match) {
            $product_name = $match[1];
            $price = str_replace(['$', ','], '', $match[2]);
            $asin = $match[3];

            $schema = array(
                '@context' => 'https://schema.org/',
                '@type' => 'Product',
                'name' => $product_name,
                'offers' => array(
                    '@type' => 'Offer',
                    'price' => $price,
                    'priceCurrency' => 'USD',
                    'url' => 'https://www.amazon.com/dp/' . $asin,
                    'availability' => 'https://schema.org/InStock',
                    'seller' => array(
                        '@type' => 'Organization',
                        'name' => 'Amazon'
                    )
                )
            );

            echo '<script type="application/ld+json">' . json_encode($schema) . '</script>';
        }
    }
}
add_action('wp_head', 'shelzy_add_product_schema');


// ============================================================================
// 7. SCHEMA MARKUP - ARTICLE
// ============================================================================

function shelzy_add_article_schema() {
    if (!is_single()) return;

    global $post;

    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => get_the_title(),
        'datePublished' => get_the_date('c'),
        'dateModified' => get_the_modified_date('c'),
        'author' => array(
            '@type' => 'Person',
            'name' => get_the_author()
        ),
        'publisher' => array(
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
            'logo' => array(
                '@type' => 'ImageObject',
                'url' => get_site_icon_url()
            )
        ),
        'description' => get_the_excerpt(),
        'mainEntityOfPage' => array(
            '@type' => 'WebPage',
            '@id' => get_permalink()
        )
    );

    if (has_post_thumbnail()) {
        $schema['image'] = get_the_post_thumbnail_url($post, 'full');
    }

    echo '<script type="application/ld+json">' . json_encode($schema) . '</script>';
}
add_action('wp_head', 'shelzy_add_article_schema');


// ============================================================================
// 8. SCHEMA MARKUP - BREADCRUMBS
// ============================================================================

function shelzy_breadcrumb_schema() {
    if (is_front_page()) return;

    $position = 1;
    $breadcrumbs = array(
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => array()
    );

    // Home
    $breadcrumbs['itemListElement'][] = array(
        '@type' => 'ListItem',
        'position' => $position++,
        'name' => 'Home',
        'item' => home_url()
    );

    // Category
    if (is_category() || is_single()) {
        $categories = get_the_category();
        if ($categories) {
            $category = $categories[0];
            $breadcrumbs['itemListElement'][] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $category->name,
                'item' => get_category_link($category->term_id)
            );
        }
    }

    // Current page
    if (is_single() || is_page()) {
        $breadcrumbs['itemListElement'][] = array(
            '@type' => 'ListItem',
            'position' => $position,
            'name' => get_the_title(),
            'item' => get_permalink()
        );
    }

    echo '<script type="application/ld+json">' . json_encode($breadcrumbs) . '</script>';
}
add_action('wp_head', 'shelzy_breadcrumb_schema');


// ============================================================================
// 9. OPTIMIZE IMAGES - LAZY LOADING
// ============================================================================

function shelzy_add_lazy_loading($content) {
    // Add loading="lazy" to all images
    $content = preg_replace('/<img(.*?)src=/i', '<img$1loading="lazy" src=', $content);
    return $content;
}
add_filter('the_content', 'shelzy_add_lazy_loading');


// ============================================================================
// 10. REMOVE QUERY STRINGS FROM STATIC RESOURCES
// ============================================================================

function shelzy_remove_query_strings($src) {
    $parts = explode('?', $src);
    return $parts[0];
}
add_filter('script_loader_src', 'shelzy_remove_query_strings', 15, 1);
add_filter('style_loader_src', 'shelzy_remove_query_strings', 15, 1);


// ============================================================================
// 11. DISABLE EMOJIS
// ============================================================================

function shelzy_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'shelzy_disable_emojis');


// ============================================================================
// 12. DISABLE XML-RPC (SECURITY)
// ============================================================================

add_filter('xmlrpc_enabled', '__return_false');


// ============================================================================
// 13. HIDE WORDPRESS VERSION (SECURITY)
// ============================================================================

function shelzy_remove_version() {
    return '';
}
add_filter('the_generator', 'shelzy_remove_version');


// ============================================================================
// 14. LIMIT POST REVISIONS
// ============================================================================

if (!defined('WP_POST_REVISIONS')) {
    define('WP_POST_REVISIONS', 5);
}


// ============================================================================
// 15. INCREASE AUTOSAVE INTERVAL
// ============================================================================

if (!defined('AUTOSAVE_INTERVAL')) {
    define('AUTOSAVE_INTERVAL', 300); // 5 minutes
}


// ============================================================================
// 16. CUSTOM 404 PAGE CONTENT
// ============================================================================

function shelzy_custom_404_content() {
    if (!is_404()) return;
    ?>
    <div class="custom-404">
        <h1>Oops! Page Not Found</h1>
        <p>The deal you're looking for might have expired or moved.</p>

        <div class="search-404">
            <?php get_search_form(); ?>
        </div>

        <div class="popular-categories">
            <h2>Shop by Category</h2>
            <?php
            $categories = get_categories(array('number' => 6));
            foreach ($categories as $cat) {
                echo '<a href="' . get_category_link($cat->term_id) . '" class="cat-button">' . $cat->name . '</a>';
            }
            ?>
        </div>

        <div class="latest-posts-404">
            <h2>Latest Deals</h2>
            <?php
            $recent_posts = wp_get_recent_posts(array('numberposts' => 6));
            echo '<div class="posts-grid">';
            foreach ($recent_posts as $post) {
                echo '<article>';
                echo '<a href="' . get_permalink($post['ID']) . '">';
                echo '<h3>' . $post['post_title'] . '</h3>';
                echo '</a>';
                echo '</article>';
            }
            echo '</div>';
            ?>
        </div>
    </div>
    <?php
}
add_action('404_template', 'shelzy_custom_404_content');


// ============================================================================
// 17. ADD ASYNC/DEFER TO SCRIPTS
// ============================================================================

function shelzy_defer_scripts($tag, $handle, $src) {
    // List of scripts to defer
    $defer_scripts = array(
        'jquery',
        'wp-embed'
    );

    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }

    return $tag;
}
add_filter('script_loader_tag', 'shelzy_defer_scripts', 10, 3);


// ============================================================================
// 18. CUSTOM EXCERPT LENGTH
// ============================================================================

function shelzy_excerpt_length($length) {
    return 30; // 30 words
}
add_filter('excerpt_length', 'shelzy_excerpt_length');


// ============================================================================
// 19. CUSTOM EXCERPT MORE
// ============================================================================

function shelzy_excerpt_more($more) {
    return '... <a href="' . get_permalink() . '" class="read-more">Read More →</a>';
}
add_filter('excerpt_more', 'shelzy_excerpt_more');


// ============================================================================
// 20. OPEN GRAPH META TAGS
// ============================================================================

function shelzy_add_opengraph() {
    if (is_single()) {
        global $post;
        ?>
        <meta property="og:title" content="<?php the_title(); ?>" />
        <meta property="og:type" content="article" />
        <meta property="og:url" content="<?php the_permalink(); ?>" />
        <meta property="og:description" content="<?php echo get_the_excerpt(); ?>" />
        <meta property="og:site_name" content="<?php bloginfo('name'); ?>" />
        <?php if (has_post_thumbnail()) : ?>
            <meta property="og:image" content="<?php the_post_thumbnail_url('large'); ?>" />
        <?php endif; ?>

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="<?php the_title(); ?>" />
        <meta name="twitter:description" content="<?php echo get_the_excerpt(); ?>" />
        <?php if (has_post_thumbnail()) : ?>
            <meta name="twitter:image" content="<?php the_post_thumbnail_url('large'); ?>" />
        <?php endif; ?>
        <?php
    }
}
add_action('wp_head', 'shelzy_add_opengraph');

?>
