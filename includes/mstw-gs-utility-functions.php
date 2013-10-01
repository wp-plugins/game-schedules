<?php
/*----------------------------------------------------------
 *	MSTW-GS-UTILITY-FUNCTIONS.PHP
 *	mstr_gs_set_options() - returns the default option settings
 * 
 *---------------------------------------------------------*/
 
/*----------------------------------------------------------
 *	mstw_gs_utility_fuctions_loaded: DO NOT DELETE
 *		It does nothing EXCEPT indicate whether or not the file is loaded!!
 *---------------------------------------------------------*/
	function mstw_gs_utility_functions_loaded( ) {
		return true;
	}

/*---------------------------------------------------------------------------------
 *	mstw_gs_get_defaults: returns the array of option defaults
 *-------------------------------------------------------------------------------*/	
	function mstw_gs_get_defaults( ) {
		//Base defaults
		$defaults = array(
				//default schedule table shortcode arguments
				'sched' => 1,  // This is used for cdt & slider shortcodes too
				'first_dtg' => '1970:01:01 00:00:00',	// first php dtg
				'last_dtg' => '2038:01:19 00:00:00', 	// last php dtg (roughly)
				'games_to_show' => -1,
				
				//default cdt shortcode arguments
				'home_only' 		=> 0,
				'intro'				=> __( 'Time to kickoff', 'mstw-loc-domain' ),
				
				//default slider shortcode arguments
				
				
				
				//show/hide date fields and default labels
				'show_date'				=> 1,
				'date_label'			=> __( 'Date', 'mstw-loc-domain' ),
				'opponent_label'		=> __( 'Opponent', 'mstw-loc-domain' ),
				'show_location'			=> 1,
				'location_label'		=> __( 'Location', 'mstw-loc-domain' ),
				'show_time'				=> 1,
				'time_label'			=> __( 'Time/Result', 'mstw-loc-domain' ),
				'show_media'			=> 3,
				'media_label'			=> __( 'Media Links', 'mstw-loc-domain' ),
				
				//date and time format defaults
				'admin_date_format' 	=>'Y-m-d',
				'custom_admin_date_format' => '',
				'admin_time_format'		=> 'H:i',
				'custom_admin_time_format' => '',
				'table_date_format'		=> 'Y m d',
				'custom_table_date_format' => '',
				'table_time_format'		=> 'H:i',
				'custom_table_time_format' => '',
				'table_widget_date_format' => 'j M y',
				'custom_table_widget_date_format' => '',
				'cdt_dtg_format'		=> 'l, j M g:i a',
				'custom_cdt_dtg_format' => '',
				'cdt_date_format'		=> 'l, j M',
				'custom_cdt_date_format' => '',
				'slider_date_format'	=> 'D, j M',
				'custom_slider_date_format' => '',
				'slider_time_format'	=> 'g:i A',
				'custom_slider_time_format' => '',
				);
				
		return $defaults;
	}
				
	?>
