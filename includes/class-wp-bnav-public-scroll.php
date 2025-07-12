<?php

/**
 * Public Scroll functionality for WP BNav
 * 
 * @since 1.4.3
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Wp_Bnav_Public_Scroll {

    /**
     * Initialize the public scroll functionality
     */
    public static function init() {
        add_action('wp_footer', array(__CLASS__, 'add_scroll_data_attributes'));
    }

    /**
     * Add scroll-related data attributes to the menu wrapper
     */
    public static function add_scroll_data_attributes() {
        // Only add if we're not in admin
        if (is_admin()) {
            return;
        }

        // Check if scroll settings class exists
        if (!class_exists('Wp_Bnav_Scroll_Settings')) {
            require_once WP_BNAV_PATH . 'includes/class-wp-bnav-scroll-settings.php';
        }
        
        $scroll_settings = Wp_Bnav_Scroll_Settings::get_scroll_settings();
        
        // Add JavaScript to set data attributes
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            var $wrapper = $('.bnav_bottom_nav_wrapper');
            if ($wrapper.length > 0) {
                // Set scroll-related data attributes
                <?php if ($scroll_settings['scrollbar_enabled']): ?>
                $wrapper.attr('data-scrollbar', '1');
                <?php endif; ?>
                
                <?php if ($scroll_settings['auto_enable_scrollbar']): ?>
                $wrapper.attr('data-auto-enable-scrollbar', '1');
                <?php endif; ?>
                
                $wrapper.attr('data-scrollbar-threshold', '<?php echo esc_js($scroll_settings['scrollbar_threshold']); ?>');
                
                <?php if ($scroll_settings['show_scroll_indicators']): ?>
                $wrapper.attr('data-scrollbar-indicators', '1');
                <?php endif; ?>
                
                <?php if ($scroll_settings['show_scroll_dots']): ?>
                $wrapper.attr('data-scrollbar-dots', '1');
                <?php endif; ?>
            }
        });
        </script>
        <?php
    }
}