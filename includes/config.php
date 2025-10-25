<?php
if (!defined('ABSPATH')) {
    exit;
}

$js_libs_manager_libraries = array(
    'gsap' => array(
        'label' => 'GSAP (with ScrollTrigger)',
        'enqueue_callback' => 'js_libs_manager_enqueue_gsap',
        'file' => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/gsap.php'
    ),
    'swiper' => array(
        'label' => 'Swiper.js (with CSS)',
        'enqueue_callback' => 'js_libs_manager_enqueue_swiper',
        'file' => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/swiper.php'
    ),
    'anime' => array(
        'label' => 'Anime.js',
        'enqueue_callback' => 'js_libs_manager_enqueue_anime',
        'file' => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/anime.php'
    ),
    // 'lodash' => ... (commented out or remove since we're skipping)
    'chartjs' => array(
        'label' => 'Chart.js',
        'enqueue_callback' => 'js_libs_manager_enqueue_chartjs',
        'file' => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/chartjs.php'
    ),
    // Example for future libs—add here
    /*
    'aos' => array(
        'label' => 'AOS (Animate on Scroll)',
        'enqueue_callback' => 'js_libs_manager_enqueue_aos',
        'file' => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/aos.php'
    ),
    */
);

foreach ($js_libs_manager_libraries as $lib) {
    if (file_exists($lib['file'])) {
        require_once $lib['file'];
    }
}