<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Replacement function for rendering plugin settings sections. Based on the WordPress core do_settings_sections(), but
 * expanded to add additional layout elements.
 *
 * @param string $page the slug name of the page whose settings section you want to output
 *
 * @see \do_settings_sections()
 *
 */
function debugpress_do_settings_sections( $page ) {
	$tabs = array(
		'debugpress_settings_activation' => 'activation',
		'debugpress_settings_special'    => 'panels',
		'debugpress_settings_ajax'       => 'tracking',
		'debugpress_settings_pretty'     => 'advanced',
	);

	global $wp_settings_sections, $wp_settings_fields;

	if ( ! isset( $wp_settings_sections[ $page ] ) ) {
		return;
	}

	$first = true;
	$close = false;

	foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
		$id = $section['id'];

		if ( isset( $tabs[ $id ] ) ) {
			$tab = $tabs[ $id ];

			if ( $close ) {
				echo '</div>';
			}

			$classes = 'tab-content nav-tab-content-' . $tab . ( $first ? ' tab-content-active' : '' );
			echo '<div class="' . esc_attr( $classes ) . '">';

			$close = true;
			$first = false;
		}

		echo '<div class="debugpress-settings-section section-' . esc_attr( $id ) . '">';
		if ( $section['title'] ) {
			echo "<h2>" . esc_html( $section['title'] ) . "</h2>\n";
		}

		if ( $section['callback'] ) {
			echo '<div class="debugpress-section-info">';
			call_user_func( $section['callback'], $section );
			echo '</div>';
		}

		if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
			continue;
		}
		echo '<table class="form-table" role="presentation">';
		do_settings_fields( $page, $section['id'] );
		echo '</table>';
		echo '</div>';
	}

	echo '</div>';
}
