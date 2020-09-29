<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Replacement function for rendering plugin settings sections. Based on the WordPress core do_settings_sections(), but
 * expanded to add additional layout elements.
 *
 * @param string $page the slug name of the page whose settings sections you want to output
 *
 * @see \do_settings_sections()
 *
 */
function debugpress_do_settings_sections( $page ) {
	global $wp_settings_sections, $wp_settings_fields;

	if ( ! isset( $wp_settings_sections[ $page ] ) ) {
		return;
	}

	foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
		echo '<div class="debugpress-settings-section">';
		if ( $section['title'] ) {
			echo "<h2>{$section['title']}</h2>\n";
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
}
