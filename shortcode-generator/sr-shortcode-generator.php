<?php

//enqueue style
//add_action('admin_enqueue_scripts', 'sr_shortcode_generator_styles_scripts');
//if (!function_exists("sr_shortcode_generator_styles_scripts")) {
//
//    function sr_shortcode_generator_styles_scripts() {
//        wp_enqueue_style('ssign-shortcode-generator', SWIFTREVIEWS__PLUGIN_URL . 'shortcode-generator/css/sr-style-shortcode-generator.css', '', '', '');
//    }
//
//}

// hooks your functions into the correct filters
if (!function_exists("sr_add_mce_dropdown")) {

    function sr_add_mce_dropdown() {
        // check user permissions
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
            return;
        }
        // check if WYSIWYG is enabled
        if ('true' == get_user_option('rich_editing')) {
            add_filter('mce_external_plugins', 'sr_add_tinymce_plugin');
            add_filter('mce_buttons', 'sr_register_mce_button');
        }
    }

}
add_action('admin_head', 'sr_add_mce_dropdown');

/**
 *  register new button in the editor
 */
if (!function_exists("sr_register_mce_button")) {

    function sr_register_mce_button($buttons) {
        array_push($buttons, 'sr_mce_button');
        return $buttons;
    }

}

/*
 *  the script will insert the shortcode on the click event
 */
if (!function_exists("sr_add_tinymce_plugin")) {

    function sr_add_tinymce_plugin($plugin_array) {
        $plugin_array['sr_mce_button'] = SWIFTREVIEWS__PLUGIN_URL . 'shortcode-generator/js/sr-shortcode-generator-script.js';
        return $plugin_array;
    }

}