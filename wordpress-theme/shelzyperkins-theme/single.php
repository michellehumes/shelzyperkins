<?php
/**
 * Single Post Template
 *
 * @package ShelzyPerkins
 */

get_header();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('sp-post sp-single'); ?>>
    <!-- Post Header -->
    <header class="sp-post__header">
        <div class="sp-container sp-container--narrow">
            <?php
            $categories = get_the_category();
            if ($categories):
            ?>
            <div class="sp-post__categories">
                <?php foreach ($categories as $cat): ?>
                <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>" class="sp-deal-badge"><?php echo esc_html($cat->name); ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <h1 class="sp-post__title"><?php the_title(); ?></h1>

            <div class="sp-post__meta">
                <span class="sp-post__date">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <?php echo get_the_date(); ?>
                </span>
                <span class="sp-post__read-time">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <?php echo sp_reading_time(); ?> min read
                </span>
                <?php if (get_the_modified_date() !== get_the_date()): ?>
                <span class="sp-post__updated">
                    Updated: <?php echo get_the_modified_date(); ?>
                </span>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Featured Image -->
    <?php if (has_post_thumbnail()): ?>
    <div class="sp-post__featured-image">
        <div class="sp-container sp-container--narrow">
            <?php the_post_thumbnail('sp-featured', array('class' => 'sp-post__image')); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Post Content -->
    <div class="sp-post__content">
        <div class="sp-container sp-container--narrow">
            <?php the_content(); ?>
        </div>
    </div>

    <!-- Post Footer -->
    <footer class="sp-post__footer">
        <div class="sp-container sp-container--narrow">
            <!-- Tags -->
            <?php
            $tags = get_the_tags();
            if ($tags):
            ?>
            <div class="sp-post__tags">
                <strong>Tags:</strong>
                <?php foreach ($tags as $tag): ?>
                <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="sp-tag"><?php echo esc_html($tag->name); ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Share Buttons -->
            <div class="sp-post__share">
                <strong>Share this post:</strong>
                <div class="sp-share-buttons">
                    <a href="https://pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink()); ?>&media=<?php echo urlencode(get_the_post_thumbnail_url()); ?>&description=<?php echo urlencode(get_the_title()); ?>" class="sp-share-btn sp-share-btn--pinterest" target="_blank" rel="noopener">
                        Pinterest
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" class="sp-share-btn sp-share-btn--facebook" target="_blank" rel="noopener">
                        Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" class="sp-share-btn sp-share-btn--twitter" target="_blank" rel="noopener">
                        Twitter
                    </a>
                </div>
            </div>

            <!-- Author Box -->
            <div class="sp-author-box">
                <div class="sp-author-box__avatar">
                    <?php echo get_avatar(get_the_author_meta('ID'), 80); ?>
                </div>
                <div class="sp-author-box__info">
                    <h4 class="sp-author-box__name"><?php the_author(); ?></h4>
                    <p class="sp-author-box__bio"><?php the_author_meta('description'); ?></p>
                </div>
            </div>
        </div>
    </footer>
</article>

<!-- Related Posts -->
<section class="sp-section sp-related-posts">
    <div class="sp-container">
        <h3 class="sp-section__title">You Might Also Like</h3>
        <div class="sp-grid sp-grid--3">
            <?php
            $related_query = new WP_Query(array(
                'posts_per_page' => 3,
                'post__not_in'   => array(get_the_ID()),
                'category__in'   => wp_get_post_categories(get_the_ID()),
                'orderby'        => 'rand',
            ));

            if ($related_query->have_posts()):
                while ($related_query->have_posts()): $related_query->the_post();
                    get_template_part('template-parts/content', 'card');
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </div>
</section>

<!-- Email Signup -->
<section class="sp-section">
    <div class="sp-container sp-container--narrow">
        <?php echo do_shortcode('[email_signup]'); ?>
    </div>
</section>

<?php
get_footer();
