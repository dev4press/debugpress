<?php

namespace Dev4Press\Plugin\DebugPress\Display;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ErrorFormat {
	public static function render_caller( $caller, $escape = false ) {
		$_print = $escape ? esc_html($caller) : $caller;

		$render = '<a href="#" class="debugpress-events-log-toggle">' . __( "Show Details", "debugpress" ) . '</a>';
		$render .= '<div class="debugpress-events-log-toggler"><strong>' . __( "From", "debugpress" ) . ':</strong><br/>' . $_print . '</div>';

		return $render;
	}

	public static function http_api_request( $item ) {
		$render = '<div class="debugpress-wrapper-warning debugpress-warning-http-api-request">';
		$render .= gdp_rx( maybe_unserialize( $item ), false );
		$render .= '</div>';

		return $render;
	}

	public static function php_error( $error ) {
		$class = 'debugpress-wrapper-warning debugpress-warning-errors debugpress-error-';

		if ( ! isset( $error['errno'] ) || ! isset( $error['caller'] ) ) {
			return __( "Error information is missing", "debugpress" );
		}

		switch ( $error['errno'] ) {
			case E_ERROR:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
				$title = __( "FATAL ERROR", "debugpress" );
				$class .= "e_error";
				break;
			case E_USER_ERROR:
			case E_RECOVERABLE_ERROR:
				$title = __( "ERROR", "debugpress" );
				$class .= "e_error";
				break;
			case E_WARNING:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
			case E_USER_WARNING:
				$title = __( "WARNING", "debugpress" );
				$class .= "e_warning";
				break;
			case E_NOTICE:
			case E_USER_NOTICE:
				$title = __( "NOTICE", "debugpress" );
				$class .= "e_notice";
				break;
			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				$title = __( "DEPRECATED", "debugpress" );
				$class .= "e_deprecated";
				break;
			case E_STRICT:
				$title = __( "STRICT", "debugpress" );
				$class .= "e_strict";
				break;
			default:
				$title = __( "UNKNOWN ERROR", "debugpress" );
				$class .= "e_default";
				break;
		}

		$caller = maybe_unserialize( $error['caller'] );
		$caller = is_array( $caller ) ? join( '<br/>', $caller ) : $caller;

		$render = '<div class="' . $class . '">';
		$render .= '<h4>' . $title . ' [' . $error['errno'] . ']</h4>';
		$render .= '<strong>' . __( "On line", "debugpress" ) . ":</strong> " . $error['errline'] . '<br/>';
		$render .= '<strong>' . __( "In file", "debugpress" ) . ":</strong> " . $error['errfile'] . '<br/>';

		if ( ! empty( $caller ) ) {
			$render .= ErrorFormat::render_caller( $caller );
		}

		$render .= '<div class="debugpress-error-message">' . esc_html( $error['errstr'] ) . '</div>';
		$render .= '</div>';

		return $render;
	}

	public static function user_object( $item ) {
		$render = '<div class="debugpress-wrapper-warning debugpress-warning-user-log">';
		$render .= '<h4>' . $item['title'] . '</h4>';
		$render .= '<strong>' . __( "Caller", "debugpress" ) . ':</strong><br/>' . join( '<br/>', maybe_unserialize( $item['caller'] ) ) . '<br/>';
		$render .= '<br/><strong>' . __( "Object", "debugpress" ) . ':</strong><br/>';
		$render .= gdp_rx( maybe_unserialize( $item['print'] ), false );
		$render .= '</div>';

		return $render;
	}

	public static function doing_it_wrong( $item ) {
		$render = '<div class="debugpress-wrapper-warning debugpress-warning-doingitwrong">';
		$render .= '<h4>' . sprintf( __( "For <strong>%s</strong>", "debugpress" ), $item['deprecated'] ) . '</h4>';
		$render .= '<strong>' . __( "Since version", "debugpress" ) . ":</strong> " . $item['version'] . ', ';
		$render .= '<strong>' . __( "On line", "debugpress" ) . ":</strong> " . $item['on_line'] . '<br/>';
		$render .= '<strong>' . __( "In file", "debugpress" ) . ":</strong> " . $item['in_file'] . '<br/>';

		$caller = isset( $item['caller'] ) ? maybe_unserialize( $item['caller'] ) : '';
		$caller = is_array( $caller ) ? join( '<br/>', $caller ) : $caller;

		if ( ! empty( $caller ) ) {
			$render .= ErrorFormat::render_caller( $caller );
		}

		if ( $item['message'] ) {
			$render .= '<div class="debugpress-error-message">' . esc_html( $item['message'] ) . '</div>';
		}
		$render .= '</div>';

		return $render;
	}

	public static function deprecated_file( $item ) {
		echo '<div class="debugpress-wrapper-warning debugpress-warning-deprecated debugpress-deprecated-file">';
		echo '<h4>' . __( "Deprecated File", "debugpress" ) . ':</h4>';
		echo '<strong>' . __( "On line", "debugpress" ) . ":</strong> " . $item["on_line"] . '<br/>';
		echo '<strong>' . __( "In file", "debugpress" ) . ":</strong> " . $item["in_file"] . '<br/>';

		if ( $item["replacement"] ) {
			echo sprintf( __( "<strong>%s</strong> is deprecated since version %s. Use <strong>%s</strong> instead.", "debugpress" ), $item["deprecated"], $item["version"], $item["replacement"] );
		} else {
			echo sprintf( __( "<strong>%s</strong> is deprecated since version %s.", "debugpress" ), $item["deprecated"], $item["version"] );
		}
		echo '</div>';
	}

	public static function deprecated_function( $item ) {
		echo '<div class="debugpress-wrapper-warning debugpress-warning-deprecated debugpress-deprecated-function">';
		echo '<h4>' . __( "Deprecated Function", "debugpress" ) . ':</h4>';
		echo '<strong>' . __( "On line", "debugpress" ) . ":</strong> " . $item["on_line"] . '<br/>';
		echo '<strong>' . __( "In file", "debugpress" ) . ":</strong> " . $item["in_file"] . '<br/>';

		if ( $item["hook"] ) {
			echo "<strong>" . __( "Hook", "debugpress" ) . ":</strong> " . $item["hook"] . "<br/>";
		}

		if ( $item["replacement"] ) {
			echo sprintf( __( "<strong>%s</strong> is deprecated since version %s. Use <strong>%s</strong> instead.", "debugpress" ), $item["deprecated"], $item["version"], $item["replacement"] );
		} else {
			echo sprintf( __( "<strong>%s</strong> is deprecated since version %s.", "debugpress" ), $item["deprecated"], $item["version"] );
		}

		if ( isset( $item['message'] ) && ! empty( $item['message'] ) ) {
			echo '<em>' . $item['message'] . '</em>';
		}
		echo '</div>';
	}

	public static function deprecated_constructor( $item ) {
		echo '<div class="debugpress-wrapper-warning debugpress-warning-deprecated debugpress-deprecated-constructor">';
		echo '<h4>' . __( "Deprecated Constructor", "debugpress" ) . ':</h4>';
		echo '<strong>' . __( "On line", "debugpress" ) . ":</strong> " . $item["on_line"] . '<br/>';
		echo '<strong>' . __( "In file", "debugpress" ) . ":</strong> " . $item["in_file"] . '<br/>';

		echo sprintf( __( "For <strong>%s</strong> since version %s.", "debugpress" ), $item["deprecated"], $item["version"] );

		if ( isset( $item['message'] ) && ! empty( $item['message'] ) ) {
			echo '<em>' . $item['message'] . '</em>';
		}
		echo '</div>';
	}

	public static function deprecated_argument( $item ) {
		echo '<div class="debugpress-wrapper-warning debugpress-warning-deprecated debugpress-deprecated-argument">';
		echo '<h4>' . __( "Deprecated Argument", "debugpress" ) . ':</h4>';
		if ( $item['in_file'] ) {
			if ( $item['on_line'] ) {
				echo "<strong>" . __( "On line", "debugpress" ) . ":</strong> " . $item["on_line"] . ", ";
			}
			echo "<strong>" . __( "In file", "debugpress" ) . ":</strong> " . $item["in_file"] . "<br/>";
		}
		echo sprintf( __( "Argument in <strong>%s</strong> is deprecated since version %s.", "debugpress" ), $item["deprecated"], $item["version"] );

		if ( isset( $item['message'] ) && ! empty( $item['message'] ) ) {
			echo '<em>' . $item['message'] . '</em>';
		}
		echo '</div>';
	}
}