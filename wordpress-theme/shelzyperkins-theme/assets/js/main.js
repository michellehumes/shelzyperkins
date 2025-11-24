/**
 * ShelzyPerkins Main JavaScript
 *
 * @package ShelzyPerkins
 */

(function() {
    'use strict';

    // DOM Ready
    document.addEventListener('DOMContentLoaded', function() {
        initMobileMenu();
        initSearchOverlay();
        initStickyHeader();
        initStickyBar();
        initSmoothScroll();
        initLazyLoad();
        initAffiliateLinks();
    });

    /**
     * Mobile Menu Toggle
     */
    function initMobileMenu() {
        const menuToggle = document.querySelector('.sp-menu-toggle');
        const navList = document.querySelector('.sp-nav__list');

        if (!menuToggle || !navList) return;

        menuToggle.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !isExpanded);
            navList.classList.toggle('is-active');
            document.body.classList.toggle('menu-open');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.sp-nav') && navList.classList.contains('is-active')) {
                navList.classList.remove('is-active');
                menuToggle.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('menu-open');
            }
        });

        // Close menu on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && navList.classList.contains('is-active')) {
                navList.classList.remove('is-active');
                menuToggle.setAttribute('aria-expanded', 'false');
                menuToggle.focus();
            }
        });
    }

    /**
     * Search Overlay
     */
    function initSearchOverlay() {
        const searchToggle = document.querySelector('.sp-search-toggle');
        const searchOverlay = document.getElementById('search-overlay');
        const searchClose = document.querySelector('.sp-search-close');
        const searchInput = searchOverlay?.querySelector('input');

        if (!searchToggle || !searchOverlay) return;

        searchToggle.addEventListener('click', function() {
            searchOverlay.classList.add('is-active');
            searchInput?.focus();
        });

        searchClose?.addEventListener('click', function() {
            searchOverlay.classList.remove('is-active');
            searchToggle.focus();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && searchOverlay.classList.contains('is-active')) {
                searchOverlay.classList.remove('is-active');
                searchToggle.focus();
            }
        });
    }

    /**
     * Sticky Header
     */
    function initStickyHeader() {
        const header = document.getElementById('site-header');
        if (!header) return;

        let lastScroll = 0;
        const scrollThreshold = 100;

        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset;

            if (currentScroll > scrollThreshold) {
                header.classList.add('is-scrolled');
            } else {
                header.classList.remove('is-scrolled');
            }

            // Hide/show on scroll direction
            if (currentScroll > lastScroll && currentScroll > 300) {
                header.classList.add('is-hidden');
            } else {
                header.classList.remove('is-hidden');
            }

            lastScroll = currentScroll;
        }, { passive: true });
    }

    /**
     * Sticky Amazon Bar
     */
    function initStickyBar() {
        const stickyBar = document.getElementById('sticky-bar');
        if (!stickyBar) return;

        const showAfter = 500;

        window.addEventListener('scroll', function() {
            if (window.pageYOffset > showAfter) {
                stickyBar.classList.add('is-visible');
            } else {
                stickyBar.classList.remove('is-visible');
            }
        }, { passive: true });
    }

    /**
     * Smooth Scroll for anchor links
     */
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;

                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    e.preventDefault();
                    const headerOffset = 80;
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    /**
     * Lazy Load Images (fallback for browsers without native support)
     */
    function initLazyLoad() {
        if ('loading' in HTMLImageElement.prototype) {
            // Browser supports native lazy loading
            return;
        }

        // Fallback for older browsers
        const lazyImages = document.querySelectorAll('img[loading="lazy"]');

        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const image = entry.target;
                        image.src = image.dataset.src || image.src;
                        imageObserver.unobserve(image);
                    }
                });
            });

            lazyImages.forEach(function(image) {
                imageObserver.observe(image);
            });
        }
    }

    /**
     * Track affiliate link clicks
     */
    function initAffiliateLinks() {
        const affiliateLinks = document.querySelectorAll('a[href*="amazon.com"]');

        affiliateLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                // Track click if analytics is available
                if (typeof gtag === 'function') {
                    const asin = this.dataset.asin || extractASIN(this.href);
                    const linkText = this.textContent.trim();

                    gtag('event', 'affiliate_click', {
                        'event_category': 'Affiliate',
                        'event_label': asin || linkText,
                        'transport_type': 'beacon'
                    });
                }

                // Add affiliate tag if missing
                const url = new URL(this.href);
                if (!url.searchParams.has('tag')) {
                    url.searchParams.set('tag', spData?.affiliateTag || 'shelzysdesigns-20');
                    this.href = url.toString();
                }
            });
        });
    }

    /**
     * Extract ASIN from Amazon URL
     */
    function extractASIN(url) {
        const asinMatch = url.match(/\/dp\/([A-Z0-9]{10})/i) ||
                          url.match(/\/gp\/product\/([A-Z0-9]{10})/i) ||
                          url.match(/\/exec\/obidos\/ASIN\/([A-Z0-9]{10})/i);
        return asinMatch ? asinMatch[1] : null;
    }

    /**
     * Newsletter form submission
     */
    document.querySelectorAll('.sp-email-signup__form, .sp-sidebar-email-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const emailInput = this.querySelector('input[type="email"]');
            const submitBtn = this.querySelector('button[type="submit"]');
            const email = emailInput?.value;

            if (!email) return;

            // Disable button and show loading
            submitBtn.disabled = true;
            submitBtn.textContent = 'Subscribing...';

            // Simulate API call (replace with actual endpoint)
            setTimeout(function() {
                submitBtn.textContent = 'Subscribed!';
                submitBtn.classList.add('success');
                emailInput.value = '';

                // Track subscription
                if (typeof gtag === 'function') {
                    gtag('event', 'newsletter_signup', {
                        'event_category': 'Engagement',
                        'event_label': 'Email Signup'
                    });
                }

                // Reset button after delay
                setTimeout(function() {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Subscribe';
                    submitBtn.classList.remove('success');
                }, 3000);
            }, 1000);
        });
    });

    /**
     * Copy coupon codes
     */
    document.querySelectorAll('.sp-coupon-code').forEach(function(element) {
        element.addEventListener('click', function() {
            const code = this.dataset.code || this.textContent.trim();

            navigator.clipboard.writeText(code).then(function() {
                element.classList.add('copied');
                const originalText = element.textContent;
                element.textContent = 'Copied!';

                setTimeout(function() {
                    element.textContent = originalText;
                    element.classList.remove('copied');
                }, 2000);
            });
        });
    });

    /**
     * Back to top button
     */
    const backToTop = document.querySelector('.sp-back-to-top');
    if (backToTop) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 500) {
                backToTop.classList.add('is-visible');
            } else {
                backToTop.classList.remove('is-visible');
            }
        }, { passive: true });

        backToTop.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /**
     * ==========================================================================
     * EXIT INTENT POPUP
     * ==========================================================================
     */
    (function initExitIntent() {
        const popup = document.getElementById('sp-exit-popup');
        if (!popup) return;

        const overlay = popup.querySelector('.sp-exit-popup__overlay');
        const closeBtn = popup.querySelector('.sp-exit-popup__close');
        const form = popup.querySelector('.sp-exit-popup__form');

        // Check if popup has been shown/dismissed
        const hasSeenPopup = localStorage.getItem('sp_exit_popup_dismissed');
        const lastShown = localStorage.getItem('sp_exit_popup_shown');
        const oneDayAgo = Date.now() - (24 * 60 * 60 * 1000);

        // Don't show if dismissed or shown in last 24 hours
        if (hasSeenPopup || (lastShown && parseInt(lastShown) > oneDayAgo)) {
            return;
        }

        let popupShown = false;
        let scrollTriggerEnabled = true;

        // Exit intent detection (desktop)
        document.addEventListener('mouseout', function(e) {
            if (popupShown) return;

            // Check if mouse is leaving through top of page
            if (e.clientY < 10 && e.relatedTarget === null) {
                showPopup();
            }
        });

        // Scroll trigger (mobile fallback - show after 60% scroll)
        window.addEventListener('scroll', function() {
            if (popupShown || !scrollTriggerEnabled) return;

            const scrollPercent = (window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100;

            if (scrollPercent > 60) {
                // Delay popup slightly
                setTimeout(function() {
                    if (!popupShown) {
                        showPopup();
                    }
                }, 2000);
                scrollTriggerEnabled = false;
            }
        }, { passive: true });

        // Time-based trigger (show after 45 seconds on page)
        setTimeout(function() {
            if (!popupShown) {
                showPopup();
            }
        }, 45000);

        function showPopup() {
            popupShown = true;
            popup.classList.add('is-active');
            document.body.style.overflow = 'hidden';
            localStorage.setItem('sp_exit_popup_shown', Date.now().toString());

            // Track popup view
            if (typeof gtag === 'function') {
                gtag('event', 'exit_popup_shown', {
                    'event_category': 'Engagement',
                    'event_label': 'Exit Intent Popup'
                });
            }
        }

        function hidePopup() {
            popup.classList.remove('is-active');
            document.body.style.overflow = '';
        }

        function dismissPopup() {
            hidePopup();
            localStorage.setItem('sp_exit_popup_dismissed', 'true');
        }

        // Close handlers
        closeBtn?.addEventListener('click', dismissPopup);
        overlay?.addEventListener('click', dismissPopup);

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && popup.classList.contains('is-active')) {
                dismissPopup();
            }
        });

        // Form submission
        form?.addEventListener('submit', function(e) {
            e.preventDefault();

            const emailInput = this.querySelector('input[type="email"]');
            const submitBtn = this.querySelector('button[type="submit"]');
            const email = emailInput?.value;

            if (!email) return;

            submitBtn.disabled = true;
            submitBtn.textContent = 'Subscribing...';

            // Simulate API call (replace with actual Mailchimp/ConvertKit integration)
            setTimeout(function() {
                submitBtn.textContent = 'Success!';
                submitBtn.classList.add('success');

                // Track conversion
                if (typeof gtag === 'function') {
                    gtag('event', 'exit_popup_signup', {
                        'event_category': 'Conversion',
                        'event_label': 'Exit Intent Email Capture'
                    });
                }

                // Close popup after delay
                setTimeout(function() {
                    dismissPopup();
                }, 1500);
            }, 1000);
        });
    })();

    /**
     * ==========================================================================
     * COUNTDOWN TIMERS
     * ==========================================================================
     */
    (function initCountdowns() {
        const countdowns = document.querySelectorAll('[data-countdown]');

        countdowns.forEach(function(element) {
            const endDate = new Date(element.dataset.countdown).getTime();
            const timerDisplay = element.querySelector('.sp-countdown-timer');

            if (!timerDisplay || !endDate) return;

            function updateTimer() {
                const now = Date.now();
                const distance = endDate - now;

                if (distance < 0) {
                    timerDisplay.textContent = 'Expired';
                    element.closest('.sp-deal-alert')?.classList.add('is-expired');
                    return;
                }

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                let timeString = '';
                if (days > 0) {
                    timeString = `${days}d ${hours}h ${minutes}m`;
                } else if (hours > 0) {
                    timeString = `${hours}h ${minutes}m ${seconds}s`;
                } else {
                    timeString = `${minutes}m ${seconds}s`;
                }

                timerDisplay.textContent = timeString;
            }

            updateTimer();
            setInterval(updateTimer, 1000);
        });
    })();

    /**
     * ==========================================================================
     * PRODUCT CARD VISIBILITY TRACKING
     * ==========================================================================
     */
    (function initProductTracking() {
        if (!('IntersectionObserver' in window)) return;

        const productCards = document.querySelectorAll('.sp-product-card, .sp-bounty-card');

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting && !entry.target.dataset.viewed) {
                    entry.target.dataset.viewed = 'true';

                    const asin = entry.target.dataset.asin;
                    const title = entry.target.querySelector('.sp-card__title, .sp-bounty-card__title')?.textContent;

                    if (typeof gtag === 'function') {
                        gtag('event', 'view_item', {
                            'event_category': 'Product',
                            'event_label': asin || title || 'Unknown Product'
                        });
                    }
                }
            });
        }, { threshold: 0.5 });

        productCards.forEach(function(card) {
            observer.observe(card);
        });
    })();

    /**
     * ==========================================================================
     * PRICE HISTORY TOOLTIPS
     * ==========================================================================
     */
    (function initPriceHistoryTooltips() {
        const priceHistoryBars = document.querySelectorAll('.sp-price-history__bar');

        priceHistoryBars.forEach(function(bar) {
            bar.addEventListener('mouseenter', function() {
                this.classList.add('is-hovered');
            });

            bar.addEventListener('mouseleave', function() {
                this.classList.remove('is-hovered');
            });
        });
    })();

    /**
     * ==========================================================================
     * BOUNTY LINK CLICK TRACKING
     * ==========================================================================
     */
    (function initBountyTracking() {
        const bountyLinks = document.querySelectorAll('.sp-bounty-card a, .sp-bounty-banner a, .sp-bounty-inline');

        bountyLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                const program = this.closest('.sp-bounty-card, .sp-bounty-banner')?.className.match(/sp-bounty-(?:card|banner)--(\w+)/)?.[1] || 'unknown';

                if (typeof gtag === 'function') {
                    gtag('event', 'bounty_click', {
                        'event_category': 'Affiliate',
                        'event_label': program,
                        'transport_type': 'beacon'
                    });
                }

                // Store in localStorage for attribution
                const bountyClicks = JSON.parse(localStorage.getItem('sp_bounty_clicks') || '[]');
                bountyClicks.push({
                    program: program,
                    timestamp: Date.now(),
                    page: window.location.pathname
                });

                // Keep only last 50 clicks
                if (bountyClicks.length > 50) {
                    bountyClicks.shift();
                }

                localStorage.setItem('sp_bounty_clicks', JSON.stringify(bountyClicks));
            });
        });
    })();

})();
