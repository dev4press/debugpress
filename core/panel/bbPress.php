<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\Panel;

class bbPress extends Panel {
	public $globals = array(
		'version',
		'db_version',
		'file',
		'basename',
		'basepath',
		'plugin_dir',
		'plugin_url',
		'includes_dir',
		'includes_url',
		'lang_base',
		'lang_dir',
		'themes_dir',
		'themes_url',
	);

	public $conditionals = array(
		'bbp_is_forum',
		'bbp_is_forum_archive',
		'bbp_is_single_forum',
		'bbp_is_forum_edit',
		'bbp_is_topic',
		'bbp_is_single_topic',
		'bbp_is_topic_archive',
		'bbp_is_topic_edit',
		'bbp_is_topic_merge',
		'bbp_is_topic_split',
		'bbp_is_topic_tag',
		'bbp_is_topic_tag_edit',
		'bbp_is_custom_post_type',
		'bbp_is_reply',
		'bbp_is_reply_edit',
		'bbp_is_reply_move',
		'bbp_is_single_reply',
		'bbp_is_favorites',
		'bbp_is_subscriptions',
		'bbp_is_topics_created',
		'bbp_is_replies_created',
		'bbp_is_user_home',
		'bbp_is_user_home_edit',
		'bbp_is_single_user',
		'bbp_is_single_user_edit',
		'bbp_is_single_user_profile',
		'bbp_is_single_user_topics',
		'bbp_is_single_user_replies',
		'bbp_is_single_view',
		'bbp_is_search',
		'bbp_is_search_results',
		'bbp_is_edit',
		'gdtox_is_topic_prefix',
		'gdmed_is_members_directory',
	);

	public function left() {
		$this->title( esc_html__( 'Query Conditionals', 'debugpress' ) );
		$this->block_header();
		$this->table_init_standard();
		$this->table_head();
		$this->table_row( array( 'is_bbpress()', 'bbPress' ) );

		foreach ( $this->conditionals as $condition ) {
			if ( function_exists( $condition ) && call_user_func( $condition ) ) {
				$this->table_row( array(
					$condition . '()',
					ucwords( str_replace( "_", " ", substr( $condition, strpos( $condition, '_is_' ) + 4 ) ) ),
				) );
			}
		}
		$this->table_foot();
		$this->block_footer();

		$this->title( esc_html__( 'bbPress Environment', 'debugpress' ) );
		$this->block_header();
		$this->table_init_standard();
		$this->table_head();
		foreach ( $this->globals as $global ) {
			$this->table_row( array( $global, bbpress()->$global ) );
		}
		$this->table_foot();
		$this->block_footer();

		$this->title( esc_html__( 'Templates Stack', 'debugpress' ) );
		$this->block_header();
		$this->add_column( __( 'Path', 'debugpress' ), '', '', true );
		$this->table_head();
		foreach ( bbp_get_template_stack() as $path ) {
			$this->table_row( array( $path ) );
		}
		$this->table_foot();
		$this->block_footer();
	}

	public function right() {
		$this->title( esc_html__( 'bbPress Queries', 'debugpress' ) );
		$this->block_header();
		if ( isset( debugpress_tracker()->objects['bbpress']['forum_query'] ) ) {
			$this->sub_title( __( 'Forum Query', 'debugpress' ) );
			debugpress_r( debugpress_tracker()->objects['bbpress']['forum_query'], false );
		}

		if ( isset( debugpress_tracker()->objects['bbpress']['topic_query'] ) ) {
			$this->sub_title( __( 'Topic Query', 'debugpress' ) );
			debugpress_r( debugpress_tracker()->objects['bbpress']['topic_query'], false );
		}

		if ( isset( debugpress_tracker()->objects['bbpress']['reply_query'] ) ) {
			$this->sub_title( __( 'Reply Query', 'debugpress' ) );
			debugpress_r( debugpress_tracker()->objects['bbpress']['reply_query'], false );
		}

		if ( isset( debugpress_tracker()->objects['bbpress']['search_query'] ) ) {
			$this->sub_title( __( 'Search Query', 'debugpress' ) );
			debugpress_r( debugpress_tracker()->objects['bbpress']['search_query'], false );
		}
		$this->block_footer();

		$this->title( esc_html__( 'Basic bbPress Object', 'debugpress' ) );
		$this->block_header();
		debugpress_r( bbpress(), false );
		$this->block_footer();
	}
}
