<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\Panel;

class Roles extends Panel {
	public function left() {
		global $wp_roles;

		$this->title( esc_html__( 'User Roles Objects', 'debugpress' ) );
		$this->list_array( $wp_roles->role_objects, '$' . 'wp_roles->role_objects' ); // phpcs:ignore Generic.Strings.UnnecessaryStringConcat
	}

	public function right() {
		global $wp_roles;

		$this->title( esc_html__( 'User Roles Options Key', 'debugpress' ) );
		$this->block_header();
		echo esc_html( $wp_roles->role_key );
		$this->block_footer();

		$this->title( esc_html__( 'User Roles Names', 'debugpress' ) );
		$this->list_array( $wp_roles->role_names, '$' . 'wp_roles->role_names' ); // phpcs:ignore Generic.Strings.UnnecessaryStringConcat
	}
}
