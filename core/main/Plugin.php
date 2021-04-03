<?php

namespace Dev4Press\Plugin\DebugPress\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Display\Loader;
use Dev4Press\Plugin\DebugPress\Track\AJAX as AJAXTracker;

class Plugin {
	private $_settings = array();
	private $_defaults = array(
		'pr'                    => 'prettyprint',
		'active'                => false,
		'admin'                 => false,
		'frontend'              => false,
		'ajax'                  => true,
		'ajax_to_debuglog'      => false,
		'button_admin'          => 'toolbar',
		'button_frontend'       => 'toolbar',
		'for_super_admin'       => true,
		'for_roles'             => true,
		'for_visitor'           => false,
		'auto_wpdebug'          => false,
		'auto_savequeries'      => false,
		'errors_override'       => true,
		'deprecated_override'   => true,
		'doingitwrong_override' => true,
		'panel_content'         => true,
		'panel_roles'           => true,
		'panel_request'         => true,
		'panel_debuglog'        => true,
		'panel_enqueue'         => false,
		'panel_system'          => false,
		'panel_user'            => false,
		'panel_constants'       => false,
		'panel_http'            => false,
		'panel_php'             => false,
		'panel_bbpress'         => false
	);

	private $_extras = array(
		'panel_hooks'          => false,
		'slow_query_cutoff'    => 10,
		'use_sql_formatter'    => true,
		'format_queries_panel' => true,
		'ajax_header_no_cache' => true
	);

	private $_allowed = false;
	private $_animated_popup_version = '1.9';
	private $_wp_version;
	private $_wp_version_real;
	private $_cp_version;
	private $_cp_version_real;

	public function __construct() {

	}

	/** @return \Dev4Press\Plugin\DebugPress\Main\Plugin */
	public static function instance() : Plugin {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Plugin();
			$instance->run();
		}

		return $instance;
	}

	private function run() {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 0 );
		add_action( 'plugins_loaded', array( $this, 'activation' ), DEBUGPRESS_ACTIVATION_PRIORITY );
		add_action( 'init', array( $this, 'init' ) );
	}

	public function wp_version() : string {
		return $this->_wp_version;
	}

	public function wp_version_real() : string {
		return $this->_wp_version_real;
	}

	public function cp_version() : string {
		return $this->_cp_version;
	}

	public function cp_version_real() : string {
		return $this->_cp_version_real;
	}

	public function plugins_loaded() {
		global $wp_version;

		if ( debugpress_is_classicpress() ) {
			$this->_cp_version_real = classicpress_version();
			$this->_cp_version      = substr( str_replace( '.', '', $this->_cp_version_real ), 0, 2 );
		}

		$this->_wp_version_real = $wp_version;
		$this->_wp_version      = substr( str_replace( '.', '', $wp_version ), 0, 2 );
	}

	public function activation() {
		$this->_settings = get_option( 'debugpress_settings', $this->_defaults );
		$this->_allowed  = apply_filters( 'debugpress-debugger-is-allowed', $this->is_user_allowed() );

		$this->load_printer();
		$this->define_constants();

		if ( DEBUGPRESS_IS_AJAX && $this->_allowed ) {
			if ( $this->get( 'ajax' ) ) {
				AJAXTracker::instance();
			}

			if ( $this->get( 'panel_debuglog' ) ) {
				AJAX::instance();
			}
		}

		debugpress_tracker();
	}

	private function load_printer( $name = '' ) {
		if ( ! function_exists( 'debugpress_r' ) ) {
			$name = empty( $name ) ? $this->get( 'pr' ) : 'prettyprint';
			$path = DEBUGPRESS_PLUGIN_PATH . 'core/printer/' . $name . '/load.php';

			if ( file_exists( $path ) ) {
				require_once( $path );
			} else {
				$this->load_printer( 'prettyprint' );
			}
		}
	}

	private function define_constants() {
		if ( $this->get( 'auto_wpdebug' ) && ! defined( 'WP_DEBUG' ) ) {
			define( 'WP_DEBUG', true );
		}

		if ( $this->get( 'auto_savequeries' ) && ! defined( 'SAVEQUERIES' ) ) {
			define( 'SAVEQUERIES', true );
		}

		define( 'DEBUGPRESS_IS_DEBUG', defined( 'WP_DEBUG' ) && WP_DEBUG );
		define( 'DEBUGPRESS_IS_DEBUG_LOG', DEBUGPRESS_IS_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG );

		$version = str_replace( '.', '', phpversion() );
		$version = intval( substr( $version, 0, 2 ) );

		define( 'DEBUGPRESS_PHP_VERSION', $version );
	}

	public function init() {
		wp_register_style( 'animated-popup', DEBUGPRESS_PLUGIN_URL . 'popup/popup.min.css', array(), $this->_animated_popup_version );
		wp_register_style( 'debugpress', DEBUGPRESS_PLUGIN_URL . 'css/debugpress' . ( DEBUGPRESS_IS_DEBUG ? '' : '.min' ) . '.css', array( 'animated-popup' ), DEBUGPRESS_VERSION );
		wp_register_script( 'animated-popup', DEBUGPRESS_PLUGIN_URL . 'popup/popup.min.js', array( 'jquery' ), $this->_animated_popup_version, true );
		wp_register_script( 'debugpress', DEBUGPRESS_PLUGIN_URL . 'js/debugpress' . ( DEBUGPRESS_IS_DEBUG ? '' : '.min' ) . '.js', array( 'animated-popup' ), DEBUGPRESS_VERSION, true );

		if ( ! DEBUGPRESS_IS_AJAX && ! DEBUGPRESS_IS_CRON && $this->_allowed ) {
			$allowed = is_admin() ? $this->get( 'admin' ) : $this->get( 'frontend' );

			if ( $allowed ) {
				Loader::instance();
			}
		}
	}

	public function get( $name, $fallback = false ) {
		if ( isset( $this->_settings[ $name ] ) ) {
			$value = $this->_settings[ $name ];
		} else if ( isset( $this->_defaults[ $name ] ) ) {
			$value = $this->_defaults[ $name ];
		} else if ( isset( $this->_extras[ $name ] ) ) {
			$value = $this->_extras[ $name ];
		} else {
			$value = $fallback;
		}

		return apply_filters( 'debugpress-get-settings-value-' . $name, $value, $fallback );
	}

	public function process_settings( $input ) : array {
		$types = array(
			'for_roles'       => 'array',
			'pr'              => 'string',
			'button_admin'    => 'string',
			'button_frontend' => 'string'
		);

		$settings = array();

		foreach ( $this->_defaults as $key => $value ) {
			if ( isset( $types[ $key ] ) ) {
				switch ( $types[ $key ] ) {
					default:
					case 'string':
						$settings[ $key ] = sanitize_text_field( $input[ $key ] );
						break;
					case 'array':
						$settings[ $key ] = isset( $input[ $key ] ) ? (array) $input[ $key ] : array();
						$settings[ $key ] = array_map( 'sanitize_text_field', $settings[ $key ] );
				}
			} else {
				$settings[ $key ] = isset( $input[ $key ] ) && $input[ $key ] == 'on';
			}
		}

		return $settings;
	}

	private function is_user_allowed() : bool {
		if ( is_super_admin() ) {
			return $this->get( 'for_super_admin' );
		} else if ( is_user_logged_in() ) {
			$allowed = $this->get( 'for_roles' );

			if ( $allowed === true || is_null( $allowed ) ) {
				return true;
			} else if ( is_array( $allowed ) && empty( $allowed ) ) {
				return false;
			} else if ( is_array( $allowed ) && ! empty( $allowed ) ) {
				global $current_user;

				if ( is_array( $current_user->roles ) ) {
					$matched = array_intersect( $current_user->roles, $allowed );

					return ! empty( $matched );
				}
			}
		} else {
			return $this->get( 'for_visitor' );
		}

		return false;
	}

	public function environment() : array {
		$env = array();

		if ( $this->_wp_version > 54 && function_exists( 'wp_get_environment_type' ) ) {
			$env['type'] = wp_get_environment_type();

			switch ( $env['type'] ) {
				default:
				case 'production':
					$env['type']  = 'production';
					$env['label'] = __( "Production Environment", "debugpress" );
					break;
				case 'staging':
					$env['label'] = __( "Staging Environment", "debugpress" );
					break;
				case 'local':
					$env['label'] = __( "Local Environment", "debugpress" );
					break;
				case 'development':
					$env['label'] = __( "Development Environment", "debugpress" );
					break;
			}
		}

		return $env;
	}

	public function build_stats( $key = 'wp_footer' ) : string {
		$gd = '<div class="debugpress-debugger-stats-block">';

		if ( is_null( $key ) ) {
			$key = is_admin() ? 'in_admin_footer' : 'wp_footer';
		}

		$env = $this->environment();

		if ( ! empty( $env ) ) {
			$gd .= '<strong class="debugpress-debugger-environment debugpress-env-' . $env['type'] . '">' . $env['label'] . '</strong> &middot; ';
		}

		$gd .= Info::cms_name() . ': <strong>' . Info::cms_version() . '</strong> &middot; ';
		$gd .= __( "PHP", "debugpress" ) . ': <strong>' . phpversion() . '</strong><span> &middot; </span>';

		$gd .= __( "IP", "debugpress" ) . ': <strong>' . IP::get_visitor_ip() . '</strong> &middot; ';
		$gd .= __( "Queries", "debugpress" ) . ': <strong>' . debugpress_tracker()->get( $key, 'queries' ) . '</strong> &middot; ';
		$gd .= __( "Memory", "debugpress" ) . ': <strong>' . debugpress_tracker()->get( $key, 'memory' ) . '</strong> &middot; ';
		$gd .= __( "Loaded", "debugpress" ) . ': <strong>' . debugpress_tracker()->get( $key, 'time' ) . ' ' . __( "seconds.", "debugpress" ) . '</strong>';
		$gd .= '</div>';

		return $gd;
	}
}