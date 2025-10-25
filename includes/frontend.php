<?php

namespace JS_Libs_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue only the enabled JavaScript libraries on the frontend.
 *
 * This runs after admin settings are loaded and respects user choices.
 */
function enqueue_enabled_libraries() {
    $enabled_libs = get_option( 'js_libs_manager_enabled_libs', [] );
    $all_libs     = get_registered_libraries(); // From config.php

    // Bail early if no libraries enabled
    if ( empty( $enabled_libs ) || ! is_array( $enabled_libs ) ) {
        return;
    }

    foreach ( $enabled_libs as $lib_slug ) {
        $lib_slug = sanitize_key( $lib_slug );

        if ( ! isset( $all_libs[ $lib_slug ]['enqueue_callback'] ) ) {
            continue;
        }

        $callback = $all_libs[ $lib_slug ]['enqueue_callback'];

        // Ensure callback is valid and namespaced
        if ( is_callable( $callback ) ) {
            call_user_func( $callback );
        }
    }
}

// Hook with priority 20 to run after other enqueues if needed
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_enabled_libraries', 20 );