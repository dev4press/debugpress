<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\Panel;

class PHP extends Panel {
	public function left() {
		$this->title( esc_html__( 'Full PHP $_SERVER', 'debugpress' ) );
		$this->list_array( wp_unslash( $_SERVER ) );
	}

	public function right() {
		$this->title( esc_html__( 'Full PHP $_REQUEST', 'debugpress' ) );
		$this->list_array( wp_unslash( $_REQUEST ) ); // phpcs:ignore WordPress.Security.NonceVerification

		$this->title( esc_html__( 'Full PHP $_COOKIE', 'debugpress' ) );
		$this->list_array( wp_unslash( $_COOKIE ) );
	}
}
