<?php

use Dev4Press\Plugin\DebugPress\Admin\Plugin as AdminPlugin;
use Dev4Press\Plugin\DebugPress\Main\AJAX;
use Dev4Press\Plugin\DebugPress\Main\Plugin;
use Dev4Press\Plugin\DebugPress\Track\Tracker;

function debugpress_plugin() {
	return Plugin::instance();
}

function debugpress_ajax() {
	return AJAX::instance();
}

function debugpress_admin() {
	return AdminPlugin::instance();
}

function debugpress_tracker() {
	return Tracker::instance();
}
