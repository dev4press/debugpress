<?php

namespace Dev4Press\Plugin\DebugPress\Main;

class Plugin {
	public function __construct() {

	}

	/** @return \Dev4Press\Plugin\DebugPress\Main\Plugin */
	public static function instance() {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Plugin();
		}

		return $instance;
	}
}
