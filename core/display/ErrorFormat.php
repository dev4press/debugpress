<?php

namespace Dev4Press\Plugin\DebugPress\Display;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ErrorFormat {
	public static function render_caller( $caller, $escape = false ) : string {
		$_print = $escape ? esc_html( $caller ) : $caller;

		$render = ' <a href="#" class="debugpress-events-log-toggle">' . __( 'Show Details', 'debugpress' ) . '</a>';
		$render .= '<div class="debugpress-events-log-toggler"><strong>' . __( 'From', 'debugpress' ) . ':</strong><br/>' . $_print . '</div>';

		return $render;
	}

	public static function php_error( $error ) : string {
		$class = 'debugpress-wrapper-warning debugpress-warning-errors debugpress-error-';

		if ( ! isset( $error['errno'] ) || ! isset( $error['caller'] ) ) {
			return __( 'Error information is missing', 'debugpress' );
		}

		switch ( $error['errno'] ) {
			case E_ERROR:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
				$title = __( 'FATAL ERROR', 'debugpress' );
				$class .= "e_error";
				break;
			case E_USER_ERROR:
			case E_RECOVERABLE_ERROR:
				$title = __( 'ERROR', 'debugpress' );
				$class .= "e_error";
				break;
			case E_WARNING:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
			case E_USER_WARNING:
				$title = __( 'WARNING', 'debugpress' );
				$class .= "e_warning";
				break;
			case E_NOTICE:
			case E_USER_NOTICE:
				$title = __( 'NOTICE', 'debugpress' );
				$class .= "e_notice";
				break;
			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				$title = __( 'DEPRECATED', 'debugpress' );
				$class .= "e_deprecated";
				break;
			case E_STRICT:
				$title = __( 'STRICT', 'debugpress' );
				$class .= "e_strict";
				break;
			default:
				$title = __( 'UNKNOWN ERROR', 'debugpress' );
				$class .= "e_default";
				break;
		}

		$caller = maybe_unserialize( $error['caller'] );
		$caller = is_array( $caller ) ? join( '<br/>', $caller ) : $caller;

		$render = '<div class="' . $class . '">';
		$render .= '<h4>' . $title . ' [' . $error['errno'] . ']</h4>';
		$render .= '<strong>' . __( 'On line', 'debugpress' ) . ":</strong> " . $error['errline'] . '<br/>';
		$render .= '<strong>' . __( 'In file', 'debugpress' ) . ":</strong> " . $error['errfile'] . '<br/>';

		if ( ! empty( $caller ) ) {
			$render .= self::render_caller( $caller );
		}

		$render .= '<div class="debugpress-error-message">' . esc_html( $error['errstr'] ) . '</div>';
		$render .= '</div>';

		return $render;
	}

	public static function doing_it_wrong( $item ) : string {
		$render = '<div class="debugpress-wrapper-warning debugpress-warning-doingitwrong">';
		$render .= '<h4>' . sprintf( __( 'For <strong>%s</strong>', 'debugpress' ), $item['wrong'] ) . '</h4>';
		$render .= '<strong>' . __( 'Since version', 'debugpress' ) . ":</strong> " . $item['version'] . ', ';
		$render .= '<strong>' . __( 'On line', 'debugpress' ) . ":</strong> " . $item['on_line'] . '<br/>';
		$render .= '<strong>' . __( 'In file', 'debugpress' ) . ":</strong> " . $item['in_file'] . '<br/>';

		$render .= self::process_caller( $item );

		if ( $item['message'] ) {
			$render .= '<div class="debugpress-error-message">' . esc_html( $item['message'] ) . '</div>';
		}

		$render .= '</div>';

		return $render;
	}

	public static function deprecated_file( $item ) : string {
		$render = '<div class="debugpress-wrapper-warning debugpress-warning-deprecated debugpress-deprecated-file">';
		$render .= '<h4>' . __( 'Deprecated File', 'debugpress' ) . ':</h4>';
		$render .= '<strong>' . __( 'On line', 'debugpress' ) . ":</strong> " . $item['on_line'] . '<br/>';
		$render .= '<strong>' . __( 'In file', 'debugpress' ) . ":</strong> " . $item['in_file'] . '<br/>';

		if ( $item['replacement'] ) {
			$render .= sprintf( __( '<strong>%1$s</strong> is deprecated since version %2$s. Use <strong>%3$s</strong> instead.', 'debugpress' ), $item['deprecated'], $item['version'], $item['replacement'] );
		} else {
			$render .= sprintf( __( '<strong>%1$s</strong> is deprecated since version %2$s.', 'debugpress' ), $item['deprecated'], $item['version'] );
		}

		$render .= self::process_caller( $item );

		$render .= '</div>';

		return $render;
	}

	public static function deprecated_function( $item ) : string {
		$render = '<div class="debugpress-wrapper-warning debugpress-warning-deprecated debugpress-deprecated-function">';
		$render .= '<h4>' . __( 'Deprecated Function', 'debugpress' ) . ':</h4>';
		$render .= '<strong>' . __( 'On line', 'debugpress' ) . ":</strong> " . $item['on_line'] . '<br/>';
		$render .= '<strong>' . __( 'In file', 'debugpress' ) . ":</strong> " . $item['in_file'] . '<br/>';

		if ( $item['hook'] ) {
			$render .= "<strong>" . __( 'Hook', 'debugpress' ) . ":</strong> " . $item['hook'] . "<br/>";
		}

		if ( $item['replacement'] ) {
			$render .= sprintf( __( '<strong>%1$s</strong> is deprecated since version %2$s. Use <strong>%3$s</strong> instead.', 'debugpress' ), $item['deprecated'], $item['version'], $item['replacement'] );
		} else {
			$render .= sprintf( __( '<strong>%1$s</strong> is deprecated since version %2$s.', 'debugpress' ), $item['deprecated'], $item['version'] );
		}

		if ( ! empty( $item['message'] ) ) {
			$render .= '<em>' . $item['message'] . '</em>';
		}

		$render .= self::process_caller( $item );

		$render .= '</div>';

		return $render;
	}

	public static function deprecated_constructor( $item ) : string {
		$render = '<div class="debugpress-wrapper-warning debugpress-warning-deprecated debugpress-deprecated-constructor">';
		$render .= '<h4>' . __( 'Deprecated Constructor', 'debugpress' ) . ':</h4>';
		$render .= '<strong>' . __( 'On line', 'debugpress' ) . ":</strong> " . $item['on_line'] . '<br/>';
		$render .= '<strong>' . __( 'In file', 'debugpress' ) . ":</strong> " . $item['in_file'] . '<br/>';

		$render .= sprintf( __( 'For <strong>%1$s</strong> since version %2$s.', 'debugpress' ), $item['deprecated'], $item['version'] );

		if ( ! empty( $item['message'] ) ) {
			$render .= '<em>' . $item['message'] . '</em>';
		}

		$render .= self::process_caller( $item );

		$render .= '</div>';

		return $render;
	}

	public static function deprecated_argument( $item ) : string {
		$render = '<div class="debugpress-wrapper-warning debugpress-warning-deprecated debugpress-deprecated-argument">';
		$render .= '<h4>' . __( 'Deprecated Argument', 'debugpress' ) . ':</h4>';

		if ( $item['in_file'] ) {
			if ( $item['on_line'] ) {
				$render .= "<strong>" . __( 'On line', 'debugpress' ) . ":</strong> " . $item['on_line'] . ", ";
			}

			$render .= "<strong>" . __( 'In file', 'debugpress' ) . ":</strong> " . $item['in_file'] . "<br/>";
		}

		$render .= sprintf( __( 'Argument in <strong>%1$s</strong> is deprecated since version %2$s.', 'debugpress' ), $item['deprecated'], $item['version'] );

		if ( ! empty( $item['message'] ) ) {
			$render .= '<em>' . $item['message'] . '</em>';
		}

		$render .= self::process_caller( $item );

		$render .= '</div>';

		return $render;
	}

	public static function process_caller( $item ) : string {
		$render = '';

		$caller = isset( $item['caller'] ) ? maybe_unserialize( $item['caller'] ) : '';
		$caller = is_array( $caller ) ? join( '<br/>', $caller ) : $caller;

		if ( ! empty( $caller ) ) {
			$render .= self::render_caller( $caller );
		}

		return $render;
	}
}
