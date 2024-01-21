<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\Panel;

class Plugins extends Panel {
	public $sides = array(
		'left'  => array(),
		'right' => array(),
	);

	public function __construct() {
		$side = 'left';

		foreach ( debugpress_tracker()->plugins as $plugin => $list ) {
			foreach ( array_keys( $list ) as $key ) {
				$this->sides[ $side ][] = array( $plugin, $key );

				$side = $side == 'left' ? 'right' : 'left';
			}
		}
	}

	private function _side( $side ) {
		foreach ( $this->sides[ $side ] as $item ) {
			$plugin = debugpress_tracker()->plugins[ $item[0] ][ $item[1] ];

			$this->title( $plugin['plugin']['Name'] );

			foreach ( $plugin['data'] as $name => $values ) {
				if ( ! empty( $values ) ) {
					$this->list_array( $values, $name );
				}
			}
		}
	}

	public function left() {
		$this->_side( 'left' );
	}

	public function right() {
		$this->_side( 'right' );
	}
}
