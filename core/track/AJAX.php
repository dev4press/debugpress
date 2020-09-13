<?php

namespace Dev4Press\Plugin\DebugPress\Track;

class AJAX {
	public function __construct() {
		add_action( 'init', array( $this, 'prepare' ), 30 );
		add_action( 'shutdown', array( $this, 'release' ), 0 );
	}

	public static function instance() {
		static $instance = null;

		if ( null === $instance ) {
			$instance = new AJAX();
		}

		return $instance;
	}

	public function prepare() {
		ob_start();

		add_action( 'send_headers', 'nocache_headers' );
	}

	public function release() {
		debugpress_tracker()->end();

		if ( ! headers_sent() ) {
			foreach ( $this->data() as $key => $value ) {
				if ( is_scalar( $value ) ) {
					$header = sprintf( 'X-DEBUGPRESS-%s: %s', $key, $value );
				} else {
					$header = sprintf( 'X-DEBUGPRESS-%s: %s', $key, json_encode( $value ) );
				}

				header( $header );
			}
		}

		if ( ob_get_length() !== false ) {
			ob_flush();
		}
	}

	public function data() {
		$data = array(
			'ajax-action-call'       => isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : '',
			'php-memory-available'   => ini_get( 'memory_limit' ) . "B",
			'php-max-execution-time' => ini_get( 'max_execution_time' ) . " " . __( "seconds", "debugpress" ),
			'page-memory-usage'      => debugpress_tracker()->get( '_end', 'memory' ),
			'page-total-time'        => debugpress_tracker()->get( '_end', 'time' ) . " " . __( "seconds", "debugpress" ),
			'sql-queries-count'      => debugpress_tracker()->get( '_end', 'queries' )
		);

		if ( defined( "SAVEQUERIES" ) && SAVEQUERIES ) {
			$data['sql-total-time'] = debugpress_tracker()->get_total_sql_time() . " " . __( "seconds", "debugpress" );
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

		return $data;
	}
}