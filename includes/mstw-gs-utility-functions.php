<?php
/*
 * mstw-gs-utility-functions.php
 * 	Utility functions for MSTW Game Schedules Plugin (front end)
 *
 *	MSTW Wordpress Plugins
 *	Copyright (C) 2013 Mark O'Donnell
 *	Contact me at http://shoalsummitsolutions.com
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.

 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 /*----------------------------------------------------------
 *	MSTW-GS-UTILITY-FUNCTIONS
 *	These functions are included in the front end.
 *
 *	0. mstw_gs_utility_functions_loaded() - indicator that the file is loaded 
 *	1. mstw_gs_get_defaults() - returns the default mstw_gs_options[]
 *	2. mstw_gs_get_dtg_defaults() - returns default mstw_gs_dtg_options[]
 *	3. mstw_gs_get_color_defaults() - returns default mstw_gs_color_options[]
 *	4. mstw_gs_build_css_rule() - helper function to build custom css rules 
 *		from option settings and included in headers
 *	 
 *---------------------------------------------------------*/
 
//---------------------------------------------------------------------------------
//	0. mstw_gs_utility_fuctions_loaded: DO NOT DELETE
//		It does nothing EXCEPT indicate whether or not the file is loaded!!
//
	function mstw_gs_utility_functions_loaded( ) {
		return true;
	}

//---------------------------------------------------------------------------------
//	1. mstw_gs_get_defaults -returns the mstw_gs_options[] default values
//
	function mstw_gs_get_defaults( ) {
		//Base defaults
		$defaults = array(
				//default schedule table shortcode arguments
				'sched' => 1,  // This is used for cdt & slider shortcodes too
				'first_dtg' => '1970:01:01 00:00:00',	// first php dtg
				'last_dtg' => '2038:01:19 00:00:00', 	// last php dtg (roughly)
				'games_to_show' => -1,
				
				//default cdt shortcode arguments
				'cd_title'			=> __( 'Countdown', 'mstw-loc-domain' ),
				'home_only' 		=> 0,
				'intro'				=> __( 'Time to kickoff', 'mstw-loc-domain' ),
				
				//default slider shortcode arguments
				'title'				=> 'Schedule',
				'link_label' 		=> '',
				'link' 				=>'',

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
				'table_opponent_format'	=> 'full-name',
				'slider_opponent_format'	=> 'full-name',
				'show_table_logos'		=> 'name-only', //Hide Logos
				'show_slider_logos'		=> 'name-only', //Hide Logos
				'venue_format'			=> 'city-name-state', //Show (location) name only
				'venue_link_format'		=> 'no-link', //No Link
				);
				
		return $defaults;
	}
	
//---------------------------------------------------------------------------------
//	2. mstw_gs_get_dtg_defaults - returns the mstw_gs_dtg_options[] default values
//	
	function mstw_gs_get_dtg_defaults( ) {
		//Base defaults
		$defaults = array(
				//date and time format defaults
				'admin_date_format' 	=>'Y-m-d',
				'custom_admin_date_format' => '',
				'admin_time_format'		=> 'H:i',
				'custom_admin_time_format' => '',
				
				'table_date_format'		=> 'Y-m-d',
				'custom_table_date_format' => '',
				'table_time_format'		=> 'H:i',
				'custom_table_time_format' => '',
				
				'table_widget_date_format' => 'j M',
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
	
//---------------------------------------------------------------------------------
//	3. mstw_gs_get_color_defaults - returns the mstw_gs_color_options[] default values
//	
	function mstw_gs_get_color_defaults( ) {
		//resets all the colors to blank
		$defaults = array(
				'gs_tbl_hdr_bkgd_color' 		=> '',
				'gs_tbl_hdr_text_color' 		=> '',
				'gs_tbl_border_color'			=> '',
				'gs_tbl_odd_bkgd_color' 		=> '',
				'gs_tbl_odd_text_color'			=> '',
				'gs_tbl_even_bkgd_color' 		=> '',
				'gs_tbl_even_text_color'		=> '',
				'gs_tbl_home_bkgd_color' 		=> '',
				'gs_tbl_home_text_color' 		=> '',
				
				'gs_cdt_game_time_color' 		=> '',
				'gs_cdt_opponent_color' 		=> '',
				'gs_cdt_location_color'			=> '',
				'gs_cdt_intro_color' 			=> '',
				'gs_cdt_countdown_color'		=> '',
				'gs_cdt_countdown_bkgd_color' 	=> '',
				'gs_tbl_even_text_color'		=> '',
				'gs_tbl_home_bkgd_color' 		=> '',
				'gs_tbl_home_text_color' 		=> '',
				
				'gs_sldr_hdr_bkgd_color' 		=> '',
				'gs_sldr_game_block_bkgd_color' => '',
				'gs_sldr_hdr_text_color'		=> '',
				'gs_sldr_hdr_divider_color' 	=> '',
				'gs_sldr_game_date_color'		=> '',
				'gs_sldr_game_opponent_color' 	=> '',
				'gs_sldr_game_location_color'	=> '',
				'gs_sldr_game_time_color' 		=> '',
				'gs_sldr_game_links_color' 		=> '',
				);
				
		return $defaults;
	}

//---------------------------------------------------------------------------------
//	4. mstw_gs_build_css_rule - helper function to build css rules
//		
	function mstw_gs_build_css_rule( $options_array, $option_name, $css_rule ) {
		if ( isset( $options_array[$option_name] ) and !empty( $options_array[$option_name] ) ) {
			return $css_rule . ":" . $options_array[$option_name] . "; \n";	
		} 
		else {
			return "";
		}
	}			
?>
