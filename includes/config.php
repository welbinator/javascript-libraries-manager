<?php
/**
 * Config file – registers every JavaScript library and loads its definition file.
 *
 * Also handles:
 * - Auto-creation of 'js_library' taxonomy terms
 * - Exposing library data to admin, frontend, and taxonomy system
 *
 * @package JS_Libs_Manager
 */

namespace JS_Libs_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ----------------------------------------------------------------------
 *  LIBRARY DEFINITIONS
 * ----------------------------------------------------------------------
 *
 * Each entry:
 *   - label            : Human-readable name (admin UI + taxonomy term)
 *   - enqueue_callback : Fully qualified callable (namespaced function)
 *   - file             : Path to the library-specific PHP file
 *   - taxonomy_slug    : (optional) Custom slug for taxonomy term
 */
$js_libs_manager_libraries = [
    'gsap' => [
        'label'            => __( 'GSAP (with ScrollTrigger)', 'js-libs-manager' ),
        'enqueue_callback' => __NAMESPACE__ . '\\js_libs_manager_enqueue_gsap',
        'file'             => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/gsap.php',
    ],
    'swiper' => [
        'label'            => __( 'Swiper.js (with CSS)', 'js-libs-manager' ),
        'enqueue_callback' => __NAMESPACE__ . '\\js_libs_manager_enqueue_swiper',
        'file'             => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/swiper.php',
    ],
    'anime' => [
        'label'            => __( 'Anime.js', 'js-libs-manager' ),
        'enqueue_callback' => __NAMESPACE__ . '\\js_libs_manager_enqueue_anime',
        'file'             => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/anime.php',
    ],
    'chartjs' => [
        'label'            => __( 'Chart.js', 'js-libs-manager' ),
        'enqueue_callback' => __NAMESPACE__ . '\\js_libs_manager_enqueue_chartjs',
        'file'             => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/chartjs.php',
    ],
    'popper' => [
        'label'            => __( 'Popper.js (v2)', 'js-libs-manager' ),
        'enqueue_callback' => __NAMESPACE__ . '\\js_libs_manager_enqueue_popper',
        'file'             => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/popper.php',
    ],
    'floatingui' => [
        'label'            => __( 'Floating UI (DOM)', 'js-libs-manager' ),
        'enqueue_callback' => __NAMESPACE__ . '\\js_libs_manager_enqueue_floatingui',
        'file'             => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/floatingui.php',
    ],
    'embla' => [
        'label'            => __( 'Embla Carousel (with CSS)', 'js-libs-manager' ),
        'enqueue_callback' => __NAMESPACE__ . '\\js_libs_manager_enqueue_embla',
        'file'             => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/embla.php',
    ],
];

/**
 * Load each library file once.
 */
foreach ( $js_libs_manager_libraries as $lib ) {
    if ( file_exists( $lib['file'] ) ) {
        require_once $lib['file'];
    }
}

/**
 * Expose registered libraries to the rest of the plugin.
 *
 * @return array
 */
function get_registered_libraries(): array {
    global $js_libs_manager_libraries;
    return $js_libs_manager_libraries;
}


/**
 * Auto-create taxonomy terms for each library using full labels.
 *
 * Uses the library's label as the display name and a sanitized version as the slug.
 * This ensures "Most Used" and autocomplete show the full human-readable name.
 */
function create_library_taxonomy_terms() {
    $libraries = get_registered_libraries();

    foreach ( $libraries as $original_slug => $lib ) {
        $term_name = $lib['label']; // e.g., "GSAP (with ScrollTrigger)"

        // Generate safe slug from label
        $term_slug = sanitize_title( $term_name ); // "gsap-with-scrolltrigger"

        // Allow override via 'taxonomy_slug' (optional)
        if ( ! empty( $lib['taxonomy_slug'] ) ) {
            $term_slug = sanitize_title( $lib['taxonomy_slug'] );
        }

        // Check by slug to avoid duplicates
        if ( ! term_exists( $term_slug, 'js_library' ) ) {
            $result = wp_insert_term(
                $term_name,
                'js_library',
                [
                    'slug'        => $term_slug,
                    'description' => sprintf(
                        /* translators: %s = library name */
                        __( 'Enables %s on this page when not globally active.', 'js-libs-manager' ),
                        $term_name
                    ),
                ]
            );

            // Optional: log errors in debug mode
            if ( is_wp_error( $result ) && WP_DEBUG ) {
                error_log( 'JS Libs Manager: Failed to create term "' . $term_name . '": ' . $result->get_error_message() );
            }
        }
    }
}

// Hook into plugin activation
register_activation_hook( JS_LIBS_MANAGER_PLUGIN_PATH . 'js-libs-manager.php', __NAMESPACE__ . '\\create_library_taxonomy_terms' );

/**
 * Optional: Re-sync terms on save (e.g., when adding new library in code)
 */
add_action( 'admin_init', function() {
    if ( get_option( 'js_libs_manager_terms_synced' ) !== JS_LIBS_MANAGER_VERSION ) {
        create_library_taxonomy_terms();
        update_option( 'js_libs_manager_terms_synced', JS_LIBS_MANAGER_VERSION );
    }
});

/**
 * Replace "Most Used" term names with full labels.
 */
add_filter( 'get_terms', function( $terms, $taxonomies, $args ) {
    if ( ! is_admin() || ! in_array( 'js_library', (array) $taxonomies, true ) ) {
        return $terms;
    }

    // Only modify when fetching most used
    if ( ! empty( $args['fields'] ) && $args['fields'] === 'ids' ) {
        return $terms;
    }

    foreach ( $terms as $term ) {
        if ( isset( $term->name ) && isset( $term->slug ) ) {
            $libraries = \JS_Libs_Manager\get_registered_libraries();
            foreach ( $libraries as $lib ) {
                $expected_slug = sanitize_title( $lib['label'] );
                if ( $term->slug === $expected_slug ) {
                    $term->name = $lib['label'];
                    break;
                }
            }
        }
    }

    return $terms;
}, 10, 3 );