<?php
/**
 * Sidebar Template
 *
 * @package ShelzyPerkins
 */

if (!is_active_sidebar('sidebar-blog')) {
    return;
}
?>

<aside class="sp-sidebar" role="complementary">
    <!-- Search Widget -->
    <div class="sp-widget sp-widget--search">
        <form role="search" method="get" class="sp-search-form" action="<?php echo esc_url(home_url('/')); ?>">
            <input type="search" class="sp-search-form__input" placeholder="Search deals..." value="<?php echo get_search_query(); ?>" name="s">
            <button type="submit" class="sp-btn sp-btn--primary">Search</button>
        </form>
    </div>

    <!-- Email Signup Widget -->
    <div class="sp-widget sp-widget--email">
        <h4 class="sp-widget__title">Get Deals in Your Inbox</h4>
        <p>Never miss a deal! Subscribe for weekly updates.</p>
        <form class="sp-sidebar-email-form">
            <input type="email" placeholder="Your email" required>
            <button type="submit" class="sp-btn sp-btn--primary sp-btn--sm">Subscribe</button>
        </form>
    </div>

    <!-- Categories Widget -->
    <div class="sp-widget sp-widget--categories">
        <h4 class="sp-widget__title">Browse Categories</h4>
        <ul class="sp-widget__list">
            <?php
            $categories = get_categories(array(
                'orderby'    => 'count',
                'order'      => 'DESC',
                'hide_empty' => true,
                'number'     => 8,
            ));
            foreach ($categories as $cat):
            ?>
            <li>
                <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>">
                    <?php echo esc_html($cat->name); ?>
                    <span class="sp-widget__count">(<?php echo $cat->count; ?>)</span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Popular Posts Widget -->
    <div class="sp-widget sp-widget--popular">
        <h4 class="sp-widget__title">Popular Posts</h4>
        <ul class="sp-widget__posts">
            <?php
            $popular_query = new WP_Query(array(
                'posts_per_page' => 5,
                'meta_key'       => 'post_views_count',
                'orderby'        => 'meta_value_num',
                'order'          => 'DESC',
            ));

            if ($popular_query->have_posts()):
                while ($popular_query->have_posts()): $popular_query->the_post();
            ?>
            <li class="sp-widget__post-item">
                <?php if (has_post_thumbnail()): ?>
                <a href="<?php the_permalink(); ?>" class="sp-widget__post-thumb">
                    <?php the_post_thumbnail('thumbnail'); ?>
                </a>
                <?php endif; ?>
                <div class="sp-widget__post-info">
                    <a href="<?php the_permalink(); ?>" class="sp-widget__post-title"><?php the_title(); ?></a>
                    <span class="sp-widget__post-date"><?php echo get_the_date('M j, Y'); ?></span>
                </div>
            </li>
            <?php
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </ul>
    </div>

    <!-- Amazon Storefront CTA -->
    <div class="sp-widget sp-widget--cta">
        <h4 class="sp-widget__title">Shop My Favorites</h4>
        <p>Browse my curated Amazon storefront for all my top picks!</p>
        <a href="https://www.amazon.com/shop/shelzyperkins?tag=shelzysdesigns-20" class="sp-btn sp-btn--amazon" target="_blank" rel="nofollow sponsored noopener">
            Visit My Storefront
        </a>
    </div>

    <!-- Social Follow Widget -->
    <div class="sp-widget sp-widget--social">
        <h4 class="sp-widget__title">Follow Along</h4>
        <div class="sp-social-links">
            <a href="https://pinterest.com/shelzyperkins" target="_blank" rel="noopener" class="sp-social-link sp-social-link--pinterest">Pinterest</a>
            <a href="https://instagram.com/shelzyperkins" target="_blank" rel="noopener" class="sp-social-link sp-social-link--instagram">Instagram</a>
            <a href="https://tiktok.com/@shelzyperkins" target="_blank" rel="noopener" class="sp-social-link sp-social-link--tiktok">TikTok</a>
        </div>
    </div>

    <?php dynamic_sidebar('sidebar-blog'); ?>
</aside>
