<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

use Dev4Press\Plugin\DebugPress\Main\Panel;

class Admin extends Panel {
	public function left() {
		$this->title( __( "Page Information", "debugpress" ), true );
		$this->block_header( true );
		$this->table_init_standard();
		$this->table_head();
		$this->table_row( array( "&#36;pagenow", isset( $GLOBALS['pagenow'] ) ? $GLOBALS['pagenow'] : '' ) );
		$this->table_row( array( "&#36;typenow", isset( $GLOBALS['typenow'] ) ? $GLOBALS['typenow'] : '' ) );
		$this->table_row( array( "&#36;taxnow", isset( $GLOBALS['taxnow'] ) ? $GLOBALS['taxnow'] : '' ) );
		$this->table_row( array(
			"&#36;hook_suffix",
			isset( $GLOBALS['hook_suffix'] ) ? $GLOBALS['hook_suffix'] : ''
		) );
		$this->table_foot();
		$this->block_footer();

		$screen = get_current_screen();

		if ( ! is_null( $screen ) ) {
			$this->title( __( "Current Screen", "debugpress" ), true );
			$this->block_header( true );
			$this->table_init_standard();
			$this->table_head();
			$this->table_row( array( __( "Base", "debugpress" ), $screen->base ) );
			$this->table_row( array( __( "ID", "debugpress" ), $screen->id ) );
			$this->table_row( array( __( "Parent Base", "debugpress" ), $screen->parent_base ) );
			$this->table_row( array( __( "Parent File", "debugpress" ), $screen->parent_file ) );
			$this->table_row( array( __( "Post Type", "debugpress" ), $screen->post_type ) );
			$this->table_row( array( __( "Taxonomy", "debugpress" ), $screen->taxonomy ) );
			$this->table_foot();
			$this->block_footer();
		}
	}

	public function right() {
		$this->title( __( "Conditionals", "debugpress" ), true );
		$this->block_header( true );
		$this->table_init_standard();
		$this->table_head();
		$this->table_row( array(
			'is_blog_admin',
			is_blog_admin() ? __( "Yes", "debugpress" ) : __( "No", "debugpress" )
		) );
		$this->table_row( array(
			'is_network_admin',
			is_network_admin() ? __( "Yes", "debugpress" ) : __( "No", "debugpress" )
		) );
		$this->table_row( array(
			'is_user_admin',
			is_user_admin() ? __( "Yes", "debugpress" ) : __( "No", "debugpress" )
		) );
		$this->table_foot();
		$this->block_footer();
	}
}
