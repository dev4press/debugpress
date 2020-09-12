<?php

namespace Dev4Press\Plugin\DebugPress\Main;

abstract class Panel {
	private $_table = array();

	public function __construct() {
	}

	public function block_header( $open = true ) {
		echo '<div class="gdpet-debugger-panel-block" style="display: ' . ( $open ? 'block' : 'none' ) . ';">';
	}

	public function block_footer() {
		echo '</div>';
	}

	public function print_it( $value ) {
		$value = maybe_unserialize( $value );

		$print = gdp_rs( $value, false );

		return ! empty( $print ) ? $print : gdp_rx( $value, false );
	}

	public function title( $title, $open = true, $hide_button = false ) {
		$render = '<h5 class="gdpet-debugger-panel-block-title">' . $title;

		if ( ! $hide_button ) {
			$render .= '<span class="' . ( $open ? 'block-open' : '' ) . '"><i class="gdpet-icon gdpet-icon-' . ( $open ? 'minus' : 'plus' ) . '"></i></span>';
		}

		$render .= '</h5>';

		echo $render;
	}

	public function sub_title( $title ) {
		echo '<h6 class="gdpet-debugger-panel-block-subtitle">' . $title . '</h6>';
	}

	public function add_column( $name, $class = '', $style = '', $reset = false ) {
		if ( $reset ) {
			$this->_table = array();
		}

		$this->_table[] = new Wrap( array( 'name' => $name, 'class' => $class, 'style' => $style ) );
	}

	public function table_init_standard() {
		$this->add_column( __( "Name", "gd-press-tools" ), '', '', true );
		$this->add_column( __( "Value", "gd-press-tools" ), '', '' );
	}

	public function table_init_right() {
		$this->add_column( __( "Name", "gd-press-tools" ), '', '', true );
		$this->add_column( __( "Value", "gd-press-tools" ), '', 'text-align: right;' );
	}

	public function table_head( $columns = array() ) {
		if ( ! empty( $columns ) ) {
			$this->_table = $columns;
		}

		echo '<table class="gdpet-debugger-table"><thead><tr>' . D4P_EOL;
		foreach ( $this->_table as $row ) {
			echo '<th scope="col" class="' . $row->class . '" style="' . $row->style . '">' . $row->name . '</th>' . D4P_EOL;
		}
		echo '</tr></thead><tbody>' . D4P_EOL;
	}

	public function table_foot() {
		echo '</tbody></table>' . D4P_EOL;
	}

	public function table_row( $data ) {
		echo '<tr>';
		$i = 0;
		foreach ( $data as $el ) {
			echo '<td style="' . $this->_table[ $i ]->style . '">' . $el . '</td>' . D4P_EOL;
			$i ++;
		}
		echo '</tr>';
	}

	public function list_defines( $defines, $subtitle = '' ) {
		$this->block_header( true );

		if ( $subtitle != '' ) {
			$this->sub_title( $subtitle );
		}

		$this->table_init_standard();
		$this->table_head();
		foreach ( $defines as $const ) {
			$val = defined( $const ) ? $this->print_it( constant( $const ) ) : '<span style="color: #DD0000;">' . __( "NOT DEFINED", "gd-press-tools" ) . '</span>';

			$this->table_row( array( $const, $val ) );
		}
		$this->table_foot();
		$this->block_footer();
	}

	public function list_array( $data, $subtitle = '' ) {
		$this->block_header( true );

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

		$this->block_footer();
	}

	public function list_properties( $object, $properties = array(), $subtitle = '' ) {
		$this->block_header( true );

		if ( $subtitle != '' ) {
			$this->sub_title( $subtitle );
		}

		$this->table_init_standard();

		$this->table_head();

		foreach ( $properties as $property ) {
			$value = isset( $object->$property ) ? $object->$property : '<span style="color: #d00;">' . __( "not defined", "gd-press-tools" ) . '</span>';

			$this->table_row( array( $property, $this->print_it( $value ) ) );
		}

		$this->table_foot();

		$this->block_footer();
	}

	public function list_plain_pairs( $list, $merge = '<br/>' ) {
		$render = array();

		foreach ( $list as $key => $value ) {
			$render[] = $key . ': ' . $value;
		}

		return join( $merge, $render );
	}
}
