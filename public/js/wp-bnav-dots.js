/**
 * Dot Scroll Indicators JavaScript
 */
(function($) {
    'use strict';

    class BNavDots {
        constructor() {
            this.init();
        }

        init() {
            $(document).ready(() => {
                this.setupDots();
                this.bindEvents();
            });
        }

        setupDots() {
            const self = this;
            
            $('.bnav_bottom_nav_wrapper').each(function() {
                const $wrapper = $(this);
                const $menu = $wrapper.find('.bnav_main_menu');
                
                if ($menu.length === 0) return;
                
                self.initializeDots($wrapper, $menu);
            });
        }

        initializeDots($wrapper, $menu) {
            // Get settings
            const showDots = $wrapper.data('show-dots') == 1 || (typeof bnavDots !== 'undefined' && bnavDots.showDots);
            const autoEnable = $wrapper.data('auto-enable') == 1 || (typeof bnavDots !== 'undefined' && bnavDots.autoEnable);
            const threshold = parseInt($wrapper.data('threshold')) || (typeof bnavDots !== 'undefined' ? bnavDots.threshold : 5);
            const scrollbarEnabled = typeof bnavDots !== 'undefined' ? bnavDots.scrollbarEnabled : false;

            const menuItems = $menu.find('li').length;
            const containerWidth = $menu.outerWidth();
            const totalWidth = $menu[0] ? $menu[0].scrollWidth : 0;
            const needsScrolling = totalWidth > containerWidth;

            // Determine if we should show dots
            let shouldShowDots = false;
            
            if (scrollbarEnabled && showDots && needsScrolling) {
                shouldShowDots = true;
            } else if (autoEnable && menuItems >= threshold && needsScrolling) {
                shouldShowDots = true;
                $wrapper.addClass('has-dots');
            }

            if (shouldShowDots) {
                this.createDots($wrapper, $menu);
                this.setupScrolling($wrapper, $menu);
            } else {
                this.removeDots($wrapper);
            }
        }

        createDots($wrapper, $menu) {
            // Remove existing dots
            $wrapper.find('.bnav-dots-container').remove();

            const containerWidth = $menu.outerWidth();
            const totalWidth = $menu[0].scrollWidth;
            
            if (totalWidth <= containerWidth) return;

            // Calculate number of dots (max 5)
            const numDots = Math.min(Math.ceil(totalWidth / containerWidth), 5);
            
            const $dotsContainer = $('<div class="bnav-dots-container"></div>');
            
            for (let i = 0; i < numDots; i++) {
                const $dot = $('<button class="bnav-scroll-dot" data-index="' + i + '" aria-label="Scroll to section ' + (i + 1) + '"></button>');
                $dotsContainer.append($dot);
            }
            
            $wrapper.append($dotsContainer);
            
            // Show dots with animation
            setTimeout(() => {
                $dotsContainer.addClass('visible');
            }, 100);

            // Handle dot clicks
            $dotsContainer.on('click', '.bnav-scroll-dot', (e) => {
                e.preventDefault();
                const index = parseInt($(e.target).data('index'));
                this.scrollToSection($menu, index, numDots);
            });

            // Update active dot on scroll
            $menu.on('scroll', () => {
                this.updateActiveDot($wrapper, $menu);
            });

            // Set initial active dot
            this.updateActiveDot($wrapper, $menu);
        }

        setupScrolling($wrapper, $menu) {
            $wrapper.addClass('has-dots');
            
            // Add keyboard navigation
            $menu.attr('tabindex', '0');
            $menu.on('keydown', (e) => {
                switch(e.key) {
                    case 'ArrowLeft':
                        e.preventDefault();
                        $menu.animate({ scrollLeft: $menu.scrollLeft() - 50 }, 200);
                        break;
                    case 'ArrowRight':
                        e.preventDefault();
                        $menu.animate({ scrollLeft: $menu.scrollLeft() + 50 }, 200);
                        break;
                    case 'Home':
                        e.preventDefault();
                        $menu.animate({ scrollLeft: 0 }, 300);
                        break;
                    case 'End':
                        e.preventDefault();
                        $menu.animate({ scrollLeft: $menu[0].scrollWidth }, 300);
                        break;
                }
            });

            // Add touch support
            this.addTouchSupport($menu);
        }

        addTouchSupport($menu) {
            let startX = 0;
            let scrollLeft = 0;
            let isDown = false;

            $menu.on('touchstart mousedown', (e) => {
                isDown = true;
                startX = (e.type === 'touchstart') ? e.touches[0].pageX : e.pageX;
                scrollLeft = $menu.scrollLeft();
                $menu.css('scroll-behavior', 'auto');
            });

            $menu.on('touchmove mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = (e.type === 'touchmove') ? e.touches[0].pageX : e.pageX;
                const walk = (x - startX) * 2;
                $menu.scrollLeft(scrollLeft - walk);
            });

            $menu.on('touchend mouseup mouseleave', () => {
                isDown = false;
                $menu.css('scroll-behavior', 'smooth');
            });
        }

        scrollToSection($menu, index, totalSections) {
            const containerWidth = $menu.outerWidth();
            const totalWidth = $menu[0].scrollWidth;
            const sectionWidth = totalWidth / totalSections;
            const targetScroll = Math.min(index * sectionWidth, totalWidth - containerWidth);
            
            $menu.animate({ scrollLeft: targetScroll }, 400);
        }

        updateActiveDot($wrapper, $menu) {
            const $dots = $wrapper.find('.bnav-scroll-dot');
            if ($dots.length === 0) return;

            const scrollLeft = $menu.scrollLeft();
            const containerWidth = $menu.outerWidth();
            const totalWidth = $menu[0].scrollWidth;
            const maxScroll = totalWidth - containerWidth;
            
            if (maxScroll <= 0) return;
            
            const scrollPercentage = scrollLeft / maxScroll;
            const activeIndex = Math.round(scrollPercentage * ($dots.length - 1));

            $dots.removeClass('active');
            $dots.eq(activeIndex).addClass('active');
        }

        removeDots($wrapper) {
            $wrapper.find('.bnav-dots-container').remove();
            $wrapper.removeClass('has-dots');
        }

        bindEvents() {
            const self = this;
            
            // Reinitialize on window resize
            $(window).on('resize', this.debounce(() => {
                self.setupDots();
            }, 250));
        }

        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    }

    // Initialize
    new BNavDots();

})(jQuery);