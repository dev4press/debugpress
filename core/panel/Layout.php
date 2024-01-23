<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\Panel;

class Layout extends Panel {
	public function single() {
		$this->title( esc_html__( 'Popup Position', 'debugpress' ) );

		echo '<div class="debugpress-layout-position">';

		foreach (
			array(
				'full'   => __( 'Full', 'debugpress' ),
				'top'    => __( 'Top', 'debugpress' ),
				'bottom' => __( 'Bottom', 'debugpress' ),
				'left'   => __( 'Left', 'debugpress' ),
				'right'  => __( 'Right', 'debugpress' ),
			) as $pos => $label
		) {
			echo '<div class="debugpress-layout-position-' . esc_attr( $pos ) . '"><i class="debugpress-icon debugpress-icon-layout-' . esc_attr( $pos ) . ' debugpress-icon-6x"></i>';
			echo '<span><label><input value="' . esc_attr( $pos ) . '" type="radio" name="debugpress-layout-position" /> <span>' . esc_html( $label ) . '</span></label></span>';
			echo '</div>';
		}

		echo '</div>';

		$this->title( esc_html__( 'Popup Settings', 'debugpress' ) );

		echo '<div class="debugpress-layout-settings">';
		echo '<div class="debugpress-layout-option debugpress-layout-size">';
		echo '<label>' . esc_html__( 'Popup Size', 'debugpress' ) . '</label>';
		echo '<select>';
		foreach (
			array(
				'30' => "30%",
				'40' => "40%",
				'50' => "50%",
				'60' => "60%",
				'70' => "70%",
			) as $size => $label
		) {
			echo '<option value="' . esc_attr( $size ) . '">' . esc_attr( $label ) . '</option>';
		}
		echo '</select>';
		echo '</div>';

		echo '<div class="debugpress-layout-option debugpress-layout-modal">';
		echo '<label>' . esc_html__( 'Popup Modal', 'debugpress' ) . '</label>';
		echo '<select>';
		foreach (
			array(
				'show' => __( 'Yes', 'debugpress' ),
				'hide' => __( 'No', 'debugpress' ),
			) as $size => $label
		) {
			echo '<option value="' . esc_attr( $size ) . '">' . esc_html( $label ) . '</option>';
		}
		echo '</select>';
		echo '</div>';

		echo '<div class="debugpress-layout-option debugpress-layout-activation">';
		echo '<label>' . esc_html__( 'Popup Activation', 'debugpress' ) . '</label>';
		echo '<select>';
		foreach (
			array(
				'manual'   => __( 'Normal', 'debugpress' ),
				'auto'     => __( 'Auto show on page load', 'debugpress' ),
				'remember' => __( 'Remember load state', 'debugpress' ),
			) as $size => $label
		) {
			echo '<option value="' . esc_attr( $size ) . '">' . esc_html( $label ) . '</option>';
		}
		echo '</select>';
		echo '</div>';
		echo '</div>';
	}
}
