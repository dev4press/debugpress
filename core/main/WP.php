<?php

namespace Dev4Press\Plugin\DebugPress\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WP {
	public function __construct() {
	}

	public static function instance() : WP {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new WP();
		}

		return $instance;
	}

	public function has_permalinks() {
		return get_option( 'permalink_structure' );
	}

	public function current_url( bool $use_wp = true ) : string {
		if ( $use_wp ) {
			return home_url( $this->current_url_request() );
		} else {
			$s        = is_ssl() ? 's' : '';
			$protocol = Str::left( strtolower( $_SERVER['SERVER_PROTOCOL'] ), '/' ) . $s; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput,WordPress.Security.NonceVerification
			$port     = isset( $_SERVER['SERVER_PORT'] ) ? absint( $_SERVER['SERVER_PORT'] ) : 80;
			$port     = $port === 80 || $port === 443 ? '' : ':' . $port;

			return $protocol . '://' . sanitize_url( $_SERVER['SERVER_NAME'] ) . $port . sanitize_url( $_SERVER['REQUEST_URI'] ); // phpcs:ignore WordPress.Security.EscapeOutput,WordPress.Security.ValidatedSanitizedInput,WordPress.Security.NonceVerification,WordPress.WP.DeprecatedFunctions
		}
	}

	public function current_url_request() : string {
		$path_info = $_SERVER['PATH_INFO'] ?? ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput,WordPress.Security.NonceVerification
		list( $path_info ) = explode( '?', $path_info );
		$path_info = str_replace( '%', '%25', $path_info );

		$request         = explode( '?', $_SERVER['REQUEST_URI'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput,WordPress.Security.NonceVerification
		$req_uri         = $request[0];
		$req_query       = $request[1] ?? false;
		$home_path       = wp_parse_url( home_url(), PHP_URL_PATH );
		$home_path       = $home_path ? trim( $home_path, '/' ) : '';
		$home_path_regex = sprintf( '|^%s|i', preg_quote( $home_path, '|' ) );

		$req_uri = str_replace( $path_info, '', $req_uri );
		$req_uri = ltrim( $req_uri, '/' );
		$req_uri = preg_replace( $home_path_regex, '', $req_uri );
		$req_uri = ltrim( $req_uri, '/' );

		$url_request = $req_uri;

		if ( $req_query !== false ) {
			$url_request .= '?' . $req_query;
		}

		return $url_request;
	}
}
