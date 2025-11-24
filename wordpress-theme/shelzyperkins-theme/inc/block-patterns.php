<?php
/**
 * Block Patterns for ShelzyPerkins Theme
 *
 * @package ShelzyPerkins
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register block patterns
 */
function sp_register_block_patterns() {
    // Product Card Pattern
    register_block_pattern(
        'shelzyperkins/product-card',
        array(
            'title'       => __('Product Card', 'shelzyperkins'),
            'description' => __('A card displaying a single Amazon product with affiliate link.', 'shelzyperkins'),
            'categories'  => array('shelzyperkins'),
            'content'     => '<!-- wp:shortcode -->[product_card asin="ASIN_HERE" title="Product Title" price="29.99" original_price="49.99" image="https://example.com/image.jpg" badge="Best Seller"]<!-- /wp:shortcode -->',
        )
    );

    // Product Comparison Table Pattern
    register_block_pattern(
        'shelzyperkins/comparison-table',
        array(
            'title'       => __('Product Comparison Table', 'shelzyperkins'),
            'description' => __('A table comparing multiple Amazon products.', 'shelzyperkins'),
            'categories'  => array('shelzyperkins'),
            'content'     => '<!-- wp:shortcode -->[comparison_table]
[product_row asin="ASIN1" title="Product 1" price="29.99" image="URL" note="Best Budget Option"]
[product_row asin="ASIN2" title="Product 2" price="49.99" image="URL" note="Best Overall"]
[product_row asin="ASIN3" title="Product 3" price="79.99" image="URL" note="Premium Choice"]
[/comparison_table]<!-- /wp:shortcode -->',
        )
    );

    // Deal Alert Box Pattern
    register_block_pattern(
        'shelzyperkins/deal-alert',
        array(
            'title'       => __('Deal Alert Box', 'shelzyperkins'),
            'description' => __('A highlighted box for time-sensitive deals.', 'shelzyperkins'),
            'categories'  => array('shelzyperkins'),
            'content'     => '<!-- wp:group {"className":"sp-deal-alert"} -->
<div class="wp-block-group sp-deal-alert">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Limited Time Deal!</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>This deal expires soon! Click below to grab it before it\'s gone.</p>
<!-- /wp:paragraph -->
<!-- wp:shortcode -->[amazon_link asin="ASIN_HERE" text="Grab This Deal" class="sp-btn sp-btn--amazon sp-btn--lg"]<!-- /wp:shortcode -->
</div>
<!-- /wp:group -->',
        )
    );

    // Email Signup Pattern
    register_block_pattern(
        'shelzyperkins/email-signup',
        array(
            'title'       => __('Email Signup Box', 'shelzyperkins'),
            'description' => __('Newsletter signup form.', 'shelzyperkins'),
            'categories'  => array('shelzyperkins'),
            'content'     => '<!-- wp:shortcode -->[email_signup title="Never Miss a Deal!" description="Join thousands of smart shoppers who get exclusive deals in their inbox." button_text="Subscribe"]<!-- /wp:shortcode -->',
        )
    );

    // Affiliate Disclosure Pattern
    register_block_pattern(
        'shelzyperkins/affiliate-disclosure',
        array(
            'title'       => __('Affiliate Disclosure', 'shelzyperkins'),
            'description' => __('Required affiliate disclosure notice.', 'shelzyperkins'),
            'categories'  => array('shelzyperkins'),
            'content'     => '<!-- wp:shortcode -->[affiliate_disclosure]<!-- /wp:shortcode -->',
        )
    );

    // Hero Section Pattern
    register_block_pattern(
        'shelzyperkins/hero-section',
        array(
            'title'       => __('Hero Section', 'shelzyperkins'),
            'description' => __('A full-width hero banner with gradient background.', 'shelzyperkins'),
            'categories'  => array('shelzyperkins'),
            'content'     => '<!-- wp:cover {"customGradient":"linear-gradient(135deg,rgb(255,107,107) 0%,rgb(255,230,109) 100%)","isDark":false,"align":"full"} -->
<div class="wp-block-cover alignfull is-light"><span aria-hidden="true" class="wp-block-cover__background has-background-gradient" style="background:linear-gradient(135deg,rgb(255,107,107) 0%,rgb(255,230,109) 100%)"></span><div class="wp-block-cover__inner-container">
<!-- wp:heading {"textAlign":"center","level":1,"style":{"color":{"text":"#ffffff"}}} -->
<h1 class="wp-block-heading has-text-align-center has-text-color" style="color:#ffffff">Your Headline Here</h1>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"color":{"text":"#ffffff"}}} -->
<p class="has-text-align-center has-text-color" style="color:#ffffff">Your subtitle or description goes here.</p>
<!-- /wp:paragraph -->
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons">
<!-- wp:button {"className":"sp-btn sp-btn--primary"} -->
<div class="wp-block-button sp-btn sp-btn--primary"><a class="wp-block-button__link wp-element-button">Shop Now</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div></div>
<!-- /wp:cover -->',
        )
    );

    // Category Grid Pattern
    register_block_pattern(
        'shelzyperkins/category-grid',
        array(
            'title'       => __('Category Grid', 'shelzyperkins'),
            'description' => __('A grid of category cards.', 'shelzyperkins'),
            'categories'  => array('shelzyperkins'),
            'content'     => '<!-- wp:group {"className":"sp-category-section"} -->
<div class="wp-block-group sp-category-section">
<!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">Shop By Category</h2>
<!-- /wp:heading -->
<!-- wp:columns -->
<div class="wp-block-columns">
<!-- wp:column -->
<div class="wp-block-column">
<a href="/category/beauty/" class="sp-category-card">
<span class="sp-category-card__icon">💄</span>
<span class="sp-category-card__title">Beauty</span>
</a>
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<a href="/category/home/" class="sp-category-card">
<span class="sp-category-card__icon">🏠</span>
<span class="sp-category-card__title">Home</span>
</a>
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<a href="/category/tech/" class="sp-category-card">
<span class="sp-category-card__icon">💻</span>
<span class="sp-category-card__title">Tech</span>
</a>
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->',
        )
    );

    // Best Of List Pattern
    register_block_pattern(
        'shelzyperkins/best-of-list',
        array(
            'title'       => __('Best Of List', 'shelzyperkins'),
            'description' => __('Numbered list of best products.', 'shelzyperkins'),
            'categories'  => array('shelzyperkins'),
            'content'     => '<!-- wp:group {"className":"sp-best-of-list"} -->
<div class="wp-block-group sp-best-of-list">
<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">1. Product Name - Best Overall</h2>
<!-- /wp:heading -->
<!-- wp:image -->
<figure class="wp-block-image"><img src="product-image.jpg" alt="Product Name"/></figure>
<!-- /wp:image -->
<!-- wp:paragraph -->
<p>Product description and review goes here. Explain why this is the best choice and who it\'s perfect for.</p>
<!-- /wp:paragraph -->
<!-- wp:list -->
<ul>
<li><strong>Pros:</strong> Great quality, affordable price</li>
<li><strong>Cons:</strong> Minor limitation</li>
</ul>
<!-- /wp:list -->
<!-- wp:shortcode -->[amazon_link asin="ASIN_HERE" text="Check Price on Amazon"]<!-- /wp:shortcode -->
</div>
<!-- /wp:group -->',
        )
    );

    // Price Drop Alert Pattern
    register_block_pattern(
        'shelzyperkins/price-drop',
        array(
            'title'       => __('Price Drop Alert', 'shelzyperkins'),
            'description' => __('Highlight a price drop or sale.', 'shelzyperkins'),
            'categories'  => array('shelzyperkins'),
            'content'     => '<!-- wp:group {"className":"sp-price-drop"} -->
<div class="wp-block-group sp-price-drop">
<!-- wp:shortcode -->[deal_badge text="PRICE DROP!" type="hot"]<!-- /wp:shortcode -->
<!-- wp:paragraph -->
<p><strong>Was $99.99 → Now $49.99</strong> (50% OFF!)</p>
<!-- /wp:paragraph -->
<!-- wp:shortcode -->[amazon_link asin="ASIN_HERE" text="Shop Now"]<!-- /wp:shortcode -->
</div>
<!-- /wp:group -->',
        )
    );
}
add_action('init', 'sp_register_block_patterns');
