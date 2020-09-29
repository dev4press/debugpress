<?php

namespace Dev4Press\Plugin\DebugPress\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WP {
	public function __construct() {

	}

	/** @return \Dev4Press\Plugin\DebugPress\Main\WP */
	public static function instance() {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new WP();
		}

		return $instance;
	}

	function has_permalinks() {
		return get_option( 'permalink_structure' );
	}
}