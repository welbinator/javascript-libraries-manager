<?php
if (!defined('ABSPATH')) {
    exit;
}

// Swiper-specific enqueue (JS + CSS)
function js_libs_manager_enqueue_swiper() {
    // Enqueue Swiper CSS
    wp_enqueue_style(
        'swiper-css',
        'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css',
        array(),
        JS_LIBS_MANAGER_VERSION
    );

    // Enqueue Swiper JS bundle
    wp_enqueue_script(
        'swiper-js',
        'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js',
        array(),
        JS_LIBS_MANAGER_VERSION,
        true
    );
}