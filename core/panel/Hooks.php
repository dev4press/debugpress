<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\Help;
use Dev4Press\Plugin\DebugPress\Main\Info;
use Dev4Press\Plugin\DebugPress\Main\Panel;

class Hooks extends Panel {
	public $hooks = array();
	public $origins = array();
	public $origins_order = array();

	public function __construct() {
		global $wp_actions, $wp_filters, $wp_filter;

		$this->origins['none::none']              = __( "No Callbacks" );
		$this->origins['php::php']                = __( "PHP" );
		$this->origins['core::core']              = __( "WordPress Core" );
		$this->origins['stylesheet::child-theme'] = __( "Child Theme" ) . ': ' . Info::cms_stylesheet_theme_name();
		$this->origins['stylesheet::theme']       = __( "Theme" ) . ': ' . Info::cms_templates_theme_name();
		$this->origins['template::theme']         = __( "Theme" ) . ': ' . Info::cms_templates_theme_name();
		$this->origins_order['none::none']        = 0;

		$hook_names = array_keys( $wp_filter );

		foreach ( $hook_names as $name ) {
			$hook = Help::process_hook( $name );

			$this->hooks[] = $hook;

			if ( isset( $hook['origins'] ) && ! empty( $hook['origins'] ) ) {
				$this->process_origin( $hook['origins'] );
			} else {
				++ $this->origins_order['none::none'];
			}
		}
	}

	private function process_origin( $origins ) {
		foreach ( $origins as $origin ) {
			if ( ! isset( $this->origins_order[ $origin ] ) ) {
				$this->origins_order[ $origin ] = 0;
			}

			++ $this->origins_order[ $origin ];

			if ( ! isset( $this->origins[ $origin ] ) ) {
				$parts = explode( '::', $origin, 2 );

				$title = '';
				if ( $parts[0] == 'plugin' ) {
					$title = __( "Plugin" ) . ': ' . $parts[1];
				}

				$this->origins[ $origin ] = $title;
			}
		}
	}

	public function left() {
		echo '<h4 class="debugpress-querie-sidebar-control"><span data-state="open"><i class="debugpress-icon debugpress-icon-caret-right debugpress-icon-flip-horizontal"></i></span></h4>';

		$this->title( __( "Queries Control", "debugpress" ) );
		$this->block_header();
		$this->add_column( __( "Name", "debugpress" ), '', '', true );
		$this->add_column( __( "Control", "debugpress" ), '', 'text-align: right;' );
		$this->table_head();
		$this->table_row( array(
			__( "Callback File", "debugpress" ),
			'<a href="#" id="sql-call-compact" class="sqlq-option-callbacks sqlq-option-on">' . __( "compact", "debugpress" ) . '</a> &middot; <a href="#" id="sql-call-full" class="sqlq-option-calls sqlq-option-off">' . __( "full", "debugpress" ) . '</a>'
		) );
		$this->table_foot();
		$this->block_footer();

		$this->title( __( "Callback Filter", "debugpress" ) );
		$this->block_header();
		$this->add_column( __( "Origin", "debugpress" ), '', '', true );
		$this->add_column( __( "Count", "debugpress" ), '', 'text-align: right;' );
		$this->table_head();
		$this->table_row( array(
			__( "Reset All", "debugpress" ),
			'<a href="#" id="sql-types-show" class="sqlq-types-reset sqlq-option-on">' . __( "show", "debugpress" ) . '</a> &middot; <a href="#" id="sql-types-hide" class="sqlq-types-reset sqlq-option-off">' . __( "hide", "debugpress" ) . '</a>'
		) );
		foreach ( $this->origins as $type => $title ) {
			$count = $this->origins_order[ $type ] ?? 0;

			if ( $count > 0 ) {
				$this->table_row( array(
					'<a href="#" data-type="' . strtolower( $type ) . '" class="sqlq-option-type sqlq-option-on">' . $title . '</a>',
					$count
				) );
			}
		}
		$this->table_foot();
		$this->block_footer();
	}

	public function right() {
		$this->title( __( "List of Hooks", "debugpress" ) );
		$this->block_header();
		$this->add_column( __( "Hook", "debugpress" ), '', '', true );
		$this->add_column( __( "Priority | Callback | Origin", "debugpress" ) );
		$this->table_head( array(), 'dbg-hooks-list' );

		foreach ( $this->hooks as $hook ) {
			$rows = count( $hook['actions'] );

			if ( $rows == 0 ) {
				echo '<tr class="' . $this->_row_filter_classes( $hook['origins'] ) . '"><th>' . $hook['name'] . '</th><td class="dbg-hook-no-callbacks">' . __( "No callbacks registered for this hook." ) . '</td></tr>';
			} else {
				echo '<tr class="' . $this->_row_filter_classes( $hook['origins'] ) . '">';
				echo '<th>' . $hook['name'] . '</th>';
				echo '<td>';
				echo '<table class="dbg-hooks-actions">';
				foreach ( $hook['actions'] as $action ) {
					echo '<tr class="' . $this->_row_filter_classes( $action['origin'] ) . '">';
					$this->_action( $action );
					echo '</tr>';
				}
				echo '</table>';
				echo '</td>';
				echo '</tr>';
			}
		}

		$this->table_foot();
		$this->block_footer();
	}

	private function _row_filter_classes( $actions ) : string {
		$actions = (array) $actions;
		$classes = array();

		if ( empty( $actions ) ) {
			$classes[] = 'dbg-hook-none--none';
		} else {
			foreach ( $actions as $a ) {
				$a = str_replace( '::', '--', $a );

				$classes[] = 'dbg-hook-' . $a;
			}
		}

		return join( ' ', $classes );
	}

	private function _action( $action ) {
		echo '<td>' . ( $action['priority'] ?? 10 ) . '</td>';
		echo '<td class="dbg-hook-column-action"><em>' . $action['name'].'</em>';

		if ( isset( $action['file'] ) && ! empty( $action['file'] ) ) {
			echo '<button class="dbg-callback-button-expander" type="button">' . __( "toggle" ) . '</button>';
			echo '<div><span>' . __( "In File" ) . ': <strong>' . $action['file'] . '</strong></span>';

			if ( isset( $action['line'] ) && ! empty( $action['line'] ) ) {
				echo '<span>' . __( "On Line" ) . ': <strong>' . $action['line'] . '</strong></span>';
			}

			echo '</div>';
		}

		echo '</td>';
		echo '<td>' . $this->origins[$action['origin']] . '</td>';
	}
}
