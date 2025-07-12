<?php

/**
 * Settings class for WP BNav
 */
class Wp_Bnav_Settings {

    /**
     * Add settings fields for dot scroll indicators
     */
    public static function add_scroll_settings_fields($settings_page) {
        // Add section for scroll settings
        add_settings_section(
            'bnav_scroll_section',
            __('Scroll Settings', 'wp-bnav'),
            array(__CLASS__, 'scroll_section_callback'),
            $settings_page
        );

        // Menu item scrollbar (existing setting - enhanced)
        add_settings_field(
            'bnav_menu_item_scrollbar',
            __('Menu item scrollbar', 'wp-bnav'),
            array(__CLASS__, 'scrollbar_field_callback'),
            $settings_page,
            'bnav_scroll_section'
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
        echo '<p>' . __('Configure horizontal scrolling behavior for your mobile menu.', 'wp-bnav') . '</p>';
    }

    /**
     * Scrollbar field callback
     */
    public static function scrollbar_field_callback() {
        $value = get_option('bnav_menu_item_scrollbar', false);
        echo '<input type="checkbox" id="bnav_menu_item_scrollbar" name="bnav_menu_item_scrollbar" value="1" ' . checked(1, $value, false) . ' />';
        echo '<label for="bnav_menu_item_scrollbar">' . __('Enable horizontal scrolling when menu items exceed the container width', 'wp-bnav') . '</label>';
    }

    /**
     * Auto-enable scrollbar field callback
     */
    public static function auto_enable_scrollbar_field_callback() {
        $value = get_option('bnav_auto_enable_scrollbar', false);
        echo '<input type="checkbox" id="bnav_auto_enable_scrollbar" name="bnav_auto_enable_scrollbar" value="1" ' . checked(1, $value, false) . ' />';
        echo '<label for="bnav_auto_enable_scrollbar">' . __('Automatically enable scrollbar when more than the threshold number of items are present', 'wp-bnav') . '</label>';
        echo '<p class="description">' . __('This setting only works when the main scrollbar is enabled.', 'wp-bnav') . '</p>';
    }

    /**
     * Scrollbar threshold field callback
     */
    public static function scrollbar_threshold_field_callback() {
        $value = get_option('bnav_scrollbar_threshold', 5);
        echo '<select id="bnav_scrollbar_threshold" name="bnav_scrollbar_threshold">';
        for ($i = 3; $i <= 10; $i++) {
            echo '<option value="' . $i . '" ' . selected($i, $value, false) . '>' . $i . ' ' . __('items', 'wp-bnav') . '</option>';
        }
        echo '</select>';
        echo '<p class="description">' . __('Number of menu items before scrollbar is automatically enabled. Only available when auto-enable is active.', 'wp-bnav') . '</p>';
    }

    /**
     * Scroll indicators field callback
     */
    public static function scroll_indicators_field_callback() {
        $value = get_option('bnav_show_scroll_indicators', false);
        echo '<input type="checkbox" id="bnav_show_scroll_indicators" name="bnav_show_scroll_indicators" value="1" ' . checked(1, $value, false) . ' />';
        echo '<label for="bnav_show_scroll_indicators">' . __('Show left/right arrow indicators when content is scrollable', 'wp-bnav') . '</label>';
        echo '<p class="description">' . __('Only available when the main scrollbar is enabled.', 'wp-bnav') . '</p>';
    }

    /**
     * Scroll dots field callback
     */
    public static function scroll_dots_field_callback() {
        $value = get_option('bnav_show_scroll_dots', false);
        echo '<input type="checkbox" id="bnav_show_scroll_dots" name="bnav_show_scroll_dots" value="1" ' . checked(1, $value, false) . ' />';
        echo '<label for="bnav_show_scroll_dots">' . __('Show dot indicators at the bottom to show scroll position', 'wp-bnav') . '</label>';
        echo '<p class="description">' . __('Click dots to jump to specific scroll positions. Only available when the main scrollbar is enabled.', 'wp-bnav') . '</p>';
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
}