<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Display\SQLFormat;
use Dev4Press\Plugin\DebugPress\Main\Panel;

class Store extends Panel {
	public function single() {
		$this->title( esc_html__( 'Stored objects and other data', 'debugpress' ), true, true );
		$this->block_header();
		$this->add_column( __( 'Time', 'debugpress' ), "", "", true );
		$this->add_column( __( 'Title', 'debugpress' ) );
		$this->add_column( __( 'Logged Data', 'debugpress' ) );
		$this->add_column( __( 'Caller', 'debugpress' ) );
		$this->table_head();
		foreach ( debugpress_tracker()->logged as $item ) {
			$this->render_item( $item );
		}
		$this->table_foot();
		$this->block_footer();
	}

	public function render_item( $item ) {
		$printed = $item['sql']
			?
			'<div class="query-sql-run-full">' . SQLFormat::format( $item['print'] ) . '</div>'
			:
			debugpress_rx( $item['print'], false );

		$this->table_row( array(
				$item['time'],
				( empty( $item['title'] ) ? '/' : '<strong>' . $item['title'] . '</strong>' ),
				$printed,
				debugpress_rx( $item['caller'], false ),
			)
		);
	}
}
