<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\Panel;

class Store extends Panel {
	public function single() {
		$this->title( __( "Stored objects and other data", "debugpress" ), true );
		$this->block_header( true );
		$this->add_column( __( "Time", "debugpress" ), "", "", true );
		$this->add_column( __( "Title", "debugpress" ), "", "" );
		$this->add_column( __( "Logged Data", "debugpress" ), "", "" );
		$this->add_column( __( "Caller", "debugpress" ), "", "" );
		$this->table_head();
		foreach ( debugpress_tracker()->logged as $item ) {
			$this->render_item( $item );
		}
		$this->table_foot();
		$this->block_footer();
	}

	public function render_item( $item ) {
		$this->table_row( array(
				$item['time'],
				( empty( $item['title'] ) ? '/' : '<strong>' . $item['title'] . '</strong>' ),
				gdp_rx( $item['print'], false ),
				gdp_rx( $item['caller'], false )
			)
		);
	}
}