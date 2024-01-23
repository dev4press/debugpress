<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Display\SQLFormat;
use Dev4Press\Plugin\DebugPress\Main\Panel;
use WP_Post;

class Query extends Panel {
	public function left() {
		global $wp_query;

		$this->title( esc_html__( 'Query Conditionals', 'debugpress' ) );
		$this->block_header();
		$this->table_init_standard();
		$this->table_head();
		foreach ( $wp_query as $property => $value ) {
			if ( substr( $property, 0, 3 ) == 'is_' && $value === true ) {
				$this->table_row( array( $property, 'WP: ' . ucwords( str_replace( "_", " ", substr( $property, 3 ) ) ) ) );
			} else if ( substr( $property, 0, 7 ) == 'bbp_is_' && $value === true ) {
				$this->table_row( array( $property, 'bbPress: ' . ucwords( str_replace( "_", " ", substr( $property, 7 ) ) ) ) );
			}
		}

		if ( is_front_page() ) {
			$this->table_row( array( 'is_front_page', 'Front Page' ) );
		}
		$this->table_foot();
		$this->block_footer();

		global $post;

		if ( $post instanceof WP_Post ) {
			$meta = get_post_meta( $post->ID );

			$this->title( esc_html__( 'Global Post', 'debugpress' ) );
			$this->block_header();
			debugpress_r( $post, false );
			$this->block_footer();

			if ( $meta ) {
				$this->title( esc_html__( 'Global Post Meta Data', 'debugpress' ) );
				$this->list_array( $meta );
			}
		}
	}

	public function right() {
		global $wp_query, $wp;

		if ( ! empty( $wp_query->request ) ) {
			$this->title( esc_html__( 'Executed SQL Query', 'debugpress' ) );
			$this->block_header();
			echo '<div class="query-sql-run-full">';
			echo SQLFormat::format( $wp_query->request ); // phpcs:ignore WordPress.Security.EscapeOutput
			echo '</div>';
			$this->block_footer();
		}

		$this->title( esc_html__( 'Complete WP Query object', 'debugpress' ) );
		$this->block_header();
		debugpress_r( $wp_query, false );
		$this->block_footer();

		$this->title( esc_html__( 'Complete WP object', 'debugpress' ) );
		$this->block_header();
		debugpress_r( $wp, false );
		$this->block_footer();

		$this->title( esc_html__( 'Query Variables', 'debugpress' ), false );
		$this->block_header( false );
		if ( $wp_query->query ) {
			$this->list_array( $wp_query->query, '$' . 'wp_query->query', false ); // phpcs:ignore Generic.Strings.UnnecessaryStringConcat
		}
		if ( $wp_query->tax_query ) {
			$this->list_array( $wp_query->tax_query, '$' . 'wp_query->tax_query', false ); // phpcs:ignore Generic.Strings.UnnecessaryStringConcat
		}
		if ( $wp_query->meta_query ) {
			$this->list_array( $wp_query->meta_query, '$' . 'wp_query->meta_query', false ); // phpcs:ignore Generic.Strings.UnnecessaryStringConcat
		}
		$this->block_footer();
	}
}
