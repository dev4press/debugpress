<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Display\SQLFormat;
use Dev4Press\Plugin\DebugPress\Main\Panel;

class Queries extends Panel {
	public $stats = array(
		'percent' => 0,
		'max'     => 0,
		'total'   => 0,
		'avg'     => 0,
		'min'     => 0,
		'count'   => 0
	);

	public $sql_caller_use = array();
	public $sql_caller_keys = array();
	public $sql_caller_classes = array();

	public $sql_tables_use = array(
		'__system__' => 0
	);
	public $sql_tables_keys = array(
		'__system__' => 0
	);
	public $sql_tables_classes = array();

	public $sql_types_use = array();
	public $sql_types_classes = array();
	public $sql_source_classes = array(
		'n/a' => 0
	);
	public $library_identification = array();

	public function __construct() {
		$this->stats[ 'max' ]   = debugpress_db()->wpdb()->queries[ 0 ][ 1 ];
		$this->stats[ 'min' ]   = debugpress_db()->wpdb()->queries[ 0 ][ 1 ];
		$this->stats[ 'count' ] = count( debugpress_db()->wpdb()->queries );

		$i  = 1;
		$j  = 0;
		$qi = 0;

		$this->_prepare_library();

		foreach ( debugpress_db()->wpdb()->queries as $q ) {
			$query = trim( $q[ 0 ] );
			$calls = explode( ', ', $q[ 2 ] );

			$this->stats[ 'total' ] += floatval( $q[ 1 ] );

			if ( $this->stats[ 'max' ] < $q[ 1 ] ) {
				$this->stats[ 'max' ] = $q[ 1 ];
			}

			if ( $this->stats[ 'min' ] > $q[ 1 ] ) {
				$this->stats[ 'min' ] = $q[ 1 ];
			}

			$this->sql_tables_classes[ $qi ] = array();

			$this->_parse_callers( $calls, $qi, $j );
			$this->_parse_tables( $query, $qi, $i );
			$this->_parse_types( $query, $qi );

			if ( ! isset( $this->library_identification[ $qi ] ) ) {
				$this->sql_source_classes[ 'n/a' ] ++;
			}

			$qi ++;
		}

		arsort( $this->sql_tables_use );

		$temp = array(
			'__system__' => $this->sql_tables_use[ '__system__' ]
		);

		foreach ( $this->sql_tables_use as $key => $val ) {
			$temp[ $key ] = $val;
		}

		$this->sql_tables_use = $temp;

		$this->stats[ 'percent' ] = $this->stats[ 'total' ] / 100;
		$this->stats[ 'avg' ]     = $this->stats[ 'total' ] / $this->stats[ 'count' ];
	}

	public function left() {
		echo '<h4 class="debugpress-querie-sidebar-control"><span data-state="open"><i class="debugpress-icon debugpress-icon-caret-left"></i></span></h4>';

		$this->title( __( "Basic Statistics", "debugpress" ) );
		$this->block_header();
		$this->add_column( __( "Name", "debugpress" ), '', '', true );
		$this->add_column( __( "Total", "debugpress" ), '', 'text-align: right;' );
		$this->add_column( __( "Filtered", "debugpress" ), '', 'text-align: right;' );
		$this->add_column( __( "Ratio", "debugpress" ), '', 'text-align: right;' );
		$this->table_head();
		$this->table_row( array(
			__( "Queries", "debugpress" ),
			$this->stats[ 'count' ],
			'<span id="sqlq-stats-filter-queries">' . $this->stats[ 'count' ] . '</span>',
			'<span id="sqlq-stats-ratio-queries">100%</span>'
		) );
		$this->table_row( array(
			__( "Total", "debugpress" ),
			number_format( $this->stats[ 'total' ], 6 ) . ' ' . __( "sec", "debugpress" ),
			'<span id="sqlq-stats-filter-total">' . number_format( $this->stats[ 'total' ], 6 ) . '</span> ' . __( "sec", "debugpress" ),
			'<span id="sqlq-stats-ratio-total">100%</span>'
		) );
		$this->table_row( array(
			__( "Fastest", "debugpress" ),
			number_format( $this->stats[ 'min' ], 6 ) . ' ' . __( "sec", "debugpress" ),
			'<span id="sqlq-stats-filter-min">' . number_format( $this->stats[ 'min' ], 6 ) . '</span> ' . __( "sec", "debugpress" ),
			'-'
		) );
		$this->table_row( array(
			__( "Slowest", "debugpress" ),
			number_format( $this->stats[ 'max' ], 6 ) . ' ' . __( "sec", "debugpress" ),
			'<span id="sqlq-stats-filter-max">' . number_format( $this->stats[ 'max' ], 6 ) . '</span> ' . __( "sec", "debugpress" ),
			'-'
		) );
		$this->table_row( array(
			__( "Average", "debugpress" ),
			number_format( $this->stats[ 'avg' ], 6 ) . ' ' . __( "sec", "debugpress" ),
			'<span id="sqlq-stats-filter-avg">' . number_format( $this->stats[ 'avg' ], 6 ) . '</span> ' . __( "sec", "debugpress" ),
			'-'
		) );
		$this->table_foot();
		$this->block_footer();

		$this->title( __( "Queries Control", "debugpress" ) );
		$this->block_header();
		$this->add_column( __( "Name", "debugpress" ), '', '', true );
		$this->add_column( __( "Control", "debugpress" ), '', 'text-align: right;' );
		$this->table_head();
		$this->table_row( array(
			__( "Call List", "debugpress" ),
			'<a href="#" id="sql-call-compact" class="sqlq-option-calls sqlq-option-on">' . __( "compact", "debugpress" ) . '</a> &middot; <a href="#" id="sql-call-full" class="sqlq-option-calls sqlq-option-off">' . __( "full", "debugpress" ) . '</a>'
		) );
		$this->table_row( array(
			__( "Sort By", "debugpress" ),
			'<a href="#" id="sql-sort-order" class="sqlq-option-sort sqlq-option-on">' . __( "order", "debugpress" ) . '</a> &middot; <a href="#" id="sql-sort-time" class="sqlq-option-sort sqlq-option-off">' . __( "time", "debugpress" ) . '</a> &middot; <a href="#" id="sql-sort-length" class="sqlq-option-sort sqlq-option-off">' . __( "length", "debugpress" ) . '</a>'
		) );
		$this->table_row( array(
			__( "Show", "debugpress" ),
			'<a href="#" data-show="all" class="sqlq-option-show sqlq-option-on">' . __( "all", "debugpress" ) . '</a> &middot; <a href="#" data-show="slow" class="sqlq-option-show sqlq-option-off">' . __( "slow", "debugpress" ) . '</a> &middot; <a href="#" data-show="fast" class="sqlq-option-show sqlq-option-off">' . __( "fast", "debugpress" ) . '</a>'
		) );
		$this->table_foot();
		$this->block_footer();

		$this->title( __( "Query Types", "debugpress" ) );
		$this->block_header();
		$this->add_column( __( "Type", "debugpress" ), '', '', true );
		$this->add_column( __( "Queries", "debugpress" ), '', 'text-align: right;' );
		$this->table_head();
		$this->table_row( array(
			__( "Reset All", "debugpress" ),
			'<a href="#" id="sql-types-show" class="sqlq-types-reset sqlq-option-on">' . __( "show", "debugpress" ) . '</a> &middot; <a href="#" id="sql-types-hide" class="sqlq-types-reset sqlq-option-off">' . __( "hide", "debugpress" ) . '</a>'
		) );
		foreach ( $this->sql_types_use as $type => $count ) {
			$this->table_row( array(
				'<a href="#" data-type="' . strtolower( $type ) . '" class="sqlq-option-type sqlq-option-on">' . $type . '</a>',
				$count
			) );
		}
		$this->table_foot();
		$this->block_footer();

		$this->title( __( "Database Tables", "debugpress" ) );
		$this->block_header();
		$this->add_column( __( "Table", "debugpress" ), '', '', true );
		$this->add_column( __( "Queries", "debugpress" ), '', 'text-align: right;' );
		$this->table_head();
		$this->table_row( array(
			__( "Reset All", "debugpress" ),
			'<a href="#" id="sql-reset-show" class="sqlq-option-reset sqlq-option-on">' . __( "show", "debugpress" ) . '</a> &middot; <a href="#" id="sql-reset-hide" class="sqlq-option-reset sqlq-option-off">' . __( "hide", "debugpress" ) . '</a>'
		) );
		foreach ( $this->sql_tables_use as $table => $count ) {
			$this->table_row( array(
				'<a href="#" data-table="' . $this->sql_tables_keys[ $table ] . '" class="sqlq-option-table sqlq-option-on">' . $table . '</a>',
				$count
			) );
		}
		$this->table_foot();
		$this->block_footer();

		$this->title( __( "Query Callers", "debugpress" ) );
		$this->block_header();
		$this->add_column( __( "Caller", "debugpress" ), '', '', true );
		$this->add_column( __( "Queries", "debugpress" ), '', 'text-align: right;' );
		$this->table_head();
		$this->table_row( array(
			__( "Reset All", "debugpress" ),
			'<a href="#" id="sql-caller-show" class="sqlq-caller-reset sqlq-option-on">' . __( "show", "debugpress" ) . '</a> &middot; <a href="#" id="sql-caller-hide" class="sqlq-caller-reset sqlq-option-off">' . __( "hide", "debugpress" ) . '</a>'
		) );
		foreach ( $this->sql_caller_use as $caller => $count ) {
			$this->table_row( array(
				'<a href="#" data-caller="' . $this->sql_caller_keys[ $caller ] . '" class="sqlq-option-caller sqlq-option-on">' . $caller . '</a>',
				$count
			) );
		}
		$this->table_foot();
		$this->block_footer();

		if ( count( $this->sql_source_classes ) > 1 ) {
			$this->title( __( "Call Source", "debugpress" ) );
			$this->block_header();
			$this->add_column( __( "Source", "debugpress" ), '', '', true );
			$this->add_column( __( "Queries", "debugpress" ), '', 'text-align: right;' );
			$this->table_head();
			$this->table_row( array(
				__( "Reset All", "debugpress" ),
				'<a href="#" id="sql-source-show" class="sqlq-source-reset sqlq-option-on">' . __( "show", "debugpress" ) . '</a> &middot; <a href="#" id="sql-source-hide" class="sqlq-source-reset sqlq-option-off">' . __( "hide", "debugpress" ) . '</a>'
			) );
			foreach ( $this->sql_source_classes as $caller => $count ) {
				$this->table_row( array(
					'<a href="#" data-source="' . $caller . '" class="sqlq-option-source sqlq-option-on">' . $caller . '</a>',
					$count
				) );
			}
			$this->table_foot();
			$this->block_footer();
		}
	}

	public function right() {
		$this->title( __( "List of Executed SQL Queries", "debugpress" ), true, true );
		$this->block_header();
		echo '<div class="sql-query-list">';

		$total  = $this->stats[ 'total' ];
		$cutoff = debugpress_plugin()->get( 'slow_query_cutoff' );

		if ( $cutoff < 1 || $cutoff > 100 ) {
			$cutoff = 10;
		}

		$cutoff = $total * ( $cutoff / 100 );

		$i = 0;
		foreach ( debugpress_db()->wpdb()->queries as $q ) {
			$calls = explode( ', ', $q[ 2 ] );

			if ( $cutoff <= $q[ 1 ] ) {
				$speed = 'slow';
			} else {
				$speed = 'fast';
			}

			$source = $this->library_identification[ $i ] ?? 'n/a';

			echo '<div class="sql-query" data-source="' . $source . '" data-caller="' . $this->sql_caller_classes[ $i ] . '" data-tables="' . join( ',', $this->sql_tables_classes[ $i ] ) . '" data-type="' . $this->sql_types_classes[ $i ] . '" data-speed="' . $speed . '" data-order="' . $i . '" data-time="' . $q[ 1 ] . '" data-length="' . strlen( $q[ 0 ] ) . '">';
			echo '<strong>' . __( "Order", "debugpress" ) . ':</strong> ' . $i . ' | ';
			echo '<strong>' . __( "Length", "debugpress" ) . ':</strong> ' . strlen( $q[ 0 ] ) . ' ' . __( "characters", "debugpress" ) . ' | ';
			echo '<strong>' . __( "Time", "debugpress" ) . ':</strong> ' . $q[ 1 ] . ' ' . __( "seconds", "debugpress" ) . ' | ';
			echo '<strong>' . __( "Share", "debugpress" ) . ':</strong> ' . number_format( $q[ 1 ] / $this->stats[ 'percent' ], 4 ) . '% | ';

			if ( $this->stats[ 'min' ] == $q[ 1 ] ) {
				echo '<strong>' . __( "Fastest Query", "debugpress" ) . '</strong>';
			} else if ( $this->stats[ 'max' ] == $q[ 1 ] ) {
				echo '<strong>' . __( "Slowest Query", "debugpress" ) . '</strong>';
			} else if ( $this->stats[ 'avg' ] < $q[ 1 ] ) {
				echo '<strong>' . __( "Slow", "debugpress" ) . '</strong>';
			} else if ( $this->stats[ 'avg' ] > $q[ 1 ] ) {
				echo '<strong>' . __( "Fast", "debugpress" ) . '</strong>';
			} else {
				echo '<strong>' . __( "Average", "debugpress" ) . '</strong>';
			}

			echo '<div class="sql-calls-full" style="display: none;">';
			echo '<strong>' . __( "Called from", "debugpress" ) . ':</strong> <br/>' . join( '<br/>', $calls );
			echo '<a class="sql-calls-button-expander" href="#">' . __( "collapse", "debugpress" ) . '</a><br />';
			echo '</div><div class="sql-calls-compact">';
			echo '<strong>' . __( "Called from", "debugpress" ) . ':</strong> ' . end( $calls );
			echo '<a class="sql-calls-button-expander" href="#">' . __( "expand", "debugpress" ) . '</a><br />';
			echo '</div><div class="sql-query-full">';

			if ( debugpress_plugin()->get( 'format_queries_panel' ) ) {
				echo SQLFormat::format( $q[ 0 ] );
			} else {
				echo $q[ 0 ];
			}
			echo '</div>';
			echo '</div>';

			$i ++;
		}

		echo '</div>';
		$this->block_footer();
	}

	private function _parse_callers( $calls, $qi, &$j ) {
		$total = count( $calls );
		$last  = $calls[ $total - 1 ];

		if ( strpos( $last, 'd4p_wpdb_core' ) === 0 || ( strpos( $last, 'Dev4Press' ) === 0 && strpos( $last, '\Core\Plugins\DBLite' ) > 0 ) ) {
			$last = $calls[ $total - 2 ];
		}

		if ( ! isset( $this->sql_caller_use[ $last ] ) ) {
			$this->sql_caller_keys[ $last ] = $j;
			$this->sql_caller_use[ $last ]  = 0;

			$j ++;
		}

		$this->sql_caller_use[ $last ] ++;

		$this->sql_caller_classes[ $qi ] = $this->sql_caller_keys[ $last ];
	}

	private function _parse_tables( $query, $qi, &$i ) {
		preg_match_all( '/\s(`?)(' . debugpress_db()->wpdb()->base_prefix . '.+?)\1(\s|;|$)/i', $query, $matches );

		$_tables = empty( $matches[ 2 ] ) ? array( '__system__' ) : $matches[ 2 ];

		$listed = array();
		foreach ( $_tables as $_table ) {
			$tables = explode( ".", $_table );
			$table  = $tables[ 0 ];

			if ( ! isset( $this->sql_tables_use[ $table ] ) ) {
				$this->sql_tables_keys[ $table ] = $i;
				$this->sql_tables_use[ $table ]  = 0;

				$i ++;
			}

			if ( ! in_array( $table, $listed ) ) {
				$this->sql_tables_use[ $table ] ++;
				$listed[] = $table;
			}

			$this->sql_tables_classes[ $qi ][] = $this->sql_tables_keys[ $table ];
		}
	}

	private function _parse_types( $query, $qi ) {
		$type = $query;

		if ( strpos( $query, '/*' ) === 0 ) {
			$type = preg_replace( '|^/\*[^\*/]+\*/|', '', $query );
		}

		$type = preg_split( '/\b/', trim( $type ), 2, PREG_SPLIT_NO_EMPTY );
		$type = strtoupper( $type[ 0 ] );

		if ( ! isset( $this->sql_types_use[ $type ] ) ) {
			$this->sql_types_use[ $type ] = 1;
		} else {
			$this->sql_types_use[ $type ] ++;
		}

		$this->sql_types_classes[ $qi ] = strtolower( $type );
	}

	private function _prepare_library() {
		if ( class_exists( 'Dev4Press\v41\Core\Helpers\DB' ) ) {
			foreach ( \Dev4Press\v41\Core\Helpers\DB::instance()->log_get_queries() as $query ) {
				$this->library_identification[ absint( $query[ 'id' ] ) ] = $query[ 'plugin' ] . '/' . $query[ 'instance' ];
			}
		}

		if ( class_exists( 'Dev4Press\v42\Core\Helpers\DB' ) ) {
			foreach ( \Dev4Press\v42\Core\Helpers\DB::instance()->log_get_queries() as $query ) {
				$this->library_identification[ absint( $query[ 'id' ] ) ] = $query[ 'plugin' ] . '/' . $query[ 'instance' ];
			}
		}

		foreach ( $this->library_identification as $code ) {
			if ( ! isset( $this->sql_source_classes[ $code ] ) ) {
				$this->sql_source_classes[ $code ] = 0;
			}

			$this->sql_source_classes[ $code ] ++;
		}
	}
}