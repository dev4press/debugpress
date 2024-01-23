<?php

namespace Dev4Press\Plugin\DebugPress\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Panel {
	private $_table = array();

	/** @return \Dev4Press\Plugin\DebugPress\Main\Panel */
	public static function instance() {
		static $instance = array();

		$class = get_called_class();

		if ( ! isset( $instance[ $class ] ) ) {
			$instance[ $class ] = new $class();
		}

		return $instance[ $class ];
	}

	public function left() {
	}

	public function middle() {
	}

	public function right() {
	}

	public function single() {
	}

	public function block_header( $open = true ) {
		echo '<div class="debugpress-debugger-panel-block" style="display: ' . ( $open ? 'block' : 'none' ) . ';">';
	}

	public function block_footer() {
		echo '</div>';
	}

	public function print_it( $value ) : string {
		$value = maybe_unserialize( $value );

		$print = debugpress_rs( $value, false );

		return ! empty( $print ) ? $print : debugpress_rx( $value, false );
	}

	public function title( $title, $open = true, $hide_button = false, $button_id = '' ) {
		$render = '<h5 class="debugpress-debugger-panel-block-title">' . debugpress_kses_basic( $title );

		if ( ! $hide_button ) {
			$id = empty( $button_id ) ? '' : 'debugpress-toggle-' . $button_id;

			$render .= '<span id="' . $id . '" class="' . ( $open ? 'block-open' : '' ) . '"><i class="debugpress-icon debugpress-icon-square-' . ( $open ? 'minus' : 'plus' ) . '"></i></span>';
		}

		$render .= '</h5>';

		echo $render; // phpcs:ignore WordPress.Security.EscapeOutput
	}

	public function sub_title( $title ) {
		echo '<h6 class="debugpress-debugger-panel-block-subtitle">' . esc_html( $title ) . '</h6>';
	}

	public function pre( $content ) {
		echo '<div class="debugpress-pre-wrap"><pre>';
		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</pre></div>';
	}

	public function add_column( $name, $class = '', $style = '', $reset = false ) {
		if ( $reset ) {
			$this->_table = array();
		}

		$this->_table[] = new Wrap( array(
			'name'  => $name,
			'class' => $class,
			'style' => $style,
		) );
	}

	public function table_init_standard() {
		$this->add_column( __( 'Name', 'debugpress' ), '', '', true );
		$this->add_column( __( 'Value', 'debugpress' ) );
	}

	public function table_init_right() {
		$this->add_column( __( 'Name', 'debugpress' ), '', '', true );
		$this->add_column( __( 'Value', 'debugpress' ), '', 'text-align: right;' );
	}

	public function table_head( $columns = array(), $id = '', $class = '', $thead = true ) {
		if ( ! empty( $columns ) ) {
			$this->_table = $columns;
		}

		if ( count( $this->_table ) == 2 ) {
			$class .= ' debugpress-table-keyvalue';
		}

		echo '<table' . ( ! empty( $id ) ? ' id="' . esc_attr( $id ) . '"' : '' ) . ' class="debugpress-debugger-table ' . esc_attr( trim( $class ) ) . '">' . PHP_EOL;
		if ( $thead ) {
			echo '<thead><tr>' . PHP_EOL;
			foreach ( $this->_table as $row ) {
				echo '<th scope="col" class="' . esc_attr( $row->class ) . '" style="' . esc_attr( $row->style ) . '">' . esc_html( $row->name ) . '</th>' . PHP_EOL;
			}
			echo '</tr></thead>' . PHP_EOL;
		}
		echo '<tbody>' . PHP_EOL;
	}

	public function table_foot() {
		echo '</tbody></table>' . PHP_EOL;
	}

	public function table_row( $data ) {
		echo '<tr>';
		$i = 0;

		foreach ( $data as $el ) {
			echo '<td style="' . esc_attr( $this->_table[ $i ]->style ) . '">' . $el . '</td>' . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput
			$i ++;
		}

		echo '</tr>';
	}

	public function list_defines( $defines, $subtitle = '', $block = true, $open = true ) {
		if ( $block ) {
			$this->block_header( $open );
		}

		if ( $subtitle != '' ) {
			$this->sub_title( $subtitle );
		}

		$this->table_init_standard();
		$this->table_head();
		foreach ( $defines as $const ) {
			$val = defined( $const ) ? $this->print_it( constant( $const ) ) : '<span style="color: #DD0000;">' . __( 'NOT DEFINED', 'debugpress' ) . '</span>';

			$this->table_row( array( $const, $val ) );
		}
		$this->table_foot();

		if ( $block ) {
			$this->block_footer();
		}
	}

	public function list_array( $data, $subtitle = '', $block = true, $open = true ) {
		if ( $block ) {
			$this->block_header( $open );
		}

		if ( $subtitle != '' ) {
			$this->sub_title( $subtitle );
		}

		$this->table_init_standard();

		if ( ( is_array( $data ) && ! empty( $data ) ) || is_object( $data ) ) {
			$this->table_head();
			foreach ( $data as $name => $value ) {
				$this->table_row( array( $name, $this->print_it( $value ) ) );
			}
			$this->table_foot();
		}

		if ( $block ) {
			$this->block_footer();
		}
	}

	public function list_properties( $object, $properties = array(), $subtitle = '' ) {
		$this->block_header();

		if ( $subtitle != '' ) {
			$this->sub_title( $subtitle );
		}

		$this->table_init_standard();

		$this->table_head();

		foreach ( $properties as $property ) {
			$value = $object->$property ?? '<span style="color: #d00;">' . __( 'not defined', 'debugpress' ) . '</span>';

			$this->table_row( array( $property, $this->print_it( $value ) ) );
		}

		$this->table_foot();

		$this->block_footer();
	}

	public function list_plain_pairs( $list, $merge = '<br/>' ) : string {
		$render = array();

		foreach ( $list as $key => $value ) {
			$render[] = $key . ': ' . $value;
		}

		return join( $merge, $render );
	}
}
