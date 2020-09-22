<?php

namespace Dev4Press\Plugin\DebugPress\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AJAX {
	public function __construct() {

	}

	/** @return \Dev4Press\Plugin\DebugPress\Main\AJAX */
	public static function instance() {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new AJAX();
		}

		return $instance;
	}
}
