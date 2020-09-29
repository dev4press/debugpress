<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\OPCache;
use Dev4Press\Plugin\DebugPress\Main\Panel;

class Tools extends Panel {
	public function left() {
		$this->title( __( "Information Panels", "debugpress" ), true, true );

		if ( current_user_can( 'manage_options' ) ) {
			$this->block_header( true );
			$this->sub_title( __( "PHP Info", "debugpress" ) );
			_e( "Load the full content of the PHP.ini file through PHPInfo function call.", "debugpress" );
			echo '<a target="_blank" href="' . admin_url( 'tools.php?page=debugpress&tab=php' ) . '" class="debugpress-button-action debugpress-action-debuglog-load">' . __( "Open PHP Info Panel", "debugpress" ) . '</a>';
			$this->block_footer();

			if ( OPCache::instance()->has_opcache() ) {
				$this->block_header( true );
				$this->sub_title( __( "OPCache Statistics", "debugpress" ) );
				_e( "Show the settings for the PHP OPCache and basic statistics.", "debugpress" );
				echo '<a target="_blank" href="' . admin_url( 'tools.php?page=debugpress&tab=opcache' ) . '" class="debugpress-button-action debugpress-action-debuglog-load">' . __( "Open OPCache Statistics Panel", "debugpress" ) . '</a>';
				$this->block_footer();
			}

			$this->block_header( true );
			$this->sub_title( __( "MySQL Variables", "debugpress" ) );
			_e( "Show all the MySQL configuration variables retrieved from the database server.", "debugpress" );
			echo '<a target="_blank" href="' . admin_url( 'tools.php?page=debugpress&tab=mysql' ) . '" class="debugpress-button-action debugpress-action-debuglog-load">' . __( "Open MySQL Variables Panel", "debugpress" ) . '</a>';
			$this->block_footer();
		} else {
			echo '<div class="debugpress-debug-notice-block">';
			$this->title( '<i class="debugpress-icon debugpress-icon-exclamation"></i> ' . __( "Information Panels not available", "debugpress" ), true, true );
			$this->block_header( true );
			_e( "Only website administrator can access the Information panels.", "debugpress" );
			$this->block_footer();
			echo '</div>';
		}
	}

	public function right() {

	}
}