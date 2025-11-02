<?php
/**
 * Plugin Name: JavaScript Libraries Manager
 * Plugin URI:  https://example.com/js-libs-manager
 * Description: A simple WordPress plugin to manage and enqueue various JavaScript libraries (GSAP, Swiper, Popper, etc.).
 * Version:     1.1.6
 * Author:      James Welbes
 * Author URI:  https://jameswelbes.com
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: js-libs-manager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Prevent direct access.
}

/**
 * ----------------------------------------------------------------------
 *  CONSTANTS
 * ----------------------------------------------------------------------
 */
define( 'JS_LIBS_MANAGER_VERSION', '1.1.6' );
define( 'JS_LIBS_MANAGER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'JS_LIBS_MANAGER_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define('YT_FOR_WP_MIN_WP_VERSION', '5.8');
define('YT_FOR_WP_MIN_PHP_VERSION', '7.4');

/**
 * ----------------------------------------------------------------------
 *  AUTOLOAD INCLUDES
 * ----------------------------------------------------------------------
 */
require_once JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/config.php';
require_once JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/admin.php';
require_once JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/frontend.php';
include JS_LIBS_MANAGER_PLUGIN_PATH . 'github-update.php';

/**
 * ----------------------------------------------------------------------
 *  REGISTER CUSTOM TAXONOMY FOR PER-PAGE LIBRARY SELECTION
 * ----------------------------------------------------------------------
 */
register_taxonomy( 'js_library', ['post', 'page'], [
    'label'        => __( 'JS Libraries', 'js-libs-manager' ),
    'public'       => false,
    'show_ui'      => true,
    'show_in_rest' => true, 
    'hierarchical' => false,
    'rewrite'      => false,
]);