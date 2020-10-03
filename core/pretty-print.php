<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Display\PrettyPrint;

/**
 * Basic pretty print replacement that handles only scalars or null, and it can't print objects or arrays.
 *
 * @param mixed $value variable to pretty print (bool, number, null or string)
 * @param bool  $echo  print or return formatted result
 *
 * @return string pretty printed output
 */
function debugpress_rs( $value, $echo = true ) {
	$result = '';

	if ( is_bool( $value ) ) {
		$result = '<div class="debugpress_rs debugpress_rs_bool">' . ( $value ? 'TRUE' : 'FALSE' ) . '</div>';
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

/**
 * Main `print_r` pretty print replacement that can pretty print and format (almost) anything you want including
 * objects with support for reflection analysis.
 *
 * @param mixed $value           variable to print
 * @param bool  $footer          include footer
 * @param bool  $collapsed       print collapsed
 * @param bool  $inspect_methods show object methods
 *
 * @uses \Dev4Press\Plugin\DebugPress\Display\PrettyPrint
 */
function debugpress_r( $value, $footer = true, $collapsed = true, $inspect_methods = true ) {
	$n = PrettyPrint::instance( $value, $footer, $collapsed, $inspect_methods );

	$n->render();
}

/**
 * Main `print_r` pretty print replacement that can pretty print and format (almost) anything you want including
 * objects with support for reflection analysis.
 *
 * @param mixed $value           variable to print
 * @param bool  $footer          include footer
 * @param bool  $collapsed       print collapsed
 * @param bool  $inspect_methods show object methods
 *
 * @return string formatted pretty printed value
 *
 * @uses \Dev4Press\Plugin\DebugPress\Display\PrettyPrint
 */
function debugpress_rx( $value, $footer = true, $collapsed = true, $inspect_methods = true ) {
	$n = PrettyPrint::instance( $value, $footer, $collapsed, $inspect_methods );

	return $n->generate();
}
