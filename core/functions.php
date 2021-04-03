<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Print the value in the WordPress debug.log, if it is available.
 *
 * @param mixed $object
 */
function debugpress_error_log( $object ) {
	if ( DEBUGPRESS_IS_DEBUG_LOG ) {
		$print = print_r( $object, true );

		error_log( $print );
	}
}

/**
 * Store the object into the DebugPress Tracker, and display it inside the Store tab of the Debugger.
 *
 * @param mixed  $object Object value to store, it can be any type.
 * @param string $title  Optional title to associate with the stored object
 * @param false  $sql    If the stored object SQL string, it will be rendered as formatted SQL
 */
function debugpress_store_object( $object, $title = '', $sql = false ) {
	debugpress_tracker()->log( $object, $title, $sql );
}

/**
 * Store the data associated with the plugin for debug purposes, to be displayed on the Plugins tab of the Debugger.
 *
 * @param string $plugin_file Name of the plugin as identified by WordPress, relative path to plugin main file
 * @param array  $data        Data to show on the Plugins page
 */
function debugpress_store_for_plugin( $plugin_file, $data = array() ) {
	debugpress_tracker()->plugin( $plugin_file, $data );
}

/**
 * Check if the bbPress plugin is currently installed and active. Requires bbPress version 2.5 or newer.
 *
 * @return bool TRUE: if the bbPress is active, FALSE: if it is not active
 */
function debugpress_has_bbpress() : bool {
	if ( function_exists( 'bbp_get_version' ) ) {
		$version = bbp_get_version();
		$version = intval( substr( str_replace( '.', '', $version ), 0, 2 ) );

		return $version > 24;
	} else {
		return false;
	}
}

/**
 * Check if the currently running CMS is ClassicPress, and not the WordPress.
 *
 * @return bool TRUE: if the ClassicPress is active, FALSE: if it is not active
 */
function debugpress_is_classicpress() : bool {
	return function_exists( 'classicpress_version' ) &&
	       function_exists( 'classicpress_version_short' );
}

/**
 * Format numeric value representing bytes (for file size, for instance) into short format rounded to KB, MB, GB, TB
 * and PB.
 *
 * @param float|int $size    size value to format
 * @param int       $decimal number of decimal points to show
 * @param string    $sep     separator between value and modifier
 *
 * @return string formatted string
 */
function debugpress_format_size( $size, $decimal = 2, $sep = ' ' ) : string {
	$units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB' );

	$size = max( $size, 0 );
	$pow  = floor( ( $size ? log( $size ) : 0 ) / log( 1024 ) );
	$pow  = min( $pow, count( $units ) - 1 );

	$size /= pow( 1024, $pow );

	return round( $size, $decimal ) . $sep . $units[ $pow ];
}

/**
 * Extract part of the string from the left, based on the position of the other string.
 *
 * @param string $input    string to extract part from
 * @param string $modifier string to use to calculate position in the $input string
 *
 * @return false|string extracted string or false on error
 */
function debugpress_strleft( string $input, string $modifier ) {
	return substr( $input, 0, strpos( $input, $modifier ) );
}

/**
 * Replace first occurrence of the $from string with the $to string inside the $subject.
 *
 * @param string $from
 * @param string $to
 * @param string $subject
 *
 * @return string|string[]|null
 */
function debugpress_str_replace_first( $from, $to, $subject ) {
	return preg_replace( '/' . preg_quote( $from, '/' ) . '/', $to, $subject, 1 );
}

/**
 * Basic pretty print replacement that handles only scalars or null, and it can't print objects or arrays.
 *
 * @param bool|numeric|string|null $value variable to pretty print
 * @param bool                     $echo  print or return formatted result
 *
 * @return string pretty printed output
 */
function debugpress_rs( $value, $echo = true ) : string {
	$result = '';

	if ( is_bool( $value ) ) {
		$result = '<div class="debugpress_rs debugpress_rs_bool debugpress_rs_bool_' . ( $value ? 'true' : 'false' ) . '">' . ( $value ? 'TRUE' : 'FALSE' ) . '</div>';
	} else if ( is_null( $value ) ) {
		$result = '<div class="debugpress_rs debugpress_rs_null">NULL</div>';
	} else if ( is_numeric( $value ) ) {
		$result = '<div class="debugpress_rs debugpress_rs_number">' . $value . '</div>';
	} else if ( is_string( $value ) ) {
		if ( empty( $value ) ) {
			$result = '<div class="debugpress_rs debugpress_rs_empty">EMPTY</div>';
		} else {
			$result = '<div class="debugpress_rs debugpress_rs_string">' . $value . '</div>';
		}
	}

	if ( $echo ) {
		echo $result;
	} else {
		return $result;
	}
}
