<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\Panel;

class Layout extends Panel {
	public function single() {
		$this->title( __( 'Popup Position', "debugpress" ) );

		echo '<div class="debugpress-layout-position">';

		foreach (
			array(
				'full'   => __( "Full" ),
				'top'    => __( "Top" ),
				'bottom' => __( "Bottom" ),
				'left'   => __( "Left" ),
				'right'  => __( "Right" )
			) as $pos => $label
		) {
			echo '<div class="debugpress-layout-position-' . $pos . '"><i class="debugpress-icon debugpress-icon-layout-' . $pos . ' debugpress-icon-6x"></i>';
			echo '<span><label><input value="' . $pos . '" type="radio" name="debugpress-layout-position" /> <span>' . $label . '</span></label></span>';
			echo '</div>';
		}

		echo '</div>';

		$this->title( __( 'Popup Size', "debugpress" ) );

		echo '<div class="debugpress-layout-position">';
		echo '</div>';

		$this->title( __( 'Popup Settings', "debugpress" ) );

		echo '<div class="debugpress-layout-position">';
		echo '</div>';
	}
}