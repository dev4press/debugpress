<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

use Dev4Press\Plugin\DebugPress\Main\Panel;

class Log extends Panel {
	public function single() {
		foreach ( debugpress_tracker()->logged as $item ) {
			echo '<div class="debugpress-wrapper-warning debugpress-warning-user-log">';
			$this->_render_logged( $item );
			echo '</div>';
		}
	}

	private function _render_logged( $item ) {
		echo '<h4>' . $item['time'] . ': ' . $item['title'] . '</h4>';
		echo '<strong>' . __( "Caller", "debugpress" ) . ":</strong><br/>" . join( '<br/>', $item['caller'] ) . '<br/>';

		gdp_r( $item['print'], false );
	}
}
