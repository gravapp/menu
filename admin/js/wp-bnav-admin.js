/**
 * Admin JavaScript for WP BNav
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Handle scroll dots setting visibility
        function toggleScrollDotsSettings() {
            const scrollbarEnabled = $('#bnav_menu_item_scrollbar').is(':checked');
            const scrollDotsRow = $('#bnav_show_scroll_dots').closest('tr');
            
            if (scrollbarEnabled) {
                scrollDotsRow.show();
            } else {
                scrollDotsRow.hide();
                $('#bnav_show_scroll_dots').prop('checked', false);
            }
        }

        // Handle auto-enable scrollbar setting visibility
        function toggleAutoEnableSettings() {
            const scrollbarEnabled = $('#bnav_menu_item_scrollbar').is(':checked');
            const autoEnableRow = $('#bnav_auto_enable_scrollbar').closest('tr');
            const thresholdRow = $('#bnav_scrollbar_threshold').closest('tr');
            
            if (scrollbarEnabled) {
                autoEnableRow.show();
                
                // Show threshold only if auto-enable is checked
                if ($('#bnav_auto_enable_scrollbar').is(':checked')) {
                    thresholdRow.show();
                } else {
                    thresholdRow.hide();
                }
            } else {
                autoEnableRow.hide();
                thresholdRow.hide();
                $('#bnav_auto_enable_scrollbar').prop('checked', false);
            }
        }

        // Handle scroll indicators setting visibility
        function toggleScrollIndicatorsSettings() {
            const scrollbarEnabled = $('#bnav_menu_item_scrollbar').is(':checked');
            const indicatorsRow = $('#bnav_show_scroll_indicators').closest('tr');
            
            if (scrollbarEnabled) {
                indicatorsRow.show();
            } else {
                indicatorsRow.hide();
                $('#bnav_show_scroll_indicators').prop('checked', false);
            }
        }

        // Initial setup
        toggleScrollDotsSettings();
        toggleAutoEnableSettings();
        toggleScrollIndicatorsSettings();

        // Event handlers
        $('#bnav_menu_item_scrollbar').on('change', function() {
            toggleScrollDotsSettings();
            toggleAutoEnableSettings();
            toggleScrollIndicatorsSettings();
        });

        $('#bnav_auto_enable_scrollbar').on('change', function() {
            const thresholdRow = $('#bnav_scrollbar_threshold').closest('tr');
            if ($(this).is(':checked')) {
                thresholdRow.show();
            } else {
                thresholdRow.hide();
            }
        });

        // Live preview for customizer
        if (typeof wp !== 'undefined' && wp.customize) {
            // Scroll dots setting
            wp.customize('bnav_show_scroll_dots', function(value) {
                value.bind(function(newval) {
                    if (newval) {
                        $('.bnav_bottom_nav_wrapper').attr('data-scrollbar-dots', '1');
                    } else {
                        $('.bnav_bottom_nav_wrapper').removeAttr('data-scrollbar-dots');
                        $('.scroll-dots-container').remove();
                    }
                    // Trigger scroll functionality update
                    $(window).trigger('resize');
                });
            });

            // Auto-enable scrollbar setting
            wp.customize('bnav_auto_enable_scrollbar', function(value) {
                value.bind(function(newval) {
                    if (newval) {
                        $('.bnav_bottom_nav_wrapper').attr('data-auto-enable-scrollbar', '1');
                    } else {
                        $('.bnav_bottom_nav_wrapper').removeAttr('data-auto-enable-scrollbar');
                    }
                    $(window).trigger('resize');
                });
            });

            // Scrollbar threshold setting
            wp.customize('bnav_scrollbar_threshold', function(value) {
                value.bind(function(newval) {
                    $('.bnav_bottom_nav_wrapper').attr('data-scrollbar-threshold', newval);
                    $(window).trigger('resize');
                });
            });
        }
    });

})(jQuery);