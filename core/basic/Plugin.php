<?php

namespace Dev4Press\Plugin\DebugPress\Basic;

class Plugin {
	public function __construct() {

	}

	public static function instance() {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Plugin();
		}

		return $instance;
	}
}
