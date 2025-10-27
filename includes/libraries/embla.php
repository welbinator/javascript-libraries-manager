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
        'js-libs-manager-embla-auto-scroll',
        'https://unpkg.com/embla-carousel-auto-scroll/embla-carousel-auto-scroll.umd.js',
        array('js-libs-manager-embla'),
        JS_LIBS_MANAGER_VERSION,
        true
    );

    wp_enqueue_script(
        'js-libs-manager-embla-auto-height',
        'https://unpkg.com/embla-carousel-auto-height/embla-carousel-auto-height.umd.js',
        array('js-libs-manager-embla'),
        JS_LIBS_MANAGER_VERSION,
        true
    );

    wp_enqueue_script(
        'js-libs-manager-embla-class-names',
        'https://unpkg.com/embla-carousel-class-names/embla-carousel-class-names.umd.js',
        array('js-libs-manager-embla'),
        JS_LIBS_MANAGER_VERSION,
        true
    );

    wp_enqueue_script(
        'js-libs-manager-embla-fade',
        'https://unpkg.com/embla-carousel-fade/embla-carousel-fade.umd.js',
        array('js-libs-manager-embla'),
        JS_LIBS_MANAGER_VERSION,
        true
    );

     wp_enqueue_script(
        'js-libs-manager-embla-wheel-gestures',
        'https://unpkg.com/embla-carousel-wheel-gestures/dist/embla-carousel-wheel-gestures.umd.js',
        array('js-libs-manager-embla'),
        JS_LIBS_MANAGER_VERSION,
        true
    );

    wp_add_inline_script(
        'js-libs-manager-embla',
        '(function() { window.emblaCarousel = this.emblaCarousel; }).call(window);',
        'after'
    );

     // EXPOSE EACH PLUGIN
    wp_add_inline_script('js-libs-manager-embla-autoplay', 'window.EmblaCarouselAutoplay = EmblaCarouselAutoplay;', 'after');
    wp_add_inline_script('js-libs-manager-embla-auto-scroll', 'window.EmblaCarouselAutoScroll = EmblaCarouselAutoScroll;', 'after');
    wp_add_inline_script('js-libs-manager-embla-auto-height', 'window.EmblaCarouselAutoHeight = EmblaCarouselAutoHeight;', 'after');
    wp_add_inline_script('js-libs-manager-embla-class-names', 'window.EmblaCarouselClassNames = EmblaCarouselClassNames;', 'after');
    wp_add_inline_script('js-libs-manager-embla-fade', 'window.EmblaCarouselFade = EmblaCarouselFade;', 'after');
    wp_add_inline_script('js-libs-manager-embla-wheel-gestures', 'window.EmblaCarouselWheelGestures = EmblaCarouselWheelGestures;', 'after');
}

