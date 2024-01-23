<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\Panel;

class HTTP extends Panel {
	public function single() {
		$this->title( esc_html__( 'Logged HTTP API Requests', 'debugpress' ) );
		$this->block_header();
		$this->add_column( __( 'URL', 'debugpress' ), "", "", true );
		$this->add_column( __( 'Request', 'debugpress' ) );
		$this->add_column( __( 'Response', 'debugpress' ) );
		$this->add_column( __( 'Time', 'debugpress' ) );
		$this->add_column( __( 'Caller', 'debugpress' ) );
		$this->table_head();
		foreach ( debugpress_tracker()->httpapi as $request ) {
			$this->render_request( $request );
		}
		$this->table_foot();
		$this->block_footer();
	}

	public function render_request( $request ) {
		$url = 'N/A';
		$arg = array();

		if ( isset( $request['info']['url'] ) ) {
			$raw_url = explode( '?', $request['info']['url'], 2 );

			$url = $raw_url[0];
			$arg = isset( $raw_url[1] ) ? explode( '&', $raw_url[1] ) : array();
		}

		if ( ! empty( $arg ) ) {
			$url .= '<br/>&nbsp;&nbsp;&nbsp;&nbsp;?' . join( '<br/>&nbsp;&nbsp;&nbsp;&nbsp;&', $arg );
		}

		$_req = array(
			__( 'Timestamp', 'debugpress' )    => $request['timers']['start'] ?? '',
			__( 'Transport', 'debugpress' )    => $request['transport'],
			__( 'Method', 'debugpress' )       => $request['args']['method'] ?? 'N/A',
			__( 'User Agent', 'debugpress' )   => $request['args']['user-agent'] ?? 'N/A',
			__( 'Timeout', 'debugpress' )      => $request['args']['timeout'] ?? 'N/A',
			__( 'Redirection', 'debugpress' )  => $request['args']['redirection'] ?? 'N/A',
			__( 'HTTP Version', 'debugpress' ) => $request['args']['httpversion'] ?? 'N/A',
		);

		$_ip = 'N/A';

		if ( isset( $request['info']['primary_ip'] ) ) {
			$_ip = $request['info']['primary_ip'];

			if ( isset( $request['info']['primary_port'] ) ) {
				$_ip .= ':' . $request['info']['primary_port'];
			}
		}

		$_res = array(
			__( 'Timestamp', 'debugpress' )    => $request['timers']['end'] ?? '',
			__( 'Code', 'debugpress' )         => $request['info']['http_code'] ?? 'N/A',
			__( 'Content Type', 'debugpress' ) => $request['info']['content_type'] ?? 'N/A',
			__( 'IP and Port', 'debugpress' )  => $_ip,
		);

		$_tme = array(
			__( 'Total', 'debugpress' )          => '<strong>' . ( $request['info']['total_time'] ?? 'N/A' ) . '</strong>',
			__( 'DNS Resolution', 'debugpress' ) => $request['info']['namelookup_time'] ?? 'N/A',
			__( 'Connect', 'debugpress' )        => $request['info']['connect_time'] ?? 'N/A',
			__( 'Pre-transfer', 'debugpress' )   => $request['info']['pretransfer_time'] ?? 'N/A',
			__( 'Redirect', 'debugpress' )       => $request['info']['redirect_time'] ?? 'N/A',
		);

		$this->table_row( array(
				$url,
				$this->list_plain_pairs( $_req ),
				$this->list_plain_pairs( $_res ),
				$this->list_plain_pairs( $_tme ),
				$this->list_plain_pairs( $request['trace'] ),
			)
		);
	}
}
