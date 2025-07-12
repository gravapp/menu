<?php

/**
 * Scroll Manager for WP BNav - Handles all scroll functionality
 * 
 * @since 1.4.3
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Wp_Bnav_Scroll_Manager {

    /**
     * Initialize the scroll manager
     */
    public static function init() {
        // Hook into WordPress
        add_action('admin_init', array(__CLASS__, 'register_settings'));
        add_action('wp_footer', array(__CLASS__, 'add_scroll_functionality'));
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_scroll_assets'));
    }

    /**
     * Register scroll settings
     */
    public static function register_settings() {
        // Only register if we're in admin and the settings group exists
        if (!is_admin()) {
            return;
        }

        // Show scroll dots setting
        register_setting(
            'bnav_settings_group',
            'bnav_show_scroll_dots',
            array(
                'type' => 'boolean',
                'default' => false,
                'sanitize_callback' => array(__CLASS__, 'sanitize_boolean')
            )
        );

        // Auto-enable scrollbar setting
        register_setting(
            'bnav_settings_group',
            'bnav_auto_enable_scrollbar',
            array(
                'type' => 'boolean',
                'default' => false,
                'sanitize_callback' => array(__CLASS__, 'sanitize_boolean')
            )
        );

        // Scrollbar threshold setting
        register_setting(
            'bnav_settings_group',
            'bnav_scrollbar_threshold',
            array(
                'type' => 'integer',
                'default' => 5,
                'sanitize_callback' => array(__CLASS__, 'sanitize_threshold')
            )
        );

        // Show scroll indicators setting
        register_setting(
            'bnav_settings_group',
            'bnav_show_scroll_indicators',
            array(
                'type' => 'boolean',
                'default' => false,
                'sanitize_callback' => array(__CLASS__, 'sanitize_boolean')
            )
        );
    }

    /**
     * Add settings fields to existing settings page
     */
    public static function add_settings_fields() {
        // This will be called by the main plugin if needed
        add_settings_section(
            'bnav_scroll_section',
            __('Scroll Settings', 'wp-bnav'),
            array(__CLASS__, 'scroll_section_callback'),
            'bnav_settings'
        );

        // Auto-enable scrollbar
        add_settings_field(
            'bnav_auto_enable_scrollbar',
            __('Auto-enable scrollbar', 'wp-bnav'),
            array(__CLASS__, 'auto_enable_scrollbar_field'),
            'bnav_settings',
            'bnav_scroll_section'
        );

        // Scrollbar threshold
        add_settings_field(
            'bnav_scrollbar_threshold',
            __('Scrollbar threshold', 'wp-bnav'),
            array(__CLASS__, 'scrollbar_threshold_field'),
            'bnav_settings',
            'bnav_scroll_section'
        );

        // Show scroll indicators
        add_settings_field(
            'bnav_show_scroll_indicators',
            __('Show scroll indicators', 'wp-bnav'),
            array(__CLASS__, 'scroll_indicators_field'),
            'bnav_settings',
            'bnav_scroll_section'
        );

        // Show scroll dots
        add_settings_field(
            'bnav_show_scroll_dots',
            __('Show scroll dots', 'wp-bnav'),
            array(__CLASS__, 'scroll_dots_field'),
            'bnav_settings',
            'bnav_scroll_section'
        );
    }

    /**
     * Scroll section callback
     */
    public static function scroll_section_callback() {
        echo '<p>' . esc_html__('Configure horizontal scrolling behavior for your mobile menu.', 'wp-bnav') . '</p>';
    }

    /**
     * Auto-enable scrollbar field
     */
    public static function auto_enable_scrollbar_field() {
        $value = get_option('bnav_auto_enable_scrollbar', false);
        echo '<input type="checkbox" id="bnav_auto_enable_scrollbar" name="bnav_auto_enable_scrollbar" value="1" ' . checked(1, $value, false) . ' />';
        echo '<label for="bnav_auto_enable_scrollbar">' . esc_html__('Automatically enable scrollbar when more than the threshold number of items are present', 'wp-bnav') . '</label>';
    }

    /**
     * Scrollbar threshold field
     */
    public static function scrollbar_threshold_field() {
        $value = get_option('bnav_scrollbar_threshold', 5);
        echo '<select id="bnav_scrollbar_threshold" name="bnav_scrollbar_threshold">';
        for ($i = 3; $i <= 10; $i++) {
            echo '<option value="' . esc_attr($i) . '" ' . selected($i, $value, false) . '>' . esc_html($i) . ' ' . esc_html__('items', 'wp-bnav') . '</option>';
        }
        echo '</select>';
        echo '<p class="description">' . esc_html__('Number of menu items before scrollbar is automatically enabled.', 'wp-bnav') . '</p>';
    }

    /**
     * Scroll indicators field
     */
    public static function scroll_indicators_field() {
        $value = get_option('bnav_show_scroll_indicators', false);
        echo '<input type="checkbox" id="bnav_show_scroll_indicators" name="bnav_show_scroll_indicators" value="1" ' . checked(1, $value, false) . ' />';
        echo '<label for="bnav_show_scroll_indicators">' . esc_html__('Show left/right arrow indicators when content is scrollable', 'wp-bnav') . '</label>';
    }

    /**
     * Scroll dots field
     */
    public static function scroll_dots_field() {
        $value = get_option('bnav_show_scroll_dots', false);
        echo '<input type="checkbox" id="bnav_show_scroll_dots" name="bnav_show_scroll_dots" value="1" ' . checked(1, $value, false) . ' />';
        echo '<label for="bnav_show_scroll_dots">' . esc_html__('Show dot indicators for scroll position', 'wp-bnav') . '</label>';
    }

    /**
     * Enqueue scroll assets
     */
    public static function enqueue_scroll_assets() {
        // Only enqueue on frontend
        if (is_admin()) {
            return;
        }

        // Enqueue the scroll CSS
        wp_enqueue_style(
            'wp-bnav-scroll',
            plugin_dir_url(__FILE__) . '../public/css/wp-bnav-scroll.css',
            array(),
            WP_BNAV_VERSION
        );

        // Enqueue the scroll JS
        wp_enqueue_script(
            'wp-bnav-scroll',
            plugin_dir_url(__FILE__) . '../public/js/wp-bnav-scroll.js',
            array('jquery'),
            WP_BNAV_VERSION,
            true
        );

        // Pass settings to JavaScript
        wp_localize_script('wp-bnav-scroll', 'bnavScrollSettings', array(
            'scrollbar_enabled' => get_option('bnav_menu_item_scrollbar', false),
            'auto_enable_scrollbar' => get_option('bnav_auto_enable_scrollbar', false),
            'scrollbar_threshold' => get_option('bnav_scrollbar_threshold', 5),
            'show_scroll_indicators' => get_option('bnav_show_scroll_indicators', false),
            'show_scroll_dots' => get_option('bnav_show_scroll_dots', false)
        ));
    }

    /**
     * Add scroll functionality to footer
     */
    public static function add_scroll_functionality() {
        // Only add on frontend
        if (is_admin()) {
            return;
        }

        $settings = array(
            'scrollbar_enabled' => get_option('bnav_menu_item_scrollbar', false),
            'auto_enable_scrollbar' => get_option('bnav_auto_enable_scrollbar', false),
            'scrollbar_threshold' => get_option('bnav_scrollbar_threshold', 5),
            'show_scroll_indicators' => get_option('bnav_show_scroll_indicators', false),
            'show_scroll_dots' => get_option('bnav_show_scroll_dots', false)
        );

        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            var $wrapper = $('.bnav_bottom_nav_wrapper');
            if ($wrapper.length > 0) {
                // Set data attributes for JavaScript
                <?php if ($settings['scrollbar_enabled']): ?>
                $wrapper.attr('data-scrollbar', '1');
                <?php endif; ?>
                
                <?php if ($settings['auto_enable_scrollbar']): ?>
                $wrapper.attr('data-auto-enable-scrollbar', '1');
                <?php endif; ?>
                
                $wrapper.attr('data-scrollbar-threshold', '<?php echo esc_js($settings['scrollbar_threshold']); ?>');
                
                <?php if ($settings['show_scroll_indicators']): ?>
                $wrapper.attr('data-scrollbar-indicators', '1');
                <?php endif; ?>
                
                <?php if ($settings['show_scroll_dots']): ?>
                $wrapper.attr('data-scrollbar-dots', '1');
                <?php endif; ?>
            }
        });
        </script>
        <?php
    }

    /**
     * Get all scroll settings
     */
    public static function get_scroll_settings() {
        return array(
            'scrollbar_enabled' => get_option('bnav_menu_item_scrollbar', false),
            'auto_enable_scrollbar' => get_option('bnav_auto_enable_scrollbar', false),
            'scrollbar_threshold' => get_option('bnav_scrollbar_threshold', 5),
            'show_scroll_indicators' => get_option('bnav_show_scroll_indicators', false),
            'show_scroll_dots' => get_option('bnav_show_scroll_dots', false)
        );
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