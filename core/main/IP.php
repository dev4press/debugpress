<?php

namespace Dev4Press\Plugin\DebugPress\Main;

class IP {
	protected static $private_ipv4 = array(
		'10.0.0.0/8',
		'127.0.0.0/8',
		'172.16.0.0/12',
		'192.168.0.0/16'
	);

	protected static $private_ipv6 = array(
		'::1/128',
		'fd00::/8'
	);

	protected static $cloudflare_ipv4 = array(
		'173.245.48.0/20',
		'103.21.244.0/22',
		'103.22.200.0/22',
		'103.31.4.0/22',
		'141.101.64.0/18',
		'108.162.192.0/18',
		'190.93.240.0/20',
		'188.114.96.0/20',
		'197.234.240.0/22',
		'198.41.128.0/17',
		'162.158.0.0/15',
		'104.16.0.0/12',
		'172.64.0.0/13',
		'131.0.72.0/22'
	);

	protected static $cloudflare_ipv6 = array(
		'2400:cb00::/32',
		'2606:4700::/32',
		'2803:f800::/32',
		'2405:b500::/32',
		'2405:8100::/32',
		'2a06:98c0::/29',
		'2c0f:f248::/32'
	);

	public static function is_ipv4_in_range( $ip, $range ) {
		if ( strpos( $range, '/' ) !== false ) {
			list( $range, $netmask ) = explode( '/', $range, 2 );

			if ( strpos( $netmask, '.' ) !== false ) {
				$netmask     = str_replace( '*', '0', $netmask );
				$netmask_dec = ip2long( $netmask );

				return ( ( ip2long( $ip ) & $netmask_dec ) == ( ip2long( $range ) & $netmask_dec ) );
			} else {
				$x = explode( '.', $range );

				while ( count( $x ) < 4 ) {
					$x[] = '0';
				}

				list( $a, $b, $c, $d ) = $x;

				$range = sprintf( "%u.%u.%u.%u",
					empty( $a ) ? '0' : $a,
					empty( $b ) ? '0' : $b,
					empty( $c ) ? '0' : $c,
					empty( $d ) ? '0' : $d
				);

				$range_dec = ip2long( $range );
				$ip_dec    = ip2long( $ip );

				$wildcard_dec = pow( 2, ( 32 - $netmask ) ) - 1;
				$netmask_dec  = ~$wildcard_dec;

				return ( ( $ip_dec & $netmask_dec ) == ( $range_dec & $netmask_dec ) );
			}
		} else {
			if ( strpos( $range, '*' ) !== false ) {
				$lower = str_replace( '*', '0', $range );
				$upper = str_replace( '*', '255', $range );
				$range = "$lower-$upper";
			}

			if ( strpos( $range, '-' ) !== false ) {
				list( $lower, $upper ) = explode( '-', $range, 2 );

				$lower_dec = (float) sprintf( "%u", ip2long( $lower ) );
				$upper_dec = (float) sprintf( "%u", ip2long( $upper ) );
				$ip_dec    = (float) sprintf( "%u", ip2long( $ip ) );

				return ( ( $ip_dec >= $lower_dec ) && ( $ip_dec <= $upper_dec ) );
			}

			return false;
		}
	}

	public static function is_ipv6_in_range( $ip, $range ) {
		$pieces = explode( '/', $range, 2 );

		$left_piece  = $pieces[0];
		$right_piece = $pieces[1];

		$ip_pieces     = explode( '::', $left_piece, 2 );
		$main_ip_piece = $ip_pieces[0];
		$last_ip_piece = $ip_pieces[1];

		$main_ip_pieces = explode( ":", $main_ip_piece );
		foreach ( $main_ip_pieces as $key => $val ) {
			$main_ip_pieces[ $key ] = str_pad( $val, 4, '0', STR_PAD_LEFT );
		}

		$first = $main_ip_pieces;
		$last  = $main_ip_pieces;

		$last_piece = '';
		$size       = count( $main_ip_pieces );

		if ( trim( $last_ip_piece ) != '' ) {
			$last_piece = str_pad( $last_ip_piece, 4, '0', STR_PAD_LEFT );

			for ( $i = $size; $i < 7; $i ++ ) {
				$first[ $i ] = '0000';
				$last[ $i ]  = 'ffff';
			}

			$main_ip_pieces[7] = $last_piece;
		} else {
			for ( $i = $size; $i < 8; $i ++ ) {
				$first[ $i ] = '0000';
				$last[ $i ]  = 'ffff';
			}
		}

		$first = self::ip2long6( implode( ':', $first ) );
		$last  = self::ip2long6( implode( ':', $last ) );

		return ( $ip >= $first && $ip <= $last );
	}

	public static function get_full_ipv6( $ip ) {
		$pieces      = explode( '/', $ip, 2 );
		$left_piece  = $pieces[0];
		$right_piece = null;

		if ( count( $pieces ) > 1 ) {
			$right_piece = $pieces[1];
		}

		$ip_pieces     = explode( "::", $left_piece, 2 );
		$main_ip_piece = $ip_pieces[0];
		$last_ip_piece = null;
		if ( count( $ip_pieces ) > 1 ) {
			$last_ip_piece = $ip_pieces[1];
		}

		$main_ip_pieces = explode( ':', $main_ip_piece );
		foreach ( $main_ip_pieces as $key => $val ) {
			$main_ip_pieces[ $key ] = str_pad( $val, 4, '0', STR_PAD_LEFT );
		}

		$last_piece = "";
		$size       = count( $main_ip_pieces );
		if ( trim( $last_ip_piece ) != '' ) {
			$last_piece = str_pad( $last_ip_piece, 4, '0', STR_PAD_LEFT );

			for ( $i = $size; $i < 7; $i ++ ) {
				$main_ip_pieces[ $i ] = '0000';
			}

			$main_ip_pieces[7] = $last_piece;
		} else {
			for ( $i = $size; $i < 8; $i ++ ) {
				$main_ip_pieces[ $i ] = '0000';
			}
		}

		$final_ip = implode( ':', $main_ip_pieces );

		return self::ip2long6( $final_ip );
	}

	public static function ip2long6( $ip ) {
		if ( substr_count( $ip, '::' ) ) {
			$ip = str_replace( '::', str_repeat( ':0000', 8 - substr_count( $ip, ':' ) ) . ':', $ip );
		}

		$ip = explode( ':', $ip );

		$r_ip = '';
		foreach ( $ip as $v ) {
			$r_ip .= str_pad( base_convert( $v, 16, 2 ), 16, 0, STR_PAD_LEFT );
		}

		return base_convert( $r_ip, 2, 10 );
	}

	public static function is_private_ip( $ip = null ) {
		if ( is_null( $ip ) ) {
			$ip = IP::get_visitor_ip();
		}

		if ( strpos( $ip, ':' ) === false ) {
			foreach ( IP::$private_ipv4 as $cf ) {
				if ( IP::is_ipv4_in_range( $ip, $cf ) ) {
					return true;
				}
			}
		} else {
			foreach ( IP::$private_ipv6 as $cf ) {
				if ( IP::is_ipv6_in_range( $ip, $cf ) ) {
					return true;
				}
			}
		}

		return false;
	}

	public static function is_cloudflare_ip( $ip = null ) {
		if ( is_null( $ip ) ) {
			if ( isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
				if ( isset( $_SERVER['HTTP_X_REAL_IP'] ) ) {
					$ip = $_SERVER['HTTP_X_REAL_IP'];
				} else {
					$ip = $_SERVER['REMOTE_ADDR'];
				}
			} else {
				return false;
			}
		}

		if ( strpos( $ip, ':' ) === false ) {
			foreach ( IP::$cloudflare_ipv4 as $cf ) {
				if ( IP::is_ipv4_in_range( $ip, $cf ) ) {
					return true;
				}
			}
		} else {
			foreach ( IP::$cloudflare_ipv6 as $cf ) {
				if ( IP::is_ipv6_in_range( $ip, $cf ) ) {
					return true;
				}
			}
		}

		return false;
	}

	public static function get_server_ip() {
		$ip = IP::validate_ip( $_SERVER['SERVER_ADDR'] );

		if ( $ip == '::1' ) {
			$ip = '127.0.0.1';
		}

		return $ip;
	}

	public static function get_all_ips() {
		$keys = array(
			'HTTP_CF_CONNECTING_IP',
			'HTTP_CLIENT_IP',
			'HTTP_X_REAL_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR',
			'SERVER_ADDR'
		);

		$ips = array();

		foreach ( $keys as $key ) {
			if ( isset( $_SERVER[ $key ] ) ) {
				$ips[ $key ] = $_SERVER[ $key ];
			}
		}

		return $ips;
	}

	public static function get_visitor_ip( $no_local_or_protected = false ) {
		if ( IP::is_cloudflare_ip() ) {
			return IP::validate_ip( $_SERVER['HTTP_CF_CONNECTING_IP'], true );
		}

		$keys = array(
			'HTTP_CLIENT_IP',
			'HTTP_X_REAL_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR'
		);

		$ip = '';

		foreach ( $keys as $key ) {
			if ( array_key_exists( $key, $_SERVER ) === true ) {
				$ip = $_SERVER[ $key ];
				break;
			}
		}

		if ( $no_local_or_protected ) {
			$ip = IP::validate_ip( $ip, true );
		} else {
			if ( $ip == '::1' ) {
				$ip = '127.0.0.1';
			} else if ( $ip != '' ) {
				$ip = IP::cleanup_ip( $ip );
			}
		}

		return $ip;
	}

	public static function validate_ip( $ip, $no_local_or_protected = false ) {
		$ips = explode( ',', $ip );

		foreach ( $ips as $_ip ) {
			$_ip = trim( $_ip );

			if ( $no_local_or_protected ) {
				if ( filter_var( $_ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false ) {
					return $_ip;
				}
			} else {
				if ( filter_var( $_ip, FILTER_VALIDATE_IP ) !== false ) {
					return $_ip;
				}
			}
		}

		return false;
	}

	public static function cleanup_ip( $ip ) {
		if ( preg_replace( '/[^0-9a-fA-F:., ]/', '', $ip ) ) {
			$ips = explode( ',', $ip );

			return trim( $ips[ count( $ips ) - 1 ] );
		} else {
			return false;
		}
	}
}
