<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\IP;
use Dev4Press\Plugin\DebugPress\Main\Panel;
use Dev4Press\Plugin\DebugPress\Main\WP;

class Request extends Panel {
	public function left() {
		$this->title( esc_html__( 'Request', 'debugpress' ) );
		$this->list_array( $this->request_basics() );

		$this->title( esc_html__( 'URL', 'debugpress' ) );
		$this->block_header();
		$this->pre( WP::instance()->current_url() );
		$this->list_array( $this->request_url(), '', false );
		$this->block_footer();

		if ( ! is_admin() ) {
			global $wp, $template;

			$this->title( esc_html__( 'Page Request', 'debugpress' ) );
			$this->block_header();
			$this->sub_title( __( 'Request', 'debugpress' ) );
			echo empty( $wp->request ) ? esc_html__( 'None', 'debugpress' ) : esc_html( $wp->request );

			$this->sub_title( __( 'Matched Rewrite Rule', 'debugpress' ) );
			echo empty( $wp->matched_rule ) ? esc_html__( 'None', 'debugpress' ) : esc_html( $wp->matched_rule );

			$this->sub_title( __( 'Matched Rewrite Query', 'debugpress' ) );
			echo empty( $wp->matched_query ) ? esc_html__( 'None', 'debugpress' ) : esc_html( $wp->matched_query );

			$this->sub_title( __( 'Loaded Template', 'debugpress' ) );
			$tpl = $template ? basename( $template ) : '';
			echo empty( $tpl ) ? esc_html__( 'None', 'debugpress' ) : esc_html( $tpl );
			$this->block_footer();
		}

		$this->title( esc_html__( 'IPs from &#36;_SERVER', 'debugpress' ) );
		$this->list_array( IP::all() );

		if ( is_admin() ) {
			$this->title( esc_html__( 'Page Information', 'debugpress' ) );
			$this->block_header();
			$this->table_init_standard();
			$this->table_head();
			$this->table_row( array( "&#36;pagenow", $GLOBALS['pagenow'] ?? '' ) );
			$this->table_row( array( "&#36;typenow", $GLOBALS['typenow'] ?? '' ) );
			$this->table_row( array( "&#36;taxnow", $GLOBALS['taxnow'] ?? '' ) );
			$this->table_row( array(
				"&#36;hook_suffix",
				$GLOBALS['hook_suffix'] ?? '',
			) );
			$this->table_foot();
			$this->block_footer();

			$screen = get_current_screen();

			if ( ! is_null( $screen ) ) {
				$this->title( esc_html__( 'Current Screen', 'debugpress' ) );
				$this->block_header();
				$this->table_init_standard();
				$this->table_head();
				$this->table_row( array( __( 'Base', 'debugpress' ), $screen->base ) );
				$this->table_row( array( __( 'ID', 'debugpress' ), $screen->id ) );
				$this->table_row( array( __( 'Parent Base', 'debugpress' ), $screen->parent_base ) );
				$this->table_row( array( __( 'Parent File', 'debugpress' ), $screen->parent_file ) );
				$this->table_row( array( __( 'Post Type', 'debugpress' ), $screen->post_type ) );
				$this->table_row( array( __( 'Taxonomy', 'debugpress' ), $screen->taxonomy ) );
				$this->table_foot();
				$this->block_footer();
			}
		}
	}

	public function right() {
		$this->title( esc_html__( 'Request Headers', 'debugpress' ) );
		$this->list_array( $this->request_headers() );

		$this->title( esc_html__( 'Response Headers', 'debugpress' ) );
		$this->list_array( $this->response_headers() );
	}

	public function request_url() : array {
		return array(
			__( 'Host', 'debugpress' )  => isset( $_SERVER['HTTP_HOST'] ) ? wp_unslash( $_SERVER['HTTP_HOST'] ) : '/', // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			__( 'Path', 'debugpress' )  => isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/', // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			__( 'Query', 'debugpress' ) => isset( $_SERVER['QUERY_STRING'] ) ? wp_unslash( $_SERVER['QUERY_STRING'] ) : '', // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		);
	}

	public function request_basics() : array {
		return array(
			__( 'Method', 'debugpress' ) => isset( $_SERVER['HTTP_HOST'] ) ? strtoupper( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) : '/', // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			__( 'Scheme', 'debugpress' ) => is_ssl() ? 'HTTPS' : 'HTTP',
		);
	}

	public function request_headers() : array {
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

	public function response_headers() : array {
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
