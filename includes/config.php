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
    'chartjs' => array(
        'label' => 'Chart.js',
        'enqueue_callback' => 'js_libs_manager_enqueue_chartjs',
        'file' => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/chartjs.php'
    ),
    'popper' => array(
        'label' => 'Popper.js (v2)',
        'enqueue_callback' => 'js_libs_manager_enqueue_popper',
        'file' => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/popper.php'
    ),
    'floatingui' => array(
        'label' => 'Floating UI (DOM)',
        'enqueue_callback' => 'js_libs_manager_enqueue_floatingui',
        'file' => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/floatingui.php'
    ),
   
);

foreach ($js_libs_manager_libraries as $lib) {
    if (file_exists($lib['file'])) {
        require_once $lib['file'];
    }
}