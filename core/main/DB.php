<?php

namespace Dev4Press\Plugin\DebugPress\Main;

class DB {
	private $_dbs;
	private $_dbv;

	public function __construct() {

	}

	/** @return \Dev4Press\Plugin\DebugPress\Main\DB */
	public static function instance() {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new DB();
		}

		return $instance;
	}

	public function wpdb() {
		global $wpdb;

		return $wpdb;
	}

	public function db_mysql_string() {
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