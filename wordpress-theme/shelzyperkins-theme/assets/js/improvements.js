/**
 * ShelzyPerkins Blog Improvements JavaScript
 * New features: Dark Mode, FAQ Accordion, Price Alerts, Exit Intent, Recently Viewed
 *
 * @package ShelzyPerkins
 * @version 1.1.0
 */

(function() {
    'use strict';

    // Initialize all improvements on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        initDarkMode();
        initTOCToggle();
        initFAQAccordion();
        initPriceAlerts();
        initExitIntentPopup();
        initRecentlyViewed();
        initCompareProducts();
        initShareDeal();
    });

    /* ==========================================================================
       Dark Mode Toggle
       ========================================================================== */
    function initDarkMode() {
        // Create dark mode toggle button
        const header = document.querySelector('.sp-header__inner');
        if (!header) return;

        const toggleBtn = document.createElement('button');
        toggleBtn.className = 'sp-dark-mode-toggle';
        toggleBtn.setAttribute('aria-label', 'Toggle dark mode');
        toggleBtn.innerHTML = `
            <svg class="sp-dark-mode-toggle__sun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="5"></circle>
                <line x1="12" y1="1" x2="12" y2="3"></line>
                <line x1="12" y1="21" x2="12" y2="23"></line>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                <line x1="1" y1="12" x2="3" y2="12"></line>
                <line x1="21" y1="12" x2="23" y2="12"></line>
                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
            </svg>
            <svg class="sp-dark-mode-toggle__moon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
            </svg>
        `;

        // Insert before the navigation
        const nav = header.querySelector('.sp-nav');
        if (nav) {
            header.insertBefore(toggleBtn, nav);
        } else {
            header.appendChild(toggleBtn);
        }

        // Check for saved preference or system preference
        const savedMode = localStorage.getItem('sp_dark_mode');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        if (savedMode === 'true' || (savedMode === null && prefersDark)) {
            document.body.classList.add('sp-dark-mode');
            toggleBtn.classList.add('is-active');
        }

        // Toggle handler
        toggleBtn.addEventListener('click', function() {
            const isDark = document.body.classList.toggle('sp-dark-mode');
            toggleBtn.classList.toggle('is-active', isDark);
            localStorage.setItem('sp_dark_mode', isDark);
            document.cookie = `sp_dark_mode=${isDark}; path=/; max-age=31536000`;
        });

        // Listen for system preference changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
            if (localStorage.getItem('sp_dark_mode') === null) {
                document.body.classList.toggle('sp-dark-mode', e.matches);
                toggleBtn.classList.toggle('is-active', e.matches);
            }
        });
    }

    /* ==========================================================================
       Table of Contents Toggle
       ========================================================================== */
    function initTOCToggle() {
        const tocToggles = document.querySelectorAll('.sp-toc__toggle');

        tocToggles.forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                const toc = this.closest('.sp-toc');
                const list = toc.querySelector('.sp-toc__list');
                const isExpanded = this.getAttribute('aria-expanded') === 'true';

                this.setAttribute('aria-expanded', !isExpanded);
                this.textContent = isExpanded ? '+' : '−';
                list.classList.toggle('is-collapsed', isExpanded);
            });
        });

        // Highlight active section on scroll
        const tocLinks = document.querySelectorAll('.sp-toc__list a');
        if (tocLinks.length === 0) return;

        const headings = Array.from(tocLinks).map(function(link) {
            const id = link.getAttribute('href').replace('#', '');
            return document.getElementById(id);
        }).filter(Boolean);

        let ticking = false;
        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    const scrollPos = window.pageYOffset + 120;

                    headings.forEach(function(heading, index) {
                        const nextHeading = headings[index + 1];
                        const isActive = scrollPos >= heading.offsetTop &&
                            (!nextHeading || scrollPos < nextHeading.offsetTop);

                        tocLinks[index].classList.toggle('is-active', isActive);
                    });

                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });
    }

    /* ==========================================================================
       FAQ Accordion
       ========================================================================== */
    function initFAQAccordion() {
        const faqQuestions = document.querySelectorAll('.sp-faq__question');

        faqQuestions.forEach(function(question) {
            question.addEventListener('click', function() {
                const faq = this.closest('.sp-faq');
                const answer = faq.querySelector('.sp-faq__answer');
                const isExpanded = this.getAttribute('aria-expanded') === 'true';

                // Close all other FAQs in the same section
                const section = faq.closest('.sp-faq-section');
                if (section) {
                    section.querySelectorAll('.sp-faq').forEach(function(otherFaq) {
                        if (otherFaq !== faq) {
                            otherFaq.querySelector('.sp-faq__question').setAttribute('aria-expanded', 'false');
                            otherFaq.querySelector('.sp-faq__answer').hidden = true;
                        }
                    });
                }

                // Toggle current FAQ
                this.setAttribute('aria-expanded', !isExpanded);
                answer.hidden = isExpanded;
            });
        });
    }

    /* ==========================================================================
       Price Alert Forms
       ========================================================================== */
    function initPriceAlerts() {
        const alertForms = document.querySelectorAll('.sp-price-alert__form');

        alertForms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const container = this.closest('.sp-price-alert');
                const emailInput = this.querySelector('input[type="email"]');
                const submitBtn = this.querySelector('button[type="submit"]');
                const asin = container.dataset.asin || '';
                const product = this.dataset.product || 'this product';
                const price = this.dataset.price || '';

                if (!emailInput.value) return;

                // Disable and show loading
                submitBtn.disabled = true;
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Saving...';

                // Send AJAX request
                const formData = new FormData();
                formData.append('action', 'sp_price_alert');
                formData.append('nonce', spData?.nonce || '');
                formData.append('email', emailInput.value);
                formData.append('asin', asin);
                formData.append('product', product);
                formData.append('price', price);

                fetch(spData?.ajaxUrl || '/wp-admin/admin-ajax.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.success) {
                        container.innerHTML = `
                            <div class="sp-price-alert__success">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                                <p>${data.data.message}</p>
                            </div>
                        `;
                    } else {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                        alert(data.data?.message || 'Something went wrong. Please try again.');
                    }
                })
                .catch(function() {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                    alert('Network error. Please try again.');
                });
            });
        });
    }

    /* ==========================================================================
       Exit Intent Popup
       ========================================================================== */
    function initExitIntentPopup() {
        // Only show on single posts
        if (!document.body.classList.contains('single-post')) return;

        // Check if already shown in this session
        if (sessionStorage.getItem('sp_exit_shown')) return;

        let popupShown = false;

        // Create popup HTML
        const popup = document.createElement('div');
        popup.className = 'sp-exit-popup';
        popup.innerHTML = `
            <div class="sp-exit-popup__overlay"></div>
            <div class="sp-exit-popup__content">
                <button class="sp-exit-popup__close" aria-label="Close popup">&times;</button>
                <div class="sp-exit-popup__icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                </div>
                <h3 class="sp-exit-popup__title">Wait! Don't Miss Out!</h3>
                <p class="sp-exit-popup__text">Get the best deals delivered straight to your inbox. Never miss a price drop again!</p>
                <form class="sp-exit-popup__form">
                    <input type="email" name="email" placeholder="Enter your email" required class="sp-exit-popup__input">
                    <button type="submit" class="sp-btn sp-btn--primary">Get Daily Deals</button>
                </form>
                <p class="sp-exit-popup__disclaimer">No spam, just amazing deals. Unsubscribe anytime.</p>
            </div>
        `;

        document.body.appendChild(popup);

        // Show popup on exit intent (mouse leaving viewport)
        document.addEventListener('mouseout', function(e) {
            if (popupShown) return;
            if (e.clientY < 10 && e.relatedTarget === null) {
                showExitPopup();
            }
        });

        // Also show after 30 seconds of inactivity
        let inactivityTimer;
        function resetInactivityTimer() {
            clearTimeout(inactivityTimer);
            if (!popupShown) {
                inactivityTimer = setTimeout(showExitPopup, 45000);
            }
        }
        document.addEventListener('mousemove', resetInactivityTimer, { passive: true });
        document.addEventListener('scroll', resetInactivityTimer, { passive: true });
        resetInactivityTimer();

        function showExitPopup() {
            if (popupShown) return;
            popupShown = true;
            popup.classList.add('is-visible');
            sessionStorage.setItem('sp_exit_shown', 'true');

            // Track showing
            if (typeof gtag === 'function') {
                gtag('event', 'exit_popup_shown', { event_category: 'Engagement' });
            }
        }

        function hidePopup() {
            popup.classList.remove('is-visible');
        }

        // Close handlers
        popup.querySelector('.sp-exit-popup__close').addEventListener('click', hidePopup);
        popup.querySelector('.sp-exit-popup__overlay').addEventListener('click', hidePopup);
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') hidePopup();
        });

        // Form submission
        popup.querySelector('.sp-exit-popup__form').addEventListener('submit', function(e) {
            e.preventDefault();

            const emailInput = this.querySelector('input[type="email"]');
            const submitBtn = this.querySelector('button[type="submit"]');

            if (!emailInput.value) return;

            submitBtn.disabled = true;
            submitBtn.textContent = 'Subscribing...';

            const formData = new FormData();
            formData.append('action', 'sp_newsletter');
            formData.append('nonce', spData?.nonce || '');
            formData.append('email', emailInput.value);

            fetch(spData?.ajaxUrl || '/wp-admin/admin-ajax.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    popup.querySelector('.sp-exit-popup__content').innerHTML = `
                        <div class="sp-exit-popup__success">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--sp-success)" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <h3>You're In!</h3>
                            <p>${data.data.message}</p>
                            <button class="sp-btn sp-btn--secondary" onclick="this.closest('.sp-exit-popup').classList.remove('is-visible')">Continue Shopping</button>
                        </div>
                    `;

                    if (typeof gtag === 'function') {
                        gtag('event', 'exit_popup_signup', { event_category: 'Engagement' });
                    }
                }
            })
            .catch(function() {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Get Daily Deals';
            });
        });
    }

    /* ==========================================================================
       Recently Viewed Products
       ========================================================================== */
    function initRecentlyViewed() {
        const STORAGE_KEY = 'sp_recently_viewed';
        const MAX_ITEMS = 10;

        // Get current post data if on single post
        if (document.body.classList.contains('single-post')) {
            const postData = {
                id: document.querySelector('article')?.id?.replace('post-', '') || '',
                title: document.querySelector('.sp-post__title')?.textContent?.trim() || '',
                url: window.location.href,
                image: document.querySelector('.sp-post__image')?.src || '',
                date: Date.now()
            };

            if (postData.id && postData.title) {
                saveRecentlyViewed(postData);
            }
        }

        // Display recently viewed section
        displayRecentlyViewed();

        function saveRecentlyViewed(item) {
            let items = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');

            // Remove if already exists
            items = items.filter(function(i) { return i.id !== item.id; });

            // Add to beginning
            items.unshift(item);

            // Limit to max items
            if (items.length > MAX_ITEMS) {
                items = items.slice(0, MAX_ITEMS);
            }

            localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
        }

        function displayRecentlyViewed() {
            const container = document.querySelector('.sp-recently-viewed');
            if (!container) return;

            let items = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');

            // Don't show current post
            const currentId = document.querySelector('article')?.id?.replace('post-', '');
            items = items.filter(function(i) { return i.id !== currentId; });

            if (items.length === 0) {
                container.style.display = 'none';
                return;
            }

            const list = container.querySelector('.sp-recently-viewed__list');
            if (!list) return;

            list.innerHTML = items.slice(0, 5).map(function(item) {
                return `
                    <a href="${item.url}" class="sp-recently-viewed__item">
                        ${item.image ? `<img src="${item.image}" alt="${item.title}" class="sp-recently-viewed__image" loading="lazy">` : ''}
                        <span class="sp-recently-viewed__title">${item.title}</span>
                    </a>
                `;
            }).join('');

            container.style.display = 'block';
        }
    }

    /* ==========================================================================
       Product Comparison Tool
       ========================================================================== */
    function initCompareProducts() {
        const STORAGE_KEY = 'sp_compare_products';
        const MAX_COMPARE = 4;

        // Add "Add to Compare" buttons to product cards
        document.querySelectorAll('.sp-product-card, .sp-card[data-asin]').forEach(function(card) {
            const asin = card.dataset.asin;
            if (!asin) return;

            const compareBtn = document.createElement('button');
            compareBtn.className = 'sp-compare-btn';
            compareBtn.innerHTML = `
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="20" x2="18" y2="10"></line>
                    <line x1="12" y1="20" x2="12" y2="4"></line>
                    <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
                Compare
            `;

            const imageContainer = card.querySelector('.sp-card__image');
            if (imageContainer) {
                imageContainer.appendChild(compareBtn);
            }

            // Update button state
            updateCompareButton(compareBtn, asin);

            compareBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleCompareProduct(asin, card);
                updateCompareButton(this, asin);
                updateCompareDrawer();
            });
        });

        // Create comparison drawer
        createCompareDrawer();

        function getCompareProducts() {
            return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
        }

        function saveCompareProducts(products) {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(products));
        }

        function toggleCompareProduct(asin, card) {
            let products = getCompareProducts();
            const index = products.findIndex(function(p) { return p.asin === asin; });

            if (index > -1) {
                products.splice(index, 1);
            } else if (products.length < MAX_COMPARE) {
                products.push({
                    asin: asin,
                    title: card.querySelector('.sp-card__title')?.textContent?.trim() || '',
                    price: card.querySelector('.sp-card__price-current')?.textContent?.trim() || '',
                    image: card.querySelector('.sp-card__image img')?.src || ''
                });
            } else {
                alert(`You can compare up to ${MAX_COMPARE} products at a time.`);
                return;
            }

            saveCompareProducts(products);
        }

        function updateCompareButton(btn, asin) {
            const products = getCompareProducts();
            const isInCompare = products.some(function(p) { return p.asin === asin; });
            btn.classList.toggle('is-active', isInCompare);
            btn.innerHTML = isInCompare ?
                `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg> Added` :
                `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg> Compare`;
        }

        function createCompareDrawer() {
            const drawer = document.createElement('div');
            drawer.className = 'sp-compare-drawer';
            drawer.innerHTML = `
                <div class="sp-compare-drawer__header">
                    <span class="sp-compare-drawer__count">0 products</span>
                    <button class="sp-compare-drawer__view sp-btn sp-btn--primary sp-btn--sm">Compare Now</button>
                    <button class="sp-compare-drawer__clear">Clear All</button>
                </div>
                <div class="sp-compare-drawer__items"></div>
            `;
            document.body.appendChild(drawer);

            drawer.querySelector('.sp-compare-drawer__clear').addEventListener('click', function() {
                saveCompareProducts([]);
                updateCompareDrawer();
                document.querySelectorAll('.sp-compare-btn').forEach(function(btn) {
                    const card = btn.closest('[data-asin]');
                    if (card) updateCompareButton(btn, card.dataset.asin);
                });
            });

            drawer.querySelector('.sp-compare-drawer__view').addEventListener('click', function() {
                // Open comparison modal or page
                showComparisonModal();
            });

            updateCompareDrawer();
        }

        function updateCompareDrawer() {
            const drawer = document.querySelector('.sp-compare-drawer');
            if (!drawer) return;

            const products = getCompareProducts();
            const count = drawer.querySelector('.sp-compare-drawer__count');
            const items = drawer.querySelector('.sp-compare-drawer__items');

            count.textContent = `${products.length} product${products.length !== 1 ? 's' : ''}`;
            drawer.classList.toggle('is-visible', products.length > 0);

            items.innerHTML = products.map(function(p) {
                return `
                    <div class="sp-compare-drawer__item" data-asin="${p.asin}">
                        ${p.image ? `<img src="${p.image}" alt="${p.title}">` : ''}
                        <button class="sp-compare-drawer__remove" data-asin="${p.asin}">&times;</button>
                    </div>
                `;
            }).join('');

            // Remove handlers
            items.querySelectorAll('.sp-compare-drawer__remove').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const asin = this.dataset.asin;
                    let products = getCompareProducts();
                    products = products.filter(function(p) { return p.asin !== asin; });
                    saveCompareProducts(products);
                    updateCompareDrawer();

                    const card = document.querySelector(`[data-asin="${asin}"]`);
                    if (card) {
                        const compareBtn = card.querySelector('.sp-compare-btn');
                        if (compareBtn) updateCompareButton(compareBtn, asin);
                    }
                });
            });
        }

        function showComparisonModal() {
            const products = getCompareProducts();
            if (products.length < 2) {
                alert('Please add at least 2 products to compare.');
                return;
            }

            const modal = document.createElement('div');
            modal.className = 'sp-compare-modal';
            modal.innerHTML = `
                <div class="sp-compare-modal__overlay"></div>
                <div class="sp-compare-modal__content">
                    <button class="sp-compare-modal__close">&times;</button>
                    <h3>Product Comparison</h3>
                    <div class="sp-compare-modal__grid">
                        ${products.map(function(p) {
                            return `
                                <div class="sp-compare-modal__product">
                                    ${p.image ? `<img src="${p.image}" alt="${p.title}">` : ''}
                                    <h4>${p.title}</h4>
                                    <div class="sp-compare-modal__price">${p.price}</div>
                                    <a href="https://www.amazon.com/dp/${p.asin}?tag=${spData?.affiliateTag || 'shelzysdesigns-20'}"
                                       class="sp-btn sp-btn--amazon sp-btn--sm" target="_blank" rel="nofollow sponsored noopener">
                                        View on Amazon
                                    </a>
                                </div>
                            `;
                        }).join('')}
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            setTimeout(function() { modal.classList.add('is-visible'); }, 10);

            function closeModal() {
                modal.classList.remove('is-visible');
                setTimeout(function() { modal.remove(); }, 300);
            }

            modal.querySelector('.sp-compare-modal__close').addEventListener('click', closeModal);
            modal.querySelector('.sp-compare-modal__overlay').addEventListener('click', closeModal);
            document.addEventListener('keydown', function handler(e) {
                if (e.key === 'Escape') {
                    closeModal();
                    document.removeEventListener('keydown', handler);
                }
            });
        }
    }

    /* ==========================================================================
       Share Deal Functionality
       ========================================================================== */
    function initShareDeal() {
        document.querySelectorAll('.sp-product-card, .sp-card[data-asin]').forEach(function(card) {
            const asin = card.dataset.asin;
            if (!asin) return;

            const shareBtn = document.createElement('button');
            shareBtn.className = 'sp-share-deal-btn';
            shareBtn.innerHTML = `
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="18" cy="5" r="3"></circle>
                    <circle cx="6" cy="12" r="3"></circle>
                    <circle cx="18" cy="19" r="3"></circle>
                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                </svg>
            `;

            const imageContainer = card.querySelector('.sp-card__image');
            if (imageContainer) {
                imageContainer.appendChild(shareBtn);
            }

            shareBtn.addEventListener('click', async function(e) {
                e.preventDefault();
                e.stopPropagation();

                const title = card.querySelector('.sp-card__title')?.textContent?.trim() || 'Check out this deal';
                const url = `https://www.amazon.com/dp/${asin}?tag=${spData?.affiliateTag || 'shelzysdesigns-20'}`;

                if (navigator.share) {
                    try {
                        await navigator.share({
                            title: title,
                            text: `Found this amazing deal: ${title}`,
                            url: url
                        });
                    } catch (err) {
                        // User cancelled or error
                    }
                } else {
                    // Fallback: copy to clipboard
                    try {
                        await navigator.clipboard.writeText(url);
                        showToast('Link copied to clipboard!');
                    } catch (err) {
                        // Fallback for older browsers
                        const input = document.createElement('input');
                        input.value = url;
                        document.body.appendChild(input);
                        input.select();
                        document.execCommand('copy');
                        document.body.removeChild(input);
                        showToast('Link copied to clipboard!');
                    }
                }
            });
        });
    }

    /* ==========================================================================
       Toast Notification Helper
       ========================================================================== */
    function showToast(message, duration = 3000) {
        const toast = document.createElement('div');
        toast.className = 'sp-toast';
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(function() { toast.classList.add('is-visible'); }, 10);

        setTimeout(function() {
            toast.classList.remove('is-visible');
            setTimeout(function() { toast.remove(); }, 300);
        }, duration);
    }

    // Export showToast for global use
    window.spShowToast = showToast;

})();
