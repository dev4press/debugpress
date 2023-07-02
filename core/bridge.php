<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Admin\Plugin as AdminPlugin;
use Dev4Press\Plugin\DebugPress\Main\DB;
use Dev4Press\Plugin\DebugPress\Main\Plugin;
use Dev4Press\Plugin\DebugPress\Main\Scope;
use Dev4Press\Plugin\DebugPress\Track\Tracker;

function debugpress_plugin() : Plugin {
	return Plugin::instance();
}

function debugpress_scope() : Scope {
	return Scope::instance();
}

function debugpress_admin() : AdminPlugin {
	return AdminPlugin::instance();
}

function debugpress_tracker() : Tracker {
	return Tracker::instance();
}

function debugpress_db() : DB {
	return DB::instance();
}
