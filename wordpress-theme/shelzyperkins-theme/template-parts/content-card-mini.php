<?php
/**
 * Template part for displaying mini post cards (carousel items)
 *
 * @package ShelzyPerkins
 */
?>

<article class="sp-card-mini">
    <a href="<?php the_permalink(); ?>" class="sp-card-mini__link">
        <?php if (has_post_thumbnail()): ?>
            <?php the_post_thumbnail('thumbnail', array('class' => 'sp-card-mini__image', 'loading' => 'lazy')); ?>
        <?php endif; ?>
        <h4 class="sp-card-mini__title"><?php the_title(); ?></h4>
    </a>
</article>
