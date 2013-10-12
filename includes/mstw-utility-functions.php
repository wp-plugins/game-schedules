<?php
/*
 *	MSTW Functions
 *	A set functions useful across the MSTW plugins that we want to require_once()
 *
 *
 */

/*------------------------------------------------------------------
 *	Check $color for proper hex color format (3 or 6 digits) 
 *	or the empty string. Returns corrected string if valid hex color, 
 *	returns null otherwise
 *----------------------------------------------------------------*/ 

function mstw_sanitize_hex_color( $color ) {
	if ( '' === $color )
		return '';

	// 3 or 6 hex digits, or the empty string.
	if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) )
		return $color;

	return null;
}

/*------------------------------------------------------------------
 *	Sets the (php) default timezone to the default timezone on the 
 *	Wordpress General Settings page. 
 *	No arguments and no return value.
 *----------------------------------------------------------------*/
function mstw_set_wp_default_timezone( ) {
	$tz = get_option( 'timezone_string' );
	if ( $tz && function_exists( 'date_default_timezone_set' ) ) {
		date_default_timezone_set( $tz );
	}
}

function mstw_sanitize_number ( $number ) {

}
?>