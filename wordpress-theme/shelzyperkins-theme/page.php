<?php
/**
 * Page Template
 *
 * @package ShelzyPerkins
 */

get_header();
?>

<article id="page-<?php the_ID(); ?>" <?php post_class('sp-page'); ?>>
    <!-- Page Header -->
    <header class="sp-page__header">
        <div class="sp-container sp-container--narrow">
            <h1 class="sp-page__title"><?php the_title(); ?></h1>
        </div>
    </header>

    <!-- Page Content -->
    <div class="sp-page__content">
        <div class="sp-container sp-container--narrow">
            <?php
            while (have_posts()): the_post();
                the_content();
            endwhile;
            ?>
        </div>
    </div>
</article>

<?php
get_footer();
