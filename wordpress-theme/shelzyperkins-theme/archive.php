<?php
/**
 * Archive Template
 *
 * @package ShelzyPerkins
 */

get_header();
?>

<div class="sp-archive">
    <!-- Archive Header -->
    <header class="sp-archive-header">
        <div class="sp-container">
            <!-- Breadcrumbs -->
            <?php sp_breadcrumbs(); ?>

            <?php
            $archive_title = '';
            $archive_description = '';

            if (is_category()) {
                $archive_title = single_cat_title('', false);
                $archive_description = category_description();
                $icon = sp_get_category_icon(get_queried_object_id());
            } elseif (is_tag()) {
                $archive_title = single_tag_title('', false);
                $archive_description = tag_description();
            } elseif (is_author()) {
                $archive_title = get_the_author();
                $archive_description = get_the_author_meta('description');
            } elseif (is_date()) {
                if (is_day()) {
                    $archive_title = get_the_date();
                } elseif (is_month()) {
                    $archive_title = get_the_date('F Y');
                } elseif (is_year()) {
                    $archive_title = get_the_date('Y');
                }
            } else {
                $archive_title = 'Archives';
            }
            ?>

            <h1 class="sp-archive-header__title">
                <?php if (isset($icon) && $icon): ?>
                <span class="sp-archive-header__icon"><?php echo $icon; ?></span>
                <?php endif; ?>
                <?php echo esc_html($archive_title); ?>
            </h1>

            <?php if ($archive_description): ?>
            <p class="sp-archive-header__description"><?php echo wp_kses_post($archive_description); ?></p>
            <?php endif; ?>

            <p class="sp-archive-header__count">
                <?php
                global $wp_query;
                printf(
                    esc_html(_n('%d post', '%d posts', $wp_query->found_posts, 'shelzyperkins')),
                    $wp_query->found_posts
                );
                ?>
            </p>
        </div>
    </header>

    <div class="sp-container">
        <?php if (is_category()): ?>
        <!-- Subcategories -->
        <?php
        $subcategories = get_categories(array(
            'parent'     => get_queried_object_id(),
            'hide_empty' => true,
        ));

        if ($subcategories):
        ?>
        <div class="sp-subcategories">
            <h3>Subcategories</h3>
            <div class="sp-subcategory-pills">
                <?php foreach ($subcategories as $subcat): ?>
                <a href="<?php echo esc_url(get_category_link($subcat->term_id)); ?>" class="sp-subcategory-pill">
                    <?php echo esc_html($subcat->name); ?>
                    <span class="sp-subcategory-pill__count">(<?php echo esc_html($subcat->count); ?>)</span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        <?php if (have_posts()): ?>

        <!-- Sort/Filter Bar -->
        <div class="sp-archive-toolbar">
            <div class="sp-archive-sort">
                <label for="sort-select">Sort by:</label>
                <select id="sort-select" class="sp-select" onchange="window.location.href=this.value">
                    <option value="<?php echo esc_url(add_query_arg('orderby', 'date')); ?>" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : 'date', 'date'); ?>>Latest</option>
                    <option value="<?php echo esc_url(add_query_arg('orderby', 'title')); ?>" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'title'); ?>>Title A-Z</option>
                    <option value="<?php echo esc_url(add_query_arg('orderby', 'comment_count')); ?>" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'comment_count'); ?>>Most Popular</option>
                </select>
            </div>

            <div class="sp-archive-view">
                <button class="sp-view-btn is-active" data-view="grid" aria-label="Grid view">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                    </svg>
                </button>
                <button class="sp-view-btn" data-view="list" aria-label="List view">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <rect x="3" y="4" width="18" height="4"></rect>
                        <rect x="3" y="10" width="18" height="4"></rect>
                        <rect x="3" y="16" width="18" height="4"></rect>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Posts Grid -->
        <div class="sp-archive-posts sp-grid sp-grid--3" id="posts-container">
            <?php
            while (have_posts()): the_post();
                get_template_part('template-parts/content', 'card');
            endwhile;
            ?>
        </div>

        <!-- Load More / Pagination -->
        <nav class="sp-pagination" aria-label="<?php esc_attr_e('Posts pagination', 'shelzyperkins'); ?>">
            <?php
            echo paginate_links(array(
                'prev_text' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg> Previous',
                'next_text' => 'Next <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>',
                'type'      => 'list',
            ));
            ?>
        </nav>

        <?php else: ?>

        <div class="sp-no-posts">
            <h2>No posts found</h2>
            <p>There are no posts in this category yet. Check back soon!</p>
            <a href="<?php echo esc_url(home_url('/')); ?>" class="sp-btn sp-btn--primary">Go to Homepage</a>
        </div>

        <?php endif; ?>
    </div>
</div>

<!-- Email Signup -->
<section class="sp-section">
    <div class="sp-container sp-container--narrow">
        <?php echo do_shortcode('[email_signup]'); ?>
    </div>
</section>

<?php
get_footer();
