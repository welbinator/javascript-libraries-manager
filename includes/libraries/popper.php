<?php

namespace JS_Libs_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue Popper.js (v2) from CDN.
 */
function js_libs_manager_enqueue_popper() {
    wp_enqueue_script(
        'js-libs-manager-popper', // Unique handle
        'https://unpkg.com/@popperjs/core@2.11.8/dist/umd/popper.min.js',
        array(),
        JS_LIBS_MANAGER_VERSION,
        true // Load in footer
    );
}

// Hook it up — use namespaced function reference
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\js_libs_manager_enqueue_popper' );