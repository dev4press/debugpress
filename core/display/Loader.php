<?php

namespace Dev4Press\Plugin\DebugPress\Display;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Loader {
	private $button = false;
	private $position;
	private $initialized = false;

	public $tabs = array();

	public function __construct() {

	}

	private function init() {
		if ( ! did_action( 'init' ) ) {
			return;
		}

		if ( ! $this->initialized ) {
			$this->initialized = true;
			$this->position    = is_admin() ? debugpress_plugin()->get( 'button_admin' ) : debugpress_plugin()->get( 'button_frontend' );

			if ( $this->position == 'toolbar' ) {
				add_action( 'admin_bar_menu', array( $this, 'display_in_toolbar' ), 1000000 );
			}

			if ( is_admin() ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			} else {
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
				add_action( 'login_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			}

			SQLFormat::$pre_attributes = '';
		}
	}

	public function run() {
		if ( is_admin() ) {
			add_action( 'admin_footer', array( $this, 'debugger_content_prepare_admin' ) );
		} else {
			add_action( 'wp_footer', array( $this, 'debugger_content_prepare_front' ) );
			add_action( 'login_footer', array( $this, 'debugger_content_prepare_front' ) );
		}
	}

	public function debugger_content_prepare_admin() {
		global $hook_suffix;

		if ( ! $this->button ) {
			add_action( 'admin_footer-' . $hook_suffix, array( $this, 'display_float_button' ), 999990 );
		}

		add_action( 'admin_footer-' . $hook_suffix, array( $this, 'debugger_content' ), 1000000 );
	}

	public function debugger_content_prepare_front() {
		if ( ! $this->button ) {
			add_action( 'shutdown', array( $this, 'display_float_button' ), 999990 );
		}

		add_action( 'shutdown', array( $this, 'debugger_content' ), 1000000 );
	}

	public static function instance() : Loader {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Loader();
			$instance->init();
		}

		return $instance;
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'debugpress' );
		wp_enqueue_script( 'debugpress' );

		wp_localize_script( 'debugpress', 'debugpress_data', array(
			'ajax_endpoint'       => admin_url( 'admin-ajax.php' ),
			'call_nonce'          => wp_create_nonce( 'debugpress-ajax-call' ),
			'events_show_details' => _x( "Show Details", "Popup message", "debugpress" ),
			'events_hide_details' => _x( "Hide Details", "Popup message", "debugpress" ),
			'icon_down'           => '<i class="debugpress-icon debugpress-icon-caret-down"></i>',
			'icon_right'          => '<i class="debugpress-icon debugpress-icon-caret-right"></i>',
			'mousetrap'           => debugpress_plugin()->get( 'mousetrap' ),
			'mousetrap_sequence'  => debugpress_plugin()->get( 'mousetrap_sequence' ),
		) );

		wp_add_inline_style( 'debugpress', $this->vars_styling_override() );
	}

	public function display_in_toolbar() {
		global $wp_admin_bar;

		$wp_admin_bar->add_menu( array(
			'id'    => 'debugpress-debugger-button',
			'title' => $this->button(),
			'href'  => '#',
			'meta'  => array( 'class' => $this->button_class() . ' menupop' )
		) );

		$this->button = true;
	}

	public function display_float_button() {
		$_position = $this->position == 'toolbar' ? apply_filters( 'debugpress-float-button-fallback_position', 'topright' ) : $this->position;

		echo '<div id="debugpress-debugger-button" class="' . $this->button_class() . ' debugpress-float-button debugpress-position-' . $_position . '"><a title="' . __( "Debugger Panel", "debugpress" ) . '" role="button" href="#">' . $this->button() . '</a></div>';
	}

	public function button_class() : string {
		$class = 'debugpress-debug-dialog-button';

		if ( debugpress_tracker()->counts[ 'total' ] > 0 ) {
			if ( debugpress_tracker()->counts[ 'errors' ] > 0 ) {
				$class .= ' debugpress-debug-has-errors';
			} else {
				$class .= ' debugpress-debug-has-warnings';
			}
		}

		return $class;
	}

	public function button() : string {
		$_error_counts = debugpress_tracker()->counts[ 'total' ];
		$_http_counts  = count( debugpress_tracker()->httpapi );
		$_store_counts = count( debugpress_tracker()->logged );

		$button = '<i class="debugpress-icon debugpress-icon-bug"></i>';
		$button .= '<span class="debugpress-debug-button-indicators">';

		if ( debugpress_plugin()->get( 'ajax' ) ) {
			$button .= '<span class="debugpress-debug-has-ajax" style="display: none;" title="' . __( "AJAX Calls", "debugpress" ) . '">0</span>';
		}

		if ( debugpress_plugin()->get( 'panel_http' ) ) {
			$button .= '<span class="debugpress-debug-has-httpcalls" style="display: ' . ( $_http_counts == 0 ? 'none' : 'inline' ) . '" title="' . __( "HTTP API Calls", "debugpress" ) . '">' . $_http_counts . '</span>';
		}

		$button .= '<span class="debugpress-debug-has-stored" style="display: ' . ( $_store_counts == 0 ? 'none' : 'inline' ) . '" title="' . __( "Storage", "debugpress" ) . '">' . $_store_counts . '</span>';
		$button .= '<span class="debugpress-debug-has-errors" style="display: ' . ( $_error_counts == 0 ? 'none' : 'inline' ) . '" title="' . __( "PHP Errors", "debugpress" ) . '">' . $_error_counts . '</span>';

		$button .= '</span>';
		$button .= '<span class="sanp-sr-only">' . __( "Open Debugger Panel", "debugpress" ) . '</span>';

		return $button;
	}

	public function debugger_content() {
		debugpress_tracker()->end();

		$this->prepare_tabs();

		include( DEBUGPRESS_PLUGIN_PATH . 'forms/display.php' );
	}

	public function prepare_tabs() {
		$this->tabs = array(
			'basics' => __( "Basics", "debugpress" )
		);

		if ( debugpress_plugin()->get( 'panel_request' ) ) {
			$this->tabs[ 'request' ] = __( "Request", "debugpress" );
		}

		if ( ! is_admin() ) {
			$this->tabs[ 'query' ] = __( "Query", "debugpress" );
		}

		if ( debugpress_plugin()->get( 'panel_content' ) ) {
			$this->tabs[ 'content' ] = __( "Content", "debugpress" );
		}

		if ( debugpress_plugin()->get( 'panel_rewriter' ) ) {
			$this->tabs[ 'rewriter' ] = __( "Rewriter", "debugpress" );
		}

		if ( debugpress_plugin()->get( 'panel_roles' ) ) {
			$this->tabs[ 'roles' ] = __( "Roles", "debugpress" );
		}

		if ( debugpress_plugin()->get( 'panel_constants' ) ) {
			$this->tabs[ 'constants' ] = __( "Constants", "debugpress" );
		}

		if ( debugpress_plugin()->get( 'panel_hooks' ) ) {
			$this->tabs[ 'hooks' ] = __( "Hooks", "debugpress" );
		}

		if ( ! empty( debugpress_db()->wpdb()->queries ) ) {
			$this->tabs[ 'queries' ] = __( "SQL Queries", "debugpress" );
		}

		if ( is_user_logged_in() && debugpress_plugin()->get( 'panel_user' ) ) {
			$this->tabs[ 'user' ] = __( "User", "debugpress" );
		}

		if ( debugpress_plugin()->get( 'panel_enqueue' ) ) {
			$this->tabs[ 'enqueue' ] = __( "Enqueue", "debugpress" );
		}

		if ( debugpress_plugin()->get( 'panel_php' ) ) {
			$this->tabs[ 'php' ] = __( "PHP", "debugpress" );
		}

		if ( debugpress_plugin()->get( 'panel_system' ) ) {
			$this->tabs[ 'server' ] = __( "System", "debugpress" );
		}

		if ( debugpress_plugin()->get( 'panel_bbpress' ) && debugpress_has_bbpress() && is_bbpress() ) {
			$this->tabs[ 'bbpress' ] = __( "bbPress", "debugpress" );
		}

		if ( ! empty( debugpress_tracker()->plugins ) ) {
			$this->tabs[ 'plugins' ] = __( "Plugins", "debugpress" );
		}

		if ( debugpress_plugin()->get( 'panel_http' ) && ! empty( debugpress_tracker()->httpapi ) ) {
			$this->tabs[ 'http' ] = __( "HTTP", "debugpress" ) . ' (' . count( debugpress_tracker()->httpapi ) . ')';
		}

		if ( debugpress_plugin()->get( 'ajax' ) ) {
			$this->tabs[ 'ajax' ] = __( "AJAX", "debugpress" ) . ' (<span>0</span>)';
		}

		if ( ! empty( debugpress_tracker()->logged ) ) {
			$this->tabs[ 'store' ] = __( "Store", "debugpress" ) . ' (' . count( debugpress_tracker()->logged ) . ')';
		}

		if ( ! empty( debugpress_tracker()->deprecated ) ) {
			$this->tabs[ 'deprecated' ] = __( "Deprecated", "debugpress" ) . ' (' . debugpress_tracker()->counts[ 'deprecated' ] . ')';
		}

		if ( ! empty( debugpress_tracker()->doingitwrong ) ) {
			$this->tabs[ 'doingitwrong' ] = __( "Doing It Wrong", "debugpress" ) . ' (' . debugpress_tracker()->counts[ 'doingitwrong' ] . ')';
		}

		if ( ! empty( debugpress_tracker()->errors ) ) {
			$this->tabs[ 'errors' ] = __( "Errors", "debugpress" ) . ' (' . debugpress_tracker()->counts[ 'errors' ] . ')';
		}

		$this->tabs[ 'layout' ] = array(
			'label' => __( "Layout", "debugpress" ),
			'tab'   => '<i class="debugpress-icon debugpress-icon-layout"></i>'
		);

		$this->tabs[ 'tools' ] = array(
			'label' => __( "Tools", "debugpress" ),
			'tab'   => '<i class="debugpress-icon debugpress-icon-screwdriver-wrench"></i>'
		);

		if ( debugpress_plugin()->get( 'panel_debuglog' ) ) {
			$this->tabs[ 'debuglog' ] = array(
				'label' => __( "Debug Log", "debugpress" ),
				'tab'   => '<i class="debugpress-icon debugpress-icon-file-lines"></i>'
			);
		}

		$this->tabs = apply_filters( 'debugpress-debugger-popup-tabs', $this->tabs );
	}

	private function vars_styling_override() : string {
		$mods = array();
		$vars = array(
			'base-font-size'   => '13px',
			'sql-font-size'    => '13px',
			'pretty-font-size' => '12px'
		);

		foreach ( $vars as $var => $value ) {
			$mod = apply_filters( 'debugpress-styling-var-override-' . $var, $value );

			if ( $mod !== $value ) {
				$mods[] = '--debugpress-' . $var . ': ' . $mod . ';';
			}
		}

		if ( ! empty( $mods ) ) {
			return ':root {' . D4P_EOL . D4P_TAB . join( D4P_EOL . D4P_TAB, $mods ) . D4P_EOL . '}';
		} else {
			return '';
		}
	}
}
