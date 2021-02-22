<?php

namespace CoreFunctionality;

/**
 * Kill Trackbacks
 *
 * @package      CoreFunctionality
 * @author       Mark Chouinard
 * @since        1.0.0
 * @license      GPL-2.0+
 **/
class Trackbacks {

	public function __construct() {

		$this->hooks();
	}

	/**
	 * Hook all the things
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function hooks() {

		add_filter( 'wp_headers', [ $this, 'filter_headers' ], 10, 1 );
		add_filter( 'rewrite_rules_array', [ $this, 'filter_rewrites' ] );
		add_filter( 'bloginfo_url', [ $this, 'kill_pingback_url' ], 10, 2 );
		// hijack options updating for XMLRPC
		add_filter( 'pre_update_option_enable_xmlrpc', '__return_false' );
		add_filter( 'pre_option_enable_xmlrpc', '__return_zero' );
		add_action( 'xmlrpc_call', [ $this, 'kill_xmlrpc' ] );
		// remove RSD link
		remove_action( 'wp_head', 'rsd_link' );
	}

	/**
	 * Kill pingback
	 *
	 * @param $headers
	 *
	 * @return mixed
	 * @since 1.0.0
	 *
	 */
	public function filter_headers( $headers ) {

		if ( isset( $headers['X-Pingback'] ) ) {
			unset( $headers['X-Pingback'] );
		}

		return $headers;
	}

	/**
	 * Kill rewrite rule
	 *
	 * @param $rules
	 *
	 * @return mixed
	 * @since 1.0.0
	 *
	 */
	public function filter_rewrites( $rules ) {

		foreach ( $rules as $rule => $rewrite ) {
			if ( preg_match( '/trackback\/\?\$$/i', $rule ) ) {
				unset( $rules[ $rule ] );
			}
		}

		return $rules;
	}

	/**
	 * Kill bloginfo( 'pingback_url' )
	 *
	 * @param $output
	 * @param $show
	 *
	 * @return string
	 * @since 1.0.0
	 *
	 */
	public function kill_pingback_url( $output, $show ) {

		if ( $show == 'pingback_url' ) {
			$output = '';
		}

		return $output;
	}

	/**
	 * Disable XMLRPC call
	 *
	 * @param $action
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function kill_xmlrpc( $action ) {

		if ( 'pingback.ping' === $action ) {
			wp_die( 'Pingbacks are not supported',
				'Not Allowed!',
				array( 'response' => 403 ) );
		}
	}

}

new Trackbacks();
