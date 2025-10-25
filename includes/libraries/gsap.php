<?php
if (!defined('ABSPATH')) {
    exit;
}

// GSAP-specific enqueue
function js_libs_manager_enqueue_gsap() {
    // Enqueue GSAP core
    wp_enqueue_script(
        'gsap-js',
        'https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/gsap.min.js',
        array(),
        JS_LIBS_MANAGER_VERSION,
        true
    );

    // Enqueue ScrollTrigger with GSAP as dependency
    wp_enqueue_script(
        'gsap-st',
        'https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/ScrollTrigger.min.js',
        array('gsap-js'),
        JS_LIBS_MANAGER_VERSION,
        true
    );
}