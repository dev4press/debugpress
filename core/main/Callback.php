<?php

namespace Dev4Press\Plugin\DebugPress\Main;

use Closure;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Callback {
	private $replace;
	private $paths = array();
	private $resolve = array();
	private $origins = array();

	public function __construct() {
		$this->replace = array(
			wp_normalize_path( ABSPATH ),
			wp_normalize_path( dirname( WP_CONTENT_DIR ) . '/' ),
		);

		foreach (
			array(
				'plugin'     => WP_PLUGIN_DIR,
				'mu-plugin'  => WPMU_PLUGIN_DIR,
				'stylesheet' => get_stylesheet_directory(),
				'template'   => get_template_directory(),
				'content'    => WP_CONTENT_DIR,
				'includes'   => ABSPATH . 'wp-includes',
				'admin'      => ABSPATH . 'wp-admin',
				'core'       => ABSPATH,
				'unknown'    => null,
			) as $key => $path
		) {
			if ( is_null( $path ) ) {
				continue;
			}

			$this->paths[ $key ] = wp_normalize_path( $path );
		}
	}

	public static function instance() : Callback {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Callback();
		}

		return $instance;
	}

	private function _path( string $path, $replace = false ) : string {
		$path = wp_normalize_path( $path );

		if ( $replace !== false ) {
			$path = str_replace( $this->replace, $replace, $path );
		}

		return $path;
	}

	private function _origin( string $file ) : string {
		$file = wp_normalize_path( $file );

		if ( isset( $this->origins[ $file ] ) ) {
			return $this->origins[ $file ];
		}

		$scope = '';

		foreach ( $this->paths as $scope => $dir ) {
			if ( $dir && ( strpos( $file, trailingslashit( $dir ) ) === 0 ) ) {
				break;
			}
		}

		$value = $scope;

		switch ( $scope ) {
			case 'plugin':
			case 'mu-plugin':
				$plugin = plugin_basename( $file );

				if ( strpos( $plugin, '/' ) ) {
					$plugin = explode( '/', $plugin );
					$plugin = reset( $plugin );
				} else {
					$plugin = basename( $plugin );
				}

				$plugin = sanitize_file_name( $plugin );
				$plugin = strtolower( $plugin );

				$value = $plugin;
				break;
			case 'stylesheet':
				$value = is_child_theme() ? 'child-theme' : 'theme';
				break;
			case 'template':
				$value = 'theme';
				break;
		}

		$this->origins[ $file ] = empty( $scope ) ? 'unknown::unknown' : $scope . '::' . $value;

		return $this->origins[ $file ];
	}

	public function process( string $code, array $callback ) : array {
		if ( isset( $this->resolve[ $code ] ) ) {
			return $this->resolve[ $code ];
		}

		if ( is_string( $callback['function'] ) && ( strpos( $callback['function'], '::' ) !== false ) ) {
			$callback['function'] = explode( '::', $callback['function'] );
		}

		if ( isset( $callback['class'] ) ) {
			$callback['function'] = array( $callback['class'], $callback['function'] );
		}

		try {
			if ( is_array( $callback['function'] ) ) {
				if ( is_object( $callback['function'][0] ) ) {
					$_the_class  = get_class( $callback['function'][0] );
					$_the_access = '->';
				} else {
					$_the_class  = $callback['function'][0];
					$_the_access = '::';
				}

				$callback['name'] = $_the_class . $_the_access . $callback['function'][1] . '()';
				$ref              = new ReflectionMethod( $_the_class, $callback['function'][1] );
			} else if ( is_object( $callback['function'] ) ) {
				if ( $callback['function'] instanceof Closure ) {
					$ref      = new ReflectionFunction( $callback['function'] );
					$filename = $ref->getFileName();

					if ( $filename ) {
						$file = $this->_path( $filename, '' );
						if ( strpos( $file, '/' ) === 0 ) {
							$file = basename( $filename );
						}

						$callback['name'] = sprintf( __( 'Closure in [%1$d] on line [%2$s]', 'debugpress' ), $file, $ref->getStartLine() );
					} else {
						$callback['name'] = __( 'The Unknown Closure', 'debugpress' );
					}
				} else {
					$class            = get_class( $callback['function'] );
					$callback['name'] = $class . '->__invoke()';
					$ref              = new ReflectionMethod( $class, '__invoke' );
				}
			} else {
				$callback['name'] = $callback['function'] . '()';
				$ref              = new ReflectionFunction( $callback['function'] );
			}

			$callback['file'] = $ref->getFileName();
			$callback['line'] = $ref->getStartLine();

			if ( ! empty( $callback['file'] ) ) {
				$callback['origin'] = $this->_origin( $callback['file'] );
			} else {
				$callback['origin'] = 'php::php';
			}
		} catch ( ReflectionException $e ) {
			$callback['error']  = new WP_Error( 'reflection-error', $e->getMessage() );
			$callback['origin'] = 'error::error';
		}

		$this->resolve[ $code ] = $callback;

		return $this->resolve[ $code ];
	}
}
