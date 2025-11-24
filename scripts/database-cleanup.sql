/**
 * WordPress Database Cleanup Script
 * Run via phpMyAdmin or wp-cli
 *
 * BACKUP YOUR DATABASE FIRST!
 *
 * Run these queries one at a time and review results
 */

-- =============================================================================
-- 1. CHECK DATABASE SIZE BEFORE CLEANUP
-- =============================================================================

SELECT
    table_name AS `Table`,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS `Size (MB)`
FROM information_schema.TABLES
WHERE table_schema = DATABASE()
ORDER BY (data_length + index_length) DESC;


-- =============================================================================
-- 2. DELETE OLD POST REVISIONS (Keep last 5)
-- =============================================================================

-- First, see how many revisions exist
SELECT COUNT(*) AS 'Revisions'
FROM wp_posts
WHERE post_type = 'revision';

-- Delete old revisions (older than 90 days, keep last 5 per post)
DELETE t1
FROM wp_posts t1
INNER JOIN (
    SELECT ID
    FROM (
        SELECT ID,
               ROW_NUMBER() OVER (PARTITION BY post_parent ORDER BY post_modified DESC) AS row_num
        FROM wp_posts
        WHERE post_type = 'revision'
          AND post_modified < DATE_SUB(NOW(), INTERVAL 90 DAY)
    ) AS ranked
    WHERE row_num > 5
) t2 ON t1.ID = t2.ID;


-- =============================================================================
-- 3. DELETE AUTO-DRAFTS
-- =============================================================================

-- Check count
SELECT COUNT(*) AS 'Auto-Drafts'
FROM wp_posts
WHERE post_status = 'auto-draft';

-- Delete auto-drafts
DELETE FROM wp_posts
WHERE post_status = 'auto-draft';


-- =============================================================================
-- 4. DELETE TRASHED POSTS
-- =============================================================================

-- Check count
SELECT COUNT(*) AS 'Trashed Posts'
FROM wp_posts
WHERE post_status = 'trash';

-- Delete trashed posts (make sure you don't need them first!)
DELETE FROM wp_posts
WHERE post_status = 'trash';


-- =============================================================================
-- 5. DELETE ORPHANED POST META
-- =============================================================================

-- Check count
SELECT COUNT(*) AS 'Orphaned Post Meta'
FROM wp_postmeta pm
LEFT JOIN wp_posts wp ON wp.ID = pm.post_id
WHERE wp.ID IS NULL;

-- Delete orphaned post meta
DELETE pm
FROM wp_postmeta pm
LEFT JOIN wp_posts wp ON wp.ID = pm.post_id
WHERE wp.ID IS NULL;


-- =============================================================================
-- 6. DELETE ORPHANED COMMENT META
-- =============================================================================

-- Check count
SELECT COUNT(*) AS 'Orphaned Comment Meta'
FROM wp_commentmeta
WHERE comment_id NOT IN (SELECT comment_id FROM wp_comments);

-- Delete orphaned comment meta
DELETE FROM wp_commentmeta
WHERE comment_id NOT IN (SELECT comment_id FROM wp_comments);


-- =============================================================================
-- 7. DELETE SPAM AND TRASHED COMMENTS
-- =============================================================================

-- Check spam comments
SELECT COUNT(*) AS 'Spam Comments'
FROM wp_comments
WHERE comment_approved = 'spam';

-- Delete spam comments
DELETE FROM wp_comments
WHERE comment_approved = 'spam';

-- Delete trashed comments
DELETE FROM wp_comments
WHERE comment_approved = 'trash';


-- =============================================================================
-- 8. DELETE ORPHANED RELATIONSHIPS
-- =============================================================================

-- Check orphaned term relationships
SELECT COUNT(*) AS 'Orphaned Term Relationships'
FROM wp_term_relationships tr
LEFT JOIN wp_posts p ON p.ID = tr.object_id
WHERE p.ID IS NULL;

-- Delete orphaned term relationships
DELETE tr
FROM wp_term_relationships tr
LEFT JOIN wp_posts p ON p.ID = tr.object_id
WHERE p.ID IS NULL;


-- =============================================================================
-- 9. DELETE UNUSED TERMS
-- =============================================================================

-- Find unused terms
SELECT t.term_id, t.name, tt.count
FROM wp_terms t
INNER JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
WHERE tt.count = 0
  AND tt.taxonomy NOT IN ('nav_menu', 'link_category');

-- Delete unused terms (be careful with this one)
-- DELETE t, tt
-- FROM wp_terms t
-- INNER JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
-- WHERE tt.count = 0
--   AND tt.taxonomy NOT IN ('nav_menu', 'link_category');


-- =============================================================================
-- 10. DELETE EXPIRED TRANSIENTS
-- =============================================================================

-- Check expired transients
SELECT COUNT(*) AS 'Expired Transients'
FROM wp_options
WHERE option_name LIKE '_transient_timeout_%'
  AND option_value < UNIX_TIMESTAMP();

-- Delete expired transients
DELETE FROM wp_options
WHERE option_name LIKE '_transient_timeout_%'
  AND option_value < UNIX_TIMESTAMP();

-- Delete associated transient values
DELETE FROM wp_options
WHERE option_name LIKE '_transient_%'
  AND option_name NOT LIKE '_transient_timeout_%'
  AND option_name NOT IN (
    SELECT REPLACE(option_name, '_timeout', '')
    FROM wp_options
    WHERE option_name LIKE '_transient_timeout_%'
  );


-- =============================================================================
-- 11. DELETE ORPHANED USER META
-- =============================================================================

-- Check orphaned user meta
SELECT COUNT(*) AS 'Orphaned User Meta'
FROM wp_usermeta um
LEFT JOIN wp_users u ON u.ID = um.user_id
WHERE u.ID IS NULL;

-- Delete orphaned user meta
DELETE um
FROM wp_usermeta um
LEFT JOIN wp_users u ON u.ID = um.user_id
WHERE u.ID IS NULL;


-- =============================================================================
-- 12. CLEAN UP WooCommerce TABLES (IF REMOVING WooCommerce)
-- =============================================================================

-- WARNING: Only run if you're removing WooCommerce completely!

-- DROP TABLE IF EXISTS wp_woocommerce_sessions;
-- DROP TABLE IF EXISTS wp_woocommerce_api_keys;
-- DROP TABLE IF EXISTS wp_woocommerce_attribute_taxonomies;
-- DROP TABLE IF EXISTS wp_woocommerce_downloadable_product_permissions;
-- DROP TABLE IF EXISTS wp_woocommerce_order_items;
-- DROP TABLE IF EXISTS wp_woocommerce_order_itemmeta;
-- DROP TABLE IF EXISTS wp_woocommerce_tax_rates;
-- DROP TABLE IF EXISTS wp_woocommerce_tax_rate_locations;
-- DROP TABLE IF EXISTS wp_woocommerce_shipping_zones;
-- DROP TABLE IF EXISTS wp_woocommerce_shipping_zone_locations;
-- DROP TABLE IF EXISTS wp_woocommerce_shipping_zone_methods;
-- DROP TABLE IF EXISTS wp_woocommerce_payment_tokens;
-- DROP TABLE IF EXISTS wp_woocommerce_payment_tokenmeta;
-- DROP TABLE IF EXISTS wp_woocommerce_log;

-- Delete WooCommerce post types
-- DELETE FROM wp_posts WHERE post_type IN (
--     'shop_order',
--     'shop_coupon',
--     'product',
--     'product_variation'
-- );

-- Delete WooCommerce post meta
-- DELETE FROM wp_postmeta WHERE post_id IN (
--     SELECT ID FROM wp_posts WHERE post_type IN (
--         'shop_order',
--         'shop_coupon',
--         'product',
--         'product_variation'
--     )
-- );

-- Delete WooCommerce options
-- DELETE FROM wp_options WHERE option_name LIKE 'woocommerce_%';
-- DELETE FROM wp_options WHERE option_name LIKE '_wc_%';
-- DELETE FROM wp_options WHERE option_name LIKE 'wc_%';


-- =============================================================================
-- 13. OPTIMIZE ALL TABLES
-- =============================================================================

OPTIMIZE TABLE
    wp_commentmeta,
    wp_comments,
    wp_options,
    wp_postmeta,
    wp_posts,
    wp_term_relationships,
    wp_term_taxonomy,
    wp_termmeta,
    wp_terms,
    wp_usermeta,
    wp_users;


-- =============================================================================
-- 14. CHECK DATABASE SIZE AFTER CLEANUP
-- =============================================================================

SELECT
    table_name AS `Table`,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS `Size (MB)`
FROM information_schema.TABLES
WHERE table_schema = DATABASE()
ORDER BY (data_length + index_length) DESC;


-- =============================================================================
-- 15. GET SPACE SAVINGS SUMMARY
-- =============================================================================

-- Run this to see how much space you saved
SELECT
    SUM(data_free) / 1024 / 1024 AS `Space Freed (MB)`
FROM information_schema.TABLES
WHERE table_schema = DATABASE();


-- =============================================================================
-- NOTES:
-- =============================================================================

/*
1. Always backup your database before running these queries
2. Run queries one at a time and check results
3. Some queries are commented out for safety - uncomment carefully
4. Expected space savings: 20-50% depending on site age
5. Run this maintenance monthly for optimal performance

WP-CLI ALTERNATIVE:
wp db optimize
wp transient delete --all
wp post delete $(wp post list --post_type='revision' --format=ids) --force
*/
