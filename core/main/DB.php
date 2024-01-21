<?php

namespace Dev4Press\Plugin\DebugPress\Main;

use wpdb;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DB {
	private $_dbs;
	private $_dbv;

	public function __construct() {
	}

	public static function instance() : DB {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new DB();
		}

		return $instance;
	}

	public function wpdb() : wpdb {
		global $wpdb;

		return $wpdb;
	}

	public function db_mysql_string() : string {
		if ( empty( $this->_dbs ) ) {
			$this->_dbs = $this->wpdb()->get_var( "SELECT VERSION()" );
		}

		return $this->_dbs;
	}

	public function db_mysql_version() {
		if ( empty( $this->_dbv ) ) {
			$this->_dbs = $this->db_mysql_string();
			$version    = ! $this->_dbs ? $this->wpdb()->db_version() : $this->_dbs;
			$this->_dbv = preg_replace( '/[^0-9.].*/', '', $version );
		}

		return $this->_dbv;
	}
}
