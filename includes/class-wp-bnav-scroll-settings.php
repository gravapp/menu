<?php

/**
 * Scroll Settings class for WP BNav
 * 
 * @since 1.4.3
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Wp_Bnav_Scroll_Settings {

    /**
     * Initialize the scroll settings
     */
    public static function init() {
        add_action('admin_init', array(__CLASS__, 'register_settings'));
    }

    /**
     * Register all scroll-related settings
     */
    public static function register_settings() {
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
     * Add settings fields for dot scroll indicators
     */
    public static function add_settings_fields($settings_page) {
        // Add section for scroll settings
        add_settings_section(
            'bnav_scroll_section',
            __('Scroll Settings', 'wp-bnav'),
            array(__CLASS__, 'scroll_section_callback'),
            $settings_page
        );

        // Auto-enable scrollbar
        add_settings_field(
            'bnav_auto_enable_scrollbar',
            __('Auto-enable scrollbar', 'wp-bnav'),
            array(__CLASS__, 'auto_enable_scrollbar_field_callback'),
            $settings_page,
            'bnav_scroll_section'
        );

        // Scrollbar threshold
        add_settings_field(
            'bnav_scrollbar_threshold',
            __('Scrollbar threshold', 'wp-bnav'),
            array(__CLASS__, 'scrollbar_threshold_field_callback'),
            $settings_page,
            'bnav_scroll_section'
        );

        // Show scroll indicators
        add_settings_field(
            'bnav_show_scroll_indicators',
            __('Show scroll indicators', 'wp-bnav'),
            array(__CLASS__, 'scroll_indicators_field_callback'),
            $settings_page,
            'bnav_scroll_section'
        );

        // Show scroll dots
        add_settings_field(
            'bnav_show_scroll_dots',
            __('Show scroll dots', 'wp-bnav'),
            array(__CLASS__, 'scroll_dots_field_callback'),
            $settings_page,
            'bnav_scroll_section'
        );
    }

    /**
     * Scroll section description
     */
    public static function scroll_section_callback() {
        echo '<p>' . esc_html__('Configure horizontal scrolling behavior for your mobile menu.', 'wp-bnav') . '</p>';
    }

    /**
     * Auto-enable scrollbar field callback
     */
    public static function auto_enable_scrollbar_field_callback() {
        $value = get_option('bnav_auto_enable_scrollbar', false);
        echo '<input type="checkbox" id="bnav_auto_enable_scrollbar" name="bnav_auto_enable_scrollbar" value="1" ' . checked(1, $value, false) . ' />';
        echo '<label for="bnav_auto_enable_scrollbar">' . esc_html__('Automatically enable scrollbar when more than the threshold number of items are present', 'wp-bnav') . '</label>';
        echo '<p class="description">' . esc_html__('This setting only works when the main scrollbar is enabled.', 'wp-bnav') . '</p>';
    }

    /**
     * Scrollbar threshold field callback
     */
    public static function scrollbar_threshold_field_callback() {
        $value = get_option('bnav_scrollbar_threshold', 5);
        echo '<select id="bnav_scrollbar_threshold" name="bnav_scrollbar_threshold">';
        for ($i = 3; $i <= 10; $i++) {
            echo '<option value="' . esc_attr($i) . '" ' . selected($i, $value, false) . '>' . esc_html($i) . ' ' . esc_html__('items', 'wp-bnav') . '</option>';
        }
        echo '</select>';
        echo '<p class="description">' . esc_html__('Number of menu items before scrollbar is automatically enabled. Only available when auto-enable is active.', 'wp-bnav') . '</p>';
    }

    /**
     * Scroll indicators field callback
     */
    public static function scroll_indicators_field_callback() {
        $value = get_option('bnav_show_scroll_indicators', false);
        echo '<input type="checkbox" id="bnav_show_scroll_indicators" name="bnav_show_scroll_indicators" value="1" ' . checked(1, $value, false) . ' />';
        echo '<label for="bnav_show_scroll_indicators">' . esc_html__('Show left/right arrow indicators when content is scrollable', 'wp-bnav') . '</label>';
        echo '<p class="description">' . esc_html__('Only available when the main scrollbar is enabled.', 'wp-bnav') . '</p>';
    }

    /**
     * Scroll dots field callback
     */
    public static function scroll_dots_field_callback() {
        $value = get_option('bnav_show_scroll_dots', false);
        echo '<input type="checkbox" id="bnav_show_scroll_dots" name="bnav_show_scroll_dots" value="1" ' . checked(1, $value, false) . ' />';
        echo '<label for="bnav_show_scroll_dots">' . esc_html__('Show dot indicators at the bottom to show scroll position', 'wp-bnav') . '</label>';
        echo '<p class="description">' . esc_html__('Click dots to jump to specific scroll positions. Only available when the main scrollbar is enabled.', 'wp-bnav') . '</p>';
    }

    /**
     * Get all scroll-related settings
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
        return max(3, min(10, $value)); // Ensure value is between 3 and 10
    }
}