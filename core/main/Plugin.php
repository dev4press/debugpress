<?php

namespace Dev4Press\Plugin\DebugPress\Main;

class Plugin {
    private $_settings = array();
    private $_defaults = array(
        'active' => false,
        'admin' => false,
        'frontend' => false,
        'ajax' => false,
        'button_admin' => 'toolbar',
        'button_frontend' => 'toolbar',
        'for_super_admin' => true,
        'for_roles' => true,
        'for_visitor' => false,

        'panel_content' => true,
        'panel_request' => true,
        'panel_enqueue' => false,
        'panel_system' => false,
        'panel_user' => false,
        'panel_constants' => false,
        'panel_http' => false,
        'panel_php' => false,
        'panel_bbpress' => false,

        'slow_query_cutoff' => 10,
        'use_sql_formatter' => true,
        'format_queries_panel' => true,
        'errors_override' => true,
        'deprecated_override' => true,
        'doingitwrong_override' => true,
        'integrate_admin_footer' => true
    );

	public function __construct() {
        add_action('plugins_loaded', array($this, 'plugins_loaded'), 0);
	}

	/** @return \Dev4Press\Plugin\DebugPress\Main\Plugin */
	public static function instance() {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Plugin();
		}

		return $instance;
	}

	public function plugins_loaded() {
        $this->_settings = get_option('debugpress_settings', $this->_defaults);
    }

    public function get($name, $fallback = false) {
	    if (isset($this->_settings[$name])) {
	        return $this->_settings[$name];
        } else if (isset($this->_defaults[$name])) {
            return $this->_defaults[$name];
        }

	    return $fallback;
    }

    public function process_settings($input) {
	    $types = array(
	        'for_roles' => 'array',
            'button_admin' => 'string',
            'button_frontend' => 'string'
        );

	    $settings = array();

	    foreach ($this->_defaults as $key => $value) {
            if (isset($types[$key])) {
                switch ($types[$key]) {
                    default:
                    case 'string':
                        $settings[$key] = sanitize_text_field($input[$key]);
                        break;
                    case 'array':
                        $settings[$key] = isset($input[$key]) ? (array)$input[$key] : array();
                        $settings[$key] = array_map('sanitize_text_field', $settings[$key]);
                }
            } else {
                $settings[$key] = isset($input[$key]) && $input[$key] == 'on';
            }
        }

	    return $settings;
    }
}
