<?php
/**
 * Template part for displaying post cards
 *
 * @package ShelzyPerkins
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('sp-card sp-post-card'); ?>>
    <!-- Card Image -->
    <div class="sp-card__image">
        <a href="<?php the_permalink(); ?>">
            <?php if (has_post_thumbnail()): ?>
                <?php the_post_thumbnail('sp-card', array('loading' => 'lazy')); ?>
            <?php else: ?>
                <div class="sp-placeholder-img"></div>
            <?php endif; ?>
        </a>

        <?php
        // Display category badge
        $categories = get_the_category();
        if ($categories):
            $category = $categories[0];
            $badge_class = 'sp-card__badge';
            if (has_category('deals')) {
                $badge_class .= ' sp-card__badge--deal';
            }
        ?>
        <span class="<?php echo esc_attr($badge_class); ?>">
            <?php echo esc_html($category->name); ?>
        </span>
        <?php endif; ?>
    </div>

    <!-- Card Content -->
    <div class="sp-card__content">
        <h3 class="sp-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <p class="sp-card__excerpt">
            <?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?>
        </p>

        <div class="sp-card__meta">
            <span class="sp-card__date"><?php echo get_the_date('M j, Y'); ?></span>
            <span class="sp-card__read-time"><?php echo sp_reading_time(); ?> min</span>
        </div>

        <a href="<?php the_permalink(); ?>" class="sp-btn sp-btn--secondary sp-btn--sm">
            Read More
        </a>
    </div>
</article>
