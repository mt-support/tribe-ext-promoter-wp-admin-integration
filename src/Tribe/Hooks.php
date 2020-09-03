<?php
/**
 * Handles hooking all the actions and filters used by the module.
 *
 * To remove a filter:
 * ```php
 *  remove_filter( 'some_filter', [ tribe( Tribe\Extensions\Promoter_WP_Admin_Integration\Hooks::class ), 'some_filtering_method' ] );
 *  remove_filter( 'some_filter', [ tribe( 'events-virtual.hooks' ), 'some_filtering_method' ] );
 * ```
 *
 * To remove an action:
 * ```php
 *  remove_action( 'some_action', [ tribe( Tribe\Extensions\Promoter_WP_Admin_Integration\Hooks::class ), 'some_method' ] );
 *  remove_action( 'some_action', [ tribe( 'events-virtual.hooks' ), 'some_method' ] );
 * ```
 *
 * @since   1.0.0
 *
 * @package Tribe\Extensions\Promoter_WP_Admin_Integration;
 */

namespace Tribe\Extensions\Promoter_WP_Admin_Integration;

/**
 * Class Hooks.
 *
 * @since   1.0.0
 *
 * @package Tribe\Extensions\Promoter_WP_Admin_Integration;
 */
class Hooks extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		$this->container->singleton( static::class, $this );
		$this->container->singleton( 'extension.promoter_wp_admin_integration.hooks', $this );

		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adds the actions required by the plugin.
	 * 
	 * @since 1.0.0
	 */
	protected function add_actions() {
		add_action( 'tribe_load_text_domains', [ $this, 'load_text_domains' ] );
	}

	/**
	 * Adds the filters required by the plugin.
	 * TODO: Add filters 'tribe_tickets_attendees_event_action_links' 'tribe_events_tickets_attendees_url'
	 * @since 1.0.0
	 */
	protected function add_filters() {
		/** @var Tribe__Promoter__PUE $pue */
		$pue = tribe( 'promoter.pue' );
		if ( ! $pue->has_license_key() ) {
			return;
		}

		add_filter( 'tribe_tickets_attendees_event_action_links', [ $this, 'add_promoter_action_link' ], 10, 2 );
	}

	/**
	 * Load text domain for localization of the plugin.
	 *
	 * @since 1.0.0
	 */
	public function load_text_domains() {
		$mopath = tribe( Plugin::class )->plugin_dir . 'lang/';
		$domain = 'promoter-wp-admin-integration';

		// This will load `wp-content/languages/plugins` files first.
		\Tribe__Main::instance()->load_text_domain( $domain, $mopath );
	}

	/**
	 * Add Promoter action links
	 * TODO: document arguments
	 * @since 1.0.0
	 */
	public function add_promoter_action_link( $action_links, $event_id ) {
		$action_links[] = '<a href="' . esc_url( 'https://promoter.theeventscalendar.com/messages/new/' . $event_id ) . '" title="' . esc_attr_x( 'Create Promoter Message', 'attendee event actions', 'promoter-wp-admin-integration' ) . '">' . esc_attr_x( 'Create Promoter Message', 'attendee event actions', 'promoter-wp-admin-integration' ) . '</a>';

		return $action_links;
	}
}
