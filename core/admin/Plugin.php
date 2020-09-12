<?php

namespace Dev4Press\Plugin\DebugPress\Admin;

class Plugin {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	/** @return \Dev4Press\Plugin\DebugPress\Admin\Plugin */
	public static function instance() {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Plugin();
		}

		return $instance;
	}

	public function admin_menu() {
		add_options_page( 'DebugPress', 'DebugPress', 'manage_options', 'debugpress', array( $this, 'settings_page' ) );
	}

	public function settings_page() {
		include( DEBUGPRESS_PLUGIN_PATH . 'forms/admin/settings.php' );
	}

	public function admin_init() {
		register_setting(
			'debugpress',
			'debugpress_settings',
			array(
				'sanitize_callback' => array( $this, 'settings_sanitize' )
			) );

		Settings::instance()->sections();
		Settings::instance()->fields();
	}

	public function settings_sanitize( $input ) {
		return debugpress_plugin()->process_settings( $input );
	}
}
