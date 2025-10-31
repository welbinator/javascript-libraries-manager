<?php

namespace JS_Libs_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue AOS (Animate On Scroll) CSS + JS.
 *
 * Exposes global `AOS` object.
 */
function js_libs_manager_enqueue_aos() {
    // AOS CSS
    wp_enqueue_style(
        'js-libs-manager-aos-css',
        'https://unpkg.com/aos@2.3.1/dist/aos.css',
        array(),
        '2.3.1'
    );

    // AOS JS
    wp_enqueue_script(
        'js-libs-manager-aos',
        'https://unpkg.com/aos@2.3.1/dist/aos.js',
        array(),
        '2.3.1',
        true // Load in footer
    );

    // Expose global AOS (required for init)
    wp_add_inline_script(
        'js-libs-manager-aos',
        'window.AOS = AOS;',
        'after'
    );

    // Initialize AOS after load
    wp_add_inline_script(
        'js-libs-manager-aos',
        'document.addEventListener("DOMContentLoaded", () => { if (typeof AOS !== "undefined") AOS.init(); });',
        'after'
    );
}