<?php
/**
 * Front Page Template
 * A visually stunning homepage for ShelzyPerkins affiliate site
 *
 * @package ShelzyPerkins
 */

get_header();
?>

<!-- Animated Hero Section -->
<section class="sp-hero-v2">
    <div class="sp-hero-v2__bg">
        <div class="sp-hero-v2__gradient"></div>
        <div class="sp-hero-v2__shapes">
            <div class="sp-shape sp-shape--1"></div>
            <div class="sp-shape sp-shape--2"></div>
            <div class="sp-shape sp-shape--3"></div>
        </div>
    </div>
    <div class="sp-container">
        <div class="sp-hero-v2__content">
            <span class="sp-hero-v2__badge">Your Personal Deal Hunter</span>
            <h1 class="sp-hero-v2__title">
                Smart Deals.<br>
                <span class="sp-hero-v2__title-accent">Real Savings.</span>
            </h1>
            <p class="sp-hero-v2__subtitle">
                I test products, track prices, and find the best Amazon deals so you don't have to. Join 10,000+ smart shoppers saving hundreds every month.
            </p>
            <div class="sp-hero-v2__cta">
                <a href="<?php echo esc_url(home_url('/deals/')); ?>" class="sp-btn sp-btn--white sp-btn--lg">
                    <span>🔥</span> Today's Hot Deals
                </a>
                <a href="https://www.amazon.com/shop/shelzyperkins?tag=shelzysdesigns-20" class="sp-btn sp-btn--outline-white sp-btn--lg" target="_blank" rel="nofollow sponsored noopener">
                    Shop My Storefront
                </a>
            </div>
            <div class="sp-hero-v2__stats">
                <div class="sp-hero-stat">
                    <span class="sp-hero-stat__number">500+</span>
                    <span class="sp-hero-stat__label">Products Tested</span>
                </div>
                <div class="sp-hero-stat">
                    <span class="sp-hero-stat__number">$10K+</span>
                    <span class="sp-hero-stat__label">Saved for Readers</span>
                </div>
                <div class="sp-hero-stat">
                    <span class="sp-hero-stat__number">10K+</span>
                    <span class="sp-hero-stat__label">Happy Shoppers</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Black Friday / Seasonal Banner -->
<section class="sp-promo-banner">
    <div class="sp-container">
        <div class="sp-promo-banner__inner">
            <div class="sp-promo-banner__icon">🛒</div>
            <div class="sp-promo-banner__content">
                <h3 class="sp-promo-banner__title">Black Friday Deals Are LIVE!</h3>
                <p class="sp-promo-banner__text">The biggest deals of the year - updated hourly. Don't miss out!</p>
            </div>
            <a href="<?php echo esc_url(home_url('/black-friday-deals-2024/')); ?>" class="sp-btn sp-btn--amazon">
                Shop Black Friday →
            </a>
        </div>
    </div>
</section>

<!-- Today's Top Picks - Featured Deal Cards -->
<section class="sp-section sp-featured-deals">
    <div class="sp-container">
        <div class="sp-section-header-v2">
            <div class="sp-section-header-v2__left">
                <span class="sp-section-tag">Updated Daily</span>
                <h2 class="sp-section__title">Today's Top Picks</h2>
                <p class="sp-section__subtitle">Hand-selected deals I'd actually buy myself</p>
            </div>
            <a href="<?php echo esc_url(home_url('/deals/')); ?>" class="sp-btn sp-btn--secondary">View All Deals</a>
        </div>

        <div class="sp-featured-grid">
            <!-- Featured Deal - Large Card -->
            <div class="sp-deal-card sp-deal-card--featured">
                <div class="sp-deal-card__badge">🔥 Deal of the Day</div>
                <div class="sp-deal-card__image">
                    <img src="https://m.media-amazon.com/images/I/61SUj2aKoEL.jpg" alt="AirPods Pro" loading="lazy">
                </div>
                <div class="sp-deal-card__content">
                    <span class="sp-deal-card__category">Tech</span>
                    <h3 class="sp-deal-card__title">Apple AirPods Pro (2nd Gen)</h3>
                    <div class="sp-deal-card__pricing">
                        <span class="sp-deal-card__price">$169</span>
                        <span class="sp-deal-card__original">$249</span>
                        <span class="sp-deal-card__savings">Save $80</span>
                    </div>
                    <p class="sp-deal-card__note">Lowest price we've ever tracked! 🎧</p>
                    <a href="https://www.amazon.com/dp/B0D1XD1ZV3?tag=shelzysdesigns-20" class="sp-btn sp-btn--amazon sp-btn--block" target="_blank" rel="nofollow sponsored noopener">
                        Grab This Deal
                    </a>
                </div>
            </div>

            <!-- Regular Deal Cards -->
            <div class="sp-deal-card">
                <div class="sp-deal-card__badge sp-deal-card__badge--hot">54% OFF</div>
                <div class="sp-deal-card__image">
                    <img src="https://m.media-amazon.com/images/I/71Q9d6N7xkL.jpg" alt="Echo Dot" loading="lazy">
                </div>
                <div class="sp-deal-card__content">
                    <span class="sp-deal-card__category">Tech</span>
                    <h3 class="sp-deal-card__title">Echo Dot (5th Gen)</h3>
                    <div class="sp-deal-card__pricing">
                        <span class="sp-deal-card__price">$22.99</span>
                        <span class="sp-deal-card__original">$49.99</span>
                    </div>
                    <a href="https://www.amazon.com/dp/B09B8V1LZ3?tag=shelzysdesigns-20" class="sp-btn sp-btn--amazon sp-btn--sm sp-btn--block" target="_blank" rel="nofollow sponsored noopener">
                        View Deal
                    </a>
                </div>
            </div>

            <div class="sp-deal-card">
                <div class="sp-deal-card__badge sp-deal-card__badge--trending">Trending</div>
                <div class="sp-deal-card__image">
                    <img src="https://m.media-amazon.com/images/I/71YqxOoExhL.jpg" alt="Stanley Tumbler" loading="lazy">
                </div>
                <div class="sp-deal-card__content">
                    <span class="sp-deal-card__category">Home</span>
                    <h3 class="sp-deal-card__title">Stanley Quencher H2.0 40oz</h3>
                    <div class="sp-deal-card__pricing">
                        <span class="sp-deal-card__price">$35</span>
                        <span class="sp-deal-card__original">$45</span>
                    </div>
                    <a href="https://www.amazon.com/dp/B0BX7GW2QM?tag=shelzysdesigns-20" class="sp-btn sp-btn--amazon sp-btn--sm sp-btn--block" target="_blank" rel="nofollow sponsored noopener">
                        View Deal
                    </a>
                </div>
            </div>

            <div class="sp-deal-card">
                <div class="sp-deal-card__badge">50% OFF</div>
                <div class="sp-deal-card__image">
                    <img src="https://m.media-amazon.com/images/I/71h76IDLL0L.jpg" alt="Revlon Hair Dryer" loading="lazy">
                </div>
                <div class="sp-deal-card__content">
                    <span class="sp-deal-card__category">Beauty</span>
                    <h3 class="sp-deal-card__title">Revlon One-Step Hair Dryer</h3>
                    <div class="sp-deal-card__pricing">
                        <span class="sp-deal-card__price">$29.99</span>
                        <span class="sp-deal-card__original">$59.99</span>
                    </div>
                    <a href="https://www.amazon.com/dp/B01LSUQSB0?tag=shelzysdesigns-20" class="sp-btn sp-btn--amazon sp-btn--sm sp-btn--block" target="_blank" rel="nofollow sponsored noopener">
                        View Deal
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Shop By Category - Visual Grid -->
<section class="sp-section sp-categories-v2">
    <div class="sp-container">
        <div class="sp-text-center sp-mb-2xl">
            <h2 class="sp-section__title">Shop By Category</h2>
            <p class="sp-section__subtitle">Find exactly what you're looking for</p>
        </div>

        <div class="sp-category-grid-v2">
            <a href="<?php echo esc_url(home_url('/category/beauty/')); ?>" class="sp-category-card-v2 sp-category-card-v2--beauty">
                <div class="sp-category-card-v2__icon">💄</div>
                <h3 class="sp-category-card-v2__title">Beauty</h3>
                <span class="sp-category-card-v2__count">50+ Products</span>
                <span class="sp-category-card-v2__arrow">→</span>
            </a>

            <a href="<?php echo esc_url(home_url('/category/home/')); ?>" class="sp-category-card-v2 sp-category-card-v2--home">
                <div class="sp-category-card-v2__icon">🏠</div>
                <h3 class="sp-category-card-v2__title">Home</h3>
                <span class="sp-category-card-v2__count">40+ Products</span>
                <span class="sp-category-card-v2__arrow">→</span>
            </a>

            <a href="<?php echo esc_url(home_url('/category/tech/')); ?>" class="sp-category-card-v2 sp-category-card-v2--tech">
                <div class="sp-category-card-v2__icon">📱</div>
                <h3 class="sp-category-card-v2__title">Tech</h3>
                <span class="sp-category-card-v2__count">35+ Products</span>
                <span class="sp-category-card-v2__arrow">→</span>
            </a>

            <a href="<?php echo esc_url(home_url('/category/fashion/')); ?>" class="sp-category-card-v2 sp-category-card-v2--fashion">
                <div class="sp-category-card-v2__icon">👗</div>
                <h3 class="sp-category-card-v2__title">Fashion</h3>
                <span class="sp-category-card-v2__count">45+ Products</span>
                <span class="sp-category-card-v2__arrow">→</span>
            </a>

            <a href="<?php echo esc_url(home_url('/category/kitchen/')); ?>" class="sp-category-card-v2 sp-category-card-v2--kitchen">
                <div class="sp-category-card-v2__icon">🍳</div>
                <h3 class="sp-category-card-v2__title">Kitchen</h3>
                <span class="sp-category-card-v2__count">30+ Products</span>
                <span class="sp-category-card-v2__arrow">→</span>
            </a>

            <a href="<?php echo esc_url(home_url('/gift-guides/')); ?>" class="sp-category-card-v2 sp-category-card-v2--gifts">
                <div class="sp-category-card-v2__icon">🎁</div>
                <h3 class="sp-category-card-v2__title">Gift Guides</h3>
                <span class="sp-category-card-v2__count">20+ Guides</span>
                <span class="sp-category-card-v2__arrow">→</span>
            </a>
        </div>
    </div>
</section>

<!-- Amazon Prime CTA -->
<section class="sp-prime-banner">
    <div class="sp-container">
        <div class="sp-prime-banner__inner">
            <div class="sp-prime-banner__content">
                <span class="sp-prime-banner__icon">⚡</span>
                <div class="sp-prime-banner__text">
                    <h3>Get Free 2-Day Shipping</h3>
                    <p>Plus exclusive deals, Prime Video, and more with Amazon Prime</p>
                </div>
            </div>
            <a href="https://www.amazon.com/amazonprime?tag=shelzysdesigns-20" class="sp-btn sp-btn--prime" target="_blank" rel="nofollow sponsored noopener">
                Try Prime FREE →
            </a>
        </div>
    </div>
</section>

<!-- Latest Posts / Reviews -->
<section class="sp-section">
    <div class="sp-container">
        <div class="sp-section-header-v2">
            <div class="sp-section-header-v2__left">
                <span class="sp-section-tag">Fresh Content</span>
                <h2 class="sp-section__title">Latest Reviews & Finds</h2>
            </div>
            <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="sp-btn sp-btn--secondary">View All Posts</a>
        </div>

        <div class="sp-posts-grid">
            <?php
            $recent_posts = new WP_Query(array(
                'posts_per_page' => 6,
                'post__not_in'   => get_option('sticky_posts'),
                'orderby'        => 'date',
                'order'          => 'DESC',
            ));

            if ($recent_posts->have_posts()):
                $count = 0;
                while ($recent_posts->have_posts()): $recent_posts->the_post();
                    $count++;
                    $featured_class = ($count === 1) ? 'sp-post-card--featured' : '';
            ?>
                <article class="sp-post-card <?php echo $featured_class; ?>">
                    <a href="<?php the_permalink(); ?>" class="sp-post-card__link">
                        <div class="sp-post-card__image">
                            <?php if (has_post_thumbnail()): ?>
                                <?php the_post_thumbnail('sp-card'); ?>
                            <?php else: ?>
                                <div class="sp-post-card__placeholder"></div>
                            <?php endif; ?>
                            <?php
                            $categories = get_the_category();
                            if ($categories): ?>
                                <span class="sp-post-card__category"><?php echo esc_html($categories[0]->name); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="sp-post-card__content">
                            <h3 class="sp-post-card__title"><?php the_title(); ?></h3>
                            <p class="sp-post-card__excerpt"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                            <span class="sp-post-card__read-more">Read More →</span>
                        </div>
                    </a>
                </article>
            <?php
                endwhile;
                wp_reset_postdata();
            else:
                // Placeholder posts
                $placeholder_posts = array(
                    array('title' => 'Best Amazon Beauty Dupes 2024', 'category' => 'Beauty'),
                    array('title' => 'Stanley vs YETI: Which is Better?', 'category' => 'Home'),
                    array('title' => 'Top Tech Gadgets Under $50', 'category' => 'Tech'),
                    array('title' => 'Holiday Gift Guide', 'category' => 'Gift Guides'),
                    array('title' => 'Kitchen Must-Haves', 'category' => 'Kitchen'),
                    array('title' => 'Fashion Finds Under $30', 'category' => 'Fashion'),
                );
                foreach ($placeholder_posts as $i => $post):
                    $featured_class = ($i === 0) ? 'sp-post-card--featured' : '';
            ?>
                <article class="sp-post-card <?php echo $featured_class; ?>">
                    <div class="sp-post-card__link">
                        <div class="sp-post-card__image">
                            <div class="sp-post-card__placeholder"></div>
                            <span class="sp-post-card__category"><?php echo esc_html($post['category']); ?></span>
                        </div>
                        <div class="sp-post-card__content">
                            <h3 class="sp-post-card__title"><?php echo esc_html($post['title']); ?></h3>
                            <p class="sp-post-card__excerpt">Coming soon! Check back for this exciting post.</p>
                            <span class="sp-post-card__read-more">Coming Soon</span>
                        </div>
                    </div>
                </article>
            <?php
                endforeach;
            endif;
            ?>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="sp-section sp-newsletter-v2">
    <div class="sp-container">
        <div class="sp-newsletter-v2__inner">
            <div class="sp-newsletter-v2__content">
                <span class="sp-newsletter-v2__icon">💌</span>
                <h2 class="sp-newsletter-v2__title">Never Miss a Deal</h2>
                <p class="sp-newsletter-v2__text">Join 10,000+ smart shoppers who get exclusive deals delivered to their inbox. No spam, just savings!</p>
                <form class="sp-newsletter-v2__form">
                    <input type="email" placeholder="Enter your email" required>
                    <button type="submit" class="sp-btn sp-btn--primary">Subscribe</button>
                </form>
                <div class="sp-newsletter-v2__perks">
                    <span>✓ Weekly deal roundups</span>
                    <span>✓ Price drop alerts</span>
                    <span>✓ Exclusive discount codes</span>
                </div>
            </div>
            <div class="sp-newsletter-v2__graphic">
                <div class="sp-newsletter-v2__mockup">
                    <div class="sp-mockup-email">
                        <div class="sp-mockup-email__header">
                            <span class="sp-mockup-email__dot"></span>
                            <span class="sp-mockup-email__dot"></span>
                            <span class="sp-mockup-email__dot"></span>
                        </div>
                        <div class="sp-mockup-email__content">
                            <div class="sp-mockup-email__line sp-mockup-email__line--short"></div>
                            <div class="sp-mockup-email__line"></div>
                            <div class="sp-mockup-email__line"></div>
                            <div class="sp-mockup-email__badge">🔥 50% OFF</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="sp-section sp-about-v2">
    <div class="sp-container">
        <div class="sp-about-v2__grid">
            <div class="sp-about-v2__image">
                <div class="sp-about-v2__frame">
                    <div class="sp-about-v2__placeholder">
                        <span>👋</span>
                    </div>
                </div>
                <div class="sp-about-v2__badge">
                    <span class="sp-about-v2__badge-icon">✨</span>
                    <span>Trusted by 10K+ readers</span>
                </div>
            </div>
            <div class="sp-about-v2__content">
                <span class="sp-section-tag">About Me</span>
                <h2>Hi, I'm Shelzy!</h2>
                <p class="sp-about-v2__lead">I'm a deal hunter, bargain finder, and your personal shopping assistant.</p>
                <p>Every product I share has been carefully researched (and often tested!) to make sure it's actually worth your money. I don't just share random affiliate links - I share products I genuinely love and use.</p>
                <ul class="sp-about-v2__list">
                    <li>🔍 I research every product before recommending</li>
                    <li>💰 I track prices to find real deals</li>
                    <li>❌ I never promote products I don't believe in</li>
                    <li>💝 I respond to every reader question</li>
                </ul>
                <a href="<?php echo esc_url(home_url('/about/')); ?>" class="sp-btn sp-btn--primary">Learn More About Me</a>
            </div>
        </div>
    </div>
</section>

<!-- Trust / Social Proof Section -->
<section class="sp-section sp-trust">
    <div class="sp-container">
        <div class="sp-trust__grid">
            <div class="sp-trust-card">
                <div class="sp-trust-card__icon">⭐</div>
                <h4 class="sp-trust-card__title">Honest Reviews</h4>
                <p class="sp-trust-card__text">I only recommend products I've tested or thoroughly researched</p>
            </div>
            <div class="sp-trust-card">
                <div class="sp-trust-card__icon">💰</div>
                <h4 class="sp-trust-card__title">Real Savings</h4>
                <p class="sp-trust-card__text">I track prices to make sure deals are actually good</p>
            </div>
            <div class="sp-trust-card">
                <div class="sp-trust-card__icon">📧</div>
                <h4 class="sp-trust-card__title">No Spam</h4>
                <p class="sp-trust-card__text">Unsubscribe anytime. I respect your inbox</p>
            </div>
            <div class="sp-trust-card">
                <div class="sp-trust-card__icon">💬</div>
                <h4 class="sp-trust-card__title">Here to Help</h4>
                <p class="sp-trust-card__text">Questions? I respond to every message</p>
            </div>
        </div>
    </div>
</section>

<?php
get_footer();
