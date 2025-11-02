<?php

namespace JS_Libs_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue MicroModal from unpkg CDN.
 *
 * MicroModal is a lightweight, configurable and a11y-enabled modal library.
 * Loads in footer and exposes the global MicroModal object for initialization.
 */
function js_libs_manager_enqueue_micromodal() {
    $handle = 'js-libs-manager-micromodal';

    wp_enqueue_script(
        $handle,
        'https://unpkg.com/micromodal/dist/micromodal.min.js',
        array(),
        JS_LIBS_MANAGER_VERSION,
        true // Load in footer
    );

    // Expose global for easier initialization
    wp_add_inline_script(
        $handle,
        'window.MicroModal = MicroModal;',
        'after'
    );
}