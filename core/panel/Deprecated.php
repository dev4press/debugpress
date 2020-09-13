<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

use Dev4Press\Plugin\DebugPress\Display\ErrorFormat;
use Dev4Press\Plugin\DebugPress\Main\Panel;

class Deprecated extends Panel {
	public function single() {
		foreach ( debugpress_tracker()->deprecated as $type => $items ) {
			foreach ( $items as $item ) {
				switch ( $type ) {
					case 'file':
						ErrorFormat::deprecated_file( $item );
						break;
					case 'function':
						ErrorFormat::deprecated_function( $item );
						break;
					case 'constructor':
						ErrorFormat::deprecated_constructor( $item );
						break;
					case 'argument':
						ErrorFormat::deprecated_argument( $item );
						break;
				}
			}
		}
	}
}
