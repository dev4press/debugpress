<?php

namespace Dev4Press\Plugin\DebugPress\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Exception;
use PDO;

class Info {
	public static function _loaded_status( $value ) : string {
		if ( $value === true ) {
			return __( 'loaded', 'debugpress' );
		} else {
			return '<strong style="color: #cc0000;">' . __( 'not loaded', 'debugpress' ) . '</strong>';
		}
	}

	public static function is_apache() : bool {
		global $is_apache;

		return $is_apache;
	}

	public static function debug_log_path() : string {
		$path = '';

		if ( DEBUGPRESS_IS_DEBUG_LOG ) {
			if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
				$path = ini_get( 'error_log' );
			}
		}

		return $path;
	}

	public static function cms_name() : string {
		return debugpress_is_classicpress() ? 'ClassicPress' : 'WordPress';
	}

	public static function cms_version() : string {
		return debugpress_is_classicpress() ? debugpress_plugin()->cp_version_real() : debugpress_plugin()->wp_version_real();
	}

	public static function cms_count_plugins() : array {
		$plugins = wp_cache_get( 'gd-press-tools', 'wordpress-plugins' );

		if ( $plugins === false ) {
			$all_plugins = get_plugins();

			$plugins = array(
				'total'    => 0,
				'active'   => 0,
				'inactive' => 0,
			);

			foreach ( array_keys( $all_plugins ) as $plugin ) {
				$plugins['total'] ++;

				if ( is_plugin_active( $plugin ) ) {
					$plugins['active'] ++;
				} else {
					$plugins['inactive'] ++;
				}
			}

			wp_cache_add( 'gd-press-tools', $plugins, 'wordpress-plugins' );
		}

		return (array) $plugins;
	}

	public static function cms_count_plugins_total() : int {
		$data = self::cms_count_plugins();

		return $data['total'];
	}

	public static function cms_count_plugins_active() : int {
		$data = self::cms_count_plugins();

		return $data['active'];
	}

	public static function cms_count_plugins_inactive() : int {
		$data = self::cms_count_plugins();

		return $data['inactive'];
	}

	public static function cms_count_themes() : int {
		$themes = wp_cache_get( 'gd-press-tools', 'wordpress-themes' );

		if ( $themes === false ) {
			$all_themes = wp_get_themes();

			$themes = count( $all_themes );

			wp_cache_add( 'gd-press-tools', $themes, 'wordpress-themes' );
		}

		return (int) $themes;
	}

	public static function cms_theme_type_in_use() : string {
		return is_child_theme() ? 'child' : 'theme';
	}

	public static function cms_stylesheet_theme_name() : string {
		return wp_get_theme()->Name;
	}

	public static function cms_templates_theme_name() : string {
		if ( is_child_theme() ) {
			$theme = wp_get_theme()->Template;

			return wp_get_theme( $theme )->Name;
		}

		return self::cms_stylesheet_theme_name();
	}

	public static function apache_modules_list() : array {
		if ( self::is_apache() && function_exists( 'apache_get_modules' ) ) {
			return apache_get_modules();
		}

		return array();
	}

	public static function apache_version() : string {
		if ( self::is_apache() && function_exists( 'apache_get_version' ) ) {
			$version = apache_get_version();

			if ( is_string( $version ) ) {
				return $version;
			}
		}

		return '';
	}

	public static function apache_mod_rewrite() : string {
		$status = apache_mod_loaded( 'mod_rewrite' );

		return self::_loaded_status( $status );
	}

	public static function apache_mod_headers() : string {
		$status = apache_mod_loaded( 'mod_headers' );

		return self::_loaded_status( $status );
	}

	public static function apache_mod_security() : string {
		$status = apache_mod_loaded( 'mod_security' );

		return self::_loaded_status( $status );
	}

	public static function apache_mod_ssl() : string {
		$status = apache_mod_loaded( 'mod_ssl' );

		return self::_loaded_status( $status );
	}

	public static function apache_mod_setenvif() : string {
		$status = apache_mod_loaded( 'mod_setenvif' );

		return self::_loaded_status( $status );
	}

	public static function apache_mod_alias() : string {
		$status = apache_mod_loaded( 'mod_alias' );

		return self::_loaded_status( $status );
	}

	public static function server_name() : string {
		return (string) ( $_SERVER['SERVER_SOFTWARE'] ?? '' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	}

	public static function server_os() : string {
		return (string) PHP_OS;
	}

	public static function server_hostname() : string {
		return (string) ( $_SERVER['SERVER_NAME'] ?? '' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	}

	public static function server_ip() : string {
		return isset( $_SERVER['SERVER_ADDR'] ) ? (string) $_SERVER['SERVER_ADDR'] : 'Missing'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	}

	public static function server_port() : string {
		return (string) ( $_SERVER['SERVER_PORT'] ?? '' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	}

	public static function mysql_variant() : string {
		$name    = 'MySQL';
		$version = debugpress_db()->db_mysql_string();

		if ( strpos( $version, 'MariaDB' ) !== false ) {
			$name = 'MariaDB';
		} else if ( strpos( $version, 'Percona' ) !== false ) {
			$name = 'PerconaDB';
		}

		return $name;
	}

	public static function mysql_version() : string {
		return (string) debugpress_db()->db_mysql_version();
	}

	public static function mysql_database() : array {
		$data = wp_cache_get( 'gd-press-tools', 'database' );

		if ( $data === false ) {
			$sql = debugpress_db()->wpdb()->prepare( "SELECT table_schema, COUNT(*) as tables_count, SUM(data_length + index_length) AS data_size, SUM(data_free) AS free_space, SUM(table_rows) AS rows_count FROM information_schema.TABLES WHERE table_schema = %s GROUP BY table_schema", DB_NAME ); // phpcs:ignore WordPress.DB.PreparedSQL
			$raw = debugpress_db()->wpdb()->get_row( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL

			$data = array(
				'tables'  => absint( $raw->tables_count ),
				'records' => absint( $raw->rows_count ),
				'size'    => debugpress_format_size( $raw->data_size ),
				'free'    => debugpress_format_size( $raw->free_space ),
			);

			wp_cache_add( 'gd-press-tools', $data, 'database' );
		}

		return (array) $data;
	}

	public static function mysql_database_size() : string {
		$data = self::mysql_database();

		return $data['size'];
	}

	public static function mysql_database_free_space() : string {
		$data = self::mysql_database();

		return $data['free'];
	}

	public static function mysql_database_tables() : int {
		$data = self::mysql_database();

		return $data['tables'];
	}

	public static function mysql_database_records() : int {
		$data = self::mysql_database();

		return $data['records'];
	}

	public static function mysql_wordpress() : array {
		$data = wp_cache_get( 'database', 'debugpress' );

		if ( $data === false ) {
			$sql = debugpress_db()->wpdb()->prepare( "SELECT table_schema, COUNT(*) as tables_count, SUM(data_length + index_length) AS data_size, SUM(data_free) AS free_space, SUM(table_rows) AS rows_count FROM information_schema.TABLES WHERE table_schema = %s AND table_name like '" . debugpress_db()->wpdb()->base_prefix . "%' GROUP BY table_schema", DB_NAME ); // phpcs:ignore WordPress.DB.PreparedSQL
			$raw = debugpress_db()->wpdb()->get_row( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL

			$data = array(
				'tables'  => absint( $raw->tables_count ?? 0 ),
				'records' => absint( $raw->rows_count ?? 0 ),
				'size'    => debugpress_format_size( $raw->data_size ?? 0 ),
				'free'    => debugpress_format_size( $raw->free_space ?? 0 ),
			);

			wp_cache_add( 'database', 'debugpress' );
		}

		return (array) $data;
	}

	public static function mysql_wordpress_size() : string {
		$data = self::mysql_wordpress();

		return $data['size'];
	}

	public static function mysql_wordpress_free_space() : string {
		$data = self::mysql_wordpress();

		return $data['free'];
	}

	public static function mysql_wordpress_tables() : int {
		$data = self::mysql_wordpress();

		return $data['tables'];
	}

	public static function mysql_wordpress_records() : int {
		$data = self::mysql_wordpress();

		return $data['records'];
	}

	public static function php_error_display() : string {
		return ini_get( 'display_errors' ) ? __( 'On', 'debugpress' ) : __( 'Off', 'debugpress' );
	}

	public static function php_error_logging() : string {
		return ini_get( 'log_errors' ) ? __( 'On', 'debugpress' ) : __( 'Off', 'debugpress' );
	}

	public static function php_error_filepath() : string {
		return ini_get( 'error_log' );
	}

	public static function php_version() : string {
		return phpversion();
	}

	public static function php_sapi() : string {
		return PHP_SAPI;
	}

	public static function php_memory_limit() : string {
		return ini_get( 'memory_limit' );
	}

	public static function php_memory_used() : string {
		if ( function_exists( 'memory_get_usage' ) ) {
			return debugpress_format_size( memory_get_usage() );
		} else {
			return '';
		}
	}

	public static function php_error_levels_display() : array {
		$levels = self::php_error_levels();

		$list = array();

		foreach ( $levels as $level => $status ) {
			$color = $status ? '#00cc00' : '#cc0000';
			$tag   = $status ? 'strong' : 'span';
			$val   = $status ? __( 'ON', 'debugpress' ) : __( 'OFF', 'debugpress' );

			$list[] = '<' . $tag . ' style="color: ' . $color . '">' . $level . ' / ' . $val . '</' . $tag . '>';
		}

		return $list;
	}

	public static function php_error_levels() : array {
		$levels = array(
			'E_ALL'               => false,
			'E_ERROR'             => false,
			'E_WARNING'           => false,
			'E_PARSE'             => false,
			'E_NOTICE'            => false,
			'E_CORE_ERROR'        => false,
			'E_CORE_WARNING'      => false,
			'E_COMPILE_ERROR'     => false,
			'E_COMPILE_WARNING'   => false,
			'E_USER_ERROR'        => false,
			'E_USER_WARNING'      => false,
			'E_USER_NOTICE'       => false,
			'E_STRICT'            => false,
			'E_RECOVERABLE_ERROR' => false,
			'E_DEPRECATED'        => false,
			'E_USER_DEPRECATED'   => false,
		);

		$error_reporting = error_reporting(); // phpcs:ignore WordPress.PHP.DevelopmentFunctions,WordPress.PHP.DiscouragedPHPFunctions

		foreach ( array_keys( $levels ) as $level ) {
			if ( defined( $level ) ) {
				$c = constant( $level );

				if ( $error_reporting & $c ) {
					$levels[ $level ] = true;
				}
			}
		}

		return $levels;
	}

	public static function php_post_size() {
		return ini_get( 'post_max_size' );
	}

	public static function php_max_uploads() {
		return ini_get( 'max_file_uploads' );
	}

	public static function php_upload_size() {
		return ini_get( 'upload_max_filesize' );
	}

	public static function php_execution_time() {
		return ini_get( 'max_execution_time' );
	}

	public static function php_allow_url_fopen() : string {
		return ini_get( 'allow_url_fopen' ) ? __( 'On', 'debugpress' ) : __( 'Off', 'debugpress' );
	}

	public static function php_zlib() : string {
		if ( extension_loaded( 'zlib' ) ) {
			if ( function_exists( 'curl_version' ) ) {
				$gdi = curl_version();

				return $gdi['libz_version'];
			} else {
				return __( 'loaded', 'debugpress' );
			}
		} else {
			return '<strong style="color: #cc0000;">' . __( 'not loaded', 'debugpress' ) . '</strong>';
		}
	}

	public static function php_curl() : string {
		if ( extension_loaded( 'curl' ) ) {
			$gdi = curl_version();

			return $gdi['version'];
		} else {
			return '<strong style="color: #cc0000;">' . __( 'not loaded', 'debugpress' ) . '</strong>';
		}
	}

	public static function php_pdo() : string {
		if ( extension_loaded( 'pdo' ) ) {
			return join( '<br/>', PDO::getAvailableDrivers() );
		} else {
			return '<strong style="color: #cc0000;">' . __( 'not loaded', 'debugpress' ) . '</strong>';
		}
	}

	public static function php_gd() : string {
		if ( extension_loaded( 'gd' ) ) {
			$gdi = gd_info();

			return $gdi['GD Version'];
		} else {
			return '<strong style="color: #cc0000;">' . __( 'not loaded', 'debugpress' ) . '</strong>';
		}
	}

	public static function php_opcache() : string {
		if ( extension_loaded( 'Zend OPcache' ) ) {
			if ( function_exists( 'opcache_get_configuration' ) ) {
				$config = opcache_get_configuration();

				return $config['version']['version'] ?? '';
			} else {
				return '<strong>' . __( 'loaded', 'debugpress' ) . '</strong>';
			}
		} else {
			return '<strong style="color: #cc0000;">' . __( 'not loaded', 'debugpress' ) . '</strong>';
		}
	}

	public static function php_apc() : string {
		if ( extension_loaded( 'apc' ) ) {
			return phpversion( 'apc' );
		} else {
			return '<strong style="color: #cc0000;">' . __( 'not loaded', 'debugpress' ) . '</strong>';
		}
	}

	public static function php_pear() : string {
		$file = 'System.php';

		if ( debugpress_file_exists( $file ) ) {
			try {
				@include_once $file; // phpcs:ignore WordPress.PHP.NoSilencedErrors

				if ( class_exists( '\System' ) === true ) {
					return __( 'loaded', 'debugpress' );
				}
			} catch ( Exception $exception ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement
				// if file can't be loaded, nothing needs to happen.
			}
		}

		return '<strong style="color: #cc0000;">' . __( 'not loaded', 'debugpress' ) . '</strong>';
	}

	public static function php_include_path() : string {
		$path = get_include_path();

		return $path === false ? '' : $path;
	}

	public static function php_loaded_extensions( bool $zend = false ) : array {
		return get_loaded_extensions( $zend );
	}

	public static function php_extension( $name ) : string {
		if ( extension_loaded( $name ) ) {
			return __( 'OK', 'debugpress' );
		} else {
			return '<strong style="color: #cc0000;">' . __( 'not available', 'debugpress' ) . '</strong>';
		}
	}

	public static function php_function( $name ) : string {
		if ( function_exists( $name ) ) {
			return __( 'OK', 'debugpress' );
		} else {
			return '<strong style="color: #cc0000;">' . __( 'not available', 'debugpress' ) . '</strong>';
		}
	}
}
