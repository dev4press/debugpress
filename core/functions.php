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
 * Check if the bbPress plugin is currently installed and active. Requires bbPress version 2.5 or newer.
 *
 * @return bool TRUE: if the bbPress is active, FALSE: if it is not active
 */
function debugpress_has_bbpress() {
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
function debugpress_is_classicpress() {
	return function_exists( 'classicpress_version' ) &&
	       function_exists( 'classicpress_version_short' );
}

/**
 * Format numeric value representing bytes (for file size, for instance) into short format rounded to KB, MB, GB, TB
 * and PB.
 *
 * @param float  $size    size value to format
 * @param int    $decimal number of decimal points to show
 * @param string $sep     separator between value and modifier
 *
 * @return string formatted string
 */
function debugpress_format_size( $size, $decimal = 2, $sep = ' ' ) {
	$units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB' );

	$size = max( $size, 0 );
	$pow  = floor( ( $size ? log( $size ) : 0 ) / log( 1024 ) );
	$pow  = min( $pow, count( $units ) - 1 );

	$size /= pow( 1024, $pow );

	return round( $size, $decimal ) . $sep . $units[ $pow ];
}

function debugpress_current_url_request() {
	$pathinfo = isset( $_SERVER['PATH_INFO'] ) ? $_SERVER['PATH_INFO'] : '';
	list( $pathinfo ) = explode( '?', $pathinfo );
	$pathinfo = str_replace( '%', '%25', $pathinfo );

	$request         = explode( '?', $_SERVER['REQUEST_URI'] );
	$req_uri         = $request[0];
	$req_query       = isset( $request[1] ) ? $request[1] : false;
	$home_path       = trim( parse_url( home_url(), PHP_URL_PATH ), '/' );
	$home_path_regex = sprintf( '|^%s|i', preg_quote( $home_path, '|' ) );

	$req_uri = str_replace( $pathinfo, '', $req_uri );
	$req_uri = trim( $req_uri, '/' );
	$req_uri = preg_replace( $home_path_regex, '', $req_uri );
	$req_uri = trim( $req_uri, '/' );

	$url_request = $req_uri;

	if ( $req_query !== false ) {
		$url_request .= '?' . $req_query;
	}

	return $url_request;
}

function debugpress_current_url( $use_wp = true ) {
	if ( $use_wp ) {
		return home_url( debugpress_current_url_request() );
	} else {
		$s        = empty( $_SERVER['HTTPS'] ) ? '' : ( $_SERVER['HTTPS'] == 'on' ? 's' : '' );
		$protocol = debugpress_strleft( strtolower( $_SERVER['SERVER_PROTOCOL'] ), '/' ) . $s;
		$port     = $_SERVER['SERVER_PORT'] == '80' || $_SERVER['SERVER_PORT'] == '443' ? '' : ':' . $_SERVER['SERVER_PORT'];

		return $protocol . '://' . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
	}
}

function debugpress_strleft( $s1, $s2 ) {
	return substr( $s1, 0, strpos( $s1, $s2 ) );
}
