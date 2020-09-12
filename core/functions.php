<?php

use Dev4Press\Plugin\DebugPress\Display\PrettyPrint;

function debugpress_has_bbpress() {
	if ( function_exists( 'bbp_get_version' ) ) {
		$version = bbp_get_version();
		$version = intval( substr( str_replace( '.', '', $version ), 0, 2 ) );

		return $version > 24;
	} else {
		return false;
	}
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