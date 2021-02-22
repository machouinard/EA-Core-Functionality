<?php

namespace CoreFunctionality;

/**
 * General
 *
 * @package      CoreFunctionality
 * @author       Mark Chouinard
 * @since        1.0.0
 * @license      GPL-2.0+
 **/

/**
 * Class General
 * @package CoreFunctionality
 */
class General {

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

		add_filter( 'http_request_args', [ $this, 'dont_update_these_plugins' ], 5, 2 );
		add_filter( 'plugin_row_meta', [ $this, 'author_links_on_cf_plugin' ], 10, 2 );
		add_filter( 'wpseo_metabox_prio', [ $this, 'yoast_low_priority' ] );
		add_filter( 'init', [ $this, 'remove_yoast_notifications' ] );
		add_filter( 'wpforms_field_new_default', [ $this, 'wpforms_default_large_field_size' ] );
		add_filter( 'gform_notification', [ $this, 'gravityforms_domain' ], 10, 3 );
		add_filter( 'pre_get_posts', [ $this, 'exclude_noindex_from_search' ] );
	}

	/**
	 * Dont Update the Plugin (or any other plugin whose name is added to the array)
	 * If there is a plugin in the repo with the same name, this prevents WP from prompting an
	 * update.
	 *
	 * @param array  $r   Existing request arguments
	 * @param string $url Request URL
	 *
	 * @return array Amended request arguments
	 * @author Jon Brown
	 * @since  1.0.0
	 */
	function dont_update_these_plugins( $r, $url ) {

		// Add any other plugins here (plugin_directory/plugin_filename.php).
		$local_plugins = [
			CF_BASENAME,
		];

		if ( 0 !== strpos( $url, 'https://api.wordpress.org/plugins/update-check/1.1/' ) ) {
			return $r; // Not a plugin update request. Bail immediately.
		}
		$plugins = json_decode( $r['body']['plugins'], true );
		foreach ( $local_plugins as $basename ) {
			unset( $plugins['plugins'][ $basename ] );
		}
		$r['body']['plugins'] = json_encode( $plugins );

		return $r;
	}

	/**
	 * Author Links on CF Plugin
	 *
	 * @since  1.0.0
	 */
	function author_links_on_cf_plugin( $links, $file ) {

		if ( false !== strpos( $file, 'core-functionality.php' ) ) {
			$links[1] = 'By <a href="https://chouinard.me">Mark Chouinard</a> | <a href="https://chouinard.me/call" target="_blank">Schedule a support call</a>.';
		}

		return $links;
	}

	/**
	 * Don't let WPSEO metabox be high priority
	 *
	 * @return string
	 * @since 1.0.0
	 *
	 */
	public function yoast_low_priority() {

		return 'low';
	}

	/**
	 * Remove WPSEO Notifications
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function remove_yoast_notifications() {

		if ( ! class_exists( 'Yoast_Notification_Center' ) ) {
			return;
		}
		$yoast = Yoast_Notification_Center::get();
		remove_action( 'admin_notices', [ $yoast, 'display_notifications' ] );
		remove_action( 'all_admin_notices', [ $yoast, 'display_notifications' ] );
	}

	/**
	 * WPForms, default large field size
	 *
	 * @param $field
	 *
	 * @return mixed
	 * @since 1.0.0
	 *
	 */
	public function wpforms_default_large_field_size( $field ) {

		if ( empty( $field['size'] ) ) {
			$field['size'] = 'large';
		}

		return $field;
	}

	/**
	 * Gravity Forms Domain
	 *
	 * Adds a notice at the end of admin email notifications
	 * specifying the domain from which the email was sent.
	 *
	 * @param array  $notification
	 * @param object $form
	 * @param object $entry
	 *
	 * @return array $notification
	 * @since  1.0.0
	 */
	function gravityforms_domain( $notification, $form, $entry ) {

		if ( $notification['name'] == 'Admin Notification' ) {
			$notification['message'] .= 'Sent from ' . home_url();
		}

		return $notification;
	}

	/**
	 * Exclude No-index content from search
	 *
	 * @since  1.0.0
	 */
	function exclude_noindex_from_search( $query ) {

		if ( $query->is_main_query() && $query->is_search() && ! is_admin() ) {

			$meta_query   = empty( $query->query_vars['meta_query'] ) ? array() : $query->query_vars['meta_query'];
			$meta_query[] = array(
				'key'     => '_yoast_wpseo_meta-robots-noindex',
				'compare' => 'NOT EXISTS',
			);

			$query->set( 'meta_query', $meta_query );
		}
	}

	/**
	 * Pretty Printing
	 *
	 * @param mixed  $obj
	 * @param string $label
	 *
	 * @return null
	 * @author Chris Bratlien
	 * @since  1.0.0
	 */
	public static function pp( $obj, $label = '', $width = 400 ) {

		$data = json_encode( print_r( $obj, true ) );
		?>
		<style type="text/css">
            #bsdLogger {
                position: absolute;
                top: 30px;
                right: 0px;
                border-left: 4px solid #bbb;
                padding: 6px;
                background: white;
                color: #444;
                z-index: 999;
                font-size: 1.25em;
                width: <?php echo $width; ?>px;
                height: 800px;
                overflow: scroll;
            }
		</style>
		<script type="text/javascript">
					var doStuff = function () {
						var obj = <?php echo $data; ?>;
						var logger = document.getElementById( 'bsdLogger' );
						if ( ! logger ) {
							logger = document.createElement( 'div' );
							logger.id = 'bsdLogger';
							document.body.appendChild( logger );
						}
						////console.log(obj);
						var pre = document.createElement( 'pre' );
						var h2 = document.createElement( 'h2' );
						pre.innerHTML = obj;
						h2.innerHTML = '<?php echo addslashes( $label ); ?>';
						logger.appendChild( h2 );
						logger.appendChild( pre );
					};
					window.addEventListener( 'DOMContentLoaded', doStuff, false );
		</script>
		<?php
	}
}

new General();
