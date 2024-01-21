<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Printer\PrettyPrint\PrettyPrint;

require_once DEBUGPRESS_PLUGIN_PATH . 'vendor/prettyprint/PrettyPrint.php';

/**
 * Main `print_r` pretty print replacement that can pretty print and format (almost) anything you want including
 * objects with support for reflection analysis.
 *
 * @param mixed $value  variable to print
 * @param bool  $footer include footer
 *
 * @uses PrettyPrint
 */
function debugpress_r( $value, $footer = true ) {
	$n = PrettyPrint::instance( $value, $footer );

	$n->render();
}

/**
 * Main `print_r` pretty print replacement that can pretty print and format (almost) anything you want including
 * objects with support for reflection analysis.
 *
 * @param mixed $value  variable to print
 * @param bool  $footer include footer
 *
 * @return string formatted pretty printed value
 *
 * @uses PrettyPrint
 */
function debugpress_rx( $value, $footer = true ) : string {
	$n = PrettyPrint::instance( $value, $footer );

	return $n->generate();
}
