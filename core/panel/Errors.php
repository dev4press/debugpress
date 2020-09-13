<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

use Dev4Press\Plugin\DebugPress\Display\ErrorFormat;
use Dev4Press\Plugin\DebugPress\Main\Panel;

class Errors extends Panel {
	public function single() {
		foreach ( debugpress_tracker()->errors as $error ) {
			echo ErrorFormat::php_error( $error );
		}
	}
}
