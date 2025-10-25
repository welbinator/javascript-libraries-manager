<?php
if (!defined('ABSPATH')) {
    exit;
}

// Popper.js-specific enqueue
function js_libs_manager_enqueue_popper() {
    wp_enqueue_script(
        'popper-js',
        'https://unpkg.com/@popperjs/core@2.11.8/dist/umd/popper.min.js',
        array(),
        JS_LIBS_MANAGER_VERSION,
        true
    );
}