<?php

namespace Dev4Press\Plugin\DebugPress\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Display\Loader;
use Dev4Press\Plugin\DebugPress\Track\AJAX;

class Plugin {
	private $_settings = array();
	private $_defaults = array(
		'active'          => false,
		'admin'           => false,
		'frontend'        => false,
		'button_admin'    => 'toolbar',
		'button_frontend' => 'toolbar',
		'for_super_admin' => true,
		'for_roles'       => true,
		'for_visitor'     => false,

		'auto_wpdebug'     => false,
		'auto_savequeries' => false,

		'panel_content'   => true,
		'panel_request'   => true,
		'panel_enqueue'   => false,
		'panel_system'    => false,
		'panel_user'      => false,
		'panel_constants' => false,
		'panel_http'      => false,
		'panel_php'       => false,
		'panel_bbpress'   => false
	);

	private $_extras = array(
		'ajax'                   => true,
		'slow_query_cutoff'      => 10,
		'use_sql_formatter'      => true,
		'format_queries_panel'   => true,
		'errors_override'        => true,
		'deprecated_override'    => true,
		'doingitwrong_override'  => true,
		'integrate_admin_footer' => true
	);

	private $_allowed = false;
	private $_animated_popup_version = '1.9';
	private $_wp_version;
	private $_wp_version_real;

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 0 );
		add_action( 'init', array( $this, 'init' ) );
	}

	/** @return \Dev4Press\Plugin\DebugPress\Main\Plugin */
	public static function instance() {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Plugin();
		}

		return $instance;
	}

	public function wp_version() {
		return $this->_wp_version;
	}

	public function wp_version_real() {
		return $this->_wp_version_real;
	}

	public function plugins_loaded() {
		global $wp_version;

		$this->_wp_version      = substr( str_replace( '.', '', $wp_version ), 0, 2 );
		$this->_wp_version_real = $wp_version;

		$this->_settings = get_option( 'debugpress_settings', $this->_defaults );
		$this->_allowed  = apply_filters( 'debugpress_debugger_is_allowed', $this->is_user_allowed() );

		if ( $this->get( 'auto_wpdebug' ) && ! defined( 'WP_DEBUG' ) ) {
			define( 'WP_DEBUG', true );
		}

		if ( $this->get( 'auto_savequeries' ) && ! defined( 'SAVEQUERIES' ) ) {
			define( 'SAVEQUERIES', true );
		}

		define( 'DEBUGPRESS_IS_DEBUG', defined( 'WP_DEBUG' ) && WP_DEBUG );
		define( 'DEBUGPRESS_IS_DEBUG_LOG', DEBUGPRESS_IS_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG );

		debugpress_tracker();

		if ( $this->get( 'ajax' ) && DEBUGPRESS_IS_AJAX ) {
			AJAX::instance();
		}
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
			return $this->_settings[ $name ];
		} else if ( isset( $this->_defaults[ $name ] ) ) {
			return $this->_defaults[ $name ];
		} else if ( isset( $this->_extras[ $name ] ) ) {
			return $this->_extras[ $name ];
		}

		return $fallback;
	}

	public function process_settings( $input ) {
		$types = array(
			'for_roles'       => 'array',
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

	private function is_user_allowed() {
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

	public function environment() {
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

	public function build_stats( $key = 'wp_footer' ) {
		$gd = '<div class="debugpress-debugger-stats-block">';

		if ( is_null( $key ) ) {
			$key = is_admin() ? 'in_admin_footer' : 'wp_footer';
		}

		$env = $this->environment();

		if ( ! empty( $env ) ) {
			$gd .= '<strong class="debugpress-debugger-environment debugpress-env-' . $env['type'] . '">' . $env['label'] . '</strong> &middot; ';
		}

		$gd .= __( "WordPress", "debugpress" ) . ': <strong>' . $this->_wp_version_real . '</strong> &middot; ';
		$gd .= __( "PHP", "debugpress" ) . ': <strong>' . phpversion() . '</strong><span> &middot; </span>';

		$gd .= __( "IP", "debugpress" ) . ': <strong>' . IP::get_visitor_ip() . '</strong> &middot; ';
		$gd .= __( "Queries", "debugpress" ) . ': <strong>' . debugpress_tracker()->get( $key, 'queries' ) . '</strong> &middot; ';
		$gd .= __( "Memory", "debugpress" ) . ': <strong>' . debugpress_tracker()->get( $key, 'memory' ) . '</strong> &middot; ';
		$gd .= __( "Loaded", "debugpress" ) . ': <strong>' . debugpress_tracker()->get( $key, 'time' ) . ' ' . __( "seconds.", "debugpress" ) . '</strong>';
		$gd .= '</div>';

		return $gd;
	}
}
