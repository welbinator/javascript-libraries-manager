<?php
/**
 * Plugin Name: JavaScript Libraries Manager – Update Checker
 *
 * @package   JS_Libs_Manager
 * @since     1.1.0
 */

namespace JS_Libs_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Hook the update checker into WordPress' plugin update transient.
 *
 * @param array $transient The pre‑set site transient for plugin updates.
 * @return array Modified transient.
 */
add_filter( 'pre_set_site_transient_update_plugins', __NAMESPACE__ . '\\check_for_updates' );

/**
 * Check GitHub for a newer release of the JavaScript Libraries Manager plugin.
 *
 * This function is called by WordPress on the `pre_set_site_transient_update_plugins`
 * filter. It contacts the GitHub Releases API, compares the latest tagged version
 * with the currently installed version, and – if a newer version exists – adds an
 * update entry to the transient.
 *
 * @param object $transient The transient object WordPress is about to save.
 * @return object The (possibly modified) transient object.
 */
function check_for_updates( $transient ) {

    // ------------------------------------------------------------------
    // 1. Basic sanity checks
    // ------------------------------------------------------------------
    if ( empty( $transient->checked ) || ! is_object( $transient ) ) {
        return $transient;
    }

    // ------------------------------------------------------------------
    // 2. GitHub repository details
    // ------------------------------------------------------------------
    // Change these if you host the plugin in a different repo.
    $owner = 'xAI';
    $repo  = 'javascript-libraries-manager';

    // ------------------------------------------------------------------
    // 3. Build the GitHub API URL
    // ------------------------------------------------------------------
    $api_url = sprintf( 'https://api.github.com/repos/%s/%s/releases/latest', $owner, $repo );

    // ------------------------------------------------------------------
    // 4. Perform the remote request
    // ------------------------------------------------------------------
    $response = wp_remote_get(
        $api_url,
        [
            'timeout'   => 10,
            'sslverify' => true,
            'headers'   => [
                // GitHub requires a User‑Agent header
                'User-Agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . home_url(),
                'Accept'     => 'application/vnd.github.v3+json',
            ],
        ]
    );

    if ( is_wp_error( $response ) ) {
        return $transient;
    }

    $code = wp_remote_retrieve_response_code( $response );
    if ( 200 !== $code ) {
        return $transient;
    }

    $body    = wp_remote_retrieve_body( $response );
    $release = json_decode( $body, true );

    // ------------------------------------------------------------------
    // 5. Validate the release payload
    // ------------------------------------------------------------------
    if ( empty( $release['tag_name'] ) ) {
        return $transient;
    }

    // The first asset is assumed to be the .zip download.
    $assets = $release['assets'] ?? [];
    if ( empty( $assets[0]['browser_download_url'] ) ) {
        return $transient;
    }

    // ------------------------------------------------------------------
    // 6. Determine versions & download URL
    // ------------------------------------------------------------------
    $latest_version = ltrim( $release['tag_name'], 'v' ); // e.g. "1.2.3"
    $download_url   = $assets[0]['browser_download_url'];

    // ------------------------------------------------------------------
    // 7. Get current plugin data
    // ------------------------------------------------------------------
    // __FILE__ points to the main plugin file (js-libs-manager.php) because
    // this file is included from there.
    $plugin_file = JS_LIBS_MANAGER_PLUGIN_PATH . 'js-libs-manager.php';
    $plugin_slug = plugin_basename( $plugin_file );

    if ( ! function_exists( 'get_plugin_data' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $plugin_data     = get_plugin_data( $plugin_file );
    $current_version = $plugin_data['Version'] ?? JS_LIBS_MANAGER_VERSION;

    // ------------------------------------------------------------------
    // 8. Compare versions & populate the transient
    // ------------------------------------------------------------------
    if ( version_compare( $latest_version, $current_version, '>' ) ) {

        $transient->response[ $plugin_slug ] = (object) [
            'slug'        => $plugin_slug,
            'new_version' => $latest_version,
            'url'         => $release['html_url'] ?? '',
            'package'     => $download_url,
            'tested'      => get_bloginfo( 'version' ),
            'requires'    => '6.0',
            'requires_php'=> '7.4',
        ];
    }

    return $transient;
}