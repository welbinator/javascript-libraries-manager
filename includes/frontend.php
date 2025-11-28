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

    // Global ON → load
    if ( in_array( $slug, (array) $global_libs, true ) ) {
        $should_load = true;
    }
    // Global OFF → check per-page for this specific library
    elseif ( is_singular() ) {
        $term_slug = sanitize_title( $lib['label'] ); // "sortable-js"
        if ( in_array( $term_slug, (array) $post_libs, true ) ) {
            $should_load = true;
        }
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

/**
 * Enqueue libraries into Etch Builder Preview canvas.
 *
 * Enqueues both globally-enabled libraries and per-page selections when Etch option is enabled.
 */
function enqueue_etch_canvas_libraries() {
    // Check if Etch enqueuing is enabled
    if ( ! get_option( 'js_libs_manager_enqueue_in_etch', false ) ) {
        return;
    }

    $all_libs    = get_registered_libraries();
    $global_libs = get_option( 'js_libs_manager_enabled_libs', [] );
    $page_libs   = [];
    
    // Get per-page Etch preview selections
    $post_id = get_the_ID();
    if ( $post_id ) {
        $page_libs = get_post_meta( $post_id, '_js_libs_manager_etch_preview', true );
        $page_libs = is_array( $page_libs ) ? $page_libs : [];
    }
    
    // Merge global and per-page selections (unique)
    $libs_to_load = array_unique( array_merge( (array) $global_libs, $page_libs ) );

    // Enqueue selected libraries
    foreach ( $all_libs as $slug => $lib ) {
        if ( ! in_array( $slug, $libs_to_load, true ) ) {
            continue;
        }

        $callback = $lib['enqueue_callback'] ?? null;
        if ( is_callable( $callback ) ) {
            call_user_func( $callback );
        }
    }
}

add_action( 'etch/canvas/enqueue_assets', __NAMESPACE__ . '\\enqueue_etch_canvas_libraries' );

/**
 * Add stylesheets to Etch Builder Preview canvas.
 *
 * Collects CSS files from globally-enabled libraries and per-page selections.
 */
function add_etch_canvas_stylesheets( $stylesheets ) {
    // Check if Etch enqueuing is enabled
    if ( ! get_option( 'js_libs_manager_enqueue_in_etch', false ) ) {
        return $stylesheets;
    }

    $all_libs    = get_registered_libraries();
    $global_libs = get_option( 'js_libs_manager_enabled_libs', [] );
    $page_libs   = [];
    
    // Get per-page Etch preview selections
    $post_id = get_the_ID();
    if ( $post_id ) {
        $page_libs = get_post_meta( $post_id, '_js_libs_manager_etch_preview', true );
        $page_libs = is_array( $page_libs ) ? $page_libs : [];
    }
    
    // Merge global and per-page selections (unique)
    $libs_to_load = array_unique( array_merge( (array) $global_libs, $page_libs ) );

    // Collect registered styles from selected libraries
    foreach ( $all_libs as $slug => $lib ) {
        if ( ! in_array( $slug, $libs_to_load, true ) ) {
            continue;
        }

        // Trigger the enqueue callback to register styles
        $callback = $lib['enqueue_callback'] ?? null;
        if ( is_callable( $callback ) ) {
            call_user_func( $callback );
        }
    }

    // Now collect all registered styles and add to Etch
    global $wp_styles;
    if ( ! isset( $wp_styles ) ) {
        return $stylesheets;
    }

    foreach ( $wp_styles->registered as $handle => $style ) {
        // Only include styles registered by this plugin
        if ( strpos( $handle, 'js-libs-manager-' ) !== 0 ) {
            continue;
        }

        $stylesheets[] = [
            'id'  => $handle,
            'url' => $style->src,
        ];
    }

    return $stylesheets;
}

add_filter( 'etch/canvas/additional_stylesheets', __NAMESPACE__ . '\\add_etch_canvas_stylesheets' );