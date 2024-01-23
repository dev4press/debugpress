<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\DB;
use Dev4Press\Plugin\DebugPress\Main\Info;
use Dev4Press\Plugin\DebugPress\Main\Panel;

class Server extends Panel {
	public function left() {
		$this->title( esc_html__( 'Server Information', 'debugpress' ), true, false, 'server-info' );
		$this->block_header();
		$this->table_init_standard();
		$this->table_head();
		$this->table_row( array( __( 'Server', 'debugpress' ), Info::server_name() ) );
		$this->table_row( array( __( 'Operating System', 'debugpress' ), Info::server_os() ) );
		$this->table_row( array( __( 'Hostname', 'debugpress' ), Info::server_hostname() ) );
		$this->table_row( array( __( 'IP', 'debugpress' ), Info::server_ip() ) );
		$this->table_row( array( __( 'Port', 'debugpress' ), Info::server_port() ) );
		$this->table_foot();
		$this->block_footer();

		if ( Info::is_apache() ) {
			$this->title( esc_html__( 'Apache Status', 'debugpress' ), true, false, 'server-apache' );
			$this->block_header();
			$this->table_init_standard();
			$this->table_head();
			$this->table_row( array( __( 'Version', 'debugpress' ), Info::apache_version() ) );
			$this->table_row( array( __( 'Module: Rewrite', 'debugpress' ), Info::apache_mod_rewrite() ) );
			$this->table_row( array( __( 'Module: SSL', 'debugpress' ), Info::apache_mod_ssl() ) );
			$this->table_row( array(
				__( 'All Loaded Modules', 'debugpress' ),
				debugpress_rx( Info::apache_modules_list(), false ),
			) );
			$this->table_foot();
			$this->block_footer();
		}

		$this->title( esc_html__( 'mySQL Status', 'debugpress' ), true, false, 'server-mysql' );
		$this->block_header();
		$this->table_init_standard();
		$this->table_head();
		$this->table_row( array( __( 'Version', 'debugpress' ), Info::mysql_version() ) );
		$this->table_row( array( __( 'Charset', 'debugpress' ), DB::instance()->wpdb()->charset ) );
		$this->table_row( array( __( 'Collation', 'debugpress' ), DB::instance()->wpdb()->collate ) );
		$this->table_foot();
		$this->block_footer();

		$this->title( esc_html__( 'mySQL Connection', 'debugpress' ), true, false, 'server-connection' );
		$this->block_header();
		$this->table_init_standard();
		$this->table_head();
		$this->table_row( array( __( 'Host', 'debugpress' ), DB_HOST ) );
		$this->table_row( array( __( 'Database', 'debugpress' ), DB_NAME ) );
		$this->table_row( array( __( 'User', 'debugpress' ), DB_USER ) );
		$this->table_row( array( __( 'Charset', 'debugpress' ), defined( 'DB_CHARSET' ) ? DB_CHARSET : '' ) );
		$this->table_row( array( __( 'Collation', 'debugpress' ), defined( 'DB_COLLATE' ) ? DB_COLLATE : '' ) );
		$this->table_foot();
		$this->block_footer();

		$this->title( esc_html__( 'WordPress Database', 'debugpress' ), true, false, 'wp-database' );
		$this->block_header();
		$this->table_init_standard();
		$this->table_head();
		$this->table_row( array( __( 'Tables', 'debugpress' ), Info::mysql_wordpress_tables() ) );
		$this->table_row( array( __( 'Records', 'debugpress' ), Info::mysql_wordpress_records() ) );
		$this->table_row( array( __( 'Size', 'debugpress' ), Info::mysql_wordpress_size() ) );
		$this->table_row( array( __( 'Free Space', 'debugpress' ), Info::mysql_wordpress_free_space() ) );
		$this->table_foot();
		$this->block_footer();
	}

	public function right() {
		$this->title( esc_html__( 'PHP Status', 'debugpress' ), true, false, 'php-status' );
		$this->block_header();
		$this->table_init_standard();
		$this->table_head();
		$this->table_row( array( __( 'Version', 'debugpress' ), Info::php_version() ) );
		$this->table_row( array( __( 'API Interface', 'debugpress' ), Info::php_sapi() ) );
		$this->table_row( array( __( 'Available Memory', 'debugpress' ), Info::php_memory_limit() ) );
		if ( function_exists( 'memory_get_usage' ) ) {
			$this->table_row( array( __( 'Used Memory', 'debugpress' ), Info::php_memory_used() ) );
		}
		$this->table_row( array( __( 'Max POST Size', 'debugpress' ), Info::php_post_size() ) );
		$this->table_row( array( __( 'Max Upload Size', 'debugpress' ), Info::php_upload_size() ) );
		$this->table_row( array( __( 'Max Execution Time', 'debugpress' ), Info::php_execution_time() ) );
		$this->table_row( array( __( 'Allow URL `fopen`', 'debugpress' ), Info::php_allow_url_fopen() ) );
		$this->table_row( array( __( 'Include path', 'debugpress' ), Info::php_include_path() ) );
		$this->table_foot();
		$this->block_footer();

		$this->title( esc_html__( 'PHP Errors Display and Logging', 'debugpress' ), true, false, 'php-errors' );
		$this->block_header();
		$this->table_init_standard();
		$this->table_head();
		$this->table_row( array( __( 'Display', 'debugpress' ), Info::php_error_display() ) );
		$this->table_row( array( __( 'Logging', 'debugpress' ), Info::php_error_logging() ) );
		$this->table_row( array( __( 'Log file', 'debugpress' ), Info::php_error_filepath() ) );
		$this->table_row( array(
			__( 'Error flags', 'debugpress' ),
			debugpress_rx( Info::php_error_levels(), false ),
		) );
		$this->table_foot();
		$this->block_footer();

		$this->title( esc_html__( 'PHP Important Extensions', 'debugpress' ), true, false, 'php-ext' );
		$this->block_header();
		$this->table_init_standard();
		$this->table_head();
		$this->table_row( array( 'PDO', Info::php_pdo() ) );
		$this->table_row( array( 'zLib', Info::php_zlib() ) );
		$this->table_row( array( 'cURL', Info::php_curl() ) );
		$this->table_row( array( 'GD', Info::php_gd() ) );
		$this->table_row( array( 'PEAR', Info::php_pear() ) );
		$this->table_foot();
		$this->block_footer();

		$this->title( esc_html__( 'PHP Cache Extensions', 'debugpress' ), true, false, 'php-cache' );
		$this->block_header();
		$this->table_init_standard();
		$this->table_head();
		$this->table_row( array( 'Zend OPCache', Info::php_opcache() ) );
		$this->table_row( array( 'APC', Info::php_apc() ) );
		$this->table_row( array( 'Memcache', Info::php_extension( 'memcache' ) ) );
		$this->table_row( array( 'Memcached', Info::php_extension( 'memcached' ) ) );
		$this->table_foot();
		$this->block_footer();

		$this->title( esc_html__( 'PHP Loaded Extensions', 'debugpress' ), true, false, 'php-loaded' );
		$this->block_header();
		$this->table_init_standard();
		$this->table_head();
		$this->table_row( array(
			__( 'Standard', 'debugpress' ),
			debugpress_rx( Info::php_loaded_extensions(), false ),
		) );
		$this->table_row( array(
			__( 'ZEND', 'debugpress' ),
			debugpress_rx( Info::php_loaded_extensions( true ), false ),
		) );
		$this->table_foot();
		$this->block_footer();
	}
}
