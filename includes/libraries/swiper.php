<?php

namespace JS_Libs_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue Swiper.js (bundle) + CSS from jsDelivr.
 *
 * Uses official bundle for full features (navigation, pagination, autoplay, etc.).
 */
function js_libs_manager_enqueue_swiper() {
    // Swiper CSS
    wp_enqueue_style(
        'js-libs-manager-swiper-css',
        'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css',
        array(),
        JS_LIBS_MANAGER_VERSION
    );

    // Swiper JS (bundle) – loads in footer
    wp_enqueue_script(
        'js-libs-manager-swiper',
        'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js',
        array(),
        JS_LIBS_MANAGER_VERSION,
        true
    );

    // Ensure global `Swiper` is available before inline scripts
    wp_add_inline_script(
        'js-libs-manager-swiper',
        'window.Swiper = Swiper;',
        'before'
    );
}

