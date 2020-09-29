<?php

namespace Dev4Press\Plugin\DebugPress\Main;

use SplFileObject;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Files {
	public function __construct() {

	}

	/** @return \Dev4Press\Plugin\DebugPress\Main\Files */
	public static function instance() {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Files();
		}

		return $instance;
	}

	public function count_lines_in_files( $file_path ) {
		if ( ! file_exists( $file_path ) ) {
			return 0;
		}

		$file = new SplFileObject( $file_path, 'r' );
		$file->seek( PHP_INT_MAX );

		return $file->key() + 1;
	}

	public function read_lines_from_file( $file_path, $last = 1000 ) {
		$file = new SplFileObject( $file_path, 'r' );
		$file->seek( PHP_INT_MAX );
		$last_line = $file->key();

		$lines = new LimitIterator( $file, $last_line - $last, $last_line );

		return iterator_to_array( $lines );
	}
}