<?php
//----------------------------------------------------------------	
//	Builds date format controls for the admin UI
//
// 	Arguments:
//	string $args['name']: 		name of option 
//	string $args['id']: 		id of option
//	string $args['curr_value']:	current value of option
//	string $args['dtg']: 		show entire date-time group or only the date
//								'date-time' -> use date-time, 
//								'date-only' -> use date only
//
//	return - none. Output is echoed.
//---------------------------------------------------------------
	function mstw_utl_date_format_ctrl( $args ) {
	
		//extract( $args );
		$name = $args['name'];
		$id = $args['id'];
		$curr_value = $args['curr_value'];
		$dtg = $args['dtg'];
		
		//echo '<pre>'; print_r( $args ); echo '</pre>';

		$mstw_utl_date_formats = array ( 
			__( 'Custom', 'mstw-loc-domain' ) => 'custom',
			'2013-04-07' => 'Y-m-d',
			'13-04-07' => 'y-m-d',
			'04/07/13' => 'm/d/y',
			'4/7/13' => 'n/j/y',
			__( '07 Apr 2013', 'mstw-loc-domain' ) => 'd M Y',
			__( '7 Apr 2013', 'mstw-loc-domain' ) => 'j M Y',
			__( 'Tues, 07 Apr 2013', 'mstw-loc-domain' ) => 'D, d M Y',
			__( 'Tues, 7 Apr 13', 'mstw-loc-domain' ) => 'D, j M y',
			__( 'Tuesday, 07 April 2013', 'mstw-loc-domain' ) => 'l, d F Y',
			__( 'Tuesday, 7 April 2013', 'mstw-loc-domain' ) => 'l, j F Y',
			__( 'Tues, 07 Apr', 'mstw-loc-domain' ) => 'D, d M',
			__( 'Tues, 7 Apr', 'mstw-loc-domain' ) => 'D, j M',
			__( '07 Apr', 'mstw-loc-domain' ) => 'd M',
			__( '7 Apr', 'mstw-loc-domain' ) => 'j M',
			);
			
		$mstw_utl_date_time_formats = array ( 
			__( 'Custom', 'mstw-loc-domain' ) => 'custom',
			__( 'Tuesday, 07 April 01:15 pm', 'mstw-loc-domain' ) => 'l, d M h:i a',
			__( 'Tuesday, 7 April 01:15 pm', 'mstw-loc-domain' ) => 'l, j M h:i a',
			__( 'Tuesday, 07 April 1:15 pm', 'mstw-loc-domain' ) => 'l, d M g:i a',
			__( 'Tuesday, 7 April 1:15 pm', 'mstw-loc-domain' ) => 'l, j M g:i a',
			__( 'Tuesday, 7 April 13:15', 'mstw-loc-domain' ) => 'l, d M H:i',
			__( 'Tuesday, 7 April 13:15', 'mstw-loc-domain' ) => 'l, j M H:i',
			__( '07 April 13:15', 'mstw-loc-domain' ) => 'd M H:i',
			__( '7 April 13:15', 'mstw-loc-domain' ) => 'j M H:i',
			__( '07 April 01:15 pm', 'mstw-loc-domain' ) => 'd M g:i a',
			__( '7 April 01:15 pm', 'mstw-loc-domain' ) => 'j M g:i a',		
			);
		
		if ( $dtg == 'date-time' ) {
			$loop_array = $mstw_utl_date_time_formats;
			$label = __( 'Formats for', 'mstw-loc-domain' ) . " " . __( '7 April 2013 13:15', 'mstw-loc-domain' );
		}
		else {
			$loop_array = $mstw_utl_date_formats;
			$label =  __( 'Formats for', 'mstw-loc-domain' ) . " " . __( '7 April 2013', 'mstw-loc-domain' );
		}
		
		echo "<select id='$id' name='$name' style='width: 160px' >";
		foreach( $loop_array as $key=>$value ) {
			$selected = ( $curr_value == $value ) ? 'selected="selected"' : '';
			echo "<option value='$value' $selected>$key</option>";
		}
		echo "</select> \n";
		echo "<label for='$id'>$label</label> \n";
		
	}
	
//----------------------------------------------------------------	
//	Builds time format controls for the admin UI
//
// 	Arguments:
//	string $args['name']: 		name of option 
//	string $args['id']: 		id of option
//	string $args['curr_value']:	current value of option
//
//	return - none. Output is echoed.
//---------------------------------------------------------------
	function mstw_utl_time_format_ctrl( $args ) {
		$mstw_utl_time_formats = array ( 
			__( 'Custom', 'mstw-loc-domain' ) 		=> 'custom',
			__( '08:00 (24hr)', 'mstw-loc-domain' ) => 'H:i',
			__( '8:00 (24hr)', 'mstw-loc-domain' ) 	=> 'G:i',
			__( '08:00 am', 'mstw-loc-domain' ) 	=> 'h:i a',
			__( '08:00 AM', 'mstw-loc-domain' ) 	=> 'h:i A',
			__( '8:00 am', 'mstw-loc-domain' ) 		=> 'g:i a',
			__( '8:00 AM', 'mstw-loc-domain' ) 		=> 'g:i A',
			);
		
		extract( $args );
		//$name = $args['name'];
		//$id = $args['id'];
		//$curr_value = $args['curr_value'];
		
		$loop_array = $mstw_utl_time_formats;
		$label = __( 'Formats for eight in the morning', 'mstw-loc-domain' ) . " 08:00";
		
		echo "<select id='$id' name='$name' style='width: 160px' >";
		foreach( $loop_array as $key=>$value ) {
			$selected = ( $curr_value == $value ) ? 'selected="selected"' : '';
			echo "<option value='$value' $selected>$key</option>";
		}
		echo "</select> \n";
		echo "<label for='$id'>$label</label> \n";	
	}
?>