<?php

use Kint\Kint;
use Kint\Renderer\AbstractRenderer;
use Kint\Renderer\RichRenderer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'DEBUGPRESS_KINT_DISABLED_CLASS_METHOD' ) ) {
	define( 'DEBUGPRESS_KINT_DISABLED_CLASS_METHOD', false );
}

if ( ! defined( 'DEBUGPRESS_KINT_DISABLED_CLASS_STATICS' ) ) {
	define( 'DEBUGPRESS_KINT_DISABLED_CLASS_STATICS', false );
}

if ( ! defined( 'DEBUGPRESS_KINT_DISABLED_COLOR' ) ) {
	define( 'DEBUGPRESS_KINT_DISABLED_COLOR', false );
}

if ( ! defined( 'DEBUGPRESS_KINT_DEPTH_LIMIT' ) ) {
	define( 'DEBUGPRESS_KINT_DEPTH_LIMIT', 12 );
}

if ( ! defined( 'DEBUGPRESS_KINT_THEME' ) ) {
	define( 'DEBUGPRESS_KINT_THEME', 'aante-light.css' );
}

Kint::$depth_limit = DEBUGPRESS_KINT_DEPTH_LIMIT;

if ( DEBUGPRESS_KINT_DISABLED_CLASS_METHOD ) {
	unset( Kint::$plugins[4] );
}

if ( DEBUGPRESS_KINT_DISABLED_CLASS_STATICS ) {
	unset( Kint::$plugins[5] );
}

if ( DEBUGPRESS_KINT_DISABLED_COLOR ) {
	unset( Kint::$plugins[7] );
}

RichRenderer::$folder = false;
RichRenderer::$theme  = DEBUGPRESS_KINT_THEME;
RichRenderer::$sort   = AbstractRenderer::SORT_FULL;

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
