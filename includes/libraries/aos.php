<?php

namespace JS_Libs_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue AOS (CSS + JS) from unpkg.
 *
 * Users will initialize AOS themselves with their own JS.
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
        true
    );

    // Expose global AOS for user scripts
    wp_add_inline_script(
        'js-libs-manager-aos',
        'window.AOS = AOS;',
        'after'
    );
}