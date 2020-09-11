<?php

namespace Dev4Press\Plugin\DebugPress\Basic;

class AJAX {
	public function __construct() {

	}

	public static function instance() {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new AJAX();
		}

		return $instance;
	}
}