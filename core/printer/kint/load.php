<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KINT_SKIP_HELPERS', true );

require_once( DEBUGPRESS_PLUGIN_PATH . 'core/printer/kint/autoload.php' );

Kint\Renderer\RichRenderer::$folder = false;
Kint\Renderer\RichRenderer::$theme  = 'aante-light.css';
Kint\Renderer\RichRenderer::$sort   = Kint\Renderer\Renderer::SORT_FULL;

do_action( 'debugpress-printer-loaded-kint' );

/**
 * Main `print_r` pretty print replacement that can pretty print and format (almost) anything you want including
 * objects with support for reflection analysis.
 *
 * @param mixed $value  variable to print
 * @param bool  $footer include footer
 */
function debugpress_r( $value, $footer = true ) {
	if ( ! $footer ) {
		Kint::$display_called_from = false;
	}

	Kint::dump( $value );

	if ( ! $footer ) {
		Kint::$display_called_from = true;
	}
}

/**
 * Main `print_r` pretty print replacement that can pretty print and format (almost) anything you want including
 * objects with support for reflection analysis.
 *
 * @param mixed $value  variable to print
 * @param bool  $footer include footer
 *
 * @return string formatted pretty printed value
 */
function debugpress_rx( $value, $footer = true ) : string {
	if ( ! $footer ) {
		Kint::$display_called_from = false;
	}

	$show = @Kint::dump( $value );

	if ( ! $footer ) {
		Kint::$display_called_from = true;
	}

	return (string)$show;
}
