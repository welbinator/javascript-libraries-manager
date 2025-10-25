<?php

namespace JS_Libs_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue Floating UI (core + DOM) from CDN.
 *
 * Uses jsDelivr for reliable CORS and version pinning.
 */
function js_libs_manager_enqueue_floatingui() {
    // Core library
    wp_enqueue_script(
        'js-libs-manager-floating-ui-core',
        'https://cdn.jsdelivr.net/npm/@floating-ui/core@1.7.3/dist/floating-ui.core.umd.min.js',
        array(),
        JS_LIBS_MANAGER_VERSION,
        true
    );

    // DOM utilities (depends on core)
    wp_enqueue_script(
        'js-libs-manager-floating-ui-dom',
        'https://cdn.jsdelivr.net/npm/@floating-ui/dom@1.7.4/dist/floating-ui.dom.umd.min.js',
        array( 'js-libs-manager-floating-ui-core' ),
        JS_LIBS_MANAGER_VERSION,
        true
    );
}

// Hook into WordPress
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\js_libs_manager_enqueue_floatingui' );