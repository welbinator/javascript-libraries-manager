<?php
if (!defined('ABSPATH')) {
    exit;
}

// Anime.js-specific enqueue
function js_libs_manager_enqueue_anime() {
    wp_enqueue_script(
        'anime-js',
        'https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js',
        array(),
        JS_LIBS_MANAGER_VERSION,
        true
    );
}