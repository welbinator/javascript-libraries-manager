<?php
if (!defined('ABSPATH')) {
    exit;
}

// Floating UI-specific enqueue (core + DOM UMD builds via cdnjs for CORS)
function js_libs_manager_enqueue_floatingui() {
    // Core
    wp_enqueue_script(
        'floating-ui-core',
        'https://cdn.jsdelivr.net/npm/@floating-ui/core@1.7.3',
        array(),
        JS_LIBS_MANAGER_VERSION,
        true
    );

    // DOM (depends on core)
    wp_enqueue_script(
        'floating-ui-dom',
        'https://cdn.jsdelivr.net/npm/@floating-ui/dom@1.7.4',
        array('floating-ui-core'),
        JS_LIBS_MANAGER_VERSION,
        true
    );
}