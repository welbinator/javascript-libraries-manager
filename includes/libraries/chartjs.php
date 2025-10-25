<?php
if (!defined('ABSPATH')) {
    exit;
}

// Chart.js-specific enqueue
function js_libs_manager_enqueue_chartjs() {
    wp_enqueue_script(
        'chart-js',
        'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
        array(),
        JS_LIBS_MANAGER_VERSION,
        true
    );
}