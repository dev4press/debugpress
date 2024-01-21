<?php

namespace Dev4Press\Plugin\DebugPress\Main;

use LimitIterator;
use SplFileObject;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Files {
	public function __construct() {
	}

	public static function instance() : Files {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Files();
		}

		return $instance;
	}

	public function count_lines_in_files( $file_path ) : int {
		if ( ! file_exists( $file_path ) ) {
			return 0;
		}

		$file = new SplFileObject( $file_path, 'r' );
		$file->seek( PHP_INT_MAX );

		return $file->key() + 1;
	}

	public function read_lines_from_file( $file_path, $last = 1000 ) : array {
		$file = new SplFileObject( $file_path, 'r' );
		$file->seek( PHP_INT_MAX );
		$last_line = $file->key();

		$first_line = $last_line < $last ? 0 : $last_line - $last;

		$lines = new LimitIterator( $file, $first_line, $last_line );

		return iterator_to_array( $lines );
	}
}
