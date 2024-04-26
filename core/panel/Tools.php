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
			$this->title( esc_html__( 'Information Panels', 'debugpress' ), true, true );

			$this->block_header();
			$this->sub_title( __( 'PHP Info', 'debugpress' ) );
			echo '<p>' . esc_html__( 'Load the full content of the PHP.ini file through PHPInfo function call.', 'debugpress' ) . '</p>';
			echo '<a target="_blank" href="' . esc_url( admin_url( 'tools.php?page=debugpress-info&tab=php' ) ) . '" class="debugpress-button-action">' . esc_html__( 'Open PHP Info Panel', 'debugpress' ) . '</a>';
			$this->block_footer();

			if ( OPCache::instance()->has_opcache() ) {
				$this->block_header();
				$this->sub_title( __( 'OPCache Statistics', 'debugpress' ) );
				echo '<p>' . esc_html__( 'Show the settings for the PHP OPCache and basic statistics.', 'debugpress' ) . '</p>';
				echo '<a target="_blank" href="' . esc_url( admin_url( 'tools.php?page=debugpress-info&tab=opcache' ) ) . '" class="debugpress-button-action">' . esc_html__( 'Open OPCache Statistics Panel', 'debugpress' ) . '</a>';
				$this->block_footer();
			}

			$this->block_header();
			$this->sub_title( __( 'MySQL Variables', 'debugpress' ) );
			echo '<p>' . esc_html__( 'Show all the MySQL configuration variables retrieved from the database server.', 'debugpress' ) . '</p>';
			echo '<a target="_blank" href="' . esc_url( admin_url( 'tools.php?page=debugpress-info&tab=mysql' ) ) . '" class="debugpress-button-action">' . esc_html__( 'Open MySQL Variables Panel', 'debugpress' ) . '</a>';
			$this->block_footer();

			$this->title( esc_html__( 'DebugPress Settings', 'debugpress' ), true, true );

			$this->block_header();
			$this->sub_title( __( 'Plugin Settings', 'debugpress' ) );
			echo '<p>' . esc_html__( 'Open the DebugPress settings panel.', 'debugpress' ) . '</p>';
			echo '<a target="_blank" href="' . esc_url( admin_url( 'options-general.php?page=debugpress' ) ) . '" class="debugpress-button-action">' . esc_html__( 'Open Settings Panel', 'debugpress' ) . '</a>';
			$this->block_footer();
		} else {
			echo '<div class="debugpress-debug-notice-block">';
			$this->title( '<i class="debugpress-icon debugpress-icon-triangle-exclamation"></i> ' . esc_html__( 'Not Available', 'debugpress' ), true, true );
			$this->block_header();
			echo '<p>' . esc_html__( 'Only administrator roles can access this information.', 'debugpress' ) . '</p>';
			$this->block_footer();
			echo '</div>';
		}
	}

	public function middle() {
		$this->title( 'coreActivity', true, true );
		echo '<p>' . esc_html__( 'coreActivity is free and powerful plugin for logging activities in WordPress for later review and analysis, supporting over 120 events, more than 10 plugins, with notifications, live logs and more. And, it has 9 events for DebugPress plugin.', 'debugpress' ) . '</p>';

		$this->block_header();
		if ( debugpress_has_coreactivity() ) {
			$this->sub_title( __( 'Events and Logs', 'debugpress' ) );
			echo '<p>' . esc_html__( 'Configure available events and check out what is logged.', 'debugpress' ) . '</p>';
			echo '<a target="_blank" href="' . esc_url( network_admin_url( 'admin.php?page=coreactivity-events&filter-component=coreactivity/debugpress' ) ) . '" class="debugpress-button-action">' . esc_html__( 'DebugPress Events', 'debugpress' ) . '</a>';
			echo '<a target="_blank" href="' . esc_url( network_admin_url( 'admin.php?page=coreactivity-logs&view=component&filter-component=coreactivity/debugpress' ) ) . '" class="debugpress-button-action">' . esc_html__( 'DebugPress Log', 'debugpress' ) . '</a>';
		} else {
			$this->sub_title( __( 'Install the Plugin', 'debugpress' ) );
			echo '<p>' . esc_html__( 'Find plugin in the repository, install and activate it.', 'debugpress' ) . '</p>';
			echo '<a target="_blank" href="' . esc_url( network_admin_url( 'plugin-install.php?s=coreactivity&tab=search&type=term' ) ) . '" class="debugpress-button-action">' . esc_html__( 'Find and Install the plugin', 'debugpress' ) . '</a>';
		}
		$this->block_footer();

		$this->title( esc_html__( 'WordPress Important Tools', 'debugpress' ), true, true );

		if ( current_user_can( 'manage_options' ) ) {
			if ( ! debugpress_is_classicpress() && debugpress_plugin()->wp_version() > 51 ) {
				$this->block_header();
				$this->sub_title( __( 'Site Health', 'debugpress' ) );
				echo '<p>' . esc_html__( 'Open the WordPress Site Health panel.', 'debugpress' ) . '</p>';
				echo '<a target="_blank" href="' . esc_url( admin_url( 'site-health.php' ) ) . '" class="debugpress-button-action">' . esc_html__( 'Open Site Health Panel', 'debugpress' ) . '</a>';
				$this->block_footer();
			}

			$this->block_header();
			$this->sub_title( __( 'WordPress Updates', 'debugpress' ) );
			echo '<p>' . esc_html__( 'Open the WordPress Updates panel.', 'debugpress' ) . '</p>';
			echo '<a target="_blank" href="' . esc_url( admin_url( 'update-core.php' ) ) . '" class="debugpress-button-action">' . esc_html__( 'Open Updates Panel', 'debugpress' ) . '</a>';
			$this->block_footer();
		} else {
			echo '<div class="debugpress-debug-notice-block">';
			$this->title( '<i class="debugpress-icon debugpress-icon-triangle-exclamation"></i> ' . __( 'Not Available', 'debugpress' ), true, true );
			$this->block_header();
			echo '<p>' . esc_html__( 'Only administrator roles can access this information.', 'debugpress' ) . '</p>';
			$this->block_footer();
			echo '</div>';
		}
	}

	public function right() {
		$this->title( esc_html__( 'Online Performance Testing', 'debugpress' ), true, true );

		if ( ! is_admin() ) {
			$this->block_header();
			$this->sub_title( __( 'Google PageSpeed Insights', 'debugpress' ) );
			echo '<p>' . esc_html__( 'Run the current page in the Google PageSpeed Insights test tool.', 'debugpress' ) . '</p>';
			echo '<a target="_blank" rel="noopener" href="' . esc_url( $this->_google_pagespeed_url() ) . '" class="debugpress-button-action">' . esc_html__( 'Run Mobile Test', 'debugpress' ) . '</a>';
			echo '<a target="_blank" rel="noopener" href="' . esc_url( $this->_google_pagespeed_url( 'desktop' ) ) . '" class="debugpress-button-action">' . esc_html__( 'Run Desktop Test', 'debugpress' ) . '</a>';
			$this->block_footer();

			$this->block_header();
			$this->sub_title( __( 'GTMetrix', 'debugpress' ) );
			echo '<p>' . esc_html__( 'Open GTMetrix website for the current page.', 'debugpress' ) . '</p>';
			echo '<a target="_blank" rel="noopener" href="' . esc_url( $this->_gtmetrix_url() ) . '" class="debugpress-button-action">' . esc_html__( 'Run the Test', 'debugpress' ) . '</a>';
			$this->block_footer();
		} else {
			echo '<div class="debugpress-debug-notice-block">';
			$this->title( '<i class="debugpress-icon debugpress-icon-triangle-exclamation"></i> ' . esc_html__( 'Not available for this page', 'debugpress' ), true, true );
			$this->block_header();
			echo '<p>' . esc_html__( 'Admin side panels can\'t be tested via online performance tools.', 'debugpress' ) . '</p>';
			$this->block_footer();
			echo '</div>';
		}
	}

	private function _gtmetrix_url() : string {
		$url = 'https://gtmetrix.com/?url=';
		$url .= rawurlencode( WP::instance()->current_url() );

		return $url;
	}

	private function _google_pagespeed_url( $tab = 'mobile' ) : string {
		$url = 'https://developers.google.com/speed/pagespeed/insights/?url=';
		$url .= rawurlencode( WP::instance()->current_url() );
		$url .= '&tab=' . $tab;

		return $url;
	}
}
