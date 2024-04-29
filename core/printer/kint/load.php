<?php

use Kint\Renderer\AbstractRenderer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once DEBUGPRESS_PLUGIN_PATH . 'vendor/kint/autoload.php';

Kint::$depth_limit = 12;

Kint\Renderer\RichRenderer::$folder = false;
Kint\Renderer\RichRenderer::$theme  = 'aante-light.css';
Kint\Renderer\RichRenderer::$sort   = AbstractRenderer::SORT_FULL;

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

	// The only way for Kint Dump method to return value, and not echo it, is with @ modifier
	$show = @Kint::dump( $value ); // phpcs:ignore WordPress.PHP.NoSilencedErrors

	if ( ! $footer ) {
		Kint::$display_called_from = true;
	}

	return (string) $show;
}
