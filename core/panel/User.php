<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\Panel;

class User extends Panel {
	public $usermeta;

	public $all_found = array();
	public $fields = array(
		'users'   => array(
			'ID',
			'user_login',
			'user_pass',
			'user_nicename',
			'user_email',
			'user_url',
			'user_registered',
			'user_activation_key',
			'user_status',
		),
		'bbpress' => array(
			'bb_capabilities',
			'bb_user_settings',
			'bb_user_settings_time',
			'bb_topics_replied',
			'bbp_last_activity',
			'_bbp_topics_replied',
		),
		'system'  => array(
			'plugins_last_view',
			'rich_editing',
			'comment_shortcuts',
			'admin_color',
			'user_level',
			'edit_pages_per_page',
			'use_ssl',
			'users_per_page',
			'show_admin_bar_front',
			'show_admin_bar_admin',
			'manageuserscolumnshidden',
			'managenavmenuscolumnshidden',
			'%reg%capabilities',
			'%reg%user_level',
			'%reg%usersettings',
			'%reg%autosave_draft_ids',
			'%reg%usersettingstime',
			'%reg%dashboard_quick_press_last_post_id',
			'closedpostboxes_%reg%',
			'metaboxhidden_%reg%',
			'metaboxorder_%reg%',
			'screen_layout_%reg%',
			'metaboxhidden_%reg%',
			'manageedit%reg%columnshidden',
			'edit_%reg%_per_page',
		),
		'info'    => array(
			'display_name',
			'first_name',
			'last_name',
			'nickname',
			'description',
			'aim',
			'yim',
			'jabber',
			'user_firstname',
			'user_lastname',
			'user_description',
		),
	);

	public function __construct() {
		global $userdata;

		$this->usermeta = get_user_meta( $userdata->ID );
	}

	public function left() {
		global $userdata;

		$this->title( esc_html__( 'Basic User Information', 'debugpress' ) );
		$this->block_header();
		$this->table_init_standard();
		$this->table_head();
		foreach ( $this->fields['users'] as $name ) {
			if ( isset( $userdata->data->$name ) ) {
				$this->all_found[] = $name;
				$this->table_row( array( $name, $this->print_it( $userdata->$name ) ) );
			}
		}
		$this->table_foot();
		$this->block_footer();

		$this->title( esc_html__( 'Core Meta Information', 'debugpress' ) );
		$this->block_header();
		$this->table_init_standard();
		$this->table_head();
		foreach ( $this->usermeta as $key => $value ) {
			$found = false;
			if ( ! in_array( $key, $this->all_found ) ) {
				foreach ( $this->fields['system'] as $name ) {
					if ( strpos( $name, '%reg%' ) !== false ) {
						$name = str_replace( '%reg%', '.+', $name );
						if ( preg_match( '/' . $name . '/i', $key ) ) {
							$found = $key;
							break;
						}
					} else {
						if ( $key == $name ) {
							$found = $key;
							break;
						}
					}
				}
			}

			if ( $found !== false ) {
				$this->all_found[] = $found;
				foreach ( $value as $v ) {
					$this->table_row( array( $found, $this->print_it( $v ) ) );
				}
			}
		}
		$this->table_foot();
		$this->block_footer();
	}

	public function right() {
		$this->title( esc_html__( 'User Information', 'debugpress' ) );
		$this->block_header();
		$this->table_init_standard();
		$this->table_head();
		foreach ( $this->fields['info'] as $name ) {
			if ( isset( $this->usermeta[ $name ] ) ) {
				$this->all_found[] = $name;
				$value             = $this->usermeta[ $name ];

				foreach ( $value as $v ) {
					$this->table_row( array( $name, $this->print_it( $v ) ) );
				}
			}
		}
		$this->table_foot();
		$this->block_footer();

		$this->title( esc_html__( 'Rest of User Meta Information', 'debugpress' ) );
		$this->block_header();
		$this->table_init_standard();
		$this->table_head();
		foreach ( $this->usermeta as $key => $value ) {
			if ( ! in_array( $key, $this->all_found ) ) {
				foreach ( $value as $v ) {
					$this->table_row( array( $key, $this->print_it( $v ) ) );
				}
			}
		}
		$this->table_foot();
		$this->block_footer();
	}
}
