<?php
/**
 * Template Name: Deals Archive
 * Description: Template for displaying daily deals and sales
 *
 * @package ShelzyPerkins
 */

get_header();
?>

<div class="sp-deals-page">
    <!-- Page Header -->
    <header class="sp-page__header">
        <div class="sp-container">
            <h1 class="sp-page__title">Today's Best Deals</h1>
            <p class="sp-page__subtitle">Handpicked deals updated daily. Bookmark this page and check back often!</p>
            <p class="sp-deals-updated">Last updated: <?php echo date('F j, Y \a\t g:i A'); ?></p>
        </div>
    </header>

    <div class="sp-container sp-section">
        <!-- Deal Filters -->
        <div class="sp-deals-filters">
            <button class="sp-filter-btn sp-filter-btn--active" data-filter="all">All Deals</button>
            <button class="sp-filter-btn" data-filter="under-25">Under $25</button>
            <button class="sp-filter-btn" data-filter="under-50">Under $50</button>
            <button class="sp-filter-btn" data-filter="beauty">Beauty</button>
            <button class="sp-filter-btn" data-filter="home">Home</button>
            <button class="sp-filter-btn" data-filter="tech">Tech</button>
        </div>

        <!-- Hot Deals Section -->
        <section class="sp-deals-section">
            <h2 class="sp-section__title">
                <span class="sp-deal-badge sp-deal-badge--hot">HOT</span>
                Lightning Deals
            </h2>
            <div class="sp-grid sp-grid--4">
                <?php
                $hot_deals = new WP_Query(array(
                    'posts_per_page' => 4,
                    'tag' => 'hot-deal',
                    'orderby' => 'date',
                    'order' => 'DESC',
                ));

                if ($hot_deals->have_posts()):
                    while ($hot_deals->have_posts()): $hot_deals->the_post();
                        get_template_part('template-parts/content', 'card');
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </section>

        <!-- All Deals Grid -->
        <section class="sp-deals-section sp-mt-2xl">
            <h2 class="sp-section__title">All Deals</h2>
            <div class="sp-grid sp-grid--3" id="deals-grid">
                <?php
                $deals_query = new WP_Query(array(
                    'posts_per_page' => 12,
                    'category_name' => 'deals',
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
                ));

                if ($deals_query->have_posts()):
                    while ($deals_query->have_posts()): $deals_query->the_post();
                        get_template_part('template-parts/content', 'card');
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>

            <!-- Pagination -->
            <div class="sp-pagination sp-mt-2xl">
                <?php
                echo paginate_links(array(
                    'total' => $deals_query->max_num_pages,
                    'current' => max(1, get_query_var('paged')),
                    'prev_text' => '&larr; Previous',
                    'next_text' => 'Next &rarr;',
                ));
                ?>
            </div>
        </section>

        <!-- Email Signup -->
        <section class="sp-mt-2xl">
            <?php echo do_shortcode('[email_signup title="Get Daily Deals in Your Inbox" description="Never miss a deal! Subscribe for daily updates on the best Amazon finds."]'); ?>
        </section>
    </div>
</div>

<?php
get_footer();
