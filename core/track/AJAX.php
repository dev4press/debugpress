<?php

namespace Dev4Press\Plugin\DebugPress\Track;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AJAX {
	private $_session_key = '';
	private $_skip_tracking = false;
	private $_to_debug_log = true;

	public function __construct() {
		add_action( 'debugpress-tracker-getting-ready', array( $this, 'prepare' ), 30 );

		add_action( 'shutdown', array( $this, 'release' ), 0 );
	}

	public static function instance() : AJAX {
		static $instance = null;

		if ( null === $instance ) {
			$instance = new AJAX();
		}

		return $instance;
	}

	public function prepare() {
		$this->_skip_tracking = apply_filters( 'debugpress-ajax-tracker-skip', $this->_skip_tracking );
		$this->_to_debug_log  = apply_filters( 'debugpress-ajax-tracker-to-debug-log', debugpress_plugin()->get( 'ajax_to_debuglog' ) );

		if ( $this->_skip_tracking ) {
			return;
		}

		$this->_session_key = time() . '-' . wp_rand( 100000, 999999 );

		if ( $this->_to_debug_log ) {
			debugpress_error_log( $this->_session_key . ' - ' . __( 'AJAX STARTED', 'debugpress' ) );
		}

		ob_start();

		add_action( 'send_headers', 'nocache_headers' );
	}

	public function release() {
		if ( $this->_skip_tracking ) {
			return;
		}

		debugpress_tracker()->end();

		if ( ! headers_sent() ) {
			$data = $this->data();

			do_action( 'debugpress-tracker-admin-ajax-logged', $data );

			foreach ( $data as $key => $value ) {
				$formatted = is_scalar( $value ) ? $value : wp_json_encode( $value );
				$header    = sprintf( 'X-DEBUGPRESS-%s: %s', $key, $formatted );

				if ( $this->_to_debug_log && $key !== 'ajax-session-key' ) {
					debugpress_error_log( sprintf( '%s: %s', $key, $formatted ) );
				}

				header( $header );
			}
		}

		if ( $this->_to_debug_log ) {
			debugpress_error_log( $this->_session_key . ' - ' . __( 'AJAX ENDED', 'debugpress' ) );
		}

		if ( ob_get_length() !== false ) {
			ob_flush();
		}
	}

	public function data() {
		$data = array(
			'ajax-session-key'       => $this->_session_key,
			'ajax-action-call'       => isset( $_REQUEST['action'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification
			'php-memory-available'   => ini_get( 'memory_limit' ) . "B",
			'php-max-execution-time' => ini_get( 'max_execution_time' ) . " " . __( 'seconds', 'debugpress' ),
			'page-memory-usage'      => debugpress_tracker()->get( '_end', 'memory' ),
			'page-total-time'        => debugpress_tracker()->get( '_end', 'time' ) . " " . __( 'seconds', 'debugpress' ),
			'sql-queries-count'      => debugpress_tracker()->get( '_end', 'queries' ),
		);

		if ( defined( "SAVEQUERIES" ) && SAVEQUERIES ) {
			$data['sql-total-time'] = debugpress_tracker()->get_total_sql_time() . " " . __( 'seconds', 'debugpress' );
		}

		if ( ! empty( debugpress_tracker()->httpapi ) ) {
			$data['http-requests-counts'] = count( debugpress_tracker()->httpapi );
		}

		if ( ! empty( debugpress_tracker()->errors ) ) {
			$data['errors-count'] = debugpress_tracker()->counts['errors'];
		}

		if ( ! empty( debugpress_tracker()->deprecated ) ) {
			$data['deprecated-count'] = debugpress_tracker()->counts['deprecated'];
		}

		if ( ! empty( debugpress_tracker()->doingitwrong ) ) {
			$data['doingitwrong-count'] = debugpress_tracker()->counts['doingitwrong'];
		}

		return apply_filters( 'debugpress-ajax-tracker-data', $data );
	}
}
