<?php

namespace Dev4Press\Plugin\DebugPress\Admin;

class Plugin {
	public function __construct() {

	}

	/** @return \Dev4Press\Plugin\DebugPress\Admin\Plugin */
	public static function instance() {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Plugin();
		}

		return $instance;
	}
}
