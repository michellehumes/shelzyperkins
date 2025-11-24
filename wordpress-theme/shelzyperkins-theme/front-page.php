<?php
/**
 * Front Page Template
 *
 * @package ShelzyPerkins
 */

get_header();
?>

<!-- Hero Section -->
<section class="sp-hero">
    <div class="sp-container">
        <h1 class="sp-hero__title">Smart Deals. Real Savings.</h1>
        <p class="sp-hero__subtitle">Discover the best Amazon finds, curated product recommendations, and money-saving tips from someone who actually tests everything.</p>
        <div class="sp-hero__cta">
            <a href="<?php echo esc_url(home_url('/deals/')); ?>" class="sp-btn sp-btn--primary sp-btn--lg">Browse Today's Deals</a>
            <a href="https://www.amazon.com/shop/shelzyperkins?tag=shelzysdesigns-20" class="sp-btn sp-btn--secondary sp-btn--lg" target="_blank" rel="nofollow sponsored noopener">Shop My Storefront</a>
        </div>
    </div>
</section>

<!-- Category Grid -->
<section class="sp-section">
    <div class="sp-container">
        <h2 class="sp-section__title sp-text-center sp-mb-xl">Shop By Category</h2>
        <div class="sp-category-grid">
            <a href="<?php echo esc_url(home_url('/category/beauty/')); ?>" class="sp-category-card">
                <div class="sp-category-card__icon">&#128132;</div>
                <h3 class="sp-category-card__title">Beauty</h3>
            </a>
            <a href="<?php echo esc_url(home_url('/category/home/')); ?>" class="sp-category-card">
                <div class="sp-category-card__icon">&#127968;</div>
                <h3 class="sp-category-card__title">Home</h3>
            </a>
            <a href="<?php echo esc_url(home_url('/category/tech/')); ?>" class="sp-category-card">
                <div class="sp-category-card__icon">&#128187;</div>
                <h3 class="sp-category-card__title">Tech</h3>
            </a>
            <a href="<?php echo esc_url(home_url('/category/fashion/')); ?>" class="sp-category-card">
                <div class="sp-category-card__icon">&#128087;</div>
                <h3 class="sp-category-card__title">Fashion</h3>
            </a>
            <a href="<?php echo esc_url(home_url('/category/kitchen/')); ?>" class="sp-category-card">
                <div class="sp-category-card__icon">&#127860;</div>
                <h3 class="sp-category-card__title">Kitchen</h3>
            </a>
            <a href="<?php echo esc_url(home_url('/gift-guides/')); ?>" class="sp-category-card">
                <div class="sp-category-card__icon">&#127873;</div>
                <h3 class="sp-category-card__title">Gift Guides</h3>
            </a>
        </div>
    </div>
</section>

<!-- Top Deals Section -->
<section class="sp-section sp-bg-light">
    <div class="sp-container">
        <div class="sp-section-header">
            <h2 class="sp-section__title">Today's Top Deals</h2>
            <a href="<?php echo esc_url(home_url('/deals/')); ?>" class="sp-btn sp-btn--secondary sp-btn--sm">View All</a>
        </div>

        <div class="sp-grid sp-grid--4">
            <?php
            $deals_query = new WP_Query(array(
                'posts_per_page' => 4,
                'category_name'  => 'deals',
                'orderby'        => 'date',
                'order'          => 'DESC',
            ));

            if ($deals_query->have_posts()):
                while ($deals_query->have_posts()): $deals_query->the_post();
                    get_template_part('template-parts/content', 'card');
                endwhile;
                wp_reset_postdata();
            else:
                // Placeholder cards if no deals yet
                for ($i = 0; $i < 4; $i++):
            ?>
                <div class="sp-card sp-card--placeholder">
                    <div class="sp-card__image">
                        <div class="sp-placeholder-img"></div>
                        <span class="sp-card__badge">Coming Soon</span>
                    </div>
                    <div class="sp-card__content">
                        <h4 class="sp-card__title">Hot Deal Coming</h4>
                        <p class="sp-card__excerpt">Check back soon for amazing deals!</p>
                    </div>
                </div>
            <?php
                endfor;
            endif;
            ?>
        </div>
    </div>
</section>

<!-- Featured Posts Grid -->
<section class="sp-section">
    <div class="sp-container">
        <h2 class="sp-section__title sp-text-center sp-mb-xl">Latest Finds & Reviews</h2>

        <div class="sp-grid sp-grid--3">
            <?php
            $featured_query = new WP_Query(array(
                'posts_per_page' => 6,
                'post__not_in'   => get_option('sticky_posts'),
                'orderby'        => 'date',
                'order'          => 'DESC',
            ));

            if ($featured_query->have_posts()):
                while ($featured_query->have_posts()): $featured_query->the_post();
                    get_template_part('template-parts/content', 'card');
                endwhile;
                wp_reset_postdata();
            else:
                // Placeholder cards
                for ($i = 0; $i < 6; $i++):
            ?>
                <div class="sp-card sp-card--placeholder">
                    <div class="sp-card__image">
                        <div class="sp-placeholder-img"></div>
                    </div>
                    <div class="sp-card__content">
                        <h4 class="sp-card__title">New Content Coming</h4>
                        <p class="sp-card__excerpt">Exciting posts are on the way!</p>
                    </div>
                </div>
            <?php
                endfor;
            endif;
            ?>
        </div>

        <div class="sp-text-center sp-mt-xl">
            <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="sp-btn sp-btn--primary">View All Posts</a>
        </div>
    </div>
</section>

<!-- Email Signup Section -->
<section class="sp-section">
    <div class="sp-container sp-container--narrow">
        <?php echo do_shortcode('[email_signup title="Never Miss a Deal!" description="Join 10,000+ smart shoppers who get the best deals delivered to their inbox weekly. No spam, just savings!" button_text="Subscribe"]'); ?>
    </div>
</section>

<!-- Trending Carousel -->
<section class="sp-section sp-bg-light">
    <div class="sp-container">
        <h2 class="sp-section__title sp-text-center sp-mb-xl">Trending This Week</h2>
        <div class="sp-trending-carousel" id="trending-carousel">
            <?php
            $trending_query = new WP_Query(array(
                'posts_per_page' => 8,
                'meta_key'       => 'post_views_count',
                'orderby'        => 'meta_value_num',
                'order'          => 'DESC',
            ));

            if ($trending_query->have_posts()):
                while ($trending_query->have_posts()): $trending_query->the_post();
                    get_template_part('template-parts/content', 'card-mini');
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </div>
</section>

<!-- Pinterest Grid Section -->
<section class="sp-section">
    <div class="sp-container">
        <div class="sp-section-header">
            <h2 class="sp-section__title">Shop My Pinterest Favorites</h2>
            <a href="https://pinterest.com/shelzyperkins" class="sp-btn sp-btn--secondary sp-btn--sm" target="_blank" rel="noopener">Follow on Pinterest</a>
        </div>

        <div class="sp-pinterest-grid">
            <?php
            $pinterest_query = new WP_Query(array(
                'posts_per_page' => 6,
                'tag'            => 'pinterest-featured',
                'orderby'        => 'date',
                'order'          => 'DESC',
            ));

            if ($pinterest_query->have_posts()):
                while ($pinterest_query->have_posts()): $pinterest_query->the_post();
                    get_template_part('template-parts/content', 'pinterest');
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </div>
</section>

<!-- About/Trust Section -->
<section class="sp-section sp-about-preview">
    <div class="sp-container">
        <div class="sp-about-grid">
            <div class="sp-about-content">
                <h2>Hi, I'm Shelzy!</h2>
                <p>I'm a deal hunter, bargain finder, and your personal shopping assistant. Every product I share has been carefully researched (and often tested!) to make sure it's actually worth your money.</p>
                <p>Whether you're looking for the best budget-friendly skincare, home organization hacks, or tech gadgets that won't break the bank, I've got you covered.</p>
                <a href="<?php echo esc_url(home_url('/about/')); ?>" class="sp-btn sp-btn--primary">Learn More About Me</a>
            </div>
            <div class="sp-about-image">
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/shelzy-about.jpg" alt="Shelzy Perkins" loading="lazy">
            </div>
        </div>
    </div>
</section>

<?php
get_footer();
