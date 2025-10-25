<?php

namespace JS_Libs_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue Chart.js (UMD build) from jsDelivr.
 *
 * Uses the official UMD build for global `Chart` constructor.
 */
function js_libs_manager_enqueue_chartjs() {
    wp_enqueue_script(
        'js-libs-manager-chartjs',
        'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
        array(),
        JS_LIBS_MANAGER_VERSION,
        true // Load in footer
    );

    // Optional: Register Chart.js global for inline scripts
    wp_add_inline_script(
        'js-libs-manager-chartjs',
        'window.Chart = Chart;', // Ensure global access
        'before'
    );
}

// Hook into WordPress
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\js_libs_manager_enqueue_chartjs' );