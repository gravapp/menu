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

        // Only run if we're on the settings page
        if ($('#bnav_menu_item_scrollbar').length > 0) {
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
        }
    });

})(jQuery);