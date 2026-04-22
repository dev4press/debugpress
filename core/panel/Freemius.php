<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

use Dev4Press\Plugin\DebugPress\Main\Panel;
use Freemius as FreemiusCore;
use FS_DebugManager;
use FS_Options;
use FS_Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Freemius extends Panel {
	private array $vars;

	public function __construct() {
		$globals = array(
			'WP_FS__REMOTE_ADDR',
			'WP_FS__ADDRESS_PRODUCTION',
			'FS_API__ADDRESS',
			'FS_API__SANDBOX_ADDRESS',
			'WP_FS__DIR',
		);

		$all_modules_sites       = FS_DebugManager::get_all_modules_sites();
		$licenses_by_module_type = $this->get_all_licenses_by_module_type();

		$this->vars = array(
			'plugin_sites'    => $all_modules_sites[ WP_FS__MODULE_TYPE_PLUGIN ],
			'theme_sites'     => $all_modules_sites[ WP_FS__MODULE_TYPE_THEME ],
			'plugin_licenses' => $licenses_by_module_type[ WP_FS__MODULE_TYPE_PLUGIN ],
			'theme_licenses'  => $licenses_by_module_type[ WP_FS__MODULE_TYPE_THEME ],
			'addons'          => FreemiusCore::get_all_addons(),
			'users'           => FreemiusCore::get_all_users(),
			'account_addons'  => FreemiusCore::get_all_account_addons(),
			'globals'         => array(
				'UNFILTERED_SITE_URL' => FreemiusCore::get_unfiltered_site_url(),
			),
		);

		foreach ( $globals as $global ) {
			$this->vars['globals'][ $global ] = defined( $global ) ? constant( $global ) : null;
		}
	}

	private function get_all_licenses_by_module_type() : array {
		$licenses = FreemiusCore::get_account_option( 'all_licenses' );

		$licenses_by_module_type = array(
			WP_FS__MODULE_TYPE_PLUGIN => array(),
			WP_FS__MODULE_TYPE_THEME  => array(),
		);

		if ( ! is_array( $licenses ) ) {
			return $licenses_by_module_type;
		}

		foreach ( $licenses as $module_id => $module_licenses ) {
			$fs = FreemiusCore::get_instance_by_id( $module_id );
			if ( false === $fs ) {
				continue;
			}

			$licenses_by_module_type[ $fs->get_module_type() ] = array_merge( $licenses_by_module_type[ $fs->get_module_type() ],
				$module_licenses );
		}

		return $licenses_by_module_type;
	}

	public function left() {
		global $fs_active_plugins;

		$this->title( esc_html__( 'Globals', 'debugpress' ) );
		$this->list_array( $this->vars['globals'] );

		$this->title( esc_html__( 'SDK Versions', 'debugpress' ) );
		$this->block_header();
		$this->add_column( __( 'Version', 'debugpress' ), "", "", true );
		$this->add_column( __( 'SDK Path', 'debugpress' ) );
		$this->add_column( __( 'Module Path', 'debugpress' ) );
		$this->add_column( __( 'Active', 'debugpress' ), "", "text-align: right;" );
		$this->table_head();
		foreach ( $fs_active_plugins->plugins as $sdk_path => $data ) {
			$this->table_row( array(
				$data->version,
				$sdk_path,
				$data->plugin_path,
				WP_FS__SDK_VERSION == $data->version ? __( 'Yes', 'debugpress' ) : __( 'No', 'debugpress' ),
			) );
		}
		$this->table_foot();
		$this->block_footer();

		$this->title( esc_html__( 'Users', 'debugpress' ) );
		$this->block_header();
		$this->add_column( __( 'ID', 'debugpress' ), "", "", true );
		$this->add_column( __( 'Name', 'debugpress' ) );
		$this->add_column( __( 'Email', 'debugpress' ) );
		$this->add_column( __( 'Verified', 'debugpress' ) );
		$this->add_column( __( 'Public Key', 'debugpress' ) );
		$this->add_column( __( 'Secret Key', 'debugpress' ) );
		$this->table_head();
		foreach ( $this->vars['users'] as $user ) {
			$this->table_row( array(
				$user->id,
				$user->first . ' ' . $user->last,
				$user->email,
				$user->is_verified ? __( 'Yes', 'debugpress' ) : __( 'No', 'debugpress' ),
				esc_html( $user->public_key ),
				current_user_can( 'manage_options' ) ? esc_html( $user->secret_key ) : '********',
			) );
		}
		$this->table_foot();
		$this->block_footer();
	}

	public function right() {
		$fs_options = FS_Options::instance( WP_FS__ACCOUNTS_OPTION_NAME, true );

		$module_types = array(
			WP_FS__MODULE_TYPE_PLUGIN => esc_html__( 'Plugins' ),
			WP_FS__MODULE_TYPE_THEME  => esc_html__( 'Themes' ),
		);

		foreach ( $module_types as $module_type => $name ) {
			$modules = fs_get_entities( $fs_options->get_option( $module_type . 's' ), FS_Plugin::get_class_name() );

			if ( is_array( $modules ) && count( $modules ) > 0 ) {
				$this->title( $name );
				$this->block_header();
				$this->add_column( __( 'ID', 'debugpress' ), "", "", true );
				$this->add_column( __( 'Slug', 'debugpress' ) );
				$this->add_column( __( 'Name', 'debugpress' ) );
				$this->add_column( __( 'Version', 'debugpress' ) );
				$this->add_column( __( 'Path', 'debugpress' ) );
				$this->add_column( __( 'Public Key', 'debugpress' ) );
				$this->table_head();
				foreach ( $modules as $plugin ) {
					$this->table_row( array(
						$plugin->id,
						$plugin->slug,
						$plugin->title,
						$plugin->version,
						$plugin->file,
						esc_html( $plugin->public_key ),
					) );
				}
				$this->table_foot();
				$this->block_footer();
			}

			$sites = $this->vars[ $module_type . '_sites' ];

			if ( is_array( $sites ) && count( $sites ) > 0 ) {
				$this->title( $name . ' ' . __( 'Sites or Installs' ) );
				$this->block_header();
				$this->add_column( __( 'ID', 'debugpress' ), "", "", true );
				$this->add_column( __( 'Slug', 'debugpress' ) );
				$this->add_column( __( 'User ID', 'debugpress' ) );
				$this->add_column( __( 'License ID', 'debugpress' ) );
				$this->add_column( __( 'Plan ID', 'debugpress' ) );
				$this->add_column( __( 'Public Key', 'debugpress' ) );
				$this->add_column( __( 'Secret Key', 'debugpress' ) );
				if ( is_multisite() ) {
					$this->add_column( __( 'Blog ID', 'debugpress' ) );
				}
				$this->table_head();
				foreach ( $sites as $slug => $list ) {
					foreach ( $list as $site ) {
						$row = array(
							$site->id,
							$slug,
							$site->user_id,
							$site->license_id,
							$site->plan_id,
							esc_html( $site->public_key ),
							current_user_can( 'manage_options' ) ? esc_html( $site->secret_key ) : '********',
						);

						if ( is_multisite() ) {
							$row[] = $site->blog_id;
						}

						$this->table_row( $row );
					}
				}
				$this->table_foot();
				$this->block_footer();
			}

			$licenses = $this->vars[ $module_type . '_licenses' ];

			if ( is_array( $licenses ) && count( $licenses ) > 0 ) {
				$this->title( $name . ' ' . __( 'Sites or Installs' ) );
				$this->block_header();
				$this->add_column( __( 'ID', 'debugpress' ), "", "", true );
				$this->add_column( __( 'Plugin ID', 'debugpress' ) );
				$this->add_column( __( 'User ID', 'debugpress' ) );
				$this->add_column( __( 'Plan ID', 'debugpress' ) );
				$this->add_column( __( 'Quota', 'debugpress' ) );
				$this->add_column( __( 'Activated', 'debugpress' ) );
				$this->add_column( __( 'Blocking', 'debugpress' ) );
				$this->add_column( __( 'Type', 'debugpress' ) );
				$this->add_column( __( 'Expiration', 'debugpress' ) );

				$this->table_head();
				foreach ( $licenses as $license ) {
					$row = array(
						$license->id,
						$license->plugin_id,
						$license->user_id,
						$license->plan_id,
						$license->is_unlimited() ? __( 'Unlimited' ) : ( $license->is_single_site() ? __( 'Single Site' ) : $license->quota ),
						$license->activated,
						$license->is_block_features ? __( 'Blocking' ) : __( 'Flexible' ),
						$license->is_whitelabeled ? __( 'Whitelabeled' ) : __( 'Normal' ),
						$license->expiration,
					);

					$this->table_row( $row );
				}
				$this->table_foot();
				$this->block_footer();
			}
		}
	}
}
