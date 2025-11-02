<?php

namespace JS_Libs_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue A11y Dialog from unpkg CDN.
 *
 * Uses a deferred script by adding the 'defer' attribute. Loads in footer
 * to follow other libraries' pattern. No inline global exposure is added —
 * consumer code should reference the library's API as documented.
 */
function js_libs_manager_enqueue_a11y_dialog() {
    $handle = 'js-libs-manager-a11y-dialog';

    wp_enqueue_script(
        $handle,
        'https://unpkg.com/a11y-dialog@8/dist/a11y-dialog.min.js',
        array(),
        JS_LIBS_MANAGER_VERSION,
        true
    );

    // Add `defer` attribute (matches upstream recommended snippet)
    wp_script_add_data( $handle, 'defer', true );
}
