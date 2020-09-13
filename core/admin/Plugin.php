<?php

namespace Dev4Press\Plugin\DebugPress\Admin;

class Plugin {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_enqueue_scripts', array($this, 'enqueue_sctipts'));

		add_filter( 'network_admin_plugin_action_links_debugpress/debugpress.php', array(
			$this,
			'plugin_action_links'
		) );
		add_filter( 'plugin_action_links_debugpress/debugpress.php', array( $this, 'plugin_action_links' ) );

		add_filter( 'plugin_row_meta', array( $this, 'plugin_links' ), 10, 2 );
	}

	/** @return \Dev4Press\Plugin\DebugPress\Admin\Plugin */
	public static function instance() {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Plugin();
		}

		return $instance;
	}

	public function plugin_action_links( $actions ) {
		$actions['settings'] = '<a href="' . admin_url( 'options-general.php?page=debugpress' ) . '">' . esc_html__( "Settings", "debugpress" ) . '</a>';

		return $actions;
	}

	function plugin_links( $links, $file ) {
		if ( $file == 'debugpress/debugpress.php' ) {
			$links[] = '<a target="_blank" rel="noopener" href="https://www.dev4press.com/">dev4Press.com</a>';
		}

		return $links;
	}

	public function enqueue_sctipts() {
		wp_enqueue_style('debugpress-admin', DEBUGPRESS_PLUGIN_URL . 'css/adminpanel' . ( DEBUGPRESS_IS_DEBUG ? '' : '.min' ) . '.css', array( ), DEBUGPRESS_VERSION);
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
