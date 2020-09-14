<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\Panel;

class PHP extends Panel {
	public function left() {
		$this->title( __( 'Full PHP $_SERVER', "debugpress" ), true );
		$this->list_array( wp_unslash( $_SERVER ) );
	}

	public function right() {
		$this->title( __( 'Full PHP $_REQUEST', "debugpress" ), true );
		$this->list_array( wp_unslash( $_REQUEST ) );

		$this->title( __( 'Full PHP $_COOKIE', "debugpress" ), true );
		$this->list_array( wp_unslash( $_COOKIE ) );
	}
}
