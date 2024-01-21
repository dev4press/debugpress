<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin autoloader class for resolving plugin classes and loading them.
 *
 * @param string $class name of the class to load
 */
function d4p_plugin_debugpress_autoload( $class ) {
	$path = __DIR__ . '/';
	$base = 'Dev4Press\\Plugin\\DebugPress\\';

	if ( substr( $class, 0, strlen( $base ) ) == $base ) {
		$clean = substr( $class, strlen( $base ) );

		$parts = explode( '\\', $clean );

		$class_name = $parts[ count( $parts ) - 1 ];
		unset( $parts[ count( $parts ) - 1 ] );

		$class_namespace = join( '/', $parts );
		$class_namespace = strtolower( $class_namespace );

		$path .= $class_namespace . '/' . $class_name . '.php';

		if ( file_exists( $path ) ) {
			include $path;
		}
	}
}

spl_autoload_register( 'd4p_plugin_debugpress_autoload' );
