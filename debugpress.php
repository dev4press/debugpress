<?php
/**
 * Plugin Name:       DebugPress: Debugger in a Popup
 * Plugin URI:        https://debug.press/
 * Description:       DebugPress is an easy-to-use plugin that implements popups for debugging and profiling website pages with support for intercepting AJAX requests.
 * Author:            Milan Petrovic
 * Author URI:        https://www.dev4press.com/
 * Text Domain:       debugpress
 * Version:           3.9.2
 * Requires at least: 5.5
 * Tested up to:      6.6
 * Requires PHP:      7.4
 * Requires CP:       2.0
 * License:           GPLv3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package DebugPress
 *
 * == Copyright ==
 * Copyright 2008 - 2024 Milan Petrovic (email: support@dev4press.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 */

const DEBUGPRESS_VERSION     = '3.9.2';
const DEBUGPRESS_FILE        = __FILE__;
const DEBUGPRESS_PLUGIN_PATH = __DIR__ . '/';

define( 'DEBUGPRESS_PLUGIN_URL', plugins_url( '/', DEBUGPRESS_FILE ) );

define( 'DEBUGPRESS_IS_AJAX', defined( 'DOING_AJAX' ) && DOING_AJAX );
define( 'DEBUGPRESS_IS_CRON', defined( 'DOING_CRON' ) && DOING_CRON );
define( 'DEBUGPRESS_IS_CLI', defined( 'WP_CLI' ) && WP_CLI );

if ( ! defined( 'D4P_TAB' ) ) {
	define( 'D4P_TAB', "\t" );
}

if ( ! defined( 'DEBUGPRESS_ACTIVATION_PRIORITY' ) ) {
	define( 'DEBUGPRESS_ACTIVATION_PRIORITY', 99 );
}

if ( DEBUGPRESS_IS_CLI || DEBUGPRESS_IS_CRON ) {
	return;
}

require_once DEBUGPRESS_PLUGIN_PATH . 'core/autoload.php';
require_once DEBUGPRESS_PLUGIN_PATH . 'core/bridge.php';
require_once DEBUGPRESS_PLUGIN_PATH . 'core/functions.php';

debugpress_plugin();

if ( defined( 'WP_ADMIN' ) && WP_ADMIN ) {
	require_once DEBUGPRESS_PLUGIN_PATH . 'core/admin.php';

	debugpress_admin();
}
