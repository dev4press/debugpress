<?php

 // phpcs:disable WordPress.PHP.DevelopmentFunctions,WordPress.WP.AlternativeFunctions

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Print the value in the WordPress debug.log, if it is available.
 *
 * @param mixed $object Object value to store, it can be any type
 */
function debugpress_error_log( $object ) {
	if ( DEBUGPRESS_IS_DEBUG_LOG ) {
		$print = print_r( $object, true );

		error_log( $print );
	}
}

/**
 * Print the value in the custom file based on the provided path and write mode.
 *
 * @param mixed  $object Object value to store, it can be any type
 * @param string $path   Full path to the file to write into
 * @param string $mode   File writing mode, @see 'fopen'
 */
function debugpress_info_file( $object, string $path, string $mode = 'w' ) {
	$handle = fopen( $path, $mode );

	if ( $handle ) {
		$print = print_r( $object, true );

		fwrite( $handle, $print );
		fclose( $handle );
	}
}

/**
 * Store the object into the DebugPress Tracker, and display it inside the Store tab of the Debugger.
 *
 * @param mixed  $object Object value to store, it can be any type
 * @param string $title  Optional title to associate with the stored object
 * @param false  $sql    If the stored object SQL string, it will be rendered as formatted SQL
 */
function debugpress_store_object( $object, string $title = '', bool $sql = false ) {
	debugpress_tracker()->log( $object, $title, $sql );
}

/**
 * Store the data associated with the plugin for debug purposes, to be displayed on the Plugins tab of the Debugger.
 *
 * @param string $plugin_file Name of the plugin as identified by WordPress, relative path to plugin main file
 * @param array  $data        Data to show on the Plugins page
 */
function debugpress_store_for_plugin( string $plugin_file, array $data = array() ) {
	debugpress_tracker()->plugin( $plugin_file, $data );
}

/**
 * Get result of the `debug_print_backtrace` function as a string.
 *
 * @return string
 */
function debugpress_backtrace() : string {
	ob_start();

	debug_print_backtrace();

	$backtrace = ob_get_contents();

	ob_end_clean();

	return (string) $backtrace;
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
 * Check if the coreActivity plugin is currently installed and active.
 *
 * @return bool TRUE: if the coreActivity is active, FALSE: if it is not active
 */
function debugpress_has_coreactivity() : bool {
	return defined( 'COREACTIVITY_VERSION' ) && function_exists( 'coreactivity' ) && class_exists( '\Dev4Press\Plugin\CoreActivity\Basic\Plugin' );
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
function debugpress_format_size( $size, int $decimal = 2, string $sep = ' ' ) : string {
	$units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB' );

	$size = max( $size, 0 );
	$pow  = floor( ( $size ? log( $size ) : 0 ) / log( 1024 ) );
	$pow  = min( $pow, count( $units ) - 1 );

	$size /= pow( 1024, $pow );

	return round( $size, $decimal ) . $sep . $units[ $pow ];
}

/**
 * Check if specified file exist even in the include paths.
 *
 * @param string $file file name to find
 *
 * @return bool true if the file is found in current path or include path.
 */
function debugpress_file_exists( string $file ) : bool {
	if ( function_exists( 'stream_resolve_include_path' ) ) {
		return ! ( stream_resolve_include_path( $file ) === false );
	} else {
		$paths = get_include_path();

		if ( $paths === false ) {
			return false;
		}

		$paths = explode( PATH_SEPARATOR, $paths );

		foreach ( $paths as $path ) {
			if ( file_exists( $path . DIRECTORY_SEPARATOR . $file ) ) {
				return true;
			}
		}

		return false;
	}
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
function debugpress_str_replace_first( string $from, string $to, string $subject ) {
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
function debugpress_rs( $value, bool $echo = true ) : string {
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
		echo $result; // phpcs:ignore WordPress.Security.EscapeOutput
	}

	return $result;
}

function debugpress_kses_basic( string $render ) : string {
	return wp_kses( $render, array(
		'br'     => array(),
		'code'   => array(),
		'a'      => array(
			'href'   => array(),
			'title'  => array(),
			'class'  => array(),
			'target' => array(),
			'data-*' => true,
		),
		'em'     => array(
			'class' => true,
			'style' => true,
		),
		'strong' => array(
			'class' => true,
			'style' => true,
		),
		'span'   => array(
			'class'  => true,
			'style'  => true,
			'title'  => true,
			'data-*' => true,
			'aria-*' => true,
		),
		'i'      => array(
			'class'  => true,
			'aria-*' => true,
		),
	) );
}

 // phpcs:enable