<?php

function debugpress_has_bbpress() {
	if ( function_exists( 'bbp_get_version' ) ) {
		$version = bbp_get_version();
		$version = intval( substr( str_replace( '.', '', $version ), 0, 2 ) );

		return $version > 24;
	} else {
		return false;
	}
}

function debugpress_store_object( $object, $title = '', $db = false, $sql = false ) {
	debugpress_tracker()->log( $object, $title, $db, $sql );
}
