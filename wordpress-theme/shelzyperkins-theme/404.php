<?php
/**
 * 404 Error Page Template
 *
 * @package ShelzyPerkins
 */

get_header();
?>

<div class="sp-error-page">
    <div class="sp-container sp-container--narrow">
        <div class="sp-error-content">
            <div class="sp-error-icon">
                <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M16 16s-1.5-2-4-2-4 2-4 2"></path>
                    <line x1="9" y1="9" x2="9.01" y2="9"></line>
                    <line x1="15" y1="9" x2="15.01" y2="9"></line>
                </svg>
            </div>

            <h1 class="sp-error-title">Oops! Page Not Found</h1>
            <p class="sp-error-message">The page you're looking for seems to have wandered off. Don't worry, we've got plenty of amazing deals waiting for you!</p>

            <!-- Search Form -->
            <div class="sp-error-search">
                <form role="search" method="get" class="sp-search-form" action="<?php echo esc_url(home_url('/')); ?>">
                    <input type="search" class="sp-search-form__input" placeholder="<?php esc_attr_e('Search for deals...', 'shelzyperkins'); ?>" value="" name="s">
                    <button type="submit" class="sp-btn sp-btn--primary">Search</button>
                </form>
            </div>

            <div class="sp-error-links">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="sp-btn sp-btn--primary">Go Home</a>
                <a href="<?php echo esc_url(home_url('/deals/')); ?>" class="sp-btn sp-btn--secondary">Browse Deals</a>
            </div>
        </div>

        <!-- Popular Posts Section -->
        <div class="sp-error-popular">
            <h2>Check Out These Popular Posts</h2>
            <div class="sp-grid sp-grid--3">
                <?php
                $popular_query = new WP_Query(array(
                    'posts_per_page' => 6,
                    'orderby'        => 'comment_count',
                    'order'          => 'DESC',
                ));

                if ($popular_query->have_posts()):
                    while ($popular_query->have_posts()): $popular_query->the_post();
                        get_template_part('template-parts/content', 'card');
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </div>

        <!-- Category Links -->
        <div class="sp-error-categories">
            <h3>Browse by Category</h3>
            <div class="sp-category-pills">
                <?php
                $categories = get_categories(array(
                    'orderby'    => 'count',
                    'order'      => 'DESC',
                    'number'     => 8,
                    'hide_empty' => true,
                ));

                foreach ($categories as $category):
                ?>
                <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="sp-category-pill">
                    <?php echo esc_html($category->name); ?>
                    <span class="sp-category-pill__count"><?php echo esc_html($category->count); ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
