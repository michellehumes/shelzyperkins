<?php
/**
 * Search Results Template
 *
 * @package ShelzyPerkins
 */

get_header();
?>

<div class="sp-search-page">
    <!-- Search Header -->
    <header class="sp-search-header">
        <div class="sp-container">
            <h1 class="sp-search-header__title">
                <?php
                printf(
                    esc_html__('Search Results for: %s', 'shelzyperkins'),
                    '<span class="sp-search-term">"' . get_search_query() . '"</span>'
                );
                ?>
            </h1>
            <p class="sp-search-header__count">
                <?php
                global $wp_query;
                printf(
                    esc_html(_n('%d result found', '%d results found', $wp_query->found_posts, 'shelzyperkins')),
                    $wp_query->found_posts
                );
                ?>
            </p>

            <!-- Search Form for Refinement -->
            <div class="sp-search-refine">
                <form role="search" method="get" class="sp-search-form sp-search-form--large" action="<?php echo esc_url(home_url('/')); ?>">
                    <input type="search" class="sp-search-form__input" placeholder="<?php esc_attr_e('Refine your search...', 'shelzyperkins'); ?>" value="<?php echo get_search_query(); ?>" name="s">
                    <button type="submit" class="sp-btn sp-btn--primary">Search</button>
                </form>
            </div>
        </div>
    </header>

    <div class="sp-container">
        <?php if (have_posts()): ?>

        <!-- Filter Bar -->
        <div class="sp-search-filters">
            <span class="sp-search-filters__label">Filter by:</span>
            <div class="sp-filter-pills">
                <a href="<?php echo esc_url(add_query_arg('post_type', 'post', home_url('/?s=' . get_search_query()))); ?>" class="sp-filter-pill <?php echo (!isset($_GET['post_type']) || $_GET['post_type'] === 'post') ? 'is-active' : ''; ?>">
                    Posts
                </a>
                <a href="<?php echo esc_url(add_query_arg('post_type', 'page', home_url('/?s=' . get_search_query()))); ?>" class="sp-filter-pill <?php echo (isset($_GET['post_type']) && $_GET['post_type'] === 'page') ? 'is-active' : ''; ?>">
                    Pages
                </a>
            </div>
        </div>

        <!-- Search Results Grid -->
        <div class="sp-search-results">
            <div class="sp-grid sp-grid--3">
                <?php
                while (have_posts()): the_post();
                    get_template_part('template-parts/content', 'card');
                endwhile;
                ?>
            </div>
        </div>

        <!-- Pagination -->
        <nav class="sp-pagination" aria-label="<?php esc_attr_e('Search results pagination', 'shelzyperkins'); ?>">
            <?php
            echo paginate_links(array(
                'prev_text' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg> Previous',
                'next_text' => 'Next <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>',
                'type'      => 'list',
            ));
            ?>
        </nav>

        <?php else: ?>

        <!-- No Results -->
        <div class="sp-no-results">
            <div class="sp-no-results__icon">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="M21 21l-4.35-4.35"></path>
                    <path d="M8 8l6 6M14 8l-6 6"></path>
                </svg>
            </div>
            <h2>No Results Found</h2>
            <p>Sorry, we couldn't find any results for "<strong><?php echo get_search_query(); ?></strong>". Try searching with different keywords or browse our categories below.</p>

            <!-- Search Suggestions -->
            <div class="sp-search-suggestions">
                <h3>Popular Searches</h3>
                <div class="sp-suggestion-pills">
                    <a href="<?php echo esc_url(home_url('/?s=beauty+deals')); ?>" class="sp-suggestion-pill">Beauty Deals</a>
                    <a href="<?php echo esc_url(home_url('/?s=amazon+finds')); ?>" class="sp-suggestion-pill">Amazon Finds</a>
                    <a href="<?php echo esc_url(home_url('/?s=kitchen+gadgets')); ?>" class="sp-suggestion-pill">Kitchen Gadgets</a>
                    <a href="<?php echo esc_url(home_url('/?s=home+decor')); ?>" class="sp-suggestion-pill">Home Decor</a>
                    <a href="<?php echo esc_url(home_url('/?s=tech+under+50')); ?>" class="sp-suggestion-pill">Tech Under $50</a>
                </div>
            </div>

            <!-- Browse Categories -->
            <div class="sp-browse-categories">
                <h3>Browse Categories</h3>
                <div class="sp-category-grid sp-category-grid--compact">
                    <?php
                    $categories = get_categories(array(
                        'orderby'    => 'count',
                        'order'      => 'DESC',
                        'number'     => 6,
                        'hide_empty' => true,
                    ));

                    foreach ($categories as $category):
                    ?>
                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="sp-category-card sp-category-card--sm">
                        <h4 class="sp-category-card__title"><?php echo esc_html($category->name); ?></h4>
                        <span class="sp-category-card__count"><?php echo esc_html($category->count); ?> posts</span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php endif; ?>
    </div>
</div>

<?php
get_footer();
