<?php
/**
 * Config file – registers every library and loads its definition file.
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
 *   - label            : Human-readable name (admin UI)
 *   - enqueue_callback : Function that enqueues the script (must be in the same namespace)
 *   - file             : Path to the library-specific PHP file
 */
$js_libs_manager_libraries = [
    'gsap' => [
        'label'            => __( 'GSAP (with ScrollTrigger)', 'js-libs-manager' ),
        'enqueue_callback' => 'js_libs_manager_enqueue_gsap',
        'file'             => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/gsap.php',
    ],
    'swiper' => [
        'label'            => __( 'Swiper.js (with CSS)', 'js-libs-manager' ),
        'enqueue_callback' => 'js_libs_manager_enqueue_swiper',
        'file'             => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/swiper.php',
    ],
    'anime' => [
        'label'            => __( 'Anime.js', 'js-libs-manager' ),
        'enqueue_callback' => 'js_libs_manager_enqueue_anime',
        'file'             => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/anime.php',
    ],
    'chartjs' => [
        'label'            => __( 'Chart.js', 'js-libs-manager' ),
        'enqueue_callback' => 'js_libs_manager_enqueue_chartjs',
        'file'             => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/chartjs.php',
    ],
    'popper' => [
        'label'            => __( 'Popper.js (v2)', 'js-libs-manager' ),
        'enqueue_callback' => 'js_libs_manager_enqueue_popper',
        'file'             => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/popper.php',
    ],
    'floatingui' => [
        'label'            => __( 'Floating UI (DOM)', 'js-libs-manager' ),
        'enqueue_callback' => 'js_libs_manager_enqueue_floatingui',
        'file'             => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/floatingui.php',
    ],
];

/**
 * Load each library file **once** – the file itself will contain the
 * enqueue function in the same namespace.
 */
foreach ( $js_libs_manager_libraries as $lib ) {
    if ( file_exists( $lib['file'] ) ) {
        require_once $lib['file'];
    }
}

/* ----------------------------------------------------------------------
 *  OPTIONAL: expose the array to other parts of the plugin (admin, etc.)
 * ---------------------------------------------------------------------- */
function get_registered_libraries(): array {
    global $js_libs_manager_libraries;
    return $js_libs_manager_libraries;
}