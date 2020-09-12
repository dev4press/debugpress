<?php

use Dev4Press\Plugin\DebugPress\Admin\Plugin as AdminPlugin;
use Dev4Press\Plugin\DebugPress\Main\AJAX;
use Dev4Press\Plugin\DebugPress\Main\Plugin;
use Dev4Press\Plugin\DebugPress\Main\Scope;
use Dev4Press\Plugin\DebugPress\Track\Tracker;

/** @return \Dev4Press\Plugin\DebugPress\Main\Plugin */
function debugpress_plugin() {
	return Plugin::instance();
}

/** @return \Dev4Press\Plugin\DebugPress\Main\Scope */
function debugpress_scope() {
	return Scope::instance();
}

/** @return \Dev4Press\Plugin\DebugPress\Main\AJAX */
function debugpress_ajax() {
	return AJAX::instance();
}

/** @return \Dev4Press\Plugin\DebugPress\Admin\Plugin */
function debugpress_admin() {
	return AdminPlugin::instance();
}

/** @return \Dev4Press\Plugin\DebugPress\Track\Tracker */
function debugpress_tracker() {
	return Tracker::instance();
}

/** @return \wpdb */
function debugpress_wpdb() {
	global $wpdb;

	return $wpdb;
}