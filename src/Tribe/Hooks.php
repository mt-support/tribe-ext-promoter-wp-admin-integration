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

use TEC\Common\Contracts\Service_Provider;

/**
 * Class Hooks.
 *
 * @since   1.0.0
 *
 * @package Tribe\Extensions\Promoter_WP_Admin_Integration;
 */
class Hooks extends Service_Provider {

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
		/** @var Tribe__Promoter__PUE $pue */
		$pue = tribe( 'promoter.pue' );
		if ( ! $pue->has_license_key() ) {
			return;
		}
		add_action('tribe_events_tickets_attendees_ticket_sales_bottom', [ $this, 'print_promoter_audiences_link'] );
	}

	/**
	 * Adds the filters required by the plugin.
	 * 
	 * @since 1.0.0
	 */
	protected function add_filters() {
		/** @var Tribe__Promoter__PUE $pue */
		$pue = tribe( 'promoter.pue' );
		if ( ! $pue->has_license_key() ) {
			return;
		}
		add_filter( 'post_row_actions', [ $this, 'filter_actions' ], 30, 2 );
		add_filter( 'tribe_tickets_attendees_event_action_links', [ $this, 'add_promoter_action_link' ], 10, 2 );
	}

	/**
	 * Load text domain for localization of the plugin.
	 * 
	 * @since 1.0.0
	 */
	public function load_text_domains() {
		$mopath = tribe( Plugin::class )->plugin_dir . 'lang/';
		$domain = 'tribe-ext-promoter-wp-admin-integration';

		// This will load `wp-content/languages/plugins` files first.
		\Tribe__Main::instance()->load_text_domain( $domain, $mopath );
	}

	/**
	 * Print a Promoter link to the audience
	 * 
	 * @param string $event_id
	 * @since 1.0.0
	 */
	public function print_promoter_audiences_link( $event_id ) {
		echo $actions['promoter_'] = sprintf(
			'<a href="%s" title="%s" target="_blank">%s</a>',
			esc_url( 'https://promoter.theeventscalendar.com/events/' . $event_id . '/audiences' ),
			esc_attr_x( 'See your Audience in Promoter', 'Title attribute for promoter link', 'tribe-ext-promoter-wp-admin-integration' ),
			esc_html__( 'Promoter Audience', 'tribe-ext-promoter-wp-admin-integration' )
		);
	}

	/**
	 * Add Promoter links to the post list table for tribe_events
	 * 
	 * @param array    $actions
	 * @param WP_Post  $post
	 * 
	 * @since 1.0.0
	 */
	public function filter_actions( $actions, $post ) {
		// Only proceed if we're viewing a tribe_events post type.
		if ( !$post->post_type === 'tribe_events' ) {
			return $actions;
		}

		// Only proceed if there are tickets.
		if ( !tribe_events_has_tickets( $post ) ) {
			return $actions;
		}

		$actions['promoter_'] = sprintf(
			'<a href="%s" title="%s" target="_blank">%s</a>',
			esc_url( 'https://promoter.theeventscalendar.com/events/' . $post->ID ),
			esc_attr_x( 'See this event in Promoter', 'Title attribute for promoter link', 'tribe-ext-promoter-wp-admin-integration' ),
			esc_html__( 'Promoter', 'tribe-ext-promoter-wp-admin-integration' )
		);

		return $actions;
	}

	/**
	 * Add Promoter action links to the attendees page
	 * 
	 * @param array  $action_links
	 * @param string $event_id
	 * 
	 * @since 1.0.0
	 */
	public function add_promoter_action_link( $action_links, $event_id ) {
		// Create Regular Message link
		$action_links[] = sprintf(
			'<a href="%s" title="%s" target="_blank">%s</a>',
			esc_url( 'https://promoter.theeventscalendar.com/messages/standard/' . $event_id ),
			esc_attr_x( 'Create New Regular Message in Promoter', 'attendee event actions', 'tribe-ext-promoter-wp-admin-integration' ),
			esc_html_x( 'Add Regular Message', 'attendee event actions', 'tribe-ext-promoter-wp-admin-integration' )
		);
		// Create Triggered Message link
		$action_links[] = sprintf(
			'<a href="%s" title="%s" target="_blank">%s</a>',
			esc_url( 'https://promoter.theeventscalendar.com/messages/trigger/' . $event_id ),
			esc_attr_x( 'Create New Triggered Message in Promoter', 'attendee event actions', 'tribe-ext-promoter-wp-admin-integration' ),
			esc_html_x( 'Add Triggered Message', 'attendee event actions', 'tribe-ext-promoter-wp-admin-integration' )
		);

		return $action_links;
	}
}
