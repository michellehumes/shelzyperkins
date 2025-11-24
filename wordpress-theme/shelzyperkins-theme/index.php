<?php
/**
 * The main template file
 *
 * @package ShelzyPerkins
 */

get_header();
?>

<div class="sp-container sp-section">
    <?php if (is_home() && !is_paged()): ?>
    <!-- Featured Section -->
    <section class="sp-featured-section sp-mb-2xl">
        <h2 class="sp-section__title">Latest Deals & Finds</h2>
        <p class="sp-section__subtitle">Discover the best products I've found this week</p>
    </section>
    <?php endif; ?>

    <div class="sp-blog-layout">
        <!-- Main Content -->
        <div class="sp-blog-main">
            <?php if (have_posts()): ?>
                <div class="sp-posts-grid sp-grid sp-grid--3">
                    <?php while (have_posts()): the_post(); ?>
                        <?php get_template_part('template-parts/content', 'card'); ?>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <div class="sp-pagination sp-mt-2xl">
                    <?php
                    the_posts_pagination(array(
                        'mid_size'  => 2,
                        'prev_text' => '<span>&larr;</span> Previous',
                        'next_text' => 'Next <span>&rarr;</span>',
                    ));
                    ?>
                </div>
            <?php else: ?>
                <div class="sp-no-posts">
                    <h2>No posts found</h2>
                    <p>Check back soon for the latest deals and recommendations!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <aside class="sp-blog-sidebar">
            <?php get_sidebar(); ?>
        </aside>
    </div>
</div>

<?php
get_footer();
