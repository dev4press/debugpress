<?php

namespace Dev4Press\Plugin\DebugPress\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Help {
	public static function process_hook( $name, bool $hide_core = false ) : array {
		global $wp_filter;

		$actions = array();
		$origins = array();

		if ( isset( $wp_filter[ $name ] ) ) {
			$action = $wp_filter[ $name ]->callbacks;

			foreach ( $action as $priority => $callbacks ) {
				foreach ( $callbacks as $code => $cb ) {
					$callback             = Callback::instance()->process( $code, $cb );
					$callback['priority'] = $priority;

					unset( $callback['function'] );

					if ( ! isset( $callback['error'] ) ) {
						$actions[] = $callback;

						if ( ! in_array( $callback['origin'], $origins ) ) {
							$origins[] = $callback['origin'];
						}
					}
				}
			}
		}

		return array(
			'name'    => $name,
			'actions' => $actions,
			'origins' => $origins,
		);
	}
}
