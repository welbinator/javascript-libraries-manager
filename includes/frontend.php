<?php
/**
 * Frontend script enqueuing logic.
 *
 * Handles both global and per-page library enabling.
 *
 * @package JS_Libs_Manager
 */

namespace JS_Libs_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue JavaScript libraries based on global + per-page settings.
 *
 * Priority:
 * 1. If a library is enabled in Settings → load site-wide.
 * 2. If NOT enabled globally → check if it's selected in the "JS Libraries" panel for this post/page.
 *
 * This ensures global settings override per-page, and per-page is a fallback.
 */
function enqueue_enabled_libraries() {
    // Bail early on non-singular pages if you want to restrict (optional)
    // if ( ! is_singular() ) return;

    $all_libs     = get_registered_libraries();
    $global_libs  = get_option( 'js_libs_manager_enabled_libs', [] );
    $post_libs    = [];

    // Only fetch per-page libraries if we're on a singular page/post
    if ( is_singular() ) {
        $post_libs = wp_get_post_terms(
            get_the_ID(),
            'js_library',
            [ 'fields' => 'slugs' ]
        );
        // Ensure it's an array
        if ( is_wp_error( $post_libs ) ) {
            $post_libs = [];
        }
    }

    $libs_to_load = [];

    foreach ( $all_libs as $slug => $lib ) {
        $callback = $lib['enqueue_callback'] ?? null;
        if ( ! is_callable( $callback ) ) {
            continue;
        }

        $should_load = false;

        // Rule 1: Global ON → always load
        if ( in_array( $slug, (array) $global_libs, true ) ) {
            $should_load = true;
        }
        // Rule 2: Global OFF → load only if per-page checked
        elseif ( empty( $global_libs ) && in_array( $slug, $post_libs, true ) ) {
            $should_load = true;
        }

        if ( $should_load ) {
            $libs_to_load[] = $slug;
            call_user_func( $callback );
        }
    }

    // Optional: Debug (remove in production)
    // error_log( 'JS Libs Loaded: ' . implode( ', ', $libs_to_load ) );
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_enabled_libraries', 20 );