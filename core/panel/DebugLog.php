<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\Info;
use Dev4Press\Plugin\DebugPress\Main\Panel;

class DebugLog extends Panel {
	private $_limit;
	private $_available = true;

	public function __construct() {
		$this->_limit = apply_filters( 'debugpress-debuglog-lines-to-load', 5000 );
	}

	public function left() {
		$this->title( __( "Information", "debugpress" ), true, true );
		$path = Info::debug_log_path();

		if ( empty( $path ) ) {
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
			echo file_exists( $path ) ? debugpress_format_size( filesize( $path ) ) : __( "File is not yet created.", "debugpress" );

			$this->sub_title( __( "Current Log Number of Lines", "debugpress" ) );
			echo debugpress_count_lines_in_files( $path );
			$this->block_footer();

			$this->title( __( "Settings", "debugpress" ), true, true );
			$this->block_header( true );
			$this->sub_title( __( "Log lines to load", "debugpress" ) );
			echo $this->_limit === 0 ? __( "Complete debug log", "debugpress" ) : $this->_limit;
			$this->block_footer();

			$this->title( __( "Actions", "debugpress" ), true, true );
			$this->block_header( true );
			echo '<button class="debugpress-button-action debugpress-action-debuglog-load">' . __( "Load the debug log", "debugpress" ) . '</button>';
			$this->block_footer();
		}
	}

	public function right() {
		$this->title( __( "Debug Log Content", "debugpress" ), true, true );

		if ( $this->_available ) {
			echo '<div id="debugpress-debuglog-content"><div>' . __( "Use the controls on the left to load the content of the debug log.", "debugpress" ) . '</div></div>';
		} else {
			$this->block_header( true );
			_e( "Debug log is not available.", "debugpress" );
			$this->block_footer();
		}
	}

	public function load_from_debug_log() {
		$path = Info::debug_log_path();

		if ( file_exists( $path ) && filesize( $path ) > 0 ) {
			return debugpress_read_lines_from_file( $path );
		}

		return array();
	}
}