/**
 * Scroll functionality for WP BNav
 */
(function($) {
    'use strict';

    // Utility function for debouncing
    function debounce(func, wait) {
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

    class BNavScrollManager {
        constructor() {
            this.init();
        }

        init() {
            // Wait for DOM to be ready
            $(document).ready(() => {
                this.setupScrollFunctionality();
                this.bindEvents();
            });
        }

        setupScrollFunctionality() {
            const self = this;
            
            $('.bnav_bottom_nav_wrapper').each(function() {
                const $wrapper = $(this);
                const $menuList = $wrapper.find('ul.bnav_main_menu');
                
                if ($menuList.length === 0) return;

                self.initializeScrollFeatures($wrapper, $menuList);
            });
        }

        initializeScrollFeatures($wrapper, $menuList) {
            // Get settings from data attributes
            const scrollbarEnabled = $wrapper.data('scrollbar') == 1;
            const autoEnableScrollbar = $wrapper.data('auto-enable-scrollbar') == 1;
            const scrollbarThreshold = parseInt($wrapper.data('scrollbar-threshold')) || 5;
            const showScrollDots = $wrapper.data('scrollbar-dots') == 1;
            const showScrollIndicators = $wrapper.data('scrollbar-indicators') == 1;

            const menuItemCount = $menuList.find('li').length;
            
            // Check if content overflows
            const containerWidth = $menuList.outerWidth();
            const totalWidth = $menuList[0] ? $menuList[0].scrollWidth : 0;
            const isOverflowing = totalWidth > containerWidth;

            // Determine if scrolling should be enabled
            let enableScrolling = scrollbarEnabled;
            if (autoEnableScrollbar && menuItemCount >= scrollbarThreshold) {
                enableScrolling = true;
            }

            if (enableScrolling && isOverflowing) {
                $wrapper.addClass('scrollable');
                $menuList.css({
                    'overflow-x': 'auto',
                    'overflow-y': 'hidden',
                    'scroll-behavior': 'smooth'
                });

                // Add scroll indicators
                if (showScrollIndicators) {
                    this.addScrollIndicators($wrapper, $menuList);
                }

                // Add dot indicators
                if (showScrollDots) {
                    this.addDotIndicators($wrapper, $menuList);
                }

                // Add keyboard navigation
                this.addKeyboardNavigation($wrapper, $menuList);

                // Add touch support
                this.addTouchSupport($wrapper, $menuList);
            } else {
                $wrapper.removeClass('scrollable');
                $menuList.css({
                    'overflow-x': 'visible',
                    'overflow-y': 'visible'
                });
                this.removeScrollFeatures($wrapper);
            }
        }

        addDotIndicators($wrapper, $menuList) {
            // Remove existing dots
            $wrapper.find('.scroll-dots-container').remove();

            const containerWidth = $menuList.outerWidth();
            const totalWidth = $menuList[0].scrollWidth;
            
            if (totalWidth <= containerWidth) return;

            // Calculate number of dots based on scroll sections
            const numDots = Math.min(Math.ceil(totalWidth / containerWidth), 5);
            
            const $dotsContainer = $('<div class="scroll-dots-container"></div>');
            
            for (let i = 0; i < numDots; i++) {
                const $dot = $('<button class="scroll-dot" data-index="' + i + '" aria-label="Scroll to section ' + (i + 1) + '"></button>');
                $dotsContainer.append($dot);
            }
            
            $wrapper.append($dotsContainer);

            // Set initial active dot
            this.updateActiveDot($wrapper, $menuList);

            // Handle dot clicks
            $dotsContainer.on('click', '.scroll-dot', (e) => {
                e.preventDefault();
                const index = parseInt($(e.target).data('index'));
                this.scrollToSection($menuList, index, numDots);
            });

            // Update active dot on scroll
            $menuList.on('scroll', () => {
                this.updateActiveDot($wrapper, $menuList);
            });
        }

        addScrollIndicators($wrapper, $menuList) {
            // Remove existing indicators
            $wrapper.find('.scroll-indicator').remove();

            const $leftIndicator = $('<div class="scroll-indicator scroll-indicator-left" aria-hidden="true">‹</div>');
            const $rightIndicator = $('<div class="scroll-indicator scroll-indicator-right" aria-hidden="true">›</div>');

            $wrapper.append($leftIndicator, $rightIndicator);

            // Handle indicator clicks
            $leftIndicator.on('click', () => {
                $menuList.animate({ scrollLeft: $menuList.scrollLeft() - 100 }, 300);
            });

            $rightIndicator.on('click', () => {
                $menuList.animate({ scrollLeft: $menuList.scrollLeft() + 100 }, 300);
            });

            // Update indicator visibility
            this.updateScrollIndicators($wrapper, $menuList);
            $menuList.on('scroll', () => {
                this.updateScrollIndicators($wrapper, $menuList);
            });
        }

        addKeyboardNavigation($wrapper, $menuList) {
            $menuList.attr('tabindex', '0');
            
            $menuList.on('keydown', (e) => {
                switch(e.key) {
                    case 'ArrowLeft':
                        e.preventDefault();
                        $menuList.animate({ scrollLeft: $menuList.scrollLeft() - 50 }, 200);
                        break;
                    case 'ArrowRight':
                        e.preventDefault();
                        $menuList.animate({ scrollLeft: $menuList.scrollLeft() + 50 }, 200);
                        break;
                    case 'Home':
                        e.preventDefault();
                        $menuList.animate({ scrollLeft: 0 }, 300);
                        break;
                    case 'End':
                        e.preventDefault();
                        $menuList.animate({ scrollLeft: $menuList[0].scrollWidth }, 300);
                        break;
                }
            });
        }

        addTouchSupport($wrapper, $menuList) {
            let startX = 0;
            let scrollLeft = 0;
            let isDown = false;

            $menuList.on('touchstart mousedown', (e) => {
                isDown = true;
                startX = (e.type === 'touchstart') ? e.touches[0].pageX : e.pageX;
                scrollLeft = $menuList.scrollLeft();
                $menuList.css('scroll-behavior', 'auto');
            });

            $menuList.on('touchmove mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = (e.type === 'touchmove') ? e.touches[0].pageX : e.pageX;
                const walk = (x - startX) * 2;
                $menuList.scrollLeft(scrollLeft - walk);
            });

            $menuList.on('touchend mouseup mouseleave', () => {
                isDown = false;
                $menuList.css('scroll-behavior', 'smooth');
            });
        }

        scrollToSection($menuList, index, totalSections) {
            const containerWidth = $menuList.outerWidth();
            const totalWidth = $menuList[0].scrollWidth;
            const sectionWidth = totalWidth / totalSections;
            const targetScroll = Math.min(index * sectionWidth, totalWidth - containerWidth);
            
            $menuList.animate({ scrollLeft: targetScroll }, 400);
        }

        updateActiveDot($wrapper, $menuList) {
            const $dots = $wrapper.find('.scroll-dot');
            if ($dots.length === 0) return;

            const scrollLeft = $menuList.scrollLeft();
            const containerWidth = $menuList.outerWidth();
            const totalWidth = $menuList[0].scrollWidth;
            const maxScroll = totalWidth - containerWidth;
            
            if (maxScroll <= 0) return;
            
            const scrollPercentage = scrollLeft / maxScroll;
            const activeIndex = Math.round(scrollPercentage * ($dots.length - 1));

            $dots.removeClass('active');
            $dots.eq(activeIndex).addClass('active');
        }

        updateScrollIndicators($wrapper, $menuList) {
            const $leftIndicator = $wrapper.find('.scroll-indicator-left');
            const $rightIndicator = $wrapper.find('.scroll-indicator-right');
            
            const scrollLeft = $menuList.scrollLeft();
            const maxScroll = $menuList[0].scrollWidth - $menuList.outerWidth();

            // Show/hide left indicator
            if (scrollLeft > 10) {
                $leftIndicator.addClass('visible');
            } else {
                $leftIndicator.removeClass('visible');
            }

            // Show/hide right indicator
            if (scrollLeft < maxScroll - 10) {
                $rightIndicator.addClass('visible');
            } else {
                $rightIndicator.removeClass('visible');
            }
        }

        removeScrollFeatures($wrapper) {
            $wrapper.find('.scroll-dots-container').remove();
            $wrapper.find('.scroll-indicator').remove();
        }

        bindEvents() {
            const self = this;
            
            // Reinitialize on window resize
            $(window).on('resize', debounce(() => {
                self.setupScrollFunctionality();
            }, 250));
        }
    }

    // Initialize when DOM is ready
    new BNavScrollManager();

})(jQuery);