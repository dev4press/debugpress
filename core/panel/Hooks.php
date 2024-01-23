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
		global $wp_filter;

		$this->origins['none::none']              = __( 'No Callbacks', 'debugpress' );
		$this->origins['php::php']                = __( 'PHP', 'debugpress' );
		$this->origins['core::core']              = __( 'WordPress: Core', 'debugpress' );
		$this->origins['admin::admin']            = __( 'WordPress: Admin', 'debugpress' );
		$this->origins['includes::includes']      = __( 'WordPress: Includes', 'debugpress' );
		$this->origins['content::content']        = __( 'WordPress: Content', 'debugpress' );
		$this->origins['stylesheet::child-theme'] = __( 'Child Theme', 'debugpress' ) . ': ' . Info::cms_stylesheet_theme_name();
		$this->origins['stylesheet::theme']       = __( 'Theme', 'debugpress' ) . ': ' . Info::cms_templates_theme_name();
		$this->origins['template::theme']         = __( 'Theme', 'debugpress' ) . ': ' . Info::cms_templates_theme_name();
		$this->origins_order['none::none']        = 0;

		$hook_names = array_keys( $wp_filter );

		foreach ( $hook_names as $name ) {
			$hook = Help::process_hook( $name );

			$this->hooks[] = $hook;

			if ( ! empty( $hook['origins'] ) ) {
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
					$title = __( 'Plugin', 'debugpress' ) . ': ' . $parts[1];
				} else if ( $parts[0] == 'mu-plugin' ) {
					$title = __( 'MU Plugin', 'debugpress' ) . ': ' . $parts[1];
				}

				$this->origins[ $origin ] = $title;
			}
		}
	}

	public function left() {
		echo '<h4 class="debugpress-query-sidebar-control"><span data-state="open"><i class="debugpress-icon debugpress-icon-caret-left"></i></span></h4>';

		$this->title( esc_html__( 'Hooks Control', 'debugpress' ), true, false, 'hooks-control' );
		$this->block_header();
		$this->add_column( __( 'Name', 'debugpress' ), '', '', true );
		$this->add_column( __( 'Control', 'debugpress' ), '', 'text-align: right;' );
		$this->table_head();
		$this->table_row( array(
			__( 'Callback File', 'debugpress' ),
			'<a href="#" id="sql-call-compact" class="sqlq-option-callbacks sqlq-option-on">' . __( 'compact', 'debugpress' ) . '</a> &middot; <a href="#" id="sql-call-full" class="sqlq-option-callbacks sqlq-option-off">' . __( 'full', 'debugpress' ) . '</a>',
		) );
		$this->table_foot();
		$this->block_footer();

		$this->title( esc_html__( 'Callback Filter', 'debugpress' ), true, false, 'hooks-filter' );
		$this->block_header();
		$this->add_column( __( 'Origin', 'debugpress' ), '', '', true );
		$this->add_column( __( 'Count', 'debugpress' ), '', 'text-align: right;' );
		$this->table_head();
		$this->table_row( array(
			__( 'Reset All', 'debugpress' ),
			'<a href="#" id="sql-types-show" class="sqlq-types-reset sqlq-option-on">' . __( 'show', 'debugpress' ) . '</a> &middot; <a href="#" id="sql-types-hide" class="sqlq-types-reset sqlq-option-off">' . __( 'hide', 'debugpress' ) . '</a>',
		) );
		foreach ( $this->origins as $type => $title ) {
			$count = $this->origins_order[ $type ] ?? 0;

			if ( $count > 0 ) {
				$this->table_row( array(
					'<a href="#" data-type="' . strtolower( $type ) . '" class="sqlq-option-type sqlq-option-on">' . $title . '</a>',
					$count,
				) );
			}
		}
		$this->table_foot();
		$this->block_footer();
	}

	public function right() {
		$this->title( esc_html__( 'List of Hooks', 'debugpress' ) );
		$this->block_header();
		$this->add_column( __( 'Hook', 'debugpress' ), '', '', true );
		$this->add_column( __( 'Priority | Callback | Origin', 'debugpress' ) );
		$this->table_head( array(), 'dbg-hooks-list' );

		foreach ( $this->hooks as $hook ) {
			$rows = count( $hook['actions'] );

			if ( $rows == 0 ) {
				echo '<tr class="' . esc_attr( $this->_row_filter_classes( $hook['origins'] ) ) . '"><th>' . esc_html( $hook['name'] ) . '</th><td class="dbg-hook-no-callbacks">' . esc_html__( 'No callbacks registered for this hook.', 'debugpress' ) . '</td></tr>';
			} else {
				echo '<tr class="' . esc_attr( $this->_row_filter_classes( $hook['origins'] ) ) . '">';
				echo '<th>' . esc_html( $hook['name'] ) . '</th>';
				echo '<td>';
				echo '<table class="dbg-hooks-actions">';
				foreach ( $hook['actions'] as $action ) {
					echo '<tr class="' . esc_attr( $this->_row_filter_classes( $action['origin'] ) ) . '">';
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
		echo '<td>' . esc_html( $action['priority'] ?? 10 ) . '</td>';
		echo '<td class="dbg-hook-column-action"><em>' . esc_html( $action['name'] ) . '</em>';

		if ( ! empty( $action['file'] ) ) {
			echo '<button class="dbg-callback-button-expander" type="button">' . esc_html__( 'toggle', 'debugpress' ) . '</button>';
			echo '<div><span>' . esc_html__( 'In File', 'debugpress' ) . ': <strong>' . esc_html( $action['file'] ) . '</strong></span>';

			if ( ! empty( $action['line'] ) ) {
				echo '<span>' . esc_html__( 'On Line', 'debugpress' ) . ': <strong>' . esc_html( $action['line'] ) . '</strong></span>';
			}

			echo '</div>';
		}

		echo '</td>';
		echo '<td>' . $this->origins[ $action['origin'] ] . '</td>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
