<?php

/*
Plugin Name:       DebugPress
Plugin URI:        https://debug.press/
Description:       DebugPress is an easy to use plugin implementing popup for debugging and profiling currently loaded WordPress powered website page with support for intercepting AJAX requests.
Author:            Milan Petrovic
Author URI:        https://www.dev4press.com/
Text Domain:       debugpress
Version:           1.4.2
Requires at least: 4.9
Tested up to:      5.6
Requires PHP:      5.6
License:           GPLv3 or later
License URI:       https://www.gnu.org/licenses/gpl-3.0.html

== Copyright ==
Copyright 2008 - 2020 Milan Petrovic (email: milan@dev4press.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>
*/

define( 'DEBUGPRESS_VERSION', '1.4.1' );
define( 'DEBUGPRESS_PLUGIN_PATH', __DIR__ . '/' );
define( 'DEBUGPRESS_PLUGIN_URL', plugins_url( '/', __FILE__ ) );

define( 'DEBUGPRESS_IS_AJAX', defined( 'DOING_AJAX' ) && DOING_AJAX );
define( 'DEBUGPRESS_IS_CRON', defined( 'DOING_CRON' ) && DOING_CRON );
define( 'DEBUGPRESS_IS_CLI', defined( 'WP_CLI' ) && WP_CLI );

if ( ! defined( 'D4P_EOL' ) ) {
	define( 'D4P_EOL', "\r\n" );
}

if ( ! defined( 'D4P_TAB' ) ) {
	define( 'D4P_TAB', "\t" );
}

if ( DEBUGPRESS_IS_CLI || DEBUGPRESS_IS_CRON ) {
	return;
}

require_once( DEBUGPRESS_PLUGIN_PATH . 'core/autoload.php' );
require_once( DEBUGPRESS_PLUGIN_PATH . 'core/bridge.php' );
require_once( DEBUGPRESS_PLUGIN_PATH . 'core/functions.php' );
require_once( DEBUGPRESS_PLUGIN_PATH . 'core/pretty-print.php' );

debugpress_plugin();

if ( defined( 'WP_ADMIN' ) && WP_ADMIN ) {
	require_once( DEBUGPRESS_PLUGIN_PATH . 'core/admin.php' );

	debugpress_admin();
}