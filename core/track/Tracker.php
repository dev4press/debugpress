<?php

namespace Dev4Press\Plugin\DebugPress\Track;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\IP;
use Exception;
use WP_Error;

class Tracker {
	public $timer_start_wp = 0;
	public $timer_start = 0;
	public $timer_current = 0;
	public $memory_start = 0;
	public $memory_current = 0;
	public $query_start = 0;
	public $query_current = 0;
	public $count_hooks = 0;
	public $has_errors = 0;
	public $has_warnings = 0;

	public $plugins = array();
	public $snapshots = array();
	public $objects = array();
	public $errors = array();
	public $deprecated = array();
	public $doingitwrong = array();
	public $logged = array();
	public $httpapi = array();

	public $counts = array(
		'total'        => 0,
		'errors'       => 0,
		'doingitwrong' => 0,
		'deprecated'   => 0
	);

	private $_snapshot_actions = array(
		'setup_theme',
		'after_setup_theme',
		'init',
		'wp',
		'wp_head',
		'wp_footer',
		'admin_init',
		'admin_head',
		'in_admin_footer',
		'admin_footer'
	);

	private $_bbp_queries = array(
		'bbp_has_forums',
		'bbp_has_topics',
		'bbp_has_replies',
		'bbp_has_search_results'
	);

	private $_error_handler = null;

	private $_http_transport = null;
	private $_http_info = null;

	public function __construct() {
		global $timestart;

		$this->timer_start_wp = $timestart;
		$this->timer_start    = microtime( true );
		$this->memory_start   = memory_get_usage();
		$this->query_start    = get_num_queries();

		$this->start();

		$this->actions();

		do_action( 'debugpress-tracker-getting-ready' );
	}

	public static function instance() : Tracker {
		static $instance = null;

		if ( null === $instance ) {
			$instance = new Tracker();
		}

		return $instance;
	}

	public function start() {
		$this->snapshot( '_start' );
	}

	public function end() {
		do_action( 'debugpress-tracker-plugins-call' );

		$this->snapshot( '_end' );
	}

	public function plugins_loaded() {
		$this->snapshot( 'plugins_loaded' );
	}

	public function actions() {
		if ( DEBUGPRESS_IS_DEBUG && debugpress_plugin()->get( 'errors_override' ) ) {
			$this->_error_handler = set_error_handler( array( $this, 'track_error' ) );

			register_shutdown_function( array( $this, 'track_fatal_error' ) );
		}

		if ( debugpress_plugin()->get( 'doingitwrong_override' ) ) {
			add_action( 'doing_it_wrong_run', array( $this, 'track_wrong' ), 10, 3 );

			add_filter( 'doing_it_wrong_trigger_error', '__return_false' );
		}

		if ( debugpress_plugin()->get( 'deprecated_override' ) ) {
			add_action( 'deprecated_function_run', array( $this, 'track_function' ), 10, 3 );
			add_action( 'deprecated_constructor_run', array( $this, 'track_constructor' ), 10, 2 );
			add_action( 'deprecated_file_included', array( $this, 'track_file' ), 10, 4 );
			add_action( 'deprecated_argument_run', array( $this, 'track_argument' ), 10, 3 );

			add_filter( 'deprecated_function_trigger_error', '__return_false' );
			add_filter( 'deprecated_constructor_trigger_error', '__return_false' );
			add_filter( 'deprecated_file_trigger_error', '__return_false' );
			add_filter( 'deprecated_argument_trigger_error', '__return_false' );
		}

		foreach ( $this->_snapshot_actions as $action ) {
			add_action( $action, array( $this, 'wp_action' ), 1 );
		}

		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 10000000 );

		if ( debugpress_has_bbpress() ) {
			foreach ( $this->_bbp_queries as $filter ) {
				add_filter( $filter, array( $this, 'bbp_query_filter' ), 1 );
			}
		}

		add_filter( 'http_request_args', array( $this, 'http_request_args' ), 10000, 2 );
		add_filter( 'pre_http_request', array( $this, 'pre_http_request' ), 10000, 3 );
		add_action( 'http_api_debug', array( $this, 'http_api_debug' ), 10000, 5 );

		add_action( 'requests-curl.before_request', array( $this, 'req_curl_before_request' ), 10000 );
		add_action( 'requests-curl.after_request', array( $this, 'req_both_after_request' ), 10000, 2 );
		add_action( 'requests-fsockopen.before_request', array( $this, 'req_fsockopen_before_request' ), 10000 );
		add_action( 'requests-fsockopen.after_request', array( $this, 'req_both_after_request' ), 10000, 2 );

		if ( is_admin() ) {
			add_action( 'admin_footer', array( $this, 'log_http_apis' ), 10000 );
		} else {
			add_action( 'wp_footer', array( $this, 'log_http_apis' ), 10000 );
		}
	}

	public function http_request_args( $args, $url ) {
		$trace = $this->_get_caller();

		if ( isset( $args[ '_debugpress_key_' ] ) ) {
			$args[ '_debugpress_key_original_' ] = $args[ '_debugpress_key_' ];
			$start                               = $this->httpapi[ $args[ '_debugpress_key_' ] ][ 'start' ];
		} else {
			$start = microtime( true );
		}

		$args[ '_debugpress_key_' ] = md5( microtime( true ) . '-' . $url ) . '-' . microtime( true );

		if ( ! empty( $url ) ) {
			$this->httpapi[ $args[ '_debugpress_key_' ] ] = array(
				'url'   => $url,
				'args'  => $args,
				'start' => $start,
				'trace' => $trace,
			);
		}

		return $args;
	}

	public function pre_http_request( $response, $args, $url ) {
		if ( $response === false ) {
			return $response;
		}

		$this->_log_http_api_call( $response, $args, $url );

		return $response;
	}

	public function http_total_time() {
		$time = 0;

		foreach ( $this->httpapi as $request ) {
			$time += isset( $request[ 'info' ][ 'total_time' ] ) ? $request[ 'info' ][ 'total_time' ] : 0;
		}

		return $time;
	}

	public function http_api_debug( $response, $action, $class, $args, $url ) {
		if ( $action == 'response' ) {
			$key = $args[ '_debugpress_key_' ];

			if ( ! empty( $class ) ) {
				$this->httpapi[ $key ][ 'transport' ] = str_replace( 'wp_http_', '', strtolower( $class ) );
			} else {
				$this->httpapi[ $key ][ 'transport' ] = null;
			}

			$this->_log_http_api_call( $response, $args, $url );
		}
	}

	public function req_curl_before_request() {
		$this->_http_transport = 'curl';
	}

	public function req_fsockopen_before_request() {
		$this->_http_transport = 'fsockopen';
	}

	public function req_both_after_request( $headers, $info = null ) {
		$this->_http_info = $info;
	}

	public function query() : int {
		$this->query_current = get_num_queries();

		return $this->query_current;
	}

	public function memory() : int {
		$this->memory_current = memory_get_usage();

		return $this->memory_current;
	}

	public function timer() : float {
		$this->timer_current = microtime( true );

		return $this->timer_current - $this->timer_start_wp;
	}

	public function hooks() : int {
		return $this->_hooks_object();
	}

	public function plugin( $plugin_file, $data = array() ) {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		$plugin = get_plugin_data( $plugin_file );

		if ( ! isset( $this->plugins[ $plugin_file ] ) ) {
			$this->plugins[ $plugin_file ] = array();
		}

		$this->plugins[ $plugin_file ][] = array(
			'file'   => $plugin_file,
			'plugin' => $plugin,
			'data'   => $data
		);
	}

	public function log( $object, $title = '', $sql = false ) {
		$log = array(
			'time'   => current_time( 'mysql' ),
			'title'  => $title,
			'print'  => $object,
			'caller' => $this->_get_caller(),
			'sql'    => $sql
		);

		$this->logged[] = $log;
	}

	public function track_fatal_error() {
		$error = error_get_last();

		if ( ! is_null( $error ) ) {
			$errno   = $error[ 'type' ];
			$errstr  = $error[ 'message' ];
			$errfile = $error[ 'file' ];
			$errline = $error[ 'line' ];

			$this->track_error( $errno, $errstr, $errfile, $errline );
		}
	}

	public function track_error( $errno, $errstr, $errfile, $errline ) {
		if ( ! ( error_reporting() & $errno ) ) {
			return false;
		}

		$caller = $this->_get_caller();

		$this->errors[] = compact( 'errno', 'errstr', 'errfile', 'errline', 'caller' );

		$this->counts[ 'errors' ] ++;
		$this->counts[ 'total' ] ++;

		if ( $errno == E_ERROR || $errno == E_USER_ERROR ) {
			$this->has_errors ++;
		} else {
			$this->has_warnings ++;
		}

		if ( is_callable( $this->_error_handler ) ) {
			call_user_func( $this->_error_handler, $errno, $errstr, $errfile, $errline );
		}

		return false;
	}

	public function track_wrong( $function, $message, $version ) {
		$backtrace = debug_backtrace();

		$deprecated = $function . '()';
		$in_file    = $this->_strip_abspath( $backtrace[ 3 ][ 'file' ] );
		$on_line    = $backtrace[ 3 ][ 'line' ];
		$caller     = $this->_get_caller( 'doing_it_wrong_run' );

		$this->doingitwrong[] = compact( 'deprecated', 'message', 'version', 'in_file', 'on_line', 'caller' );

		$this->counts[ 'doingitwrong' ] ++;
		$this->counts[ 'total' ] ++;
	}

	public function track_function( $function, $replacement, $version ) {
		$backtrace = debug_backtrace();

		$deprecated = $function . '()';
		$hook       = null;
		$bt         = 4;

		if ( ! isset( $backtrace[ 4 ][ 'file' ] ) && 'call_user_func_array' == $backtrace[ 5 ][ 'function' ] ) {
			$hook = $backtrace[ 6 ][ 'args' ][ 0 ];
			$bt   = 6;
		}

		$in_file = $this->_strip_abspath( $backtrace[ $bt ][ 'file' ] );
		$on_line = $backtrace[ $bt ][ 'line' ];

		$this->deprecated[ 'function' ][] = compact( 'deprecated', 'replacement', 'version', 'hook', 'in_file', 'on_line' );

		$this->counts[ 'deprecated' ] ++;
		$this->counts[ 'total' ] ++;
	}

	public function track_file( $file, $replacement, $version, $message ) {
		$backtrace = debug_backtrace();

		$deprecated = $this->_strip_abspath( $backtrace[ 3 ][ 'file' ] );
		$in_file    = $this->_strip_abspath( $backtrace[ 4 ][ 'file' ] );
		$on_line    = $backtrace[ 4 ][ 'line' ];

		$this->deprecated[ 'file' ][] = compact( 'deprecated', 'replacement', 'message', 'version', 'in_file', 'on_line', 'file' );

		$this->counts[ 'deprecated' ] ++;
		$this->counts[ 'total' ] ++;
	}

	public function track_argument( $function, $message, $version ) {
		$backtrace = debug_backtrace();

		$deprecated = $function . '()';
		$menu       = null;
		$in_file    = null;
		$on_line    = null;

		switch ( $function ) {
			case 'options.php' :
				$deprecated = __( "Unregistered Setting", "debugpress" );
				break;
			case 'has_cap' :
				if ( 0 === strpos( $backtrace[ 7 ][ "function" ], "add_" ) && "_page" == substr( $backtrace[ 7 ][ "function" ], - 5 ) ) {
					$bt = 7;
					if ( 0 === strpos( $backtrace[ 8 ][ "function" ], "add_" ) && "_page" == substr( $backtrace[ 8 ][ "function" ], - 5 ) ) {
						$bt = 8;
					}
					$in_file    = $this->_strip_abspath( $backtrace[ $bt ][ "file" ] );
					$on_line    = $backtrace[ $bt ][ "line" ];
					$deprecated = $backtrace[ $bt ][ "function" ] . "()";
				} else if ( "_wp_menu_output" == $backtrace[ 7 ][ "function" ] ) {
					$deprecated = "current_user_can()";
					$menu       = true;
				} else {
					$in_file    = $this->_strip_abspath( $backtrace[ 6 ][ "file" ] );
					$on_line    = $backtrace[ 6 ][ "line" ];
					$deprecated = "current_user_can()";
				}
				break;
			case 'get_plugin_data' :
				$in_file = $this->_strip_abspath( $backtrace[ 4 ][ "args" ][ 0 ] );
				break;
			case 'define()' :
			case 'define' :
				if ( 'ms_subdomain_constants' == $backtrace[ 4 ][ "function" ] ) {
					$deprecated = 'VHOST';
				}
				break;
			default :
				$in_file = $this->_strip_abspath( $backtrace[ 4 ][ "file" ] );
				$on_line = $backtrace[ 4 ][ "line" ];
				break;
		}

		$this->deprecated[ 'argument' ][] = compact( 'deprecated', 'message', 'menu', 'version', 'in_file', 'on_line' );

		$this->counts[ 'deprecated' ] ++;
		$this->counts[ 'total' ] ++;
	}

	public function track_constructor( $class, $version ) {
		$backtrace = debug_backtrace();

		$deprecated = $class;
		$in_file    = $this->_strip_abspath( $backtrace[ 4 ][ 'file' ] );
		$on_line    = $backtrace[ 4 ][ 'line' ];

		$this->deprecated[ 'constructor' ][] = compact( 'deprecated', 'version', 'in_file', 'on_line' );

		$this->counts[ 'deprecated' ] ++;
		$this->counts[ 'total' ] ++;
	}

	public function wp_action() {
		$this->snapshot( current_filter() );
	}

	public function bbp_query_filter( $out ) {
		$filter = current_filter();

		switch ( $filter ) {
			case 'bbp_has_forums':
				$this->objects[ 'bbpress' ][ 'forum_query' ] = maybe_unserialize( maybe_serialize( bbpress()->forum_query ) );
				break;
			case 'bbp_has_topics':
				$this->objects[ 'bbpress' ][ 'topic_query' ] = maybe_unserialize( maybe_serialize( bbpress()->topic_query ) );
				break;
			case 'bbp_has_replies':
				$this->objects[ 'bbpress' ][ 'reply_query' ] = maybe_unserialize( maybe_serialize( bbpress()->reply_query ) );
				break;
			case 'bbp_has_search_results':
				$this->objects[ 'bbpress' ][ 'search_query' ] = maybe_unserialize( maybe_serialize( bbpress()->topic_query ) );
				break;
		}

		return $out;
	}

	public function snapshot( $name = 'now' ) {
		$snapshot = array(
			'memory'  => $this->memory(),
			'time'    => $this->timer(),
			'hooks'   => $this->hooks(),
			'queries' => $this->query()
		);

		$this->snapshots[ $name ] = $snapshot;
	}

	public function get( $snapshot, $value ) {
		if ( isset( $this->snapshots[ $snapshot ] ) ) {
			$data = $this->snapshots[ $snapshot ][ $value ];

			switch ( $value ) {
				case 'memory':
					return debugpress_format_size( $data, 2, '' );
				case 'time':
					return number_format( $data, 5 );
				default:
					return $data;
			}
		} else {
			return '';
		}
	}

	public function get_counts() : array {
		return $this->counts;
	}

	public function get_stats() : array {
		$key = is_admin() ? 'in_admin_footer' : 'wp_footer';

		$stats = array(
			__( "Used Memory", "debugpress" ) => debugpress_tracker()->get( $key, 'memory' ),
			__( "Total Time", "debugpress" )  => debugpress_tracker()->get( $key, 'time' ) . 's',
			__( "SQL Queries", "debugpress" ) => debugpress_tracker()->get( $key, 'queries' )
		);

		if ( defined( "SAVEQUERIES" ) && SAVEQUERIES ) {
			$stats[ __( "SQL Time", "debugpress" ) ] = debugpress_tracker()->get_total_sql_time() . 's';
		}

		$stats[ __( "Visitor IP", "debugpress" ) ] = IP::get_visitor_ip();

		return $stats;
	}

	public function get_total_sql_time() : string {
		$timer = 0;

		if ( debugpress_db()->wpdb()->queries ) {
			foreach ( debugpress_db()->wpdb()->queries as $q ) {
				$timer += $q[ 1 ];
			}
		}

		return number_format( $timer, 5 );
	}

	public function log_http_apis() {
		if ( ! empty( $this->httpapi ) ) {
			$this->_prepare_http_api_logs();
		}
	}

	private function _hooks_object() : int {
		global $wp_filter;

		$this->count_hooks = 0;

		if ( is_array( $wp_filter ) && ! empty( $wp_filter ) ) {
			foreach ( $wp_filter as $hooks ) {
				if ( isset( $hooks->callbacks ) ) {
					foreach ( $hooks->callbacks as $priorities ) {
						foreach ( $priorities as $items ) {
							$this->count_hooks += count( $items );
						}
					}
				}
			}
		}

		return $this->count_hooks;
	}

	private function _strip_abspath( $path ) : string {
		return ltrim( str_replace( array( untrailingslashit( ABSPATH ), '\\' ), array( '', '/' ), $path ), '/' );
	}

	private function _get_caller( $type = '' ) : array {
		$_abspath = str_replace( '\\', '/', ABSPATH );

		$filters = array( 'do_action', 'apply_filter', 'do_action_ref_array', 'call_user_func_array' );

		$trace  = array_reverse( debug_backtrace() );
		$caller = array();

		foreach ( $trace as $call ) {
			if ( isset( $call[ 'class' ] ) && "Dev4Press\\Plugin\\DebugPress\\Track\\Tracker" == $call[ 'class' ] ) {
				continue;
			}

			if ( $call[ 'function' ] == 'debugpress_tracker' || $call[ 'function' ] == 'debugpress_store_object' ) {
				continue;
			}

			if ( $call[ 'function' ] == 'call_user_func_array' ) {
				if ( isset( $call[ 'args' ][ 0 ][ 0 ] ) && $call[ 'args' ][ 0 ][ 0 ] instanceof Tracker ) {
					continue;
				}
			}

			if ( $type == 'doing_it_wrong_run' ) {
				if ( $call[ 'function' ] == 'do_action' && $call[ 'args' ][ 0 ] == 'doing_it_wrong_run' ) {
					continue;
				}
			}

			$value = isset( $call[ "class" ] ) ? "{$call["class"]}->{$call["function"]}" : $call[ "function" ];

			$file = '';
			if ( isset( $call[ 'file' ] ) && isset( $call[ 'line' ] ) ) {
				$_file = str_replace( '\\', '/', $call[ 'file' ] );

				if ( strpos( $_file, $_abspath ) === 0 ) {
					$_file = substr( $_file, strlen( $_abspath ) );
				}

				$file = ' (' . $_file . ' => ' . $call[ 'line' ] . ')';
			}

			$filter = '';

			try {
				if ( in_array( $call[ 'function' ], $filters ) ) {
					if ( isset( $call[ 'args' ][ 0 ] ) ) {
						if ( is_array( $call[ 'args' ][ 0 ] ) ) {
							$filter = ' (' . maybe_serialize( current( $call[ 'args' ][ 0 ] ) ) . ')';
						} else {
							$filter = ' (' . maybe_serialize( $call[ 'args' ][ 0 ] ) . ')';
						}
					}
				}
			} catch ( Exception $ex ) {

			}

			$caller[] = $value . $file . $filter;
		}

		return $caller;
	}

	private function _log_http_api_call( $response, $args, $url ) {
		$key = $args[ '_debugpress_key_' ];
		$log = array(
			'end'       => microtime( true ),
			'info'      => $this->_http_info,
			'transport' => $this->_http_transport,
			'response'  => $response,
			'args'      => $args
		);

		if ( isset( $args[ '_debugpress_key_original_' ] ) ) {
			$key_original = $args[ '_debugpress_key_original_' ];

			$this->httpapi[ $key_original ][ 'end' ]      = $this->httpapi[ $key_original ][ 'start' ];
			$this->httpapi[ $key_original ][ 'response' ] = new WP_Error( 'http_request_not_executed', sprintf( __( "Request not executed because of the filter on %s.", "debugpress" ), 'pre_http_request' ) );
		}

		$this->_http_info      = null;
		$this->_http_transport = null;

		$this->httpapi[ $key ] = $log;
	}

	private function _prepare_http_api_logs() {
		if ( ! empty( $this->httpapi ) ) {
			foreach ( $this->httpapi as &$raw ) {
				$log = array(
					'transport' => $raw[ 'transport' ],
					'info'      => $raw[ 'info' ],
					'args'      => array()
				);

				foreach ( $raw[ 'args' ] as $key => $val ) {
					if ( substr( $key, 0, 1 ) !== '_' ) {
						$log[ 'args' ][ $key ] = $val;
					}
				}

				if ( is_wp_error( $raw ) ) {
					$response = array( 'errors' => $raw->get_error_message() );
				} else if ( isset( $raw[ 'response' ] ) && is_wp_error( $raw[ 'response' ] ) ) {
					$response = array( 'errors' => $raw[ 'response' ]->get_error_message() );
				} else if ( isset( $raw[ 'response' ] ) && isset( $raw[ 'response' ][ 'http_response' ] ) ) {
					$response              = $raw[ 'response' ][ 'http_response' ]->to_array();
					$response[ 'headers' ] = $response[ 'headers' ]->getAll();
				} else {
					$response = array();
				}

				$log[ 'response' ] = $response;

				$raw = $log;
			}
		}
	}
}
