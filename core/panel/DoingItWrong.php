<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Display\ErrorFormat;
use Dev4Press\Plugin\DebugPress\Main\Panel;

class DoingItWrong extends Panel {
	public function single() {
		foreach ( debugpress_tracker()->doingitwrong as $error ) {
			echo ErrorFormat::doing_it_wrong( $error ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}
}
