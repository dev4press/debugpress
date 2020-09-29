<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Display\PrettyPrint;

function debugpress_rs( $value, $echo = true ) {
	$result = '';

	if ( is_bool( $value ) ) {
		$result = '<div class="debugpress_rs debugpress_rs_bool">' . ( $value ? 'TRUE' : 'FALSE' ) . '</div>';
	} else if ( is_null( $value ) ) {
		$result = '<div class="debugpress_rs debugpress_rs_null">NULL</div>';
	} else if ( is_string( $value ) ) {
		if ( empty( $value ) ) {
			$result = '<div class="debugpress_rs debugpress_rs_empty">EMPTY</div>';
		} else {
			$result = '<div class="debugpress_rs debugpress_rs_string">' . $value . '</div>';
		}
	} else if ( is_int( $value ) || is_float( $value ) ) {
		$result = '<div class="debugpress_rs debugpress_rs_number">' . $value . '</div>';
	}

	if ( $echo ) {
		echo $result;
	} else {
		return $result;
	}
}

function debugpress_r( $value, $footer = true, $collapsed = true, $inspect_methods = true ) {
	$n = PrettyPrint::instance( $value, $footer, $collapsed, $inspect_methods );

	$n->render();
}

function debugpress_rx( $value, $footer = true, $collapsed = true, $inspect_methods = true ) {
	$n = PrettyPrint::instance( $value, $footer, $collapsed, $inspect_methods );

	return $n->generate();
}
