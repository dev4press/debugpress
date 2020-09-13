<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

use Dev4Press\Plugin\DebugPress\Main\IP;
use Dev4Press\Plugin\DebugPress\Main\Panel;

class Request extends Panel {
	public function left() {
		$this->title( __( "Request", "debugpress" ), true );
		$this->list_array( $this->request_basics() );

		$this->title( __( "URL", "debugpress" ), true );
		$this->block_header( true );
		echo debugpress_current_url();
		$this->block_footer();
		$this->list_array( $this->request_url() );

		if ( ! is_admin() ) {
			global $wp, $template;

			$this->title( __( "Page Request", "debugpress" ), true );
			$this->block_header( true );
			$this->sub_title( __( "Request", "debugpress" ) );
			echo empty( $wp->request ) ? __( "None", "debugpress" ) : esc_html( $wp->request );

			$this->sub_title( __( "Matched Rewrite Rule", "debugpress" ) );
			echo empty( $wp->matched_rule ) ? __( "None", "debugpress" ) : esc_html( $wp->matched_rule );

			$this->sub_title( __( "Matched Rewrite Query", "debugpress" ) );
			echo empty( $wp->matched_query ) ? __( "None", "debugpress" ) : esc_html( $wp->matched_query );

			$this->sub_title( __( "Loaded Template", "debugpress" ) );
			$tpl = basename( $template );
			echo empty( $tpl ) ? __( "None", "debugpress" ) : esc_html( $tpl );
			$this->block_footer();
		}

		$this->title( __( "IP's from &#36;_SERVER", "debugpress" ), true );
		$this->list_array( IP::get_all_ips() );
	}

	public function right() {
		$this->title( __( "Request Headers", "debugpress" ), true );
		$this->list_array( $this->request_headers() );

		$this->title( __( "Response Headers", "debugpress" ), true );
		$this->list_array( $this->response_headers() );
	}

	public function request_url() {
		return array(
			__( "Host", "debugpress" )  => wp_unslash( $_SERVER['HTTP_HOST'] ),
			__( "Path", "debugpress" )  => isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/',
			__( "Query", "debugpress" ) => isset( $_SERVER['QUERY_STRING'] ) ? wp_unslash( $_SERVER['QUERY_STRING'] ) : '',
		);
	}

	public function request_basics() {
		return array(
			__( "Method", "debugpress" ) => strtoupper( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ),
			__( "Scheme", "debugpress" ) => is_ssl() ? 'HTTPS' : 'HTTP'
		);
	}

	public function request_headers() {
		$server = wp_unslash( $_SERVER );

		$headers = array();
		$extra   = array( 'CONTENT_LENGTH', 'CONTENT_MD5', 'CONTENT_TYPE' );

		foreach ( $server as $key => $value ) {
			if ( strpos( $key, 'HTTP_' ) === 0 ) {
				$headers[ substr( $key, 5 ) ] = $value;
			} else if ( in_array( $key, $extra ) ) {
				$headers[ $key ] = $value;
			}
		}

		return $headers;
	}

	public function response_headers() {
		$raw = headers_list();

		$list = array();

		foreach ( $raw as $header ) {
			$parts = explode( ":", $header, 2 );
			$key   = trim( $parts[0] );
			$value = isset( $parts[1] ) ? trim( $parts[1] ) : '/';

			$list[ $key ] = $value;
		}

		return $list;
	}
}
