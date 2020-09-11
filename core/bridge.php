<?php

use Dev4Press\Plugin\DebugPress\Admin\Plugin as AdminPlugin;
use Dev4Press\Plugin\DebugPress\Basic\AJAX;
use Dev4Press\Plugin\DebugPress\Basic\Plugin;

function debugpress_plugin() {
	return Plugin::instance();
}

function debugpress_ajax() {
	return AJAX::instance();
}

function debugpress_admin() {
	return AdminPlugin::instance();
}
