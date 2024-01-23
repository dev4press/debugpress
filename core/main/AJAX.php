<?php

namespace Dev4Press\Plugin\DebugPress\Main;

use Dev4Press\Plugin\DebugPress\Panel\DebugLog;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AJAX {
	public function __construct() {
		add_action( 'wp_ajax_debugpress_load_debuglog', array( $this, 'load_debuglog' ) );
		add_action( 'wp_ajax_nopriv_debugpress_load_debuglog', array( $this, 'load_debuglog' ) );
	}

	public static function instance() : AJAX {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new AJAX();
		}

		return $instance;
	}

	public function load_debuglog() {
		$_nonce  = isset( $_REQUEST['_ajax_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_ajax_nonce'] ) ) : '';
		$_verify = wp_verify_nonce( $_nonce, 'debugpress-ajax-call' );

		if ( $_verify === false ) {
			die( - 1 );
		}

		$lines = DebugLog::instance()->load_from_debug_log();

		$this->respond( '<pre>' . join( '', $lines ) . '</pre>' );
	}

	public function respond( $response, $code = 200, $json = false ) {
		status_header( $code );

		if ( debugpress_plugin()->get( 'ajax_header_no_cache' ) ) {
			nocache_headers();
		}

		if ( $json ) {
			header( 'Content-Type: application/json' );
		}

		die( $response ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
