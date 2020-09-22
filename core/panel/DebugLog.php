<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\Info;
use Dev4Press\Plugin\DebugPress\Main\Panel;

class DebugLog extends Panel {
	private $_available = true;

	public function left() {
		$this->title( __( "Information", "debugpress" ), true, true);
		$path = Info::debug_log_path();

		if (empty($path)) {
			echo '<div class="debugpress-debug-notice-block">';
			$this->title( '<i class="debugpress-icon debugpress-icon-exclamation"></i> ' . __( "Debug Log not available", "debugpress" ), true, true );
			$this->block_header( true );
			_e( "WordPress Debug Log is not currently enabled.", "debugpress" );
			echo ' <a href="https://debug.press/documentation/wordpress-setup/" target="_blank">' . __( "More Information", "debugpress" ) . '</a>';
			$this->block_footer();
			echo '</div>';

			$this->_available = false;
		} else {
			$this->block_header( true );
			$this->sub_title( __( "Debug Log Path", "debugpress" ) );
			echo $path;

			$this->sub_title( __( "Current Log Size", "debugpress" ) );
			echo file_exists($path) ? debugpress_format_size(filesize($path)) : __("File is not yet created.");

			$this->sub_title( __( "Current Log Number of Lines", "debugpress" ) );
			echo debugpress_count_lines_in_files($path);

			$this->block_footer();

			$this->title( __( "Actions", "debugpress" ), true, true);
		}
	}

	public function right() {
		$this->title( __( "Debug Log Content", "debugpress" ), true, true);

		if ($this->_available) {

		} else {
			$this->block_header( true );
			_e("Debug log is not available.");
			$this->block_footer();
		}
	}
}
