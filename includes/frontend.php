<?php
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue scripts based on settings
function js_libs_manager_enqueue_scripts() {
    global $js_libs_manager_libraries;
    $enabled_libs = get_option('js_libs_manager_enabled_libs', array());

    foreach ($enabled_libs as $lib_slug) {
        if (isset($js_libs_manager_libraries[$lib_slug]['enqueue_callback'])) {
            $callback = $js_libs_manager_libraries[$lib_slug]['enqueue_callback'];
            if (function_exists($callback)) {
                call_user_func($callback);
            }
        }
    }
}
add_action('wp_enqueue_scripts', 'js_libs_manager_enqueue_scripts');