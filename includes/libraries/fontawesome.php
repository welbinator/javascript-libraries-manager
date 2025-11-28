<?php

namespace JS_Libs_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue Font Awesome kit (user-provided kit URL or script tag).
 *
 * The admin stores the sanitized kit URL in the `js_libs_manager_fontawesome_kit`
 * option. This function will enqueue the kit in the <head> (not footer),
 * and add `crossorigin="anonymous"` which is recommended by Font Awesome.
 */
function js_libs_manager_enqueue_fontawesome() {
    $kit = get_option( 'js_libs_manager_fontawesome_kit', '' );
    if ( empty( $kit ) ) {
        return;
    }

    // $kit is stored as a sanitized URL (see admin sanitize callback).
    $handle = 'js-libs-manager-fontawesome-kit';

    // Check if we're in Etch canvas context
    $is_etch_canvas = did_action( 'etch/canvas/enqueue_assets' ) || doing_action( 'etch/canvas/enqueue_assets' );
    
    // Skip normal enqueue in Etch context - Font Awesome will be handled separately
    // to prevent Etch from appending query parameters that cause 403 errors
    if ( $is_etch_canvas ) {
        return;
    }
    
    // Enqueue in the head (in_footer = false)
    wp_enqueue_script( $handle, $kit, array(), JS_LIBS_MANAGER_VERSION, false );

    // Many kits require crossorigin attribute
    wp_script_add_data( $handle, 'crossorigin', 'anonymous' );
}
