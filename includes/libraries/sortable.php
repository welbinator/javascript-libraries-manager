<?php

namespace JS_Libs_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue Sortable.js from jsDelivr.
 *
 * Users will write their own inline JS using the global `Sortable`.
 */
function js_libs_manager_enqueue_sortable() {
    wp_enqueue_script(
        'js-libs-manager-sortable',
        'https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js',
        array(),
        JS_LIBS_MANAGER_VERSION,
        true
    );

    // Expose global Sortable for user scripts
    wp_add_inline_script(
        'js-libs-manager-sortable',
        'window.Sortable = Sortable;',
        'after'
    );
}