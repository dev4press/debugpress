<?php

namespace Dev4Press\Plugin\DebugPress\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin {
	private $_pages = array();

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_filter( 'network_admin_plugin_action_links_debugpress/debugpress.php', array(
			$this,
			'plugin_action_links',
		) );
		add_filter( 'plugin_action_links_debugpress/debugpress.php', array(
			$this,
			'plugin_action_links',
		) );

		add_filter( 'plugin_row_meta', array( $this, 'plugin_links' ), 10, 2 );
	}

	public static function instance() : Plugin {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Plugin();
		}

		return $instance;
	}

	public function plugin_action_links( $actions ) {
		$actions['settings'] = '<a href="' . admin_url( 'options-general.php?page=debugpress' ) . '">' . esc_html__( 'Settings', 'debugpress' ) . '</a>';

		return $actions;
	}

	public function plugin_links( $links, $file ) {
		if ( $file == 'debugpress/debugpress.php' ) {
			$links[] = '<a target="_blank" rel="noopener" href="https://debug.press/"><span class="dashicons dashicons-flag" aria-hidden="true" style="font-size: 16px; line-height: 1.3"></span>' . esc_html__( 'Home Page', 'debugpress' ) . '</a>';
			$links[] = '<a target="_blank" rel="noopener" href="https://www.buymeacoffee.com/millan"><span class="dashicons dashicons-coffee" aria-hidden="true" style="font-size: 16px; line-height: 1.3"></span>' . esc_html__( 'Buy Me A Coffee', 'debugpress' ) . '</a>';
		}

		return $links;
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'debugpress-admin', DEBUGPRESS_PLUGIN_URL . 'css/adminpanel' . ( DEBUGPRESS_IS_DEBUG ? '' : '.min' ) . '.css', array(), DEBUGPRESS_VERSION );
		wp_enqueue_script( 'debugpress-admin', DEBUGPRESS_PLUGIN_URL . 'js/adminpanel' . ( DEBUGPRESS_IS_DEBUG ? '' : '.min' ) . '.js', array( 'jquery' ), DEBUGPRESS_VERSION, true );
	}

	public function admin_menu() {
		$this->_pages['settings'] = add_options_page( __( 'DebugPress Settings', 'debugpress' ), __( 'DebugPress', 'debugpress' ), 'manage_options', 'debugpress', array(
			$this,
			'settings_page',
		) );
		$this->_pages['info']     = add_management_page( __( 'DebugPress Info', 'debugpress' ), __( 'DebugPress Info', 'debugpress' ), 'manage_options', 'debugpress-info', array(
			$this,
			'tools_page',
		) );

		add_action( 'load-' . $this->_pages['settings'], array( $this, 'settings_context_help' ) );
	}

	public function settings_page() {
		include DEBUGPRESS_PLUGIN_PATH . 'forms/admin/settings.php';
	}

	public function tools_page() {
		include DEBUGPRESS_PLUGIN_PATH . 'forms/admin/tools.php';
	}

	public function settings_context_help() {
		$screen = get_current_screen();

		$screen->add_help_tab(
			array(
				'id'      => 'debugpress-debug',
				'title'   => __( 'Debug Mode', 'debugpress' ),
				'content' => '<h2>' . __( 'Plugin Debug Mode Activation', 'debugpress' ) . '</h2><p>' . __( 'On this page, Advanced Tab, you have options to attempt enabling debug mode and save queries. But, it is highly recommended to do it via wp-config.php.', 'debugpress' ) .
				             '</p><h2>' . __( 'How to enable WordPress Debug Mode', 'debugpress' ) . '</h2><p>' . __( 'Add the following code into wp-config.php. Find the line in that file where WP_DEBUG is set, and replace that line with this code.', 'debugpress' ) .
				             '</p><pre>define( \'WP_DEBUG\', true );
define( \'WP_DEBUG_DISPLAY\', false );
define( \'WP_DEBUG_LOG\', true );
define( \'SAVEQUERIES\', true );</pre><p>' . __( 'This code enables debug mode, hides errors from being displayed, but enables logging errors into debug log file. It also enables saving of all SQL queries.', 'debugpress' ) . '</p><p><a href="https://debug.press/documentation/wordpress-setup/" class="button-primary" target="_blank" rel="noopener">' . __( 'More Information', 'debugpress' ) . '</a></p>',
			)
		);

		$screen->add_help_tab(
			array(
				'id'      => 'debugpress-demand',
				'title'   => __( 'On Demand', 'debugpress' ),
				'content' => '<h2>' . __( 'Debugger On Demand Activation', 'debugpress' ) . '</h2><p>' . __( 'Add the following argument and value to the URL. If the access key value is not configured in the plugin settings, On Demand loading is disabled.', 'debugpress' ) .
				             '</p><pre>?debugpress={ACCESS_KEY}</pre><p>' . __( 'If the URL already has arguments (? is already in URL), replace ? with &.', 'debugpress' ) . '</p>',
			)
		);

		$screen->add_help_tab(
			array(
				'id'      => 'debugpress-info',
				'title'   => __( 'Help & Support', 'debugpress' ),
				'content' => '<h2>' . __( 'Help & Support', 'debugpress' ) . '</h2><p>' . __( 'To get help with DebugPress, you can start with Knowledge Base list of frequently asked questions, user guides, articles (tutorials) and reference guide (for developers).', 'debugpress' ) .
				             '</p><p><a href="https://www.dev4press.com/kb/product/debugpress/" class="button-primary" target="_blank" rel="noopener">' . __( 'Knowledge Base', 'debugpress' ) . '</a> <a href="https://support.dev4press.com/forums/forum/plugins-free/debugpress/" class="button-secondary" target="_blank">' . __( 'Support Forum', 'debugpress' ) . '</a></p>',
			)
		);

		$screen->add_help_tab(
			array(
				'id'      => 'debugpress-bugs',
				'title'   => __( 'Found a bug?', 'debugpress' ),
				'content' => '<h2>' . __( 'Found a bug?', 'debugpress' ) . '</h2><p>' . __( 'If you find a bug in DebugPress, you can report it in the support forum.', 'debugpress' ) .
				             '</p><p>' . __( 'Before reporting a bug, make sure you use latest plugin version, your website and server meet system requirements. And, please be as descriptive as possible, include server side logged errors, or errors from browser debugger.', 'debugpress' ) .
				             '</p><p><a href="https://support.dev4press.com/forums/forum/plugins-free/debugpress/" class="button-primary" target="_blank" rel="noopener">' . __( 'Open new topic', 'debugpress' ) . '</a></p>',
			)
		);

		$screen->set_help_sidebar(
			'<p><strong>' . __( 'DebugPress', 'debugpress' ) . '</strong></p>' .
			'<p>' . join( '<br/>', array(
				'home'  => '<a target="_blank" rel="noopener" href="https://debug.press/">' . esc_html__( 'Home Page', 'debugpress' ) . '</a>',
				'kb'    => '<a target="_blank" rel="noopener" href="https://www.dev4press.com/kb/product/debugpress/">' . esc_html__( 'Knowledge Base', 'debugpress' ) . '</a>',
				'forum' => '<a target="_blank" rel="noopener" href="https://support.dev4press.com/forums/forum/plugins-free/debugpress/">' . esc_html__( 'Support Forum', 'debugpress' ) . '</a>',
			) ) . '</p>'
		);
	}

	public function admin_init() {
		register_setting(
			'debugpress',
			'debugpress_settings',
			array(
				'sanitize_callback' => array( $this, 'settings_sanitize' ),
			) );

		Settings::instance()->sections();
		Settings::instance()->fields();
	}

	public function settings_sanitize( $input ) : array {
		return debugpress_plugin()->process_settings( $input );
	}
}
