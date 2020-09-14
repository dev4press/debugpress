<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Display\SQLFormat;
use Dev4Press\Plugin\DebugPress\Main\Panel;

class Query extends Panel {
	public function left() {
		global $wp_query;

		$this->title( __( "Query Conditionals", "debugpress" ), true );
		$this->block_header( true );
		$this->table_init_standard();
		$this->table_head();
		foreach ( $wp_query as $property => $value ) {
			if ( substr( $property, 0, 3 ) == "is_" && $value === true ) {
				$this->table_row( array( $property, ucwords( str_replace( "_", " ", substr( $property, 3 ) ) ) ) );
			}
		}

		if ( is_front_page() ) {
			$this->table_row( array( 'is_front_page', 'Front Page' ) );
		}
		$this->table_foot();
		$this->block_footer();

		$this->title( __( "Query Variables", "debugpress" ), true );
		$this->list_array( $wp_query->query, '$' . 'wp_query->query' );
		$this->list_array( $wp_query->tax_query, '$' . 'wp_query->tax_query' );
		$this->list_array( $wp_query->meta_query, '$' . 'wp_query->meta_query' );
	}

	public function right() {
		global $wp_query, $wp;

		$this->title( __( "Executed SQL Query", "debugpress" ), true );
		$this->block_header( true );
		echo '<div class="query-sql-run-full">';
		echo SQLFormat::format( $wp_query->request, true );
		echo '</div>';
		$this->block_footer();

		$this->title( __( "Complete WP Query object", "debugpress" ), true );
		$this->block_header( true );
		gdp_r( $wp_query, false );
		$this->block_footer();

		$this->title( __( "Complete WP object", "debugpress" ), true );
		$this->block_header( true );
		gdp_r( $wp, false );
		$this->block_footer();
	}
}
