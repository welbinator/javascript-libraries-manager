<?php

namespace JS_Libs_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue Embla Carousel (JS + CSS) from jsDelivr.
 *
 * Uses the official UMD bundle for global `emblaCarousel` constructor.
 * Includes CSS for basic styling (optional but recommended).
 */
function js_libs_manager_enqueue_embla() {
   

    // Embla JS (bundle) – loads in footer
    wp_enqueue_script(
        'js-libs-manager-embla',
        'https://unpkg.com/embla-carousel/embla-carousel.umd.js',
        array(),
        JS_LIBS_MANAGER_VERSION,
        true
    );

    wp_enqueue_script(
        'js-libs-manager-embla-autoplay',
        'https://unpkg.com/embla-carousel-autoplay/embla-carousel-autoplay.umd.js',
        array('js-libs-manager-embla'),
        JS_LIBS_MANAGER_VERSION,
        true
    );

     wp_enqueue_script(
        'js-libs-manager-embla-autoplay',
        'https://unpkg.com/embla-carousel-auto-scroll/embla-carousel-auto-scroll.umd.js',
        array('js-libs-manager-embla'),
        JS_LIBS_MANAGER_VERSION,
        true
    );

    wp_enqueue_script(
        'js-libs-manager-embla-autoplay',
        'https://unpkg.com/embla-carousel-auto-height/embla-carousel-auto-height.umd.js',
        array('js-libs-manager-embla'),
        JS_LIBS_MANAGER_VERSION,
        true
    );

    wp_enqueue_script(
        'js-libs-manager-embla-autoplay',
        'https://unpkg.com/embla-carousel-class-names/embla-carousel-class-names.umd.js',
        array('js-libs-manager-embla'),
        JS_LIBS_MANAGER_VERSION,
        true
    );

    wp_enqueue_script(
        'js-libs-manager-embla-autoplay',
        'https://unpkg.com/embla-carousel-fade/embla-carousel-fade.umd.js',
        array('js-libs-manager-embla'),
        JS_LIBS_MANAGER_VERSION,
        true
    );

     wp_enqueue_script(
        'js-libs-manager-embla-autoplay',
        'https://unpkg.com/embla-carousel-wheel-gestures/dist/embla-carousel-wheel-gestures.umd.js',
        array('js-libs-manager-embla'),
        JS_LIBS_MANAGER_VERSION,
        true
    );

    // Ensure global `emblaCarousel` is available before inline scripts
    wp_add_inline_script(
        'js-libs-manager-embla',
        'window.emblaCarousel = emblaCarousel;',
        'before'
    );
}

// NO add_action() here! – Controlled by frontend.php