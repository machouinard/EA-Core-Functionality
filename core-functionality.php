<?php
/**
 * Plugin Name: Core Functionality
 * Description: This contains all your site's core functionality so that it is theme independent.
 * <strong>It should always be activated</strong>.
 * Version:     1.0.0
 * Author:      Mark Chouinard
 *
 * Forked from Bill Erickson and objectified https://github.com/billerickson/EA-Core-Functionality
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 2, as published by the
 * Free Software Foundation.  You may NOT assume that you can use any other
 * version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.
 *
 * @package    CoreFunctionality
 * @since      1.0.0
 * @copyright  Copyright (c) 2021 Mark Chouinard
 * @license    GPL-2.0+
 */

// Plugin constants
define( 'CF_URL', plugin_dir_url( __FILE__ ) );
define( 'CF_DIR', plugin_dir_path( __FILE__ ) );
define( 'CF_BASENAME', plugin_basename( __FILE__ ) );

// Require all class files in inc directory
foreach ( glob( CF_DIR . 'inc/class-*.php' ) as $file ) {
	require_once $file;
}

/**
 * Wrapper for Pretty Printing
 *
 * @param        $obj
 * @param string $label
 * @param int    $width
 *
 * @return void
 * @since 1.0.0
 *
 */
function cfpp( $obj, $label = '', $width = 400 ) {

	\CoreFunctionality\General::pp( $obj, $label, $width );

}
