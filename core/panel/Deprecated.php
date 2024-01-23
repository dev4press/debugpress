<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Display\ErrorFormat;
use Dev4Press\Plugin\DebugPress\Main\Panel;

class Deprecated extends Panel {
	public function single() {
		foreach ( debugpress_tracker()->deprecated as $type => $items ) {
			foreach ( $items as $item ) {
				switch ( $type ) {
					case 'file':
						echo ErrorFormat::deprecated_file( $item ); // phpcs:ignore WordPress.Security.EscapeOutput
						break;
					case 'function':
						echo ErrorFormat::deprecated_function( $item ); // phpcs:ignore WordPress.Security.EscapeOutput
						break;
					case 'constructor':
						echo ErrorFormat::deprecated_constructor( $item ); // phpcs:ignore WordPress.Security.EscapeOutput
						break;
					case 'argument':
						echo ErrorFormat::deprecated_argument( $item ); // phpcs:ignore WordPress.Security.EscapeOutput
						break;
				}
			}
		}
	}
}
