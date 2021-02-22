<?php

namespace CoreFunctionality;

/**
 * WordPress Cleanup
 *
 * @package      CoreFunctionality
 * @author       Mark Chouinard
 * @since        1.0.0
 * @license      GPL-2.0+
 **/
class Cleanup {

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

		add_filter( 'wp_get_attachment_image_attributes',
			[ $this, 'attachment_id_on_images' ],
			10,
			2 );
		add_action( 'init', [ $this, 'default_image_link' ] );
		add_action( 'add_meta_boxes', [ $this, 'remove_cmb' ] );
	}

	/**
	 * Add attachment ID to Images
	 *
	 * @since  1.0.0
	 */
	public function attachment_id_on_images( $attr, $attachment ) {

		if ( false === strpos( $attr['class'], 'wp-image-' . $attachment->ID ) ) {
			$attr['class'] .= ' wp-image-' . $attachment->ID;
		}

		return $attr;
	}


	/**
	 * Ensure default image link is none
	 *
	 * @since 1.0.0
	 */
	public function default_image_link() {

		$link = get_option( 'image_default_link_type' );
		if ( 'none' !== $link ) {
			update_option( 'image_default_link_type', 'none' );
		}
	}

	/**
	 * Remove ancient Custom Fields Metabox because it's slow and most often useless anymore
	 *
	 * @see   https://core.trac.wordpress.org/ticket/33885
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function remove_cmb() {

		remove_meta_box( 'postcustom', null, 'normal' );
	}

	//// Could use 'load-post-new.php' instead of 'admin_init'
	//add_action( 'admin_init', function() {
	//	foreach ( get_post_types_by_support( 'post-custom' ) as $type ) {
	//		remove_post_type_support( $type, 'post-custom' );
	//	}
	//} );
}

new Cleanup();
