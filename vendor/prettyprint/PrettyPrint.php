<?php

namespace Dev4Press\Plugin\DebugPress\Printer\PrettyPrint;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use ReflectionFunction;
use ReflectionMethod;

class PrettyPrint {
	protected static $been_there = '__DEBUGPRESS_INFINITE_RECURSION__';
	protected static $instance = null;

	protected $value;
	protected $is_root = true;

	public $css_class = 'debugpress_r';
	public $html_id = 'debugpress_r_v';
	public $js_func = 'debugpress_r_toggle';

	public $inspect_methods = false;
	public $collapsed = true;
	public $display_docs = true;
	public $display_footer = true;

	public $STR_EMPTY_ARRAY;
	public $STR_NO_PROPERTIES;
	public $STR_NO_METHODS;
	public $STR_INFINITE_RECURSION_WARNING;
	public $STR_STR_DESC;
	public $STR_RES_DESC;
	public $STR_ARR_DESC;
	public $STR_OBJ_DESC;
	public $STR_FOOTER_CALL;
	public $STR_FOOTER_LINE;

	public $ICON_DOWN = '<i class="debugpress-icon debugpress-icon-caret-down"></i>';
	public $ICON_RIGHT = '<i class="debugpress-icon debugpress-icon-caret-right"></i>';

	protected $_has_reflection = null;
	protected $_visible_mods = array( 'abstract', 'final', 'private', 'protected', 'public', 'static' );

	public function __construct() {
		if ( is_null( $this->_has_reflection ) ) {
			$this->_has_reflection = class_exists( 'ReflectionClass' );
		}

		$this->STR_EMPTY_ARRAY                = _x( 'Empty Array', 'PrettyPrint message', 'debugpress' );
		$this->STR_NO_PROPERTIES              = _x( 'No Properties', 'PrettyPrint message', 'debugpress' );
		$this->STR_NO_METHODS                 = _x( 'No Methods', 'PrettyPrint message', 'debugpress' );
		$this->STR_INFINITE_RECURSION_WARNING = _x( 'Infinite Recursion Detected!', 'PrettyPrint message', 'debugpress' );
		$this->STR_STR_DESC                   = _x( '%d characters', 'PrettyPrint message', 'debugpress' );
		$this->STR_RES_DESC                   = _x( '%s type', 'PrettyPrint message', 'debugpress' );
		$this->STR_ARR_DESC                   = _x( '%d elements', 'PrettyPrint message', 'debugpress' );
		$this->STR_OBJ_DESC                   = _x( '%d properties', 'PrettyPrint message', 'debugpress' );
		$this->STR_FOOTER_CALL                = _x( 'Called From', 'PrettyPrint message', 'debugpress' );
		$this->STR_FOOTER_LINE                = _x( 'line', 'PrettyPrint message', 'debugpress' );
	}

	public static function init() : PrettyPrint {
		if ( null === self::$instance ) {
			self::$instance = new PrettyPrint();
		}

		return self::$instance;
	}

	public static function instance( $value, $footer = true, $collapsed = true, $inspect_methods = true ) : PrettyPrint {
		if ( null === self::$instance ) {
			self::$instance = new PrettyPrint();
		}

		self::$instance->is_root         = true;
		self::$instance->value           = $value;
		self::$instance->collapsed       = $collapsed;
		self::$instance->inspect_methods = $inspect_methods;
		self::$instance->display_footer  = $footer;

		return self::$instance;
	}

	public function generate() : string {
		return $this->_generate_root( $this->value, $this->css_class );
	}

	public function render() {
		echo $this->generate(); // phpcs:ignore WordPress.Security.EscapeOutput
	}

	protected function _esc_html( $text ) : string {
		return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
	}

	protected function _generate_keyvalues( $array, &$html ) : bool {
		$has_subitems = false;

		foreach ( $array as $k => $v ) {
			if ( $k !== self::$been_there ) {
				$html         .= $this->_generate_keyvalue( $k, $v );
				$has_subitems = true;
			}
		}

		return $has_subitems;
	}

	protected function _inspect_array( &$html, &$var ) {
		if ( ! $this->_generate_keyvalues( $var, $html ) ) {
			$html .= '<span class="' . $this->css_class . '_ni">' . $this->STR_EMPTY_ARRAY . '</span>';
		}
	}

	protected function _inspect_object( &$html, &$var ) {
		if ( ! $this->_generate_keyvalues( (array) $var, $html ) ) {
			$html .= '<span class="' . $this->css_class . '_ni">' . $this->STR_NO_PROPERTIES . '</span>';
		}

		if ( $this->inspect_methods ) {
			$has_subitems = false;

			foreach ( (array) get_class_methods( $var ) as $method ) {
				$html         .= $this->_generate_callable( $var, $method );
				$has_subitems = true;
			}

			if ( ! $has_subitems ) {
				$html .= '<span class="' . $this->css_class . '_ni">' . $this->STR_NO_METHODS . '</span>';
			}
		}
	}

	protected function _generate_root( $var, $class = '' ) : string {
		$html = '<div class="' . $class . ' ' . $this->css_class . '_root">';

		$root_wrapper = false;
		if ( $this->collapsed && ( is_array( $var ) || is_object( $var ) ) ) {
			$root_wrapper = true;
		}

		$id  = '';
		$cls = $class;

		if ( $root_wrapper ) {
			$t = gettype( $var );
			$d = '';

			switch ( $t ) {
				case 'array':
					$d .= ', ' . sprintf( $this->STR_ARR_DESC, count( $var ) );
					break;
				case 'object':
					$d .= ', <span class="' . $cls . '_rn">' . get_class( $var ) . '</span>, ' . sprintf( $this->STR_OBJ_DESC, count( get_object_vars( $var ) ) );
					break;
			}

			$id = $this->html_id . '_v' . $this->_generate_dropid();

			$html .= '<a role="button" class="' . $cls . '_c ' . $cls . '_aa" href="#" data-branch="' . $id . '">';
			$html .= '<span class="' . $cls . '_a">' . $this->ICON_RIGHT . '</span>';
			$html .= '<span class="' . $cls . '_k">&middot &middot &middot &middot &middot</span>';
			$html .= '<span class="' . $cls . '_d">(<span>' . ucwords( $t ) . '</span>' . $d . ')</span>';
			$html .= '</a>';

			$cls .= '_v';
		}

		$html .= $this->_generate_value( $var, $cls, $id );

		if ( $this->display_footer ) {
			$_ = debug_backtrace(); // phpcs:ignore WordPress.PHP.DevelopmentFunctions

			while ( $d = array_pop( $_ ) ) { // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition
				if ( ( strToLower( $d[ 'function' ] ) == 'debugpress_r' ) || ( strToLower( $d[ 'function' ] ) == 'debugpress_rx' ) ) {
					break;
				}
			}

			$html .= $this->_generate_footer( $d );
		}

		$html .= '</div>';

		return $html;
	}

	protected function _generate_value( $var, $class = '', $id = '' ) : string {
		$BEENTHERE = self::$been_there;
		$class     .= ' ' . $this->css_class . '_t_' . gettype( $var );

		$html = '<div id="' . $id . '" class="' . $class . '">';

		switch ( true ) {
			case is_array( $var ):
				if ( isset( $var[ $BEENTHERE ] ) ) {
					$html .= '<span class="' . $this->css_class . '_ir">' . $this->STR_INFINITE_RECURSION_WARNING . '</span>';
				} else {
					$var[ $BEENTHERE ] = true;

					$this->_inspect_array( $html, $var );

					unset( $var[ $BEENTHERE ] );
				}
				break;
			case is_object( $var ):
				if ( isset( $var->$BEENTHERE ) ) {
					$html .= '<span class="' . $this->css_class . '_ir">' . $this->STR_INFINITE_RECURSION_WARNING . '</span>';
				} else {
					$var->$BEENTHERE = true;

					$this->_inspect_object( $html, $var );

					unset( $var->$BEENTHERE );
				}
				break;
			default:
				$html .= $this->_generate_keyvalue( '', $var );
				break;
		}

		$html .= '</div>';

		return $html;
	}

	protected function _generate_footer( $d ) : string {
		$footer = '<div class="' . $this->css_class . '_f">';
		$footer .= $this->STR_FOOTER_CALL . ' <code>' . $d[ 'file' ] . '</code> ';
		$footer .= $this->STR_FOOTER_LINE . ' <code>' . $d[ 'line' ] . '</code>';
		$footer .= '</div>';

		return $footer;
	}

	protected function _generate_dropid() : int {
		static $id = 0;

		return ++ $id;
	}

	protected function _generate_keyvalue( $key, $val ) : string {
		$id      = $this->_generate_dropid();
		$p       = '';
		$d       = '';
		$t       = gettype( $val );
		$is_hash = ( $t == 'array' ) || ( $t == 'object' );

		switch ( $t ) {
			case 'boolean':
				$p = $val ? 'TRUE' : 'FALSE';
				break;
			case 'integer':
			case 'double':
				$p = (string) $val;
				break;
			case 'string':
				$d .= ', ' . sprintf( $this->STR_STR_DESC, strlen( $val ) );
				$p = $val;
				break;
			case 'resource':
				$d .= ', ' . sprintf( $this->STR_RES_DESC, get_resource_type( $val ) );
				$p = (string) $val;
				break;
			case 'array':
				$d .= ', ' . sprintf( $this->STR_ARR_DESC, count( $val ) );
				break;
			case 'object':
				$d .= ', ' . get_class( $val ) . ', ' . sprintf( $this->STR_OBJ_DESC, count( get_object_vars( $val ) ) );
				break;
		}

		$cls  = $this->css_class;
		$xcls = ! $is_hash ? $cls . '_ad' : $cls . '_aa';

		$html = '<a role="button" class="' . $cls . '_c ' . $xcls . '" href="#" data-branch="' . $this->html_id . '_v' . $id . '">';
		$html .= '<span class="' . $cls . '_a">' . $this->ICON_RIGHT . '</span>';
		$html .= '<span class="' . $cls . '_k">' . $this->_esc_html( $key ) . '</span>';
		$html .= '<span class="' . $cls . '_d">(<span>' . ucwords( $t ) . '</span>' . $d . ')</span>';
		$html .= '<span class="' . $cls . '_p ' . $cls . '_t_' . $t . '">' . $this->_esc_html( $p ) . '</span>';
		$html .= '</a>';

		if ( $is_hash ) {
			$html .= $this->_generate_value( $val, $cls . '_v', $this->html_id . '_v' . $id );
		}

		return $html;
	}

	protected function _format_phpdoc( $doc ) : string {
		$doc = $this->_esc_html( $doc );

		$doc = preg_replace( '/(\\n)\\s+?\\*([\\s\\/])/', '$1 *$2', $doc );
		$doc = preg_replace( '/(\\s)(@\\w+)/', '$1<b>$2</b>', $doc );
		$doc = nl2br( str_replace( ' ', '&nbsp;', $doc ) );

		$doc = preg_replace( '/(((f|ht){1}tp:\\/\\/)[-a-zA-Z0-9@:%_\\+.~#?&\\/\\/=]+)/', '<a href="$1">$1</a>', $doc );
		$doc = preg_replace( '/(www\\.[-a-zA-Z0-9@:%_\\+.~#?&\\/=]+)/', '<a href="http://$1">$1</a>', $doc );
		$doc = preg_replace( '/([_\\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\\.)+[a-z]{2,3})/', '<a href="mailto:$1">$1</a>', $doc );

		return $doc;
	}

	protected function _generate_callable( $context, $callback ) : string {
		$id   = $this->_generate_dropid();
		$ref  = null;
		$name = 'Anonymous';
		$cls  = $this->css_class;
		$mods = array();

		if ( $this->_has_reflection ) {
			if ( is_null( $context ) ) {
				$ref = new ReflectionFunction( $callback );
			} else {
				$ref = new ReflectionMethod( $context, $callback );

				foreach (
					array(
						'abstract'    => $ref->isAbstract(),
						'constructor' => $ref->isConstructor(),
						'deprecated'  => $ref->isDeprecated(),
						'destructor'  => $ref->isDestructor(),
						'final'       => $ref->isFinal(),
						'internal'    => $ref->isInternal(),
						'private'     => $ref->isPrivate(),
						'protected'   => $ref->isProtected(),
						'public'      => $ref->isPublic(),
						'static'      => $ref->isStatic(),
						'magic'       => substr( $ref->name, 0, 2 ) === '__',
						'returnsRef'  => $ref->returnsReference(),
						'inherited'   => get_class( $context ) !== $ref->getDeclaringClass()->name
					) as $name => $cond
				) {
					if ( $cond ) {
						$mods[] = $name;
					}
				}
			}

			$name = $ref->getName();
		} else if ( is_string( $callback ) ) {
			$name = $callback;
		}

		if ( ! is_null( $ref ) ) {
			$doc  = $this->display_docs ? $ref->getDocComment() : null;
			$prms = array();

			foreach ( $ref->getParameters() as $p ) {
				$prms[] = '$' . $p->getName() . ( $p->isDefaultValueAvailable() ? ' = <span class="' . $cls . '_mv">' . var_export( $p->getDefaultValue(), true ) . '</span>' : '' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			}
		} else {
			$doc  = null;
			$prms = array( '???' );
		}

		$xcls = is_null( $doc ) || $doc === false ? $cls . '_ad' : $cls . '_aa';

		$hmod = implode( ' ', array_intersect( $mods, $this->_visible_mods ) );

		foreach ( $mods as $mod ) {
			$xcls .= ' ' . $this->css_class . '_m_' . $mod;
		}

		if ( $hmod != '' ) {
			$hmod = '<span class="' . $this->css_class . '_mo">' . $hmod . '</span> ';
		}

		$html = '<a role="button" class="' . $cls . '_c ' . $cls . '_m ' . $xcls . '" href="#" data-branch="' . $this->html_id . '_v' . $id . '">';
		$html .= '<span class="' . $cls . '_a" id="' . $this->html_id . '_a' . $id . '">' . $this->ICON_RIGHT . '</span>';
		$html .= '<span class="' . $cls . '_k">' . $hmod . $this->_esc_html( $name ) . '<span class="' . $cls . '_ma">(<span>' . implode( ', ', $prms ) . '</span>)</span></span>';
		$html .= '</a>';

		if ( $doc ) {
			$html .= '<div id="' . $this->html_id . '_v' . $id . '" class="' . $this->css_class . '_v ' . $this->css_class . '_t_comment">';
			$html .= $this->_format_phpdoc( $doc );
			$html .= '</div>';
		}

		return $html;
	}
}