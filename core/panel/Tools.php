<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\OPCache;
use Dev4Press\Plugin\DebugPress\Main\Panel;
use Dev4Press\Plugin\DebugPress\Main\WP;

class Tools extends Panel {
	public function left() {
		if ( current_user_can( 'manage_options' ) ) {
			$this->title( __( "Information Panels", "debugpress" ), true, true );

			$this->block_header();
			$this->sub_title( __( "PHP Info", "debugpress" ) );
			echo '<p>' . __( "Load the full content of the PHP.ini file through PHPInfo function call.", "debugpress" ) . '</p>';
			echo '<a target="_blank" href="' . admin_url( 'tools.php?page=debugpress-info&tab=php' ) . '" class="debugpress-button-action">' . __( "Open PHP Info Panel", "debugpress" ) . '</a>';
			$this->block_footer();

			if ( OPCache::instance()->has_opcache() ) {
				$this->block_header();
				$this->sub_title( __( "OPCache Statistics", "debugpress" ) );
				echo '<p>' . __( "Show the settings for the PHP OPCache and basic statistics.", "debugpress" ) . '</p>';
				echo '<a target="_blank" href="' . admin_url( 'tools.php?page=debugpress-info&tab=opcache' ) . '" class="debugpress-button-action">' . __( "Open OPCache Statistics Panel", "debugpress" ) . '</a>';
				$this->block_footer();
			}

			$this->block_header();
			$this->sub_title( __( "MySQL Variables", "debugpress" ) );
			echo '<p>' . __( "Show all the MySQL configuration variables retrieved from the database server.", "debugpress" ) . '</p>';
			echo '<a target="_blank" href="' . admin_url( 'tools.php?page=debugpress-info&tab=mysql' ) . '" class="debugpress-button-action">' . __( "Open MySQL Variables Panel", "debugpress" ) . '</a>';
			$this->block_footer();

			$this->title( __( "DebugPress Settings", "debugpress" ), true, true );

			$this->block_header();
			$this->sub_title( __( "Plugin Settings", "debugpress" ) );
			echo '<p>' . __( "Open the DebugPress settings panel.", "debugpress" ) . '</p>';
			echo '<a target="_blank" href="' . admin_url( 'options-general.php?page=debugpress' ) . '" class="debugpress-button-action">' . __( "Open Settings Panel", "debugpress" ) . '</a>';
			$this->block_footer();
		} else {
			echo '<div class="debugpress-debug-notice-block">';
			$this->title( '<i class="debugpress-icon debugpress-icon-triangle-exclamation"></i> ' . __( "Not Available", "debugpress" ), true, true );
			$this->block_header();
			echo '<p>' . __( "Only administrator roles can access this information.", "debugpress" ) . '</p>';
			$this->block_footer();
			echo '</div>';
		}
	}

	public function middle() {
		$this->title( __( "WordPress Important Tools", "debugpress" ), true, true );

		if ( current_user_can( 'manage_options' ) ) {
			if ( ! debugpress_is_classicpress() && debugpress_plugin()->wp_version() > 51 ) {
				$this->block_header();
				$this->sub_title( __( "Site Health", "debugpress" ) );
				echo '<p>' . __( "Open the WordPress Site Health panel.", "debugpress" ) . '</p>';
				echo '<a target="_blank" href="' . admin_url( 'site-health.php' ) . '" class="debugpress-button-action">' . __( "Open Site Health Panel", "debugpress" ) . '</a>';
				$this->block_footer();
			}

			$this->block_header();
			$this->sub_title( __( "WordPress Updates", "debugpress" ) );
			echo '<p>' . __( "Open the WordPress Updates panel.", "debugpress" ) . '</p>';
			echo '<a target="_blank" href="' . admin_url( 'update-core.php' ) . '" class="debugpress-button-action">' . __( "Open Updates Panel", "debugpress" ) . '</a>';
			$this->block_footer();
		} else {
			echo '<div class="debugpress-debug-notice-block">';
			$this->title( '<i class="debugpress-icon debugpress-icon-triangle-exclamation"></i> ' . __( "Not Available", "debugpress" ), true, true );
			$this->block_header();
			echo '<p>' . __( "Only administrator roles can access this information.", "debugpress" ) . '</p>';
			$this->block_footer();
			echo '</div>';
		}
	}

	public function right() {
		$this->title( __( "Online Performance Testing", "debugpress" ), true, true );

		if ( ! is_admin() ) {
			$this->block_header();
			$this->sub_title( __( "Google PageSpeed Insights", "debugpress" ) );
			echo '<p>' . __( "Run the current page in the Google PageSpeed Insights test tool.", "debugpress" ) . '</p>';
			echo '<a target="_blank" rel="noopener" href="' . $this->_google_pagespeed_url() . '" class="debugpress-button-action">' . __( "Run Mobile Test", "debugpress" ) . '</a>';
			echo '<a target="_blank" rel="noopener" href="' . $this->_google_pagespeed_url( 'desktop' ) . '" class="debugpress-button-action">' . __( "Run Desktop Test", "debugpress" ) . '</a>';
			$this->block_footer();

			$this->block_header();
			$this->sub_title( __( "GTMetrix", "debugpress" ) );
			echo '<p>' . __( "Open GTMetrix website for the current page.", "debugpress" ) . '</p>';
			echo '<a target="_blank" rel="noopener" href="' . $this->_gtmetrix_url() . '" class="debugpress-button-action">' . __( "Run the Test", "debugpress" ) . '</a>';
			$this->block_footer();
		} else {
			echo '<div class="debugpress-debug-notice-block">';
			$this->title( '<i class="debugpress-icon debugpress-icon-triangle-exclamation"></i> ' . __( "Not available for this page", "debugpress" ), true, true );
			$this->block_header();
			echo '<p>' . __( "Admin side panels can't be tested via online performance tools.", "debugpress" ) . '</p>';
			$this->block_footer();
			echo '</div>';
		}
	}

	private function _gtmetrix_url() : string {
		$url = 'https://gtmetrix.com/?url=';
		$url .= urlencode( WP::instance()->current_url() );

		return $url;
	}

	private function _google_pagespeed_url( $tab = 'mobile' ) : string {
		$url = 'https://developers.google.com/speed/pagespeed/insights/?url=';
		$url .= urlencode( WP::instance()->current_url() );
		$url .= '&tab=' . $tab;

		return $url;
	}
}
