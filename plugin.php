<?php
/**
 * Plugin Name:       Event Tickets Extension: Promoter WP Admin Integration
 * Plugin URI:        https://theeventscalendar.com/extensions/promoter-wp-admin-integration/
 * GitHub Plugin URI: https://github.com/mt-support/tribe-ext-promoter-wp-admin-integration
 * Description:       Just a little extension to bring quick access to your Promoter account from the Event Tickets admin pages
 * Version:           1.0.1
 * Author:            Modern Tribe, Inc.
 * Author URI:        http://m.tri.be/1971
 * License:           GPL version 3 or any later version
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       tribe-ext-promoter-wp-admin-integration
 *
 *     This plugin is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     any later version.
 *
 *     This plugin is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *     GNU General Public License for more details.
 */

/**
 * Define the base file that loaded the plugin for determining plugin path and other variables.
 *
 * @since 1.0.0
 *
 * @var string Base file that loaded the plugin.
 */
define( 'TRIBE_EXTENSION_PROMOTER_WP_ADMIN_INTEGRATION_FILE', __FILE__ );

/**
 * Register and load the service provider for loading the extension.
 *
 * @since 1.0.0
 */
function tribe_extension_promoter_wp_admin_integration() {
	// When we dont have autoloader from common we bail.
	if  ( ! class_exists( 'Tribe__Autoloader' ) ) {
		return;
	}

	// Register the namespace so we can the plugin on the service provider registration.
	Tribe__Autoloader::instance()->register_prefix(
		'\\Tribe\\Extensions\\Promoter_WP_Admin_Integration\\',
		__DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Tribe',
		'tribe-ext-promoter-wp-admin-integration'
	);
	
	if ( ! class_exists( '\Tribe\Extensions\Promoter_WP_Admin_Integration\Plugin' ) ) {
		tribe_transient_notice(
			'ext-autoload-error-promoter-wp-admin-integration',
			'<p>' . esc_html__( 'Version incompatibility for "Event Tickets Extension: Promoter WP Admin Integration" the extension was disabled.', 'tribe-ext-promoter-wp-admin-integration' ) . '</p>'
		);
		
		if ( ! function_exists( 'deactivate_plugins' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		deactivate_plugins( __FILE__, true );
		
		return;
	}
	
	tribe_register_provider( '\Tribe\Extensions\Promoter_WP_Admin_Integration\Plugin' );
}

// Loads after common is already properly loaded.
add_action( 'tribe_common_loaded', 'tribe_extension_promoter_wp_admin_integration' );
