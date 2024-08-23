<?php

namespace Dev4Press\Plugin\DebugPress\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OPCache {
	public $status = 'disabled';
	public $version;
	public $enabled;
	public $settings = array();
	public $memory = array();
	public $statistics = array();

	public function __construct() {
	}

	public static function instance() : OPCache {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new OPCache();
			$instance->init();
		}

		return $instance;
	}

	public function init() {
		if ( $this->has_opcache() ) {
			$this->status = 'enabled';

			if ( function_exists( 'opcache_get_status' ) && function_exists( 'opcache_get_configuration' ) ) {
				$this->status = 'restricted';

				$_status = opcache_get_status();
				$_config = opcache_get_configuration();

				$this->version = $_config['version']['version'] ?? 'NA';
				$this->enabled = $_status['opcache_enabled'] ?? false;

				foreach ( $_config['directives'] as $key => $value ) {
					$name = substr( $key, 8 );

					$this->settings[ $name ] = $value;
				}

				$this->memory     = $_status['memory_usage'] ?? 0;
				$this->statistics = $_status['opcache_statistics'] ?? 0;
			}
		}
	}

	public function has_opcache() : bool {
		return extension_loaded( 'Zend OPcache' );
	}
}
