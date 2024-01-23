<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\Info;
use Dev4Press\Plugin\DebugPress\Main\Panel;
use Dev4Press\Plugin\DebugPress\Main\WP;

class Basics extends Panel {
	public function left() {
		$env  = $this->_env();
		$test = $this->_test();

		if ( ! empty( $env ) ) {
			echo '<div class="debugpress-debug-environment debugpress-debug-env-' . esc_attr( $env['type'] ) . '">';

			$this->title( $env['label'], true, true );

			echo '</div>';
		}

		if ( ! empty( $test ) ) {
			echo '<div class="debugpress-debug-notice-block">';

			$this->title( '<i class="debugpress-icon debugpress-icon-triangle-exclamation"></i> ' . esc_html__( 'Debug mode problems', 'debugpress' ), true, true );
			$this->block_header();
			foreach ( $test as $t ) {
				$this->sub_title( $t[0] );
				echo esc_html( $t[1] );
				echo ' <a rel="noopener" href="https://debug.press/documentation/wordpress-setup/" target="_blank">' . esc_html__( 'More Information', 'debugpress' ) . '</a>';
			}
			$this->block_footer();

			echo '</div>';
		}

		$this->title( esc_html__( 'Page Loading Stats', 'debugpress' ) );
		$this->block_header();
		$this->table_init_standard();
		$this->table_head();
		$this->table_row( array( __( 'Memory Used by PHP', 'debugpress' ), debugpress_tracker()->get( '_end', 'memory' ) ) );
		$this->table_row( array( __( 'Total Page Time', 'debugpress' ), debugpress_tracker()->get( '_end', 'time' ) . " " . __( 'seconds', 'debugpress' ) ) );
		$this->table_row( array( __( 'Number of SQL Queries', 'debugpress' ), debugpress_tracker()->get( '_end', 'queries' ) ) );

		if ( defined( "SAVEQUERIES" ) && SAVEQUERIES ) {
			$this->table_row( array( __( 'Time for SQL Queries', 'debugpress' ), debugpress_tracker()->get_total_sql_time() . " " . __( 'seconds', 'debugpress' ) ) );
		}

		if ( debugpress_tracker()->count_hooks > 0 ) {
			$this->table_row( array( __( 'Executed Hooks', 'debugpress' ), debugpress_tracker()->count_hooks ) );
		}

		if ( ! empty( debugpress_tracker()->httpapi ) ) {
			$this->table_row( array( __( 'HTTP API Calls', 'debugpress' ), count( debugpress_tracker()->httpapi ) ) );
			$this->table_row( array( __( 'HTTP API Total Time', 'debugpress' ), debugpress_tracker()->http_total_time() . " " . __( 'seconds', 'debugpress' ) ) );
		}
		$this->table_row( array( __( 'Current Timestamp', 'debugpress' ), time() ) );
		$this->table_row( array( __( 'Current Datetime', 'debugpress' ), gmdate( 'c' ) ) );
		$this->table_foot();
		$this->block_footer();

		$this->title( esc_html__( 'Current PHP Limits', 'debugpress' ) );
		$this->block_header();
		$this->table_init_standard();
		$this->table_head();
		$this->table_row( array( __( 'PHP Memory Available', 'debugpress' ), ini_get( 'memory_limit' ) . "B" ) );
		$this->table_row( array( __( 'PHP Max Execution Time', 'debugpress' ), ini_get( 'max_execution_time' ) . " " . __( 'seconds', 'debugpress' ) ) );
		$this->table_foot();
		$this->block_footer();

		$this->title( esc_html__( 'Upload Directory', 'debugpress' ) );
		$this->list_array( wp_upload_dir() );
	}

	public function right() {
		$this->title( Info::cms_name() );
		$this->block_header();
		$this->table_init_standard();
		$this->table_head();
		$this->table_row( array( __( 'Version', 'debugpress' ), Info::cms_version() ) );
		$this->table_row( array(
			Info::cms_theme_type_in_use() == 'child' ? __( 'Child Theme', 'debugpress' ) : __( 'Theme', 'debugpress' ),
			Info::cms_stylesheet_theme_name(),
		) );

		if ( is_child_theme() ) {
			$this->table_row( array(
				__( 'Parent Theme', 'debugpress' ),
				Info::cms_templates_theme_name(),
			) );
		}

		$this->table_row( array(
			__( 'Pretty Permalinks', 'debugpress' ),
			WP::instance()->has_permalinks() ? __( 'Enabled', 'debugpress' ) : __( 'Disabled', 'debugpress' ),
		) );
		$this->table_foot();
		$this->block_footer();

		$this->title( esc_html__( 'Page Scope', 'debugpress' ) );
		$this->list_array( debugpress_scope()->scope() );

		$this->title( esc_html__( 'Load Snapshots', 'debugpress' ) );
		$this->block_header();
		$this->add_column( __( 'Name', 'debugpress' ), "", "", true );
		$this->add_column( __( 'Memory', 'debugpress' ), "", "text-align: right;" );
		$this->add_column( __( 'Timer', 'debugpress' ), "", "text-align: right;" );
		$this->add_column( __( 'SQL', 'debugpress' ), "", "text-align: right;" );
		$this->add_column( __( 'Hooks', 'debugpress' ), "", "text-align: right;" );
		$this->table_head();
		foreach ( debugpress_tracker()->snapshots as $name => $obj ) {
			$this->table_row( array(
				$name,
				debugpress_format_size( $obj['memory'] ),
				number_format( $obj['time'], 5 ),
				$obj['queries'],
				$obj['hooks'],
			) );
		}
		$this->table_foot();
		$this->block_footer();
	}

	private function _env() : array {
		return debugpress_plugin()->environment();
	}

	private function _test() : array {
		$test = array();

		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			$test[] = array(
				'WP_DEBUG',
				__( 'Debug mode is not enabled. Some of the debug related information is not available.', 'debugpress' ),
			);
		}

		if ( ! defined( 'SAVEQUERIES' ) || ! SAVEQUERIES ) {
			$test[] = array(
				'SAVEQUERIES',
				__( 'Saving of SQL queries is not enabled. SQL queries debug is not available.', 'debugpress' ),
			);
		}

		return $test;
	}
}
