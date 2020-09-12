<?php

namespace Dev4Press\Plugin\DebugPress\Display;

class Loader {
	private $position;
	public $tabs = array();

	public function __construct() {
		$this->position = is_admin() ? debugpress_plugin()->get( 'button_admin' ) : debugpress_plugin()->get( 'button_frontend' );

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		} else {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		PrettyPrint::init()->ICON_DOWN  = '&#9660;';
		PrettyPrint::init()->ICON_RIGHT = '&#9658;';

		if ( $this->position == 'toolbar' ) {
			add_action( 'admin_bar_menu', array( $this, 'display_in_toolbar' ), 1000000 );
		} else {
			if ( is_admin() ) {
				add_action( 'admin_footer', array( $this, 'display_float_button' ), 1000001 );
			} else {
				add_action( 'wp_footer', array( $this, 'display_float_button' ), 1000001 );
			}
		}

		add_action( 'shutdown', array( $this, 'debugger_content' ), 1000000 );
	}

	public static function instance() {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Loader();
		}

		return $instance;
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'debugpress' );
		wp_enqueue_script( 'debugpress' );

		wp_localize_script( 'debugpress', 'debugpress_data', array(
			'position'            => $this->position,
			'events_show_details' => _x( "Show Details", "Popup message", "debugpress" ),
			'events_hide_details' => _x( "Hide Details", "Popup message", "debugpress" ),
			'icon_down'           => '&#9660;',
			'icon_right'          => '&#9658;'
		) );
	}

	public function button_class() {
		$class = 'gdpet-debug-dialog-button';

		if ( debugpress_tracker()->counts['total'] > 0 ) {
			if ( debugpress_tracker()->counts['errors'] > 0 ) {
				$class .= ' gdpet-debug-has-errors';
			} else {
				$class .= ' gdpet-debug-has-warnings';
			}
		}

		return $class;
	}

	public function display_in_toolbar() {
		global $wp_admin_bar;

		$wp_admin_bar->add_menu( array(
			'id'    => 'gdpet-debugger-button',
			'title' => $this->button(),
			'href'  => '#',
			'meta'  => array( 'class' => $this->button_class() )
		) );
	}

	public function display_float_button() {
		echo '<div id="gdpet-debugger-button" class="' . $this->button_class() . ' gdpet-float-button gdpet-position-' . $this->position . '"><a title="' . __( "Debugger Panel", "gd-press-tools" ) . '" role="button" href="#">' . $this->button() . '</a></div>';
	}

	public function button() {
		$button = '<i class="gdpet-icon gdpet-icon-bug"></i>';
		$button .= '<span class="gdpet-debug-button-indicators">';

		if ( debugpress_plugin()->get( 'ajax' ) ) {
			$button .= '<span class="gdpet-debug-has-ajax" style="display: none;" title="' . __( "AJAX Calls", "gd-press-tools" ) . '">0</span>';
		}

		$button .= '<span class="gdpet-debug-has-errors" style="display: ' . ( debugpress_tracker()->counts['total'] == 0 ? 'none' : 'inline' ) . '" title="' . __( "PHP Errors", "gd-press-tools" ) . '">' . debugpress_tracker()->counts['total'] . '</span></span>';
		$button .= '<span class="sanp-sr-only">' . __( "Open Debugger Panel", "gd-press-tools" ) . '</span>';

		return $button;
	}

	public function debugger_content() {
		debugpress_tracker()->end();

		$this->prepare_tabs();

		include( DEBUGPRESS_PLUGIN_PATH . 'forms/display.php' );
	}

	public function prepare_tabs() {
		$this->tabs = array(
			'basics' => __( "Basics", "gd-press-tools" )
		);

		if ( debugpress_plugin()->get( 'panel_request' ) ) {
			$this->tabs['request'] = __( "Request", "gd-press-tools" );
		}

		if ( ! is_admin() ) {
			$this->tabs['query'] = __( "Query", "gd-press-tools" );
		} else {
			$this->tabs['admin'] = __( "Admin", "gd-press-tools" );
		}

		if ( debugpress_plugin()->get( 'panel_content' ) ) {
			$this->tabs['content'] = __( "Content", "gd-press-tools" );
		}

		if ( debugpress_plugin()->get( 'panel_constants' ) ) {
			$this->tabs['constants'] = __( "Constants", "gd-press-tools" );
		}

		if ( ! empty( debugpress_wpdb()->queries ) ) {
			$this->tabs['queries'] = __( "SQL Queries", "gd-press-tools" );
		}

		if ( is_user_logged_in() && debugpress_plugin()->get( 'panel_user' ) ) {
			$this->tabs['user'] = __( "User", "gd-press-tools" );
		}

		if ( debugpress_plugin()->get( 'panel_enqueue' ) ) {
			$this->tabs['enqueue'] = __( "Enqueue", "gd-press-tools" );
		}

		if ( debugpress_plugin()->get( 'panel_php' ) ) {
			$this->tabs['php'] = __( "PHP", "gd-press-tools" );
		}

		if ( debugpress_plugin()->get( 'panel_system' ) ) {
			$this->tabs['system'] = __( "System", "gd-press-tools" );
		}

		if ( debugpress_plugin()->get( 'panel_http' ) && ! empty( debugpress_tracker()->httpapi ) ) {
			$this->tabs['http'] = __( "HTTP", "gd-press-tools" );
		}

		if ( debugpress_plugin()->get( 'panel_bbpress' ) && debugpress_has_bbpress() && is_bbpress() ) {
			$this->tabs['bbpress'] = __( "bbPress", "gd-press-tools" );
		}

		if ( ! empty( debugpress_tracker()->errors ) ) {
			$this->tabs['errors'] = __( "Errors", "gd-press-tools" ) . ' (' . debugpress_tracker()->counts['errors'] . ')';
		}

		if ( ! empty( debugpress_tracker()->deprecated ) ) {
			$this->tabs['deprecated'] = __( "Deprecated", "gd-press-tools" ) . ' (' . debugpress_tracker()->counts['deprecated'] . ')';
		}

		if ( ! empty( debugpress_tracker()->doingitwrong ) ) {
			$this->tabs['doingitwrong'] = __( "Doing It Wrong", "gd-press-tools" ) . ' (' . debugpress_tracker()->counts['doingitwrong'] . ')';
		}

		if ( debugpress_plugin()->get( 'ajax' ) ) {
			$this->tabs['ajax'] = __( "AJAX", "gd-press-tools" ) . ' (<span>0</span>)';
		}

		if ( ! empty( debugpress_tracker()->logged ) ) {
			$this->tabs['log'] = __( "Log", "gd-press-tools" );
		}

		$this->tabs = apply_filters( 'debugpress_debugger_popup_tabs', $this->tabs );
	}
}
