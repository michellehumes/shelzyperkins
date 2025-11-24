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
     * Exit Intent Popup
     */
    function initExitIntentPopup() {
        const popup = document.getElementById('exit-popup');
        const closeBtn = document.getElementById('popup-close');
        const form = document.getElementById('popup-form');

        if (!popup) return;

        // Check if popup was already shown or user subscribed
        if (localStorage.getItem('sp_popup_shown') || document.cookie.includes('sp_subscribed')) {
            return;
        }

        let popupShown = false;
        let scrollThreshold = 0.5; // 50% scroll

        // Exit intent on mouse leave (desktop)
        document.addEventListener('mouseout', function(e) {
            if (popupShown) return;

            // Check if mouse is leaving at the top of the page
            if (e.clientY < 10 && e.relatedTarget === null) {
                showPopup();
            }
        });

        // Scroll-based trigger (mobile fallback)
        window.addEventListener('scroll', function() {
            if (popupShown) return;

            const scrollPercent = window.scrollY / (document.body.scrollHeight - window.innerHeight);

            if (scrollPercent > scrollThreshold) {
                // Wait a bit before showing
                setTimeout(function() {
                    if (!popupShown) {
                        showPopup();
                    }
                }, 5000);
            }
        }, { passive: true });

        // Show popup after time on page (30 seconds)
        setTimeout(function() {
            if (!popupShown) {
                showPopup();
            }
        }, 30000);

        function showPopup() {
            popupShown = true;
            popup.classList.add('is-active');
            popup.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            // Mark as shown in localStorage (expires on session end)
            localStorage.setItem('sp_popup_shown', 'true');

            // Focus on input
            const input = popup.querySelector('input[type="email"]');
            if (input) {
                setTimeout(() => input.focus(), 300);
            }
        }

        function hidePopup() {
            popup.classList.remove('is-active');
            popup.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        // Close button
        if (closeBtn) {
            closeBtn.addEventListener('click', hidePopup);
        }

        // Close on backdrop click
        popup.addEventListener('click', function(e) {
            if (e.target === popup) {
                hidePopup();
            }
        });

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && popup.classList.contains('is-active')) {
                hidePopup();
            }
        });

        // Form submission
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const input = form.querySelector('input[type="email"]');
                const button = form.querySelector('button[type="submit"]');
                const email = input?.value;

                if (!email) return;

                // Disable button and show loading
                button.disabled = true;
                button.textContent = 'Subscribing...';

                // Simulate API call (replace with actual endpoint)
                setTimeout(function() {
                    button.textContent = 'Subscribed!';
                    button.classList.add('success');

                    // Set cookie to prevent showing again
                    document.cookie = 'sp_subscribed=true; path=/; max-age=31536000'; // 1 year

                    // Track subscription
                    if (typeof gtag === 'function') {
                        gtag('event', 'newsletter_signup', {
                            'event_category': 'Engagement',
                            'event_label': 'Exit Intent Popup'
                        });
                    }

                    // Hide popup after delay
                    setTimeout(hidePopup, 2000);
                }, 1000);
            });
        }
    }

    // Initialize exit intent popup after DOM is ready
    initExitIntentPopup();

    /**
     * Mobile search toggle
     */
    const mobileSearchToggle = document.getElementById('mobile-search-toggle');
    if (mobileSearchToggle) {
        mobileSearchToggle.addEventListener('click', function() {
            const searchOverlay = document.getElementById('search-overlay');
            if (searchOverlay) {
                searchOverlay.classList.add('is-active');
                const searchInput = searchOverlay.querySelector('input');
                if (searchInput) {
                    setTimeout(() => searchInput.focus(), 300);
                }
            }
        });
    }

    /**
     * Archive view toggle
     */
    document.querySelectorAll('.sp-view-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            const container = document.getElementById('posts-container');

            if (!container) return;

            // Update active state
            document.querySelectorAll('.sp-view-btn').forEach(b => b.classList.remove('is-active'));
            this.classList.add('is-active');

            // Toggle view class
            if (view === 'list') {
                container.classList.remove('sp-grid--3');
                container.classList.add('sp-list-view');
            } else {
                container.classList.remove('sp-list-view');
                container.classList.add('sp-grid--3');
            }
        });
    });

    /**
     * Skeleton loader for images
     */
    document.querySelectorAll('img[loading="lazy"]').forEach(function(img) {
        if (!img.complete) {
            img.classList.add('sp-loading');

            img.addEventListener('load', function() {
                this.classList.remove('sp-loading');
                this.classList.add('sp-loaded');
            });

            img.addEventListener('error', function() {
                this.classList.remove('sp-loading');
                this.classList.add('sp-error');
            });
        }
    });

})();
