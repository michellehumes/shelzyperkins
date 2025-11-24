/**
 * ShelzyPerkins Affiliate Link Tracking
 *
 * Tracks affiliate link clicks and conversions
 * Uses Google Analytics 4 for reporting
 *
 * @package ShelzyPerkins
 */

(function() {
    'use strict';

    const AFFILIATE_TAG = (typeof spData !== 'undefined' && spData.affiliateTag) ? spData.affiliateTag : 'shelzysdesigns-20';

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        initAffiliateTracking();
        ensureAffiliateTags();
        trackProductViews();
    });

    /**
     * Initialize affiliate link tracking
     */
    function initAffiliateTracking() {
        // Track all Amazon links
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a[href*="amazon.com"], a[href*="amzn.to"]');
            if (!link) return;

            const linkData = extractLinkData(link);
            trackAffiliateClick(linkData);
        });
    }

    /**
     * Extract data from affiliate link
     */
    function extractLinkData(link) {
        const url = new URL(link.href);
        const asin = extractASIN(link.href);
        const productTitle = link.dataset.productTitle ||
                             link.closest('.sp-card')?.querySelector('.sp-card__title')?.textContent ||
                             link.textContent.trim();

        return {
            url: link.href,
            asin: asin,
            productTitle: productTitle,
            linkType: getLinkType(link),
            position: getLinkPosition(link),
            pageUrl: window.location.href,
            pageTitle: document.title,
            timestamp: new Date().toISOString()
        };
    }

    /**
     * Extract ASIN from Amazon URL
     */
    function extractASIN(url) {
        const patterns = [
            /\/dp\/([A-Z0-9]{10})/i,
            /\/gp\/product\/([A-Z0-9]{10})/i,
            /\/exec\/obidos\/ASIN\/([A-Z0-9]{10})/i,
            /\/gp\/aw\/d\/([A-Z0-9]{10})/i,
            /\?asin=([A-Z0-9]{10})/i
        ];

        for (const pattern of patterns) {
            const match = url.match(pattern);
            if (match) return match[1];
        }

        return null;
    }

    /**
     * Determine link type for tracking
     */
    function getLinkType(link) {
        if (link.classList.contains('sp-btn--amazon')) return 'cta_button';
        if (link.closest('.sp-product-table')) return 'comparison_table';
        if (link.closest('.sp-card')) return 'product_card';
        if (link.closest('.sp-sticky-bar')) return 'sticky_bar';
        if (link.closest('.sp-post__content')) return 'inline_content';
        if (link.closest('.sp-sidebar')) return 'sidebar';
        return 'other';
    }

    /**
     * Get link position on page
     */
    function getLinkPosition(link) {
        const rect = link.getBoundingClientRect();
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const viewportHeight = window.innerHeight;
        const linkTop = rect.top + scrollTop;

        if (linkTop < viewportHeight) return 'above_fold';
        if (linkTop < viewportHeight * 2) return 'first_scroll';
        return 'deep_content';
    }

    /**
     * Track affiliate click to Google Analytics
     */
    function trackAffiliateClick(linkData) {
        // Google Analytics 4
        if (typeof gtag === 'function') {
            gtag('event', 'affiliate_click', {
                'event_category': 'Affiliate',
                'event_label': linkData.asin || 'unknown',
                'asin': linkData.asin,
                'product_title': linkData.productTitle,
                'link_type': linkData.linkType,
                'link_position': linkData.position,
                'transport_type': 'beacon'
            });
        }

        // Store in localStorage for session analysis
        storeClickData(linkData);

        // Log for debugging (remove in production)
        if (window.location.hostname === 'localhost' || window.location.search.includes('debug=1')) {
            console.log('Affiliate Click Tracked:', linkData);
        }
    }

    /**
     * Store click data locally for analysis
     */
    function storeClickData(linkData) {
        try {
            const storageKey = 'sp_affiliate_clicks';
            const existingData = JSON.parse(localStorage.getItem(storageKey) || '[]');

            // Keep only last 100 clicks
            if (existingData.length >= 100) {
                existingData.shift();
            }

            existingData.push(linkData);
            localStorage.setItem(storageKey, JSON.stringify(existingData));
        } catch (e) {
            // Storage not available or full
        }
    }

    /**
     * Ensure all Amazon links have affiliate tag
     */
    function ensureAffiliateTags() {
        document.querySelectorAll('a[href*="amazon.com"]').forEach(function(link) {
            try {
                const url = new URL(link.href);

                // Don't modify non-product links
                if (url.pathname.includes('/ap/') ||
                    url.pathname.includes('/gp/help') ||
                    url.pathname.includes('/hz/')) {
                    return;
                }

                // Add or update tag parameter
                url.searchParams.set('tag', AFFILIATE_TAG);
                link.href = url.toString();

                // Add rel attributes for compliance
                const relValues = ['nofollow', 'sponsored', 'noopener'];
                const existingRel = (link.getAttribute('rel') || '').split(' ');
                const newRel = [...new Set([...existingRel, ...relValues])].filter(Boolean);
                link.setAttribute('rel', newRel.join(' '));

                // Ensure external links open in new tab
                link.setAttribute('target', '_blank');

            } catch (e) {
                // Invalid URL, skip
            }
        });
    }

    /**
     * Track product card views
     */
    function trackProductViews() {
        if (!('IntersectionObserver' in window)) return;

        const productCards = document.querySelectorAll('.sp-card[data-asin], .sp-product-card');

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const card = entry.target;
                    const asin = card.dataset.asin;
                    const title = card.querySelector('.sp-card__title')?.textContent || '';

                    if (typeof gtag === 'function') {
                        gtag('event', 'view_item', {
                            'event_category': 'Product',
                            'event_label': asin || title,
                            'asin': asin
                        });
                    }

                    // Only track once
                    observer.unobserve(card);
                }
            });
        }, {
            threshold: 0.5,
            rootMargin: '0px'
        });

        productCards.forEach(function(card) {
            observer.observe(card);
        });
    }

    /**
     * Get click statistics
     */
    window.spGetClickStats = function() {
        try {
            const clicks = JSON.parse(localStorage.getItem('sp_affiliate_clicks') || '[]');

            const stats = {
                totalClicks: clicks.length,
                byLinkType: {},
                byPosition: {},
                topASINs: {},
                recentClicks: clicks.slice(-10)
            };

            clicks.forEach(function(click) {
                // Count by link type
                stats.byLinkType[click.linkType] = (stats.byLinkType[click.linkType] || 0) + 1;

                // Count by position
                stats.byPosition[click.position] = (stats.byPosition[click.position] || 0) + 1;

                // Count by ASIN
                if (click.asin) {
                    stats.topASINs[click.asin] = (stats.topASINs[click.asin] || 0) + 1;
                }
            });

            return stats;
        } catch (e) {
            return { error: 'Unable to retrieve stats' };
        }
    };

    /**
     * Clear stored click data
     */
    window.spClearClickData = function() {
        localStorage.removeItem('sp_affiliate_clicks');
        console.log('Affiliate click data cleared');
    };

})();
