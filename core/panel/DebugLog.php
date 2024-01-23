<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\Files;
use Dev4Press\Plugin\DebugPress\Main\Info;
use Dev4Press\Plugin\DebugPress\Main\Panel;

class DebugLog extends Panel {
	private $_limit;
	private $_available = true;

	public function __construct() {
		$this->_limit = apply_filters( 'debugpress-debuglog-lines-to-load', 5000 );
	}

	public function left() {
		$this->title( esc_html__( 'Information', 'debugpress' ), true, true );
		$path = Info::debug_log_path();

		if ( empty( $path ) ) {
			echo '<div class="debugpress-debug-notice-block">';
			$this->title( '<i class="debugpress-icon debugpress-icon-triangle-exclamation"></i> ' . __( 'Debug Log not available', 'debugpress' ), true, true );
			$this->block_header();
			esc_html_e( 'WordPress Debug Log is not currently enabled.', 'debugpress' );
			echo ' <a rel="noopener" href="https://debug.press/documentation/wordpress-setup/" target="_blank">' . esc_html__( 'More Information', 'debugpress' ) . '</a>';
			$this->block_footer();
			echo '</div>';

			$this->_available = false;
		} else {
			$this->block_header();
			$this->sub_title( esc_html__( 'Debug Log Path', 'debugpress' ) );
			echo esc_html( $path );

			$this->sub_title( esc_html__( 'Current Log Size', 'debugpress' ) );
			echo file_exists( $path ) ? debugpress_format_size( filesize( $path ) ) : esc_html__( 'File is not yet created.', 'debugpress' ); // phpcs:ignore WordPress.Security.EscapeOutput

			$this->sub_title( esc_html__( 'Current Log Number of Lines', 'debugpress' ) );
			echo esc_html( Files::instance()->count_lines_in_files( $path ) );
			$this->block_footer();

			$this->title( esc_html__( 'Settings', 'debugpress' ), true, true );
			$this->block_header();
			$this->sub_title( __( 'Log lines to load', 'debugpress' ) );
			echo $this->_limit === 0 ? esc_html__( 'Complete debug log', 'debugpress' ) : esc_html( $this->_limit );
			$this->block_footer();

			$this->title( esc_html__( 'Actions', 'debugpress' ), true, true );
			$this->block_header();
			echo '<button class="debugpress-button-action debugpress-action-debuglog-load">' . esc_html__( 'Load the debug log', 'debugpress' ) . '</button>';
			$this->block_footer();
		}
	}

	public function right() {
		$this->title( esc_html__( 'Debug Log Content', 'debugpress' ), true, true );

		if ( $this->_available ) {
			echo '<div id="debugpress-debuglog-content"><div>' . esc_html__( 'Use the controls on the left to load the content of the debug log.', 'debugpress' ) . '</div></div>';
		} else {
			$this->block_header();
			esc_html_e( 'Debug log is not available.', 'debugpress' );
			$this->block_footer();
		}
	}

	public function load_from_debug_log() : array {
		$path = Info::debug_log_path();

		if ( file_exists( $path ) && filesize( $path ) > 0 ) {
			return Files::instance()->read_lines_from_file( $path );
		}

		return array();
	}
}
