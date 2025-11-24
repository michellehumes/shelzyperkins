<?php
/**
 * Template Name: Gift Guide
 * Description: Template for gift guide posts
 *
 * @package ShelzyPerkins
 */

get_header();
?>

<article class="sp-gift-guide">
    <!-- Hero -->
    <header class="sp-gift-guide__header">
        <div class="sp-container">
            <?php if (has_post_thumbnail()): ?>
                <div class="sp-gift-guide__hero-image">
                    <?php the_post_thumbnail('sp-featured'); ?>
                </div>
            <?php endif; ?>
            <h1 class="sp-gift-guide__title"><?php the_title(); ?></h1>
            <p class="sp-gift-guide__meta">
                Last Updated: <?php echo get_the_modified_date(); ?> |
                <?php echo sp_reading_time(); ?> min read
            </p>
        </div>
    </header>

    <!-- Affiliate Disclosure -->
    <div class="sp-container sp-container--narrow">
        <?php sp_affiliate_disclosure(true); ?>
    </div>

    <!-- Table of Contents -->
    <nav class="sp-toc">
        <div class="sp-container sp-container--narrow">
            <h3>Jump to Section</h3>
            <ul class="sp-toc__list">
                <!-- Auto-generated via JS or manually listed -->
            </ul>
        </div>
    </nav>

    <!-- Content -->
    <div class="sp-gift-guide__content">
        <div class="sp-container sp-container--narrow">
            <?php
            while (have_posts()): the_post();
                the_content();
            endwhile;
            ?>
        </div>
    </div>

    <!-- Quick Links Bar -->
    <div class="sp-quick-links">
        <div class="sp-container">
            <h4>Quick Shop</h4>
            <div class="sp-quick-links__grid">
                <!-- Auto-populated with product images -->
            </div>
        </div>
    </div>

    <!-- Related Gift Guides -->
    <section class="sp-related-guides sp-section">
        <div class="sp-container">
            <h3 class="sp-section__title">More Gift Ideas</h3>
            <div class="sp-grid sp-grid--3">
                <?php
                $related = new WP_Query(array(
                    'posts_per_page' => 3,
                    'post__not_in' => array(get_the_ID()),
                    'tag' => 'gift-guide',
                ));

                if ($related->have_posts()):
                    while ($related->have_posts()): $related->the_post();
                        get_template_part('template-parts/content', 'card');
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </div>
    </section>
</article>

<?php
get_footer();
