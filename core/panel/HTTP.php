<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\Panel;

class HTTP extends Panel {
	public function single() {
		$this->title( __( "Logged HTTP API Requests", "debugpress" ), true );
		$this->block_header( true );
		$this->add_column( __( "URL", "debugpress" ), "", "", true );
		$this->add_column( __( "Request", "debugpress" ), "", "" );
		$this->add_column( __( "Response", "debugpress" ), "", "" );
		$this->add_column( __( "Time", "debugpress" ), "", "" );
		$this->table_head();
		foreach ( debugpress_tracker()->httpapi as $request ) {
			$this->render_request( $request );
		}
		$this->table_foot();
		$this->block_footer();
	}

	public function render_request( $request ) {
		$raw_url = explode( '?', $request['info']['url'], 2 );
		$url     = $raw_url[0];

		if ( isset( $raw_url[1] ) ) {
			$url .= '<br/>&nbsp;&nbsp;&nbsp;&nbsp;?' . join( '<br/>&nbsp;&nbsp;&nbsp;&nbsp;&', explode( '&', $raw_url[1] ) );
		}

		$_req = array(
			__( "Transport", "debugpress" )    => $request['transport'],
			__( "Method", "debugpress" )       => $request['args']['method'],
			__( "User Agent", "debugpress" )   => $request['args']['user-agent'],
			__( "Timeout", "debugpress" )      => $request['args']['timeout'],
			__( "Redirection", "debugpress" )  => $request['args']['redirection'],
			__( "HTTP Version", "debugpress" ) => $request['args']['httpversion']
		);

		$_res = array(
			__( "Code", "debugpress" )         => $request['info']['http_code'],
			__( "Content Type", "debugpress" ) => $request['info']['content_type'],
			__( "IP and Port", "debugpress" )  => $request['info']['primary_ip'] . ':' . $request['info']['primary_port'],
			__( "Content Type", "debugpress" ) => $request['info']['content_type']
		);

		$_tme = array(
			__( "Total", "debugpress" )          => '<strong>' . $request['info']['total_time'] . '</strong>',
			__( "DNS Resolution", "debugpress" ) => $request['info']['namelookup_time'],
			__( "Connect", "debugpress" )        => $request['info']['connect_time'],
			__( "Pretransfer", "debugpress" )    => $request['info']['pretransfer_time'],
			__( "Redirect", "debugpress" )       => $request['info']['redirect_time']
		);

		$this->table_row( array(
				$url,
				$this->list_plain_pairs( $_req ),
				$this->list_plain_pairs( $_res ),
				$this->list_plain_pairs( $_tme )
			)
		);
	}
}