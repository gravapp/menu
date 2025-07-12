<?php
/**
 * Dot Scroll Indicators for WP BNav
 * 
 * @since 1.4.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Wp_Bnav_Dots {

    /**
     * Initialize the dots functionality
     */
    public static function init() {
        // Only run on frontend
        if (!is_admin()) {
            add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_assets'));
            add_action('wp_footer', array(__CLASS__, 'add_dots_functionality'));
        }
        
        // Admin settings
        if (is_admin()) {
            add_action('admin_init', array(__CLASS__, 'register_settings'));
        }
    }

    /**
     * Register settings
     */
    public static function register_settings() {
        // Only register if settings group exists
        if (get_option('bnav_settings_group') !== false || current_user_can('manage_options')) {
            register_setting('bnav_settings_group', 'bnav_show_scroll_dots', array(
                'type' => 'boolean',
                'default' => false,
                'sanitize_callback' => array(__CLASS__, 'sanitize_boolean')
            ));
            
            register_setting('bnav_settings_group', 'bnav_auto_enable_scrollbar', array(
                'type' => 'boolean', 
                'default' => false,
                'sanitize_callback' => array(__CLASS__, 'sanitize_boolean')
            ));
            
            register_setting('bnav_settings_group', 'bnav_scrollbar_threshold', array(
                'type' => 'integer',
                'default' => 5,
                'sanitize_callback' => array(__CLASS__, 'sanitize_threshold')
            ));
        }
    }

    /**
     * Enqueue CSS and JS
     */
    public static function enqueue_assets() {
        wp_enqueue_style(
            'wp-bnav-dots',
            plugin_dir_url(__FILE__) . '../public/css/wp-bnav-dots.css',
            array(),
            WP_BNAV_VERSION
        );

        wp_enqueue_script(
            'wp-bnav-dots',
            plugin_dir_url(__FILE__) . '../public/js/wp-bnav-dots.js',
            array('jquery'),
            WP_BNAV_VERSION,
            true
        );

        // Pass settings to JavaScript
        wp_localize_script('wp-bnav-dots', 'bnavDots', array(
            'showDots' => get_option('bnav_show_scroll_dots', false),
            'autoEnable' => get_option('bnav_auto_enable_scrollbar', false),
            'threshold' => get_option('bnav_scrollbar_threshold', 5),
            'scrollbarEnabled' => get_option('bnav_menu_item_scrollbar', false)
        ));
    }

    /**
     * Add dots functionality to footer
     */
    public static function add_dots_functionality() {
        $show_dots = get_option('bnav_show_scroll_dots', false);
        $scrollbar_enabled = get_option('bnav_menu_item_scrollbar', false);
        
        if (!$show_dots && !$scrollbar_enabled) {
            return;
        }
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Add data attributes for dots functionality
            var $wrapper = $('.bnav_bottom_nav_wrapper');
            if ($wrapper.length > 0) {
                <?php if ($show_dots): ?>
                $wrapper.attr('data-show-dots', '1');
                <?php endif; ?>
                
                <?php if (get_option('bnav_auto_enable_scrollbar', false)): ?>
                $wrapper.attr('data-auto-enable', '1');
                <?php endif; ?>
                
                $wrapper.attr('data-threshold', '<?php echo esc_js(get_option('bnav_scrollbar_threshold', 5)); ?>');
            }
        });
        </script>
        <?php
    }

    /**
     * Sanitize boolean values
     */
    public static function sanitize_boolean($value) {
        return (bool) $value;
    }

    /**
     * Sanitize threshold value
     */
    public static function sanitize_threshold($value) {
        $value = intval($value);
        return max(3, min(10, $value));
    }
}