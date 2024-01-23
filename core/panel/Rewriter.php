<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\Panel;

class Rewriter extends Panel {
	public function left() {
		global $wp_rewrite;

		$this->title( esc_html__( 'Rewrite Rules', 'debugpress' ) );

		if ( ! is_admin() ) {
			if ( ! empty( $wp_rewrite->rules ) ) {
				$this->list_array( $wp_rewrite->rules, '$' . 'wp_rewrite->rules' ); // phpcs:ignore Generic.Strings.UnnecessaryStringConcat
			} else {
				echo '<div class="debugpress-debug-notice-block">';
				$this->title( '<i class="debugpress-icon debugpress-icon-triangle-exclamation"></i> ' . esc_html__( 'Rewrite Rules problem', 'debugpress' ), true, true );
				$this->block_header();
				esc_html_e( 'Permalinks are disabled.', 'debugpress' );
				$this->block_footer();
				echo '</div>';
			}
		} else {
			$this->block_header();
			esc_html_e( 'Rewrite rules are not loaded on the WordPress admin side.', 'debugpress' );
			$this->block_footer();
		}
	}

	public function right() {
		global $wp_rewrite;

		$this->title( esc_html__( 'Extra Permalinks Structure', 'debugpress' ) );
		$this->list_array( $wp_rewrite->extra_permastructs, '$' . 'wp_rewrite' ); // phpcs:ignore Generic.Strings.UnnecessaryStringConcat

		$this->title( esc_html__( 'Rewrite Structures', 'debugpress' ) );
		$this->list_properties( $wp_rewrite, array(
			'permalink_structure',
			'feed_structure',
			'comment_feed_structure',
			'author_structure',
			'date_structure',
		), '$' . 'wp_rewrite' ); // phpcs:ignore Generic.Strings.UnnecessaryStringConcat

		$this->title( esc_html__( 'Rewrite Base', 'debugpress' ) );
		$this->list_properties( $wp_rewrite, array(
			'author_base',
			'search_base',
			'comments_base',
			'pagination_base',
			'feed_base',
		), '$' . 'wp_rewrite' ); // phpcs:ignore Generic.Strings.UnnecessaryStringConcat

		$this->title( esc_html__( 'Rewrite Tags', 'debugpress' ) );
		$this->list_properties( $wp_rewrite, array(
			'rewritecode',
			'rewritereplace',
			'queryreplace',
		), '$' . 'wp_rewrite' ); // phpcs:ignore Generic.Strings.UnnecessaryStringConcat
	}
}
