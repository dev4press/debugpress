<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Display\PrettyPrint;

function debugpress_do_settings_sections( $page ) {
	global $wp_settings_sections, $wp_settings_fields;

	if ( ! isset( $wp_settings_sections[ $page ] ) ) {
		return;
	}

	foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
		echo '<div class="debugpress-settings-section">';
			if ( $section['title'] ) {
				echo "<h2>{$section['title']}</h2>\n";
			}

			if ( $section['callback'] ) {
				echo '<div class="debugpress-section-info">';
				call_user_func( $section['callback'], $section );
				echo '</div>';
			}

			if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
				continue;
			}
			echo '<table class="form-table" role="presentation">';
			do_settings_fields( $page, $section['id'] );
			echo '</table>';
		echo '</div>';
	}
}

function debugpress_has_bbpress() {
	if ( function_exists( 'bbp_get_version' ) ) {
		$version = bbp_get_version();
		$version = intval( substr( str_replace( '.', '', $version ), 0, 2 ) );

		return $version > 24;
	} else {
		return false;
	}
}

function debugpress_has_permalinks() {
	return get_option( 'permalink_structure' );
}

function debugpress_is_classicpress() {
	return function_exists( 'classicpress_version' ) &&
	       function_exists( 'classicpress_version_short' );
}

function debugpress_error_log( $log ) {
	if ( DEBUGPRESS_IS_DEBUG_LOG ) {
		$print = print_r( $log, true );

		error_log( $print );
	}
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

function debugpress_format_size( $size, $decimal = 2, $sep = ' ' ) {
	$units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB' );

	$size = max( $size, 0 );
	$pow  = floor( ( $size ? log( $size ) : 0 ) / log( 1024 ) );
	$pow  = min( $pow, count( $units ) - 1 );

	$size /= pow( 1024, $pow );

	return round( $size, $decimal ) . $sep . $units[ $pow ];
}

function debugpress_store_object( $object, $title = '', $sql = false ) {
	debugpress_tracker()->log( $object, $title, $sql );
}

function debugpress_count_lines_in_files( $file_path ) {
	if (!file_exists($file_path)) {
		return 0;
	}

	$file = new SplFileObject( $file_path, 'r' );
	$file->seek( PHP_INT_MAX );

	echo $file->key() + 1;
}

function debugpress_read_lines_from_file( $file_path, $last = 1000 ) {
	$file = new SplFileObject( $file_path, 'r' );
	$file->seek( PHP_INT_MAX );
	$last_line = $file->key();

	$lines = new LimitIterator( $file, $last_line - $last, $last_line );

	return iterator_to_array( $lines );
}

function gdp_rs( $value, $echo = true ) {
	$result = '';

	if ( is_bool( $value ) ) {
		$result = '<div class="gdp_rs gdp_rs_bool">' . ( $value ? 'TRUE' : 'FALSE' ) . '</div>';
	} else if ( is_null( $value ) ) {
		$result = '<div class="gdp_rs gdp_rs_null">NULL</div>';
	} else if ( is_string( $value ) ) {
		if ( empty( $value ) ) {
			$result = '<div class="gdp_rs gdp_rs_empty">EMPTY</div>';
		} else {
			$result = '<div class="gdp_rs gdp_rs_string">' . $value . '</div>';
		}
	} else if ( is_int( $value ) || is_float( $value ) ) {
		$result = '<div class="gdp_rs gdp_rs_number">' . $value . '</div>';
	}

	if ( $echo ) {
		echo $result;
	} else {
		return $result;
	}
}

function gdp_r( $value, $footer = true, $collapsed = true, $inspect_methods = true ) {
	$n = PrettyPrint::instance( $value, $footer, $collapsed, $inspect_methods );

	$n->render();
}

function gdp_rx( $value, $footer = true, $collapsed = true, $inspect_methods = true ) {
	$n = PrettyPrint::instance( $value, $footer, $collapsed, $inspect_methods );

	return $n->generate();
}
