<?php
/**
 * Template Name: Deal Browser
 * Description: Browse and filter all deals with advanced filters
 *
 * @package ShelzyPerkins
 */

get_header();

// Get filter parameters
$category_filter = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
$price_min = isset($_GET['price_min']) ? floatval($_GET['price_min']) : 0;
$price_max = isset($_GET['price_max']) ? floatval($_GET['price_max']) : 0;
$discount_min = isset($_GET['discount']) ? intval($_GET['discount']) : 0;
$sort = isset($_GET['sort']) ? sanitize_text_field($_GET['sort']) : 'newest';

// Build query args
$query_args = array(
    'post_type' => 'post',
    'posts_per_page' => 24,
    'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
);

// Category filter
if (!empty($category_filter)) {
    $query_args['category_name'] = $category_filter;
}

// Sort options
switch ($sort) {
    case 'price_low':
        $query_args['meta_key'] = '_sp_price';
        $query_args['orderby'] = 'meta_value_num';
        $query_args['order'] = 'ASC';
        break;
    case 'price_high':
        $query_args['meta_key'] = '_sp_price';
        $query_args['orderby'] = 'meta_value_num';
        $query_args['order'] = 'DESC';
        break;
    case 'discount':
        $query_args['meta_key'] = '_sp_discount';
        $query_args['orderby'] = 'meta_value_num';
        $query_args['order'] = 'DESC';
        break;
    case 'popular':
        $query_args['meta_key'] = '_sp_post_views';
        $query_args['orderby'] = 'meta_value_num';
        $query_args['order'] = 'DESC';
        break;
    default:
        $query_args['orderby'] = 'date';
        $query_args['order'] = 'DESC';
}

$deals_query = new WP_Query($query_args);
$categories = get_categories(array('hide_empty' => true));
?>

<div class="sp-deal-browser">
    <!-- Page Header -->
    <header class="sp-page__header">
        <div class="sp-container">
            <h1 class="sp-page__title">Browse All Deals</h1>
            <p class="sp-page__subtitle">Find the perfect deal with our advanced filters</p>
        </div>
    </header>

    <div class="sp-container">
        <div class="sp-deal-browser__layout">
            <!-- Filters Sidebar -->
            <aside class="sp-deal-browser__filters">
                <form class="sp-filters" method="GET" id="deal-filters">
                    <div class="sp-filters__header">
                        <h3>Filters</h3>
                        <a href="<?php echo esc_url(get_permalink()); ?>" class="sp-filters__clear">Clear All</a>
                    </div>

                    <!-- Category Filter -->
                    <div class="sp-filter-group">
                        <h4 class="sp-filter-group__title">Category</h4>
                        <div class="sp-filter-group__options">
                            <label class="sp-filter-option">
                                <input type="radio" name="category" value="" <?php checked($category_filter, ''); ?>>
                                <span>All Categories</span>
                            </label>
                            <?php foreach ($categories as $cat) : ?>
                            <label class="sp-filter-option">
                                <input type="radio" name="category" value="<?php echo esc_attr($cat->slug); ?>" <?php checked($category_filter, $cat->slug); ?>>
                                <span><?php echo esc_html($cat->name); ?></span>
                                <span class="sp-filter-option__count">(<?php echo $cat->count; ?>)</span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Price Range Filter -->
                    <div class="sp-filter-group">
                        <h4 class="sp-filter-group__title">Price Range</h4>
                        <div class="sp-filter-group__price-inputs">
                            <div class="sp-price-input">
                                <span>$</span>
                                <input type="number" name="price_min" placeholder="Min" value="<?php echo $price_min > 0 ? esc_attr($price_min) : ''; ?>" min="0">
                            </div>
                            <span class="sp-price-input__separator">to</span>
                            <div class="sp-price-input">
                                <span>$</span>
                                <input type="number" name="price_max" placeholder="Max" value="<?php echo $price_max > 0 ? esc_attr($price_max) : ''; ?>" min="0">
                            </div>
                        </div>
                    </div>

                    <!-- Discount Filter -->
                    <div class="sp-filter-group">
                        <h4 class="sp-filter-group__title">Minimum Discount</h4>
                        <div class="sp-filter-group__options">
                            <label class="sp-filter-option">
                                <input type="radio" name="discount" value="0" <?php checked($discount_min, 0); ?>>
                                <span>Any Discount</span>
                            </label>
                            <label class="sp-filter-option">
                                <input type="radio" name="discount" value="10" <?php checked($discount_min, 10); ?>>
                                <span>10% or more</span>
                            </label>
                            <label class="sp-filter-option">
                                <input type="radio" name="discount" value="25" <?php checked($discount_min, 25); ?>>
                                <span>25% or more</span>
                            </label>
                            <label class="sp-filter-option">
                                <input type="radio" name="discount" value="50" <?php checked($discount_min, 50); ?>>
                                <span>50% or more</span>
                            </label>
                            <label class="sp-filter-option">
                                <input type="radio" name="discount" value="75" <?php checked($discount_min, 75); ?>>
                                <span>75% or more</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="sp-btn sp-btn--primary sp-filters__apply">Apply Filters</button>
                </form>
            </aside>

            <!-- Results -->
            <main class="sp-deal-browser__results">
                <!-- Results Header -->
                <div class="sp-results-header">
                    <div class="sp-results-header__count">
                        <strong><?php echo $deals_query->found_posts; ?></strong> deals found
                    </div>
                    <div class="sp-results-header__sort">
                        <label for="sort">Sort by:</label>
                        <select name="sort" id="sort" onchange="this.form.submit()" form="deal-filters">
                            <option value="newest" <?php selected($sort, 'newest'); ?>>Newest</option>
                            <option value="popular" <?php selected($sort, 'popular'); ?>>Most Popular</option>
                            <option value="price_low" <?php selected($sort, 'price_low'); ?>>Price: Low to High</option>
                            <option value="price_high" <?php selected($sort, 'price_high'); ?>>Price: High to Low</option>
                            <option value="discount" <?php selected($sort, 'discount'); ?>>Biggest Discount</option>
                        </select>
                    </div>
                    <div class="sp-results-header__view">
                        <button class="sp-view-toggle sp-view-toggle--grid is-active" data-view="grid" aria-label="Grid view">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                        </button>
                        <button class="sp-view-toggle sp-view-toggle--list" data-view="list" aria-label="List view">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                <line x1="8" y1="12" x2="21" y2="12"></line>
                                <line x1="8" y1="18" x2="21" y2="18"></line>
                                <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                <line x1="3" y1="18" x2="3.01" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Active Filters -->
                <?php if (!empty($category_filter) || $price_min > 0 || $price_max > 0 || $discount_min > 0) : ?>
                <div class="sp-active-filters">
                    <span>Active filters:</span>
                    <?php if (!empty($category_filter)) : ?>
                    <a href="<?php echo esc_url(remove_query_arg('category')); ?>" class="sp-active-filter">
                        Category: <?php echo esc_html($category_filter); ?> &times;
                    </a>
                    <?php endif; ?>
                    <?php if ($price_min > 0 || $price_max > 0) : ?>
                    <a href="<?php echo esc_url(remove_query_arg(array('price_min', 'price_max'))); ?>" class="sp-active-filter">
                        Price: $<?php echo $price_min; ?> - $<?php echo $price_max > 0 ? $price_max : '∞'; ?> &times;
                    </a>
                    <?php endif; ?>
                    <?php if ($discount_min > 0) : ?>
                    <a href="<?php echo esc_url(remove_query_arg('discount')); ?>" class="sp-active-filter">
                        <?php echo $discount_min; ?>%+ off &times;
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Deals Grid -->
                <div class="sp-deals-grid sp-grid sp-grid--3" id="deals-grid">
                    <?php if ($deals_query->have_posts()) : ?>
                        <?php while ($deals_query->have_posts()) : $deals_query->the_post(); ?>
                            <?php get_template_part('template-parts/content', 'card'); ?>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <div class="sp-no-results">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                <line x1="8" y1="11" x2="14" y2="11"></line>
                            </svg>
                            <h3>No deals found</h3>
                            <p>Try adjusting your filters or browse all deals.</p>
                            <a href="<?php echo esc_url(get_permalink()); ?>" class="sp-btn sp-btn--primary">Clear Filters</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($deals_query->max_num_pages > 1) : ?>
                <nav class="sp-pagination">
                    <?php
                    echo paginate_links(array(
                        'total' => $deals_query->max_num_pages,
                        'current' => max(1, get_query_var('paged')),
                        'prev_text' => '&larr; Previous',
                        'next_text' => 'Next &rarr;',
                        'type' => 'list',
                    ));
                    ?>
                </nav>
                <?php endif; ?>

                <?php wp_reset_postdata(); ?>
            </main>
        </div>
    </div>
</div>

<!-- Mobile Filter Toggle -->
<button class="sp-mobile-filter-toggle" aria-label="Toggle filters">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="4" y1="21" x2="4" y2="14"></line>
        <line x1="4" y1="10" x2="4" y2="3"></line>
        <line x1="12" y1="21" x2="12" y2="12"></line>
        <line x1="12" y1="8" x2="12" y2="3"></line>
        <line x1="20" y1="21" x2="20" y2="16"></line>
        <line x1="20" y1="12" x2="20" y2="3"></line>
        <line x1="1" y1="14" x2="7" y2="14"></line>
        <line x1="9" y1="8" x2="15" y2="8"></line>
        <line x1="17" y1="16" x2="23" y2="16"></line>
    </svg>
    Filters
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // View toggle
    const viewToggles = document.querySelectorAll('.sp-view-toggle');
    const dealsGrid = document.getElementById('deals-grid');

    viewToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            viewToggles.forEach(t => t.classList.remove('is-active'));
            this.classList.add('is-active');
            dealsGrid.classList.toggle('sp-deals-grid--list', this.dataset.view === 'list');
        });
    });

    // Mobile filter toggle
    const mobileFilterToggle = document.querySelector('.sp-mobile-filter-toggle');
    const filtersPanel = document.querySelector('.sp-deal-browser__filters');

    if (mobileFilterToggle && filtersPanel) {
        mobileFilterToggle.addEventListener('click', function() {
            filtersPanel.classList.toggle('is-visible');
            document.body.classList.toggle('filters-open');
        });
    }
});
</script>

<style>
/* Deal Browser Specific Styles */
.sp-deal-browser__layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: var(--sp-spacing-2xl);
    padding: var(--sp-spacing-2xl) 0;
}

@media (max-width: 992px) {
    .sp-deal-browser__layout {
        grid-template-columns: 1fr;
    }

    .sp-deal-browser__filters {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        width: 300px;
        background: var(--sp-white);
        z-index: 1001;
        transform: translateX(-100%);
        transition: transform var(--sp-transition-base);
        overflow-y: auto;
        padding: var(--sp-spacing-lg);
    }

    .sp-deal-browser__filters.is-visible {
        transform: translateX(0);
        box-shadow: var(--sp-shadow-lg);
    }

    body.filters-open::after {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
    }
}

.sp-filters {
    background: var(--sp-white);
    border-radius: var(--sp-radius-lg);
    padding: var(--sp-spacing-lg);
    box-shadow: var(--sp-shadow-sm);
    position: sticky;
    top: 100px;
}

.sp-filters__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--sp-spacing-lg);
    padding-bottom: var(--sp-spacing-md);
    border-bottom: 1px solid var(--sp-gray-lighter);
}

.sp-filters__header h3 {
    margin: 0;
}

.sp-filters__clear {
    font-size: var(--sp-font-size-sm);
    color: var(--sp-coral);
}

.sp-filter-group {
    margin-bottom: var(--sp-spacing-lg);
}

.sp-filter-group__title {
    font-size: var(--sp-font-size-sm);
    font-weight: 600;
    margin-bottom: var(--sp-spacing-sm);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--sp-gray);
}

.sp-filter-group__options {
    display: flex;
    flex-direction: column;
    gap: var(--sp-spacing-xs);
}

.sp-filter-option {
    display: flex;
    align-items: center;
    gap: var(--sp-spacing-sm);
    padding: var(--sp-spacing-sm);
    border-radius: var(--sp-radius-sm);
    cursor: pointer;
    transition: background var(--sp-transition-fast);
}

.sp-filter-option:hover {
    background: var(--sp-off-white);
}

.sp-filter-option input {
    accent-color: var(--sp-coral);
}

.sp-filter-option__count {
    margin-left: auto;
    font-size: var(--sp-font-size-xs);
    color: var(--sp-gray-light);
}

.sp-filter-group__price-inputs {
    display: flex;
    align-items: center;
    gap: var(--sp-spacing-sm);
}

.sp-price-input {
    flex: 1;
    display: flex;
    align-items: center;
    border: 1px solid var(--sp-gray-lighter);
    border-radius: var(--sp-radius-sm);
    padding: 0 var(--sp-spacing-sm);
}

.sp-price-input span {
    color: var(--sp-gray);
}

.sp-price-input input {
    border: none;
    padding: var(--sp-spacing-sm);
    width: 100%;
    font-family: var(--sp-font-primary);
}

.sp-price-input input:focus {
    outline: none;
}

.sp-price-input__separator {
    color: var(--sp-gray-light);
}

.sp-filters__apply {
    width: 100%;
    margin-top: var(--sp-spacing-md);
}

/* Results Header */
.sp-results-header {
    display: flex;
    align-items: center;
    gap: var(--sp-spacing-lg);
    margin-bottom: var(--sp-spacing-lg);
    padding-bottom: var(--sp-spacing-md);
    border-bottom: 1px solid var(--sp-gray-lighter);
    flex-wrap: wrap;
}

.sp-results-header__count {
    font-size: var(--sp-font-size-sm);
    color: var(--sp-gray);
}

.sp-results-header__sort {
    display: flex;
    align-items: center;
    gap: var(--sp-spacing-sm);
    margin-left: auto;
}

.sp-results-header__sort label {
    font-size: var(--sp-font-size-sm);
    color: var(--sp-gray);
}

.sp-results-header__sort select {
    padding: var(--sp-spacing-sm) var(--sp-spacing-md);
    border: 1px solid var(--sp-gray-lighter);
    border-radius: var(--sp-radius-sm);
    font-family: var(--sp-font-primary);
    cursor: pointer;
}

.sp-results-header__view {
    display: flex;
    gap: var(--sp-spacing-xs);
}

.sp-view-toggle {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--sp-off-white);
    border: 1px solid var(--sp-gray-lighter);
    border-radius: var(--sp-radius-sm);
    cursor: pointer;
    transition: all var(--sp-transition-fast);
}

.sp-view-toggle:hover,
.sp-view-toggle.is-active {
    background: var(--sp-coral);
    color: var(--sp-white);
    border-color: var(--sp-coral);
}

/* Active Filters */
.sp-active-filters {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: var(--sp-spacing-sm);
    margin-bottom: var(--sp-spacing-lg);
    font-size: var(--sp-font-size-sm);
}

.sp-active-filter {
    display: inline-flex;
    align-items: center;
    gap: var(--sp-spacing-xs);
    padding: 4px 10px;
    background: var(--sp-off-white);
    border-radius: var(--sp-radius-full);
    color: var(--sp-charcoal);
    text-decoration: none;
    transition: all var(--sp-transition-fast);
}

.sp-active-filter:hover {
    background: var(--sp-coral);
    color: var(--sp-white);
}

/* No Results */
.sp-no-results {
    grid-column: 1 / -1;
    text-align: center;
    padding: var(--sp-spacing-3xl);
    background: var(--sp-white);
    border-radius: var(--sp-radius-lg);
}

.sp-no-results svg {
    color: var(--sp-gray-light);
    margin-bottom: var(--sp-spacing-md);
}

.sp-no-results h3 {
    margin-bottom: var(--sp-spacing-sm);
}

.sp-no-results p {
    color: var(--sp-gray);
    margin-bottom: var(--sp-spacing-lg);
}

/* List View */
.sp-deals-grid--list {
    display: flex;
    flex-direction: column;
    gap: var(--sp-spacing-md);
}

.sp-deals-grid--list .sp-card {
    display: grid;
    grid-template-columns: 150px 1fr auto;
    gap: var(--sp-spacing-lg);
}

.sp-deals-grid--list .sp-card__image {
    aspect-ratio: 1;
}

/* Mobile Filter Toggle */
.sp-mobile-filter-toggle {
    display: none;
    position: fixed;
    bottom: var(--sp-spacing-lg);
    left: 50%;
    transform: translateX(-50%);
    padding: var(--sp-spacing-md) var(--sp-spacing-xl);
    background: var(--sp-gradient-brand);
    color: var(--sp-white);
    border: none;
    border-radius: var(--sp-radius-full);
    font-weight: 600;
    cursor: pointer;
    z-index: 999;
    box-shadow: var(--sp-shadow-brand);
    gap: var(--sp-spacing-sm);
    align-items: center;
}

@media (max-width: 992px) {
    .sp-mobile-filter-toggle {
        display: flex;
    }
}
</style>

<?php
get_footer();
