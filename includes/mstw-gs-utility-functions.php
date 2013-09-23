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
				'show_date'				=> 1,
				'date_label'			=> __( 'Date', 'mstw-loc-domain' ),
				'opponent_label'		=> __( 'Opponent', 'mstw-loc-domain' ),
				'show_location'			=> 1,
				'location_label'		=> __( 'Location', 'mstw-loc-domain' ),
				'show_time'				=> 1,
				'time_label'			=> __( 'Time/Result', 'mstw-loc-domain' ),
				'show_media'			=> 3,
				'media_label'			=> __( 'Media Links', 'mstw-loc-domain' ),
				
				);
				
		return $defaults;
	}
				
	?>
