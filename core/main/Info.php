<?php

namespace Dev4Press\Plugin\DebugPress\Main;

use PDO;

class Info {
	public static function _loaded_status( $value ) {
		if ( $value === true ) {
			return __( "loaded", "debugpress" );
		} else {
			return '<strong style="color: #cc0000;">' . __( "not loaded", "debugpress" ) . '</strong>';
		}
	}

	public static function cms_name() {
		return debugpress_is_classicpress() ? 'ClassicPress' : 'WordPress';
	}

	public static function cms_version() {
		return debugpress_plugin()->wp_version_real;
	}

	public static function cms_count_plugins() {
		$plugins = wp_cache_get( 'gd-press-tools', 'wordpress-plugins' );

		if ( $plugins === false ) {
			$all_plugins = get_plugins();

			$plugins = array( 'total' => 0, 'active' => 0, 'inactive' => 0 );

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

		return $plugins;
	}

	public static function cms_count_plugins_total() {
		$data = Info::cms_count_plugins();

		return $data['total'];
	}

	public static function cms_count_plugins_active() {
		$data = Info::cms_count_plugins();

		return $data['active'];
	}

	public static function cms_count_plugins_inactive() {
		$data = Info::cms_count_plugins();

		return $data['inactive'];
	}

	public static function cms_count_themes() {
		$themes = wp_cache_get( 'gd-press-tools', 'wordpress-themes' );

		if ( $themes === false ) {
			$all_themes = wp_get_themes();

			$themes = count( $all_themes );

			wp_cache_add( 'gd-press-tools', $themes, 'wordpress-themes' );
		}

		return $themes;
	}

	public static function apache_mod_rewrite() {
		$status = apache_mod_loaded( 'mod_rewrite' );

		return Info::_loaded_status( $status );
	}

	public static function apache_mod_headers() {
		$status = apache_mod_loaded( 'mod_headers' );

		return Info::_loaded_status( $status );
	}

	public static function apache_mod_security() {
		$status = apache_mod_loaded( 'mod_security' );

		return Info::_loaded_status( $status );
	}

	public static function apache_mod_ssl() {
		$status = apache_mod_loaded( 'mod_ssl' );

		return Info::_loaded_status( $status );
	}

	public static function apache_mod_setenvif() {
		$status = apache_mod_loaded( 'mod_setenvif' );

		return Info::_loaded_status( $status );
	}

	public static function apache_mod_alias() {
		$status = apache_mod_loaded( 'mod_alias' );

		return Info::_loaded_status( $status );
	}

	public static function server_name() {
		return $_SERVER['SERVER_SOFTWARE'];
	}

	public static function server_os() {
		return PHP_OS;
	}

	public static function server_hostname() {
		return $_SERVER['SERVER_NAME'];
	}

	public static function server_ip() {
		return $_SERVER['SERVER_ADDR'];
	}

	public static function server_port() {
		return $_SERVER['SERVER_PORT'];
	}

	public static function mysql_variant() {
		$name    = 'MySQL';
		$version = debugpress_db()->db_mysql_string();

		if ( strpos( $version, 'MariaDB' ) !== false ) {
			$name = 'MariaDB';
		} else if ( strpos( $version, 'Percona' ) !== false ) {
			$name = 'PerconaDB';
		}

		return $name;
	}

	public static function mysql_version() {
		return debugpress_db()->db_mysql_version();
	}

	public static function mysql_database() {
		$data = wp_cache_get( 'gd-press-tools', 'database' );

		if ( $data === false ) {
			$raw = debugpress_db()->wpdb()->get_row( "SELECT table_schema, COUNT(*) as tables_count, SUM(data_length + index_length) AS data_size, SUM(data_free) AS free_space, SUM(table_rows) AS rows_count FROM information_schema.TABLES WHERE table_schema = '" . DB_NAME . "' GROUP BY table_schema" );

			$data = array(
				'tables'  => $raw->tables_count,
				'records' => $raw->rows_count,
				'size'    => debugpress_format_size( $raw->data_size ),
				'free'    => debugpress_format_size( $raw->free_space )
			);

			wp_cache_add( 'gd-press-tools', $data, 'database' );
		}

		return $data;
	}

	public static function mysql_database_size() {
		$data = Info::mysql_database();

		return $data['size'];
	}

	public static function mysql_database_free_space() {
		$data = Info::mysql_database();

		return $data['free'];
	}

	public static function mysql_database_tables() {
		$data = Info::mysql_database();

		return $data['tables'];
	}

	public static function mysql_database_recrods() {
		$data = Info::mysql_database();

		return $data['records'];
	}

	public static function mysql_wordpress() {
		$data = wp_cache_get( 'gd-press-tools', 'database' );

		if ( $data === false ) {
			$raw = debugpress_db()->get_row( "SELECT table_schema, COUNT(*) as tables_count, SUM(data_length + index_length) AS data_size, SUM(data_free) AS free_space, SUM(table_rows) AS rows_count FROM information_schema.TABLES WHERE table_schema = '" . DB_NAME . "' AND table_name like '" . debugpress_db()->base_prefix() . "%' GROUP BY table_schema" );

			$data = array(
				'tables'  => $raw->tables_count,
				'records' => $raw->rows_count,
				'size'    => debugpress_format_size( $raw->data_size ),
				'free'    => debugpress_format_size( $raw->free_space )
			);

			wp_cache_add( 'gd-press-tools', $data, 'database' );
		}

		return $data;
	}

	public static function mysql_wordpress_size() {
		$data = Info::mysql_wordpress();

		return $data['size'];
	}

	public static function mysql_wordpress_free_space() {
		$data = Info::mysql_wordpress();

		return $data['free'];
	}

	public static function mysql_wordpress_tables() {
		$data = Info::mysql_wordpress();

		return $data['tables'];
	}

	public static function mysql_wordpress_recrods() {
		$data = Info::mysql_wordpress();

		return $data['records'];
	}

	public static function php_error_display() {
		return ini_get( 'display_errors' ) ? __( "On", "debugpress" ) : __( "Off", "debugpress" );
	}

	public static function php_error_logging() {
		return ini_get( 'log_errors' ) ? __( "On", "debugpress" ) : __( "Off", "debugpress" );
	}

	public static function php_error_filepath() {
		return ini_get( 'error_log' );
	}

	public static function php_version() {
		return phpversion();
	}

	public static function php_sapi() {
		$sapi = PHP_SAPI;

		return $sapi;
	}

	public static function php_memory_limit() {
		return ini_get( 'memory_limit' );
	}

	public static function php_memory_used() {
		if ( function_exists( 'memory_get_usage' ) ) {
			return debugpress_format_size( memory_get_usage() );
		} else {
			return false;
		}
	}

	public static function php_error_levels_disaply() {
		$levels = Info::php_error_levels();

		$list = array();

		foreach ( $levels as $level => $status ) {
			$color = $status ? '#00cc00' : '#cc0000';
			$tag   = $status ? 'strong' : 'span';
			$val   = $status ? __( "ON", "debugpress" ) : __( "OFF", "debugpress" );

			$list[] = '<' . $tag . ' style="color: ' . $color . '">' . $level . ' / ' . $val . '</' . $tag . '>';
		}

		return $list;
	}

	public static function php_error_levels() {
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
			'E_USER_DEPRECATED'   => false
		);

		$error_reporting = error_reporting();

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

	public static function php_allow_url_fopen() {
		return ini_get( 'allow_url_fopen' ) ? __( "On", "debugpress" ) : __( "Off", "debugpress" );
	}

	public static function php_zlib() {
		if ( extension_loaded( 'zlib' ) ) {
			if ( function_exists( 'curl_version' ) ) {
				$gdi = curl_version();

				return $gdi['libz_version'];
			} else {
				return __( "loaded", "debugpress" );
			}
		} else {
			return '<strong style="color: #cc0000;">' . __( "not loaded", "debugpress" ) . '</strong>';
		}
	}

	public static function php_curl() {
		if ( extension_loaded( 'curl' ) ) {
			$gdi = curl_version();

			return $gdi['version'];
		} else {
			return '<strong style="color: #cc0000;">' . __( "not loaded", "debugpress" ) . '</strong>';
		}
	}

	public static function php_pdo() {
		if ( extension_loaded( 'pdo' ) ) {
			return join( '<br/>', PDO::getAvailableDrivers() );
		} else {
			return '<strong style="color: #cc0000;">' . __( "not loaded", "debugpress" ) . '</strong>';
		}
	}

	public static function php_gd() {
		if ( extension_loaded( 'gd' ) ) {
			$gdi = gd_info();

			return $gdi['GD Version'];
		} else {
			return '<strong style="color: #cc0000;">' . __( "not loaded", "debugpress" ) . '</strong>';
		}
	}

	public static function php_opcache() {
		if ( extension_loaded( 'Zend OPcache' ) ) {
			$config = opcache_get_configuration();

			return $config['version']['version'];
		} else {
			return '<strong style="color: #cc0000;">' . __( "not loaded", "debugpress" ) . '</strong>';
		}
	}

	public static function php_apc() {
		if ( extension_loaded( 'apc' ) ) {
			return phpversion( 'apc' );
		} else {
			return '<strong style="color: #cc0000;">' . __( "not loaded", "debugpress" ) . '</strong>';
		}
	}

	public static function php_pear() {
		@include_once( 'System.php' );

		if ( class_exists( 'System' ) === true ) {
			return __( "loaded", "debugpress" );
		} else {
			return '<strong style="color: #cc0000;">' . __( "not loaded", "debugpress" ) . '</strong>';
		}
	}

	public static function php_function( $name ) {
		if ( function_exists( $name ) ) {
			return __( "OK", "debugpress" );
		} else {
			return '<strong style="color: #cc0000;">' . __( "not available", "debugpress" ) . '</strong>';
		}
	}
}