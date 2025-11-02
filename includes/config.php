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
    'a11y-dialog' => [
        'label'            => __( 'A11y Dialog', 'js-libs-manager' ),
        'enqueue_callback' => __NAMESPACE__ . '\\js_libs_manager_enqueue_a11y_dialog',
        'file'             => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/a11y-dialog.php',
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
    'sortable' => [
        'label'            => __( 'Sortable.js', 'js-libs-manager' ),
        'enqueue_callback' => __NAMESPACE__ . '\\js_libs_manager_enqueue_sortable',
        'file'             => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/sortable.php',
    ],
    'fontawesome' => [
        'label'            => __( 'Font Awesome (Kit)', 'js-libs-manager' ),
        'enqueue_callback' => __NAMESPACE__ . '\\js_libs_manager_enqueue_fontawesome',
        'file'             => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/fontawesome.php',
    ],
        'aos' => [
        'label'            => __( 'AOS (Animate On Scroll)', 'js-libs-manager' ),
        'enqueue_callback' => __NAMESPACE__ . '\\js_libs_manager_enqueue_aos',
        'file'             => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/aos.php',
    ],
    'micromodal' => [
        'label'            => __( 'MicroModal', 'js-libs-manager' ),
        'enqueue_callback' => __NAMESPACE__ . '\\js_libs_manager_enqueue_micromodal',
        'file'             => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/micromodal.php',
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
/**
 * Auto-detect changes to the registered libraries and re-create missing terms.
 *
 * Previously this compared against the plugin version which required bumping
 * the version to trigger a resync. Instead we compute a signature of the
 * registered libraries (labels + optional taxonomy_slug) and store it. When
 * the signature changes we create any missing terms without deleting existing
 * terms (safe default).
 */
add_action( 'admin_init', function() {
    $libraries = get_registered_libraries();

    // Build a compact signature from labels + taxonomy_slug (if present).
    $parts = array_map( function( $lib ) {
        return ($lib['label'] ?? '') . '|' . ($lib['taxonomy_slug'] ?? '');
    }, $libraries );

    $signature = md5( serialize( $parts ) );
    $stored    = get_option( 'js_libs_manager_libraries_signature' );

    if ( $stored !== $signature ) {
        // Only create missing terms — do not delete existing terms so we
        // preserve user term assignments.
        create_library_taxonomy_terms();

        // Persist signature and a terms_synced marker (for compatibility).
        update_option( 'js_libs_manager_libraries_signature', $signature );
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

// to use this, visit /wp-admin/options-general.php?recreate_terms=1

add_action('admin_init', function() {
    if ( isset( $_GET['recreate_terms'] ) ) {
        // Ensure only admins can invoke this.
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized', 'js-libs-manager' ), '', 403 );
        }

        // If ?recreate_terms=1&force=1 then delete all existing terms first.
        $force = isset( $_GET['force'] ) && $_GET['force'] === '1';

        if ( $force ) {
            $terms = get_terms( [ 'taxonomy' => 'js_library', 'hide_empty' => false, 'fields' => 'ids' ] );
            foreach ( (array) $terms as $id ) {
                wp_delete_term( $id, 'js_library' );
            }
        }

        // Create missing terms (safe). If force was used terms were deleted
        // above so this will re-create them.
        create_library_taxonomy_terms();

        // Update signature so automatic resync doesn't immediately re-run.
        $libraries = get_registered_libraries();
        $parts     = array_map( function( $lib ) {
            return ($lib['label'] ?? '') . '|' . ($lib['taxonomy_slug'] ?? '');
        }, $libraries );
        $signature = md5( serialize( $parts ) );
        update_option( 'js_libs_manager_libraries_signature', $signature );

        wp_die( 'Terms recreated' . ( $force ? ' (force delete used)' : '' ) );
    }
});