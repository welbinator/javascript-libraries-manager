<?php

namespace JS_Libs_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue Anime.js from Cloudflare CDN.
 *
 * Uses official minified build that exposes global `anime` function.
 */
function js_libs_manager_enqueue_anime() {
    wp_enqueue_script(
        'js-libs-manager-anime',
        'https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js',
        array(),
        JS_LIBS_MANAGER_VERSION,
        true // Load in footer
    );

    // Ensure global `anime` is available even if UMD wrapper is delayed
    wp_add_inline_script(
        'js-libs-manager-anime',
        'window.anime = anime;',
        'after'
    );
}

