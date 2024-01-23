<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\Panel;

class Enqueue extends Panel {
	public function left() {
		global $wp_scripts;

		$this->title( esc_html__( 'Scripts in Header', 'debugpress' ) );
		$this->block_header();
		$this->table_init_standard();
		$this->table_head();
		foreach ( $wp_scripts->queue as $scr ) {
			$this->table_row( array( $scr, $wp_scripts->registered[ $scr ]->src ) );
		}
		$this->table_foot();
		$this->block_footer();

		$this->title( esc_html__( 'Scripts in Footer', 'debugpress' ) );
		$this->block_header();
		$this->table_init_standard();
		$this->table_head();
		foreach ( $wp_scripts->in_footer as $scr ) {
			$this->table_row( array( $scr, $wp_scripts->registered[ $scr ]->src ) );
		}
		$this->table_foot();
		$this->block_footer();
	}

	public function right() {
		global $wp_styles;

		$this->title( esc_html__( 'Styles in Header', 'debugpress' ) );
		$this->block_header();
		$this->table_init_standard();
		$this->table_head();
		foreach ( $wp_styles->queue as $scr ) {
			$this->table_row( array( $scr, $wp_styles->registered[ $scr ]->src ) );
		}
		$this->table_foot();
		$this->block_footer();
	}
}
