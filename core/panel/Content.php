<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\Panel;

class Content extends Panel {
	public function left() {
		global $wp_taxonomies, $wp_post_types, $wp_post_statuses, $_wp_additional_image_sizes;

		$this->title( __( "Registered Post Types", "debugpress" ) );
		$this->list_array( $wp_post_types );

		$this->title( __( "Registered Taxonomies", "debugpress" ) );
		$this->list_array( $wp_taxonomies );

		$this->title( __( "Registered Post Statuses", "debugpress" ) );
		$this->list_array( $wp_post_statuses );

		$this->title( __( "Additional Image Sizes", "debugpress" ) );
		$this->list_array( $_wp_additional_image_sizes );
	}

	public function right() {
		global $wp_rewrite;

		$this->title( __( "Rewrite Rules", "debugpress" ) );

		if ( ! is_admin() ) {
			if ( ! empty( $wp_rewrite->rules ) ) {
				$this->list_array( $wp_rewrite->rules, '$' . 'wp_rewrite->rules' );
			} else {
				echo '<div class="debugpress-debug-notice-block">';
				$this->title( '<i class="debugpress-icon debugpress-icon-triangle-exclamation"></i> ' . __( "Rewrite Rules problem", "debugpress" ), true, true );
				$this->block_header();
				_e( "Permalinks are disabled.", "debugpress" );
				$this->block_footer();
				echo '</div>';
			}
		} else {
			$this->block_header();
			_e( "Rewrite rules are not loaded on the WordPress admin side.", "debugpress" );
			$this->block_footer();
		}

		$this->title( __( "Extra Permalinks Structure", "debugpress" ) );
		$this->list_array( $wp_rewrite->extra_permastructs, '$' . 'wp_rewrite' );

		$this->title( __( "Rewrite Structures", "debugpress" ) );
		$this->list_properties( $wp_rewrite, array(
			"permalink_structure",
			"feed_structure",
			"comment_feed_structure",
			"author_structure",
			"date_structure"
		), '$' . 'wp_rewrite' );

		$this->title( __( "Rewrite Base", "debugpress" ) );
		$this->list_properties( $wp_rewrite, array(
			"author_base",
			"search_base",
			"comments_base",
			"pagination_base",
			"feed_base"
		), '$' . 'wp_rewrite' );

		$this->title( __( "Rewrite Tags", "debugpress" ) );
		$this->list_properties( $wp_rewrite, array(
			"rewritecode",
			"rewritereplace",
			"queryreplace"
		), '$' . 'wp_rewrite' );
	}
}
