<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\Panel;

class Roles extends Panel {
	public function left() {
		global $wp_roles;

		$this->title( __( "User Roles Objects", "debugpress" ) );
		$this->list_array( $wp_roles->role_objects, '$' . 'wp_roles->role_objects' );
	}

	public function right() {
		global $wp_roles;

		$this->title( __( "User Roles Options Key", "debugpress" ) );
		$this->block_header( true );
		echo $wp_roles->role_key;
		$this->block_footer();

		$this->title( __( "User Roles Names", "debugpress" ) );
		$this->list_array( $wp_roles->role_names, '$' . 'wp_roles->role_names' );
	}
}
