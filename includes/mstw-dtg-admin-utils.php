<?php
/*----------------------------------------------------------------	
 *	Builds date format controls for the admin UI
 *
 * 	Arguments:
 *	$args['opt_name'] (string) name of option (array) 
 *	$args['set_name'] (string) setting name  from option array
 *	$args['set_default'] (string) default to use of setting is blank
 *	$args['cdt'] (boolean) true -> this is countdown timer date setting
 *		true -> use date-time, false -> use date only
 *
 *	return - none. Output is echoed.
 *---------------------------------------------------------------*/
	function mstw_utl_date_format_ctrl( $args ) {

		$mstw_utl_date_formats = array ( 
			__( 'Custom', 'mstw-loc-domain' ) 	=> 'custom',
			'2013-04-07' => 'Y-m-d',
			'13-04-07' => 'y-m-d',
			'04/07/13' => 'm/d/y',
			'4/7/13' => 'n/j/y',
			__( '7 Apr 2013', 'mstw-loc-domain' ) => 'j M Y',
			__( '07 Apr 2013', 'mstw-loc-domain' ) => 'd M Y',
			__( '7 Apr 2013', 'mstw-loc-domain' ) => 'j M Y',
			__( '07 Apr 2013', 'mstw-loc-domain' ) => 'd M Y',
			__( '07 Apr 2013', 'mstw-loc-domain' ) => 'j M Y',
			__( 'Tues, 07 Apr 2013', 'mstw-loc-domain' ) => 'D, d M Y',
			__( 'Tues, 7 Apr 13', 'mstw-loc-domain' ) => 'D, j M y',
			__( 'Tuesday, 07 April 2013', 'mstw-loc-domain' ) => 'l, d F Y',
			__( 'Tuesday, 07 April 2013', 'mstw-loc-domain' ) => 'l, j F Y',
			__( 'Tuesday, 07 Apr', 'mstw-loc-domain' ) => 'l, d M',
			__( 'Tues, 7 Apr', 'mstw-loc-domain' ) => 'D, j M',
			__( '7 Apr', 'mstw-loc-domain' ) => 'j M',
			__( '07 Apr', 'mstw-loc-domain' ) => 'd M',
			);
			
	$mstw_utl_date_time_formats = array ( 
			__( 'Custom', 'mstw-loc-domain' ) 	=> 'custom',
			__( 'Tuesday, 07 April 01:15 pm', 'mstw-loc-domain' ) => 'l, d M h:i a',
			__( 'Tuesday, 7 April 1:15 pm', 'mstw-loc-domain' ) => 'l, j M g:i a',
			__( 'Tuesday, 07 April 13:15', 'mstw-loc-domain' ) => 'l, d M H:i',
			__( 'Tuesday, 7 April 13:15', 'mstw-loc-domain' ) => 'l, j M H:i',
			__( '07 April 13:15', 'mstw-loc-domain' ) => 'd M H:i',
			__( '7 April 13:15', 'mstw-loc-domain' ) => 'j M H:i',
			__( '07 April 01:15 pm', 'mstw-loc-domain' ) => 'd M g:i a',
			__( '7 April 01:15 pm', 'mstw-loc-domain' ) => 'j M g:i a',		
			);
		
		/*
		if ( !isset( $mstw_date_formats ) ) {
			include 'mstw-date-format-array.php';
		}
		*/
		
		$opt_name = $args['opt_name'];
		$set_name = $args['set_name'];
		$set_default = $args['set_default'];
		$cdt = $args['cdt'];
		
		if ( $cdt ) {
			$loop_array = $mstw_utl_date_time_formats;
		}
		else {
			$loop_array = $mstw_utl_date_formats;
		}
		
		// get option value from the database
		$options = get_option( $opt_name );
		if ( ( $dtg_format = $options[$set_name] ) == '' )
			$dtg_format = $set_default;
			
		echo "<select id=$set_name name='mstw_gs_options[$set_name]'>";
		foreach( $loop_array as $key=>$value ) {
			//echo '<p> key: ' . $key . ' value: ' . $value .'</p>';
			$selected = ( $dtg_format == $value ) ? 'selected="selected"' : '';
			echo "<option value='$value' $selected>$key</option>";
		}
		if ( $cdt ) {
			echo "</select>" . __( 'Formats for', 'mstw-loc-domain' ) . " " . __( '7 April 2013 13:15', 'mstw-loc-domain' );
		}
		else {
			echo "</select>" . __( 'Formats for', 'mstw-loc-domain' ) . " " . __( '7 April 2013', 'mstw-loc-domain' );
		}
		
	}
	
/*----------------------------------------------------------------	
 *	Builds time format controls for the admin UI
 *
 * 	Arguments:
 *	$args['opt_name'] (string) name of option (array) 
 *	$args['set_name'] (string) setting name  from option array
 *	$args['set_default'] (string) default to use of setting is blank
 *
 *	return - none. Output is echoed.
 *---------------------------------------------------------------*/
	function mstw_utl_time_format_ctrl( $args ) {
		// need the $mstw_time_formats array
		$mstw_utl_time_formats = array ( 
			__( 'Custom', 'mstw-loc-domain' ) 		=> 'custom',
			__( '08:00 (24hr)', 'mstw-loc-domain' ) => 'H:i',
			__( '8:00 (24hr)', 'mstw-loc-domain' ) 	=> 'G:i',
			__( '08:00 am', 'mstw-loc-domain' ) 	=> 'h:i a',
			__( '08:00 AM', 'mstw-loc-domain' ) 	=> 'h:i A',
			__( '8:00 am', 'mstw-loc-domain' ) 		=> 'g:i a',
			__( '8:00 AM', 'mstw-loc-domain' ) 		=> 'g:i A',
			);
		
		/*if ( !isset( $mstw_time_formats ) ) {
			include 'mstw-time-format-array.php';
		}*/
		
		$opt_name = $args['opt_name'];
		$set_name = $args['set_name'];
		$set_default = $args['set_default'];
		
		// get option value from the database
		$options = get_option( $opt_name );
		if ( ( $time_format = $options[$set_name] ) == '' )
			$time_format = $set_default;
			
		echo "<select id=$set_name name='mstw_gs_options[$set_name]' style='width: 160px' >";
		foreach( $mstw_utl_time_formats as $key=>$value ) {
			//echo '<p> key: ' . $key . ' value: ' . $value .'</p>';
			$selected = ( $time_format == $value ) ? 'selected="selected"' : '';
			echo "<option value='$value' $selected>$key</option>";
		}
		
		echo "</select>" . __( 'Formats for eight in the morning', 'mstw-loc-domain' ) . " 08:00";
		
	}
?>