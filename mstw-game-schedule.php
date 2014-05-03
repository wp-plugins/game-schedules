<?php
/*
Plugin Name: Game Schedule
Plugin URI: http://wordpress.org/extend/plugins/
Description: The Game Schedule Plugin defines a custom type - Scheduled Games - for use in the MySportTeamWebite framework. Generations a game schedule (html table) using a shortcode.
Version: 4.1
Author: Mark O'Donnell
Author URI: http://shoalsummitsolutions.com
*/

/*
Game Schedule (Wordpress Plugin)
Copyright (C) 2012-13 Mark O'Donnell
Contact me at http://shoalsummitsolutions.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

//-----------------------------------------------------------------------------------
// PLUGIN PREFIX:                                                          
// 'mstw_gs_'   derived from mysportsteamwebsite game schedule
//
// See http://wordpress.org/plugins/game-schedules/developers/ for CHANGE LOG
//
//-----------------------------------------------------------------------------------

	//------------------------------------------------------------------------
	// Load the Game Schedules utility functions (once)
	//
	if ( !function_exists( 'mstw_gs_utility_fuctions_loaded' ) ) {
		// we're in wp-admin
		require_once ( dirname( __FILE__ ) . '/includes/mstw-gs-utility-functions.php' );
    }
		
	//------------------------------------------------------------------------
	// If an admin screen, load the admin functions (once)
	//
	if ( is_admin( ) ) {
		// we're in wp-admin
		require_once ( dirname( __FILE__ ) . '/includes/mstw-game-schedules-admin.php' );
    }
	
	// ----------------------------------------------------------------
	// Set up localization
	//
	add_action( 'plugins_loaded', 'mstw_gs_load_localization' );
		
	function mstw_gs_load_localization( ) {
		load_plugin_textdomain( 'mstw-loc-domain', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
		//echo dirname( plugin_basename( __FILE__ ) ) . '/lang/';
		//die();
	} 
	
	// ----------------------------------------------------------------
	// Deactivate, request upgrade, and exit if WP version is not right
	//
	add_action( 'admin_init', 'mstw_gs_requires_wp_ver' );

	function mstw_gs_requires_wp_ver() {
		global $wp_version;
		$plugin = plugin_basename( __FILE__ );
		$plugin_data = get_plugin_data( __FILE__, false );

		if ( version_compare($wp_version, "3.3", "<" ) ) {
			if( is_plugin_active($plugin) ) {
				deactivate_plugins( $plugin );
				wp_die( "'".$plugin_data['Name']."' requires WordPress 3.3 or higher, and has been deactivated! 
					Please upgrade WordPress and try again.<br /><br />Back to <a href='".admin_url()."'>WordPress admin</a>." );
			}
		}
	} //end mstw_gs_requires_wp_ver()

	// ----------------------------------------------------------------
	// Add the CSS code from the settings/options to the header
	//
	add_filter( 'wp_head', 'mstw_gs_add_css');
		
	function mstw_gs_add_css( ) {
		
		//$options = get_option( 'mstw_gs_options' );
		$colors = get_option( 'mstw_gs_color_options' );
		
		echo '<style type="text/css">';
		
		// SCHEDULE TABLES
		echo ".mstw-gs-table-head th, .mstw-gs-sw-tab-head th { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_tbl_hdr_text_color', 'color' );
			echo mstw_gs_build_css_rule( $colors, 'gs_tbl_hdr_bkgd_color', 'background-color' );
			echo mstw_gs_build_css_rule( $colors, 'gs_tbl_border_color', 'border-color' );
		echo "} \n";
		
		echo "tr.mstw-gs-odd, td.mstw-gs-odd, td.mstw-gs-odd a { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_tbl_odd_text_color', 'color' );
			echo mstw_gs_build_css_rule( $colors, 'gs_tbl_odd_bkgd_color', 'background-color' );
			echo mstw_gs_build_css_rule( $colors, 'gs_tbl_border_color', 'border-color' );
		echo "} \n";
		
		echo ".mstw-gs-sw-odd td a, .mstw-gs-sw-odd td a:visited, .mstw-gs-sw-odd td a:active { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_tbl_odd_text_color', 'color' );
			//echo mstw_gs_build_css_rule( $colors, 'gs_tbl_odd_bkgd_color', 'background-color' );
			echo mstw_gs_build_css_rule( $colors, 'gs_tbl_border_color', 'border-color' );
		echo "} \n";
		
		echo "tr.mstw-gs-sw-odd, td.mstw-gs-sw-odd a, td.mstw-gs-sw-odd { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_tbl_odd_text_color', 'color' );
			echo mstw_gs_build_css_rule( $colors, 'gs_tbl_odd_bkgd_color', 'background-color' );
			echo mstw_gs_build_css_rule( $colors, 'gs_tbl_border_color', 'border-color' );
		echo "} \n";
		
		echo ".mstw-gs-even tr, .mstw-gs-even td, .mstw-gs-even td a { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_tbl_even_text_color', 'color' );
			echo mstw_gs_build_css_rule( $colors, 'gs_tbl_even_bkgd_color', 'background-color' );
			echo mstw_gs_build_css_rule( $colors, 'gs_tbl_border_color', 'border-color' );
		echo "} \n";
		
		echo ".mstw-gs-sw-even td a, .mstw-gs-sw-even td a:visited, .mstw-gs-sw-even td a:active { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_tbl_even_text_color', 'color' );
			//echo mstw_gs_build_css_rule( $colors, 'gs_tbl_even_bkgd_color', 'background-color' );
			echo mstw_gs_build_css_rule( $colors, 'gs_tbl_border_color', 'border-color' );
		echo "} \n";
		
		
		echo "tr.mstw-gs-sw-even, td.mstw-gs-sw-even a, td.mstw-gs-sw-even { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_tbl_even_text_color', 'color' );
			echo mstw_gs_build_css_rule( $colors, 'gs_tbl_even_bkgd_color', 'background-color' );
			echo mstw_gs_build_css_rule( $colors, 'gs_tbl_border_color', 'border-color' );
		echo "} \n";
				
		echo ".mstw-gs-even.mstw-gs-home td, .mstw-gs-odd.mstw-gs-home td { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_tbl_home_text_color', 'color' );
			echo mstw_gs_build_css_rule( $colors, 'gs_tbl_home_bkgd_color', 'background-color' );
		echo "} \n";
		
		echo ".mstw-gs-odd.mstw-gs-home td a, .mstw-gs-even.mstw-gs-home td a { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_tbl_home_text_color', 'color' );
		echo "} \n";
		
		// COUNTDOWN TIMER
		echo ".mstw-gs-cdt-dtg { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_cdt_game_time_color', 'color' );
		echo "} \n";
		
		echo ".mstw-gs-cdt-opponent, .mstw-gs-cdt-opponent a { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_cdt_opponent_color', 'color' );
		echo "} \n";
		
		echo ".mstw-gs-cdt-location, .mstw-gs-cdt-location a { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_cdt_location_color', 'color' );
		echo "} \n";
		
		echo ".mstw-gs-cdt-intro { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_cdt_intro_color', 'color' );
		echo "} \n";
		
		echo ".mstw-gs-cdt-countdown { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_cdt_countdown_color', 'color' );
			echo mstw_gs_build_css_rule( $colors, 'gs_cdt_countdown_bkgd_color', 'background-color' );
		echo "} \n";
		
		
		
		// SCHEDULE SLIDER
		echo ".gs-slider .title, .gs-slider .full-schedule-link { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_sldr_hdr_text_color', 'color' );
		echo "} \n";
		
		echo ".gs-slider .full-schedule-link a { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_sldr_hdr_text_color', 'color' );
		echo "} \n";
		
		echo ".gs-slider .box { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_sldr_hdr_bkgd_color', 'background-color' );
		echo "} \n";
		
		echo ".gs-divider { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_sldr_hdr_divider_color', 'border-bottom-color' );
		echo "} \n";
		
		echo ".gs-slider #schedule-slider { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_sldr_game_block_bkgd_color', 'background-color' );
		echo "} \n";
		
		echo ".game-block .date { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_sldr_game_date_color', 'color' );
			echo mstw_gs_build_css_rule( $colors, 'gs_sldr_game_date_color', 'border-bottom-color' );
		echo "} \n";
		
		echo ".game-block .opponent, .game-block .opponent a:hover { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_sldr_game_opponent_color', 'color' );
			echo "text-decoration: none; \n";
		echo "} \n";
		
		echo ".game-block .opponent a { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_sldr_game_opponent_color', 'color' );
			echo "text-decoration: underline; \n";
		echo "} \n";
		
		echo ".game-block .location { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_sldr_game_location_color', 'color' );
		echo "} \n";
		
		echo ".game-block .location a { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_sldr_game_location_color', 'color' );
			echo "text-decoration: underline; \n";
		echo "} \n";
		
		echo ".game-block .location a:hover { \n";
			echo "text-decoration: none; \n";
		echo "} \n";

		echo ".game-block .time-result { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_sldr_game_time_color', 'color' );
		echo "} \n";
		
		echo ".game-block .links, .gs-slider .game-block .links a  { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_sldr_game_links_color', 'color' );
		echo "} \n";
		
		echo "#gs-slider-right-arrow, #gs-slider-left-arrow { \n";
			echo mstw_gs_build_css_rule( $colors, 'gs_sldr_game_location_color', 'color' );
		echo "} \n";

		echo '</style>';	
	} //end mstw_gs_add_css()


// --------------------------------------------------------------------------------
// Set-up Action and Filter Hooks for the Settings on the admin side
//
// register_activation_hook(__FILE__, 'mstw_gs_set_defaults');
register_uninstall_hook(__FILE__, 'mstw_gs_delete_plugin_options');

// --------------------------------------------------------------------------------
// Callback for: register_uninstall_hook(__FILE__, 'mstw_gs_delete_plugin_options')
// 
// It runs when the user deactivates AND DELETES the plugin. 
// It deletes the plugin options DB entry, which is an array,
// storing all the plugin options
// 
function mstw_gs_delete_plugin_options() {
	delete_option('mstw_gs_options');
}

// --------------------------------------------------------------------------------
// Queue up the necessary CSS 
// add_action( 'wp_head', 'mstw_gs_enqueue_styles' ); 
//
	add_action( 'wp_enqueue_scripts', 'mstw_gs_enqueue_styles' );

	function mstw_gs_enqueue_styles( ) {
		
		// Find the full path to the plugin's css file 
		$mstw_gs_style_url = plugins_url('/css/mstw-gs-styles.css', __FILE__);
		$mstw_gs_style_file = WP_PLUGIN_DIR . '/game-schedules/css/mstw-gs-styles.css';
		
		wp_register_style( 'mstw_gs_style', plugins_url('/css/mstw-gs-styles.css', __FILE__) );
		
		//echo 'file url: ' . $mstw_gs_style_url . "\n";
		//echo 'file name: ' . $mstw_gs_style_file . "\n";
		
		// If stylesheet exists, enqueue the style
		if ( file_exists( $mstw_gs_style_file ) ) {	
			wp_enqueue_style( 'mstw_gs_style' );			
			
		} 

		wp_enqueue_script( 'gs-slider', plugins_url( 'game-schedules/js/gs-slider.js' ), array('jquery'), false, true );
		
	} //end mstw_gs_enqueue_styles( )

	// --------------------------------------------------------------------------------
	// CUSTOM POST TYPES
	//	registers scheduled_games, mstw_gs_schedules, and mstw_gs_teams
	//
	add_action( 'init', 'mstw_gs_register_post_types' );

	function mstw_gs_register_post_types( ) {
		
		$menu_icon_url = plugins_url( ) . '/game-schedules/images/mstw-admin-menu-icon.png';
		
		// show ui (or not) based on user's capability
		
		// filter default capability so developers can modify
		$capability = apply_filters( 'mstw_gs_user_capability', 'edit_others_posts', 'game_schedules_menu' );
		
		// if filter returns the empty string, someone screwed up; use edit_others_posts as default (editor role)
		if ( $capability == '' )
			$capability = 'edit_others_posts';
			
		// set show_ui based on user and capability
		$show_ui = ( current_user_can( $capability ) == true ? true : false );
		
		//-----------------------------------------------------------------------
		// register scheduled_games post type
		//
		$args = array(
			'public' 			=> true,
			'menu_icon'     	=> $menu_icon_url,
			'query_var' 		=> 'scheduled_games',
			'rewrite' 			=> array(
				'slug' 			=> 'scheduled-games',
				'with_front' 	=> false,
			),
			'supports' 			=> array(
										'title'
										),
			'show_ui'			=> $show_ui,
			//'capabilities'		=> array(
			//							'edit_posts',
			//							),
			'labels' 			=> array(
										'name' => __( 'Game Schedules', 'mstw-loc-domain' ),
										'singular_name' => __( 'Game', 'mstw-loc-domain' ),
										'all_items' => __( 'Games', 'mstw-loc-domain' ),
										'add_new' => __( 'Add New Game', 'mstw-loc-domain' ),
										'add_new_item' => __( 'Add Game', 'mstw-loc-domain' ),
										'edit_item' => __( 'Edit Game', 'mstw-loc-domain' ),
										'new_item' => __( 'New Game', 'mstw-loc-domain' ),
										//'View Game Schedule' needs a custom page template that is of no value.
										'view_item' => null, 
										'search_items' => __( 'Search Games', 'mstw-loc-domain' ),
										'not_found' => __( 'No Games Found', 'mstw-loc-domain' ),
										'not_found_in_trash' => __( 'No Games Found In Trash', 'mstw-loc-domain' ),
										)
			);
			
		register_post_type( 'scheduled_games', $args);
		
		//add_action( 'load-post-new.php', 'mstw_gs_settings_help' );
		//add_action( 'load-post.php', 'mstw_gs_settings_help' );
		
		//----------------------------------------------------------------------------
		// register mstw_gs_teams post type
		//
		$args = array(
			'public' 			=> true,
			'menu_icon'     	=> $menu_icon_url,
			'show_in_menu' 		=> 'edit.php?post_type=scheduled_games',
			'query_var' 		=> 'mstw_gs_teams',
			'rewrite' 			=> array(
				'slug' 			=> 'mstw-gs-teams',
				'with_front' 	=> false,
			),
			'supports' 			=> array(
										'title'
										),
			//'capabilities'		=> array(
			//							'edit_posts',
			//							),
			'labels' 			=> array(
										'name' => __( 'Teams', 'mstw-loc-domain' ),
										'singular_name' => __( 'Team', 'mstw-loc-domain' ),
										'all_items' => __( 'Teams', 'mstw-loc-domain' ),
										'add_new' => __( 'Add New Team', 'mstw-loc-domain' ),
										'add_new_item' => __( 'Add Team', 'mstw-loc-domain' ),
										'edit_item' => __( 'Edit Team', 'mstw-loc-domain' ),
										'new_item' => __( 'New Team', 'mstw-loc-domain' ),
										//'View Game Schedule' needs a custom page template that is of no value.
										'view_item' => null, 
										'search_items' => __( 'Search Teams', 'mstw-loc-domain' ),
										'not_found' => __( 'No Teams Found', 'mstw-loc-domain' ),
										'not_found_in_trash' => __( 'No Teams Found In Trash', 'mstw-loc-domain' ),
										)
			);
			
		register_post_type( 'mstw_gs_teams', $args);
		
		//---------------------------------------------------------------------
		// register mstw_gs_schedules post type
		//
		$args = array(
			'public' 			=> true,
			'menu_icon'     	=> $menu_icon_url,
			'show_in_menu' 		=> 'edit.php?post_type=scheduled_games',
			'query_var' 		=> 'mstw_gs_schedules',
			'rewrite' 			=> array(
				'slug' 			=> 'mstw_gs_schedules',
				'with_front' 	=> false,
			),
			'supports' 			=> array(
										'title'
										),
			//'capabilities'		=> array(
			//							'edit_posts',
			//							),
			'labels' 			=> array(
										'name' => __( 'Schedules', 'mstw-loc-domain' ),
										'singular_name' => __( 'Schedule', 'mstw-loc-domain' ),
										'all_items' => __( 'Schedules', 'mstw-loc-domain' ),
										'add_new' => __( 'Add New Schedule', 'mstw-loc-domain' ),
										'add_new_item' => __( 'Add Schedule', 'mstw-loc-domain' ),
										'edit_item' => __( 'Edit Schedule', 'mstw-loc-domain' ),
										'new_item' => __( 'New Schedule', 'mstw-loc-domain' ),
										//'View Game Schedule' needs a custom page template that is of no value.
										'view_item' => null, 
										'search_items' => __( 'Search Schedules', 'mstw-loc-domain' ),
										'not_found' => __( 'No Schedules Found', 'mstw-loc-domain' ),
										'not_found_in_trash' => __( 'No Schedules Found In Trash', 'mstw-loc-domain' ),
										)
			);
			
		register_post_type( 'mstw_gs_schedules', $args);
		
	} //end mstw_gs_register_post_types() 

// --------------------------------------------------------------------------------------
// Add the shortcode handler, which will create the Game Schedule table on the user side.
// Handles the shortcode parameters, if there were any, 
// then calls mstw_gs_build_loc_tab() to create the output
// --------------------------------------------------------------------------------------
	add_shortcode( 'mstw_gs_table', 'mstw_gs_shortcode_handler' );

	function mstw_gs_shortcode_handler( $atts ){
		// get the options set in the admin display settings screen
		$base_options = get_option( 'mstw_gs_options' );
		$output = '';
		//$output .= '<pre>OPTIONS:' . print_r( $base_options, true ) . '</pre>';
		//return $output;
		$dtg_options = get_option( 'mstw_gs_dtg_options' );
		//$output .= '<pre>OPTIONS:' . print_r( $dtg_options, true ) . '</pre>';
		//$options = get_option( 'mstw_gs_options' );
		$options = array_merge( (array)$base_options, (array)$dtg_options );
		//$output .= '<pre>OPTIONS:' . print_r( $options, true ) . '</pre>';
		// Remove all keys with empty values
		foreach ( $options as $k=>$v ) {
			//if ( $k == 'show_date' )
				//$output .= $k . '=> ' . $v;
			if( $v == '' ) {
				//$output .= 'unset: ' . $k . '=> ' . $v;
				unset( $options[$k] );
				
			}
		}
		
		//if ( isset( $options['show_date'] ) ) 
			//$output .= 'show_date = ' . $options['show_date'];
		
		//$output = '';
		//$output .= '<pre>OPTIONS:' . print_r( $options, true ) . '</pre>';
		//return $output;
		
		// and merge them with the defaults
		$defaults = array_merge( mstw_gs_get_defaults( ), mstw_gs_get_dtg_defaults( ) );
		$args = wp_parse_args( $options, $defaults );
		//$output .= '<pre>ARGS:' . print_r( $args, true ) . '</pre>';
		//return $output;
			
		// then merge the parameters passed to the shortcode with the result									
		$attribs = shortcode_atts( $args, $atts );
		//$output .= '<pre>ATTRIBS:' . print_r( $attribs, true ) . '</pre>';
		//return $output;
			
		$mstw_gs_sched_tab = mstw_gs_build_sched_tab( $attribs );
		
		return $output . $mstw_gs_sched_tab;
	}

//--------------------------------------------------------------------------------------
// MSTW_GS_BUILD_SCHED_TAB
// 	Called by mstw_gs_shortcode_handler()
// 	Builds the Game Schedules table as a string (to replace the [shortcode] in a page or post.
// 	Loops through the Game Schedules Custom posts and formats them into a pretty table.
// ARGUMENTS:
// 	$args - the display settings and shortcode arguments, properly combined by mstw_gs_shortcode_handler()
//
	function mstw_gs_build_sched_tab( $args ) {
	
		$output = ''; //This is the return string
		
		//Pull the $args array into individual variables
		extract( $args );
		
		$scheds = explode( ',', $sched );
		
		if ( $scheds[0] == '' ) {
			return '<h3>' . __( 'No schedule specified.', 'mstw-loc-domain' ) . '</h3>';
		}
		
		//This changes if and only if last_dtg == now
		$sort_order = 'ASC';
		
		//$output .= '<pre>mstw_gs_build_tab: $args:' . print_r( $args, true ) . '</pre>';
		//$output .= '<pre>ATTRIBS:' . print_r( $attribs, true ) . '</pre>';
		//return $output;
		
		//full date format 
		$dtg_format = ( $table_date_format == 'custom' ? $custom_table_date_format : $table_date_format ); 
		
		//time format
		$time_format = ( $table_time_format == 'custom' ? $custom_table_time_format : $table_time_format );

		// Need to set $first_dtg and $last_dtg by converting strings
		// OR convert $first_dtg='now' to current php DTG stamp
		if ( $first_dtg == 'now' ) {
			$first_dtg = time( );
		}
		else { 
			$first_dtg = strtotime( $first_dtg );
		}		
		$first_dtg = ( $first_dtg <= 0 ? 1 : $first_dtg );
		
		if ( $last_dtg == 'now' ) {
			$sort_order = 'DESC';
			$last_dtg = time( );
		}
		else { 
			$last_dtg = strtotime( $last_dtg );
		}
		//echo '<p> last_dtg_str: ' . $last_dtg_str . ' last_dtg_int: ' . $last_dtg. '</p>';
		//echo '<p> reverse it: ' . date( 'Y m d' , $last_dtg ) . '</p>';
		$last_dtg = ( $last_dtg <= 0 ? PHP_INT_MAX : $last_dtg );	
		
		// Get the games posts
		$posts = get_posts( array( 'numberposts' => $games_to_show,
								  'post_type' => 'scheduled_games',
								  'meta_query' => array(
													'relation' => 'AND',
													array(
														'key' => '_mstw_gs_sched_id',
														'value' => $scheds,
														'compare' => 'IN',
													),
													array(
														'key' => '_mstw_gs_unix_dtg',
														'value' => array( $first_dtg, $last_dtg),
														'type' => 'NUMERIC',
														'compare' => 'BETWEEN'
													)
												),
								  
								  'orderby' => 'meta_value', 
								  'meta_key' => '_mstw_gs_unix_dtg',
								  'order' => $sort_order 
								) );						
		
		if ( $posts ) {
			// Make table of posts
			// Start with the table header
			$output .= '<table class="mstw-gs-table">'; 
			$output .= "<thead class='mstw-gs-table-head mstw-gs-table-head_" . $scheds[0] . "'><tr>";
			if( $show_date ) { 
				$label = sanitize_title( $date_label );
				$output .= "<th class='col-1'>" . __( $date_label, 'mstw-loc-domain' ) . '</th>'; //'<th>'. $date_label . '</th>';
			}
			
			$output .= '<th class="col-2">'. __( $opponent_label, 'mstw-loc-domain' ) . '</th>';
			
			if( $show_location ) {
				$output .= '<th class="col-3">'. __( $location_label, 'mstw-loc-domain' ) . '</th>';
			}
			
			if( $show_time ) {
				$output .= '<th class="col-4">'. __( $time_label, 'mstw-loc-domain' ) . '</th>';
			}
			
			if ( $show_media > 0 ) { 
				$output .= '<th class="col-5">'.  __( $media_label, 'mstw-loc-domain' ) . '</th>';
			}
			
			$output .= '</tr></thead>';
			
			   
			// Keeps track of even and odd rows. Start with row 1 = odd.
			$even_and_odd = array('even', 'odd');
			$row_cnt = 1; 
		
			// Loop through the posts and make the rows
			foreach( $posts as $post ) {
				// set up some housekeeping to make styling in the loop easier
				
				$even_or_odd_row = $even_and_odd[$row_cnt]; 
				$row_class = 'mstw-gs-' . $even_or_odd_row;
				$row_class .= ' ' . $row_class . '_' . $scheds[0];
				
				$is_home_game = get_post_meta($post->ID, '_mstw_gs_home_game', true );
				if ( $is_home_game == 'home' ) 
					$row_class .= ' mstw-gs-home';
				
				$row_tr = '<tr class="' . $row_class . '">';
				//$row_td = '<td class="' . $row_class . '">';
				$td_1 = '<td class="' . $row_class . ' col-1">';
				$td_2 = '<td class="' . $row_class . ' col-2">';
				$td_3 = '<td class="' . $row_class . ' col-3">';
				$td_4 = '<td class="' . $row_class . ' col-4">';
				$td_5 = '<td class="' . $row_class . ' col-5">';
				
				// create the row
				$row_string = $row_tr;			
				
				// column 1: Build the game date in a specified format
				if ( $show_date ) {
					$new_date_string = mstw_date_loc( $dtg_format, (int)get_post_meta( $post->ID, '_mstw_gs_unix_dtg', true ) );

					$row_string = $row_string. $td_1 . $new_date_string . '</td>';	
				}
				
				// column 2: create the opponent entry ALWAYS SHOWN
				$opponent_entry = mstw_gs_build_opponent_entry( $post, $args, "table" );
				$row_string =  $row_string . $td_2 . $opponent_entry . '</td>';
				
				// column 3: create the location entry
				if ( $show_location ) {
					$location_entry = mstw_gs_build_location_entry( $post, $args );
					$row_string =  $row_string . $td_3 . $location_entry . '</td>';
				}
				
				// column 4: create the time/results entry
				// 20120221-MAO: Rewritten to handle new game time entry logic
				//		and to use time format settings
				
				if ( $show_time ) {
					// $time_entry = mstw_gs_build_time_entry( $post );
					// If there is a game result, stick it in and we're done
					$game_result = get_post_meta( $post->ID, '_mstw_gs_game_result', true); 
					if ( $game_result != '' ) {
						$row_string .=  $td_4 . $game_result . '</td>';
					}
					else {	
						// There's no game result, so add a game time
						// Check if the game time is TBA
						$time_is_tba = get_post_meta( $post->ID, '_mstw_gs_game_time_tba', true );
						
						if ( $time_is_tba != '' ) {	
							//Time is TBA. Stick it in and we're done
							$row_string .=  $td_4 . $time_is_tba . '</td>';
						}
						else {	
							//Time is not TBA. Build the time string from the unix timestamp
							$unix_dtg = get_post_meta( $post->ID, '_mstw_gs_unix_dtg', true );
							$time_str = date( $time_format, $unix_dtg );
							$row_string .=  $td_4 . $time_str . '</td>';
						}	
					}
				}
				
				// column 5: create the media listings in a pretty format 
				
				if( $show_media > 0 ) { //if ( $show_media ) {
					$media_links = $td_5 . "";
					
					$mstw_media_label_1 = trim( get_post_meta($post->ID, '_mstw_gs_media_label_1', true ) );
					if ( $mstw_media_label_1 <> "" ) {
						$mstw_media_url_1 = trim( get_post_meta($post->ID, '_mstw_gs_media_url_1', true ) );
						if ( $mstw_media_url_1 <> "" ) {
							// build the link
							$href = '<a href="' . $mstw_media_url_1 . '" target="_blank">' . $mstw_media_label_1 .'</a>';
						}
						else {
							$href = $mstw_media_label_1; 
						}
						$media_links = $media_links . $href;
						
						$mstw_media_label_2 = trim( get_post_meta($post->ID, '_mstw_gs_media_label_2', true ) );
						if ( $show_media > 1 and $mstw_media_label_2 <> "" ) {
							$mstw_media_url_2 = trim( get_post_meta($post->ID, '_mstw_gs_media_url_2', true ) );
							if ( $mstw_media_url_2 <> "" ) {
								// build the link
								$href = '<a href="' . $mstw_media_url_2 . '" target="_blank">' . $mstw_media_label_2 .'</a>';
							}
							else {
								$href = $mstw_media_label_2; 
							}
							$media_links = $media_links . " | " . $href;
							
							$mstw_media_label_3 = trim( get_post_meta($post->ID, '_mstw_gs_media_label_3', true ) );
							if ( $show_media > 2 and $mstw_media_label_3 <> "" ) {
								$mstw_media_url_3 = trim( get_post_meta($post->ID, '_mstw_gs_media_url_3', true ) );
								if ( $mstw_media_url_3 <> "" ) {
									// build the link
									$href = '<a href="' . $mstw_media_url_3 . '" target="_blank">' . $mstw_media_label_3 .'</a>';
								}
								else {
									$href = $mstw_media_label_3; 
								}
								$media_links = $media_links . " | " . $href;
							}
						}
					}
					
					$row_string .= $media_links . '</td>';  //			Should have a </tr> here??
				}
				
				//$output = $output . $row_string;
				$output .= $row_string . '</tr>';
				
				$row_cnt = 1- $row_cnt;  // Get the styles right
				
			} // end of foreach post
			
			$output = $output . '</table>';
		}
		else { // No posts were found
			$output =  '<h3>' . __( 'No scheduled games found for ', 'mstw-loc-domain' ) .$scheds[0] . '.</h3>';	
		}
		
		return $output;

	} //End function mstw_gs_build_sched_tab
	
//---------------------------------------------------------------------	
//	MSTW_GS_BUILD_OPPONENT_ENTRY
//	Builds the opponent entry for the schedule table shortcode
//
	function mstw_gs_build_opponent_entry( $post, $options, $entry_type ) {
		//$post - the game post
		//$options - the combined base and dtg options, args, atts
		//$entry_type - "slider" or "table" controls image used/image size
		//Defaults to "table", which is the smaller size
		
		$opponent_entry = '';  //this should never survive
				
		$team_ID = get_post_meta( $post->ID, 'gs_opponent_team', true );
		
		$show_entry_logo = ( $entry_type == "slider" ? $options['show_slider_logos'] : $options['show_table_logos'] );
		$team_logo_url = ( $entry_type == "slider" ? get_post_meta( $team_ID, 'team_alt_logo', true ) : get_post_meta( $team_ID, 'team_logo', true ) );
		$opponent_format = ( $entry_type == 'slider' ? $options['slider_opponent_format'] : $options['table_opponent_format'] );
		
		//is an entry for the opponent in the TEAMS DB specfied?
		//no team ID entry ('----') is stored as -1
		//the empty string is there for legacy purposes
		if ( $team_ID != '' and $team_ID > 0 ) {
			//Need to check display settings for formats
			// long name + long mascot, short name + short mascot, etc.
			
			$team_short_name = get_post_meta( $team_ID, 'team_short_name', true );
			$team_full_name = get_post_meta( $team_ID, 'team_full_name', true );
			$team_short_name = ( trim( $team_short_name ) == '' ? $team_full_name : $team_short_name );
			
			$team_short_mascot = get_post_meta( $team_ID, 'team_short_mascot', true );
			$team_full_mascot = get_post_meta( $team_ID, 'team_full_mascot', true );
			$team_short_mascot = ( trim( $team_short_mascot ) == '' ? $team_full_mascot : $team_short_mascot );
			
			switch ( $opponent_format ) {
				case 'short-name':
					$opponent_entry .= $team_short_name;
					break;
				case 'full-name':
					$opponent_entry .= $team_full_name; 
					break;
				case 'full-name-mascot':
					$opponent_entry .= "$team_full_name $team_full_mascot";
					break;
				default: //'short-name-mascot'
					$opponent_entry .= "$team_short_name $team_short_mascot";
					break;
			}
				
			//check for a link in the Teams DB, not the game post
			$opponent_link = get_post_meta( $team_ID, 'team_link', true );
			
			
			//get the format setting for name & logo
			if ( $show_entry_logo == 'logo-only' or $show_entry_logo == 'logo-name' ) {
				$img_url = mstw_gs_build_logo_url( $post, $entry_type );
				$img_str = "<img class=mstw-gs-$entry_type-logo src=$img_url>";
			}
			
			if ( $show_entry_logo == 'logo-only' ) {
				//$img_url = get_post_meta( $team_ID, 'team_alt_logo', true );
				if ( $opponent_link != '' and $opponent_link != -1 ) {
					$opponent_entry = "<a href='$opponent_link' target='_blank' >$img_str</a>";
				}
				else {
					$opponent_entry = $img_str;
				}
			}
			else if ( $show_entry_logo == 'logo-name' ) {
				if ( $opponent_link != '' and $opponent_link != -1 ) {
					$opponent_entry = "$img_str<a href='$opponent_link' target='_blank' >$opponent_entry</a>";
				} else {
					$opponent_entry = $img_str . $opponent_entry;
				}
				
			}
			else {
				if ( $opponent_link != '' and $opponent_link != -1 ) {
					//$opponent_entry = "<img class=mstw-gs-slider-logo src=$team_logo_url>$opponent_entry";
					$opponent_entry = "<a href='$opponent_link' target='_blank' >$opponent_entry</a>";
				} //else we'll just leave the opponent_entry as is
			
			}
		}
		else { //no entry in Teams DB specified for opponent
			$opponent_entry = get_post_meta( $post->ID, '_mstw_gs_opponent', true );
			//check for a link the the game post
			if ( ( $opponent_link = get_post_meta( $post->ID, '_mstw_gs_opponent_link', true ) ) != '' ) {
				$opponent_entry = "<a href='$opponent_link' target='_blank'>$opponent_entry</a>";
			}
		}
		
		return $opponent_entry;
	}
	
//---------------------------------------------------------------------	
//Builds the logo url for the schedule table & slider shortcodes
//	
	function mstw_gs_build_logo_url( $post, $type ) {
	//	$post - a game post
	//	$type - 'slider' or 'table'
	
		$team_ID = get_post_meta( $post->ID, 'gs_opponent_team', true );
		
		//Set the default logo (MSTW logo)
		$default_logo_file = ( $type == 'slider' ?  'default-slider-logo.png' : 'default-table-logo.png' );
		
		$logo_url = ( $type == 'slider' ? get_post_meta( $team_ID, 'team_alt_logo', true ) : get_post_meta( $team_ID, 'team_logo', true ) );
	
		$ret_url = ( $logo_url != '' ? $logo_url : plugins_url( ) . '/game-schedules/images/logos/' . $default_logo_file );
		
		return $ret_url;
	
	}
	
//---------------------------------------------------------------------	
//Builds the location entry for the schedule table & slider shortcodes
//
	function mstw_gs_build_location_entry( $post, $options ) {
		//$post - the game post
		//$options - display settings/options/arguments
		
		//May need formatting options
		//$options = get_option( 'mstw_gs_options' );
		$venue_format = $options['venue_format'];	//Name only  or City, ST (name)
		$venue_link_format = $options['venue_link_format']; //None, venue link, map link
	
		$location_entry = ""; //default return value
		
		//location entry in game post
		$location = get_post_meta( $post->ID, '_mstw_gs_location', true );
		//game locations DB location entry in game post
		$gl_location = get_post_meta( $post->ID, '_mstw_gs_gl_location', true );
		
		//if there's a location entry in game post, use it
		if ( trim( $location ) != '' and $location != -1 ) { 
			$location_entry = $location;
			//if there's a custom location link entry, use it
			$location_link = get_post_meta( $post->ID, '_mstw_gs_location_link', true );
			if ( $location_link != '' ) {
				$location_entry = '<a href="' . $location_link . '" target="_blank" >' . $location_entry . '</a>';
			}
		}
		
		//else if there's a location entry from the GL DB, use it
		else if ( trim( $gl_location ) != '' and $gl_location != -1 ) { 
			//grab the data
			$location_name = get_the_title( $gl_location );	
			$location_street = get_post_meta( $gl_location, '_mstw_gl_street', true );
			$location_city = get_post_meta( $gl_location, '_mstw_gl_city', true );
			$location_state = get_post_meta( $gl_location, '_mstw_gl_state', true ); 
			$location_zip = get_post_meta( $gl_location, '_mstw_gl_zip', true );
			$location_map_url = get_post_meta( $gl_location, '_mstw_gl_custom_url', true );
			$location_venue_url = get_post_meta( $gl_location, '_mstw_gl_venue_url', true );
			
			//if location's custom_url is not set, don't build a link
			if ( ($location_venue_url == '' or $location_venue_url == -1 ) and $venue_link_format == 'link-to-venue' ) {
				$venue_link_format = 'no-link';
			}
			
			switch ( $venue_link_format ) {
				case 'link-to-venue':
					$venue_name = "<a href='$location_venue_url' target='_blank'>$location_name</a>";
					break;
				case 'link-to-map':
					//use the venue's custom map URL if it exists
					if ( $location_map_url != "" and $location_map_url != -1 ) {
						$map_url = $location_map_url;
					}
					//otherwise build the google map url
					else {	
						$map_url = mstw_gs_build_google_map_url( $location_name, $location_street, $location_city, $location_state, $location_zip );
					}
					$venue_name = "<a href='$map_url' target='_blank'>$location_name</a>";
					break;
				default: //no-link
					$venue_name = $location_name;
					break;
			}
			//check the format setting
			if ( $venue_format == 'city-state-name' ) {
				$location_entry = "$location_city, $location_state ($venue_name)";
			}
			else { //default is venue name only
				$location_entry =  $venue_name;
			}
			
			//if ( empty( $custom_url ) ) {  // build the url from the address fields
			//	$center_string = get_the_title( $gl_location ) . "," .
			//		get_post_meta( $gl_location, '_mstw_gl_street', true ) . ', ' .
			//		get_post_meta( $gl_location, '_mstw_gl_city', true ) . ', ' .
			//		get_post_meta( $gl_location, '_mstw_gl_state', true ) . ', ' . 
			//		get_post_meta( $gl_location, '_mstw_gl_zip', true );
			//		
			//	$location_entry = '<a href="https://maps.google.com?q=' .$center_string . '" target="_blank" >'; 
			//}
			//else {
			//	$location_entry = '<a href="' . $custom_url . '" target="_blank">';
			//}
			
			//finish the location entry
			//$location_entry .= get_the_title( $gl_location ) . '</a>';
		}
		
		//if an away game, and there's an opponent entry in the TEAMS DB, use it
		else if ( get_post_meta( $post->ID, '_mstw_gs_home_game', true ) != 'home' ) {
			//ID of opponent in Teams DB
			$team_ID = get_post_meta( $post->ID, 'gs_opponent_team', true );
			if ( ( $team_ID != '' ) and ( $team_ID != -1 ) ) {
				$venue_ID = get_post_meta( $team_ID, 'team_home_venue', true );
				if ( ( $venue_ID != '' ) and ( $venue_ID != -1 ) ) {
					$venue_name = get_the_title( $venue_ID ); //this is basically the default
					
					switch ( $venue_link_format ) {
						case 'link-to-venue': //venue_url
							if ( ( $venue_url = get_post_meta( $venue_ID, '_mstw_gl_venue_url', true ) ) != '' ) {
								$location_entry = "<a href='$venue_url' target='_blank'>";
							}
							break;
						case 'link-to-map': //map_url
							//check for custom_map_url in Locations DB
							if ( $map_url = get_post_meta( $gl_location, '_mstw_gl_custom_url', true ) != '' ) {
								// if found, use
								$location_entry = "<a href='$map_url' target='_blank'>";
							}
							else {
								// else, build it 
								$center_string = $venue_name . "," .
									get_post_meta( $venue_ID, '_mstw_gl_street', true ) . ', ' .
									get_post_meta( $venue_ID, '_mstw_gl_city', true ) . ', ' .
									get_post_meta( $venue_ID, '_mstw_gl_state', true ) . ', ' . 
									get_post_meta( $venue_ID, '_mstw_gl_zip', true );
					
									$location_entry = '<a href="https://maps.google.com?q=' .$center_string . '" target="_blank" >'; 
							}
							break;
						default: //no link
							// use default venue_name set above switch
							$location_entry = '';
							break;
					}
					
					$location_end = ( $location_entry == '' ? '' : '</a>' );
					
					if ( $venue_format == 'city-state-name' ) {  //city, state (venue)
						$city = get_post_meta( $venue_ID, '_mstw_gl_city', true );
						$state = get_post_meta( $venue_ID, '_mstw_gl_state', true );
						$location_entry = "$city, $state (" . $location_entry . $venue_name . $location_end . ")"; 
					} else {  //show name only
						$location_entry = $location_entry . $venue_name . $location_end;
					}
				}
			}
		}
		
		// else it's a home game, so if there's an entry in the schedules DB, use it
		else {
			//From the game, find the schedule id
			$schedule_id = get_post_meta( $post->ID, '_mstw_gs_sched_id', true );

			if( !empty( $schedule_id ) ) {  //this should never, ever be empty
				$sched_entry = get_posts( array( 'numberposts' => -1,
								  'post_type' => 'mstw_gs_schedules',
								  'meta_query' => array(
													array(
														'key' => 'schedule_id',
														'value' => $schedule_id,
														'compare' => '='
													)
												),
								  
								  //'orderby' => 'meta_value', 
								  //'meta_key' => '_mstw_gs_unix_dtg',
								  //'order' => 'ASC' 
									)
								);
								
				if ( !empty( $sched_entry ) ) {
					$home_team_id = get_post_meta( $sched_entry[0]->ID, 'schedule_team', true );
					$home_team_venue_id = get_post_meta( $home_team_id, 'team_home_venue', true );
					$home_venue_name = get_the_title( $home_team_venue_id );
					$home_venue_street = get_post_meta( $home_team_venue_id, '_mstw_gl_street', true );					
					$home_venue_city = get_post_meta( $home_team_venue_id, '_mstw_gl_city', true );
					$home_venue_state = get_post_meta( $home_team_venue_id, '_mstw_gl_state', true );
					$home_venue_zip = get_post_meta( $home_team_venue_id, '_mstw_gl_zip', true );
					$home_venue_url = get_post_meta( $home_team_venue_id, '_mstw_gl_venue_url', true );
					$home_venue_map_url = get_post_meta( $home_team_venue_id, '_mstw_gl_custom_url', true );
					//check the link setting
					switch( $venue_link_format ) {
						case 'link-to-venue':
							$venue_name = "<a href='$home_venue_url' target='_blank'>$home_venue_name</a>";
							break;
						case 'link-to-map':
							//use the venue's custom map URL if it exists
							if ( $home_venue_map_url != "" and $home_venue_map_url != -1 ) {
								$map_url = $home_venue_map_url;
							}
							//otherwise build the google map url
							else {
								$map_url = mstw_gs_build_google_map_url( $home_venue_name, $home_venue_street, $home_venue_city, $home_venue_state, $home_venue_zip );
							}
							$venue_name = "<a href='$map_url' target='_blank'>$home_venue_name</a>";
							break;
							//break;
						default: //no-link
							$venue_name = $home_venue_name;
							break;
					}
					//check the format setting
					if ( $venue_format == 'city-state-name' ) {
						$location_entry = "$home_venue_city, $home_venue_state ($venue_name)";
					}
					else { //default is venue name only
						$location_entry =  $venue_name;
					}
					
					
				}
			}
		}
		
		return $location_entry;
	}
	
// ------------------------------------------------------------------------------
// Simple convenience function to build a google maps URL
// 	
	function mstw_gs_build_google_map_url( $name, $street, $city, $state, $zip ) {
		//don't want to add commas after blanks
		$name = ( $name == '' ) ? '' : "$name,";
		$street = ( $street == '' ) ? '' : "$street,";
		$city = ( $city == '' ) ? '' : "$city,";
		$state = ( $state == '' ) ? '' : "$state,";
		$zip = ( $zip == '' ) ? '' : "$zip";
		
		$google_url = "https://maps.google.com?q=$name $street $city $state $zip";
		
		return $google_url;
	}

// ------------------------------------------------------------------------------
add_shortcode( 'mstw_gs_countdown', 'mstw_gs_countdown_handler' );
// ------------------------------------------------------------------------------
// The countdown shortcode handler, parses the args, 
// 		and calls mstw_gs_build_countdown(), which creates the output
// ---------------------------------------------------------------------------
function mstw_gs_countdown_handler( $atts ){

	//$output .= '<pre>ATTS:' . print_r( $atts, true ) . '</pre>';
	
	
	// get the options set in the admin display settings screen
	$base_options = get_option( 'mstw_gs_options' );
	$output = '';
	//$output .= '<pre>OPTIONS:' . print_r( $base_options, true ) . '</pre>';
	//return $output;
	
	$dtg_options = get_option( 'mstw_gs_dtg_options' );
	//$output .= '<pre>OPTIONS:' . print_r( $dtg_options, true ) . '</pre>';
	
	$options = array_merge( $base_options, $dtg_options );
	//$output .= '<pre>OPTIONS:' . print_r( $options, true ) . '</pre>';
	
	// Remove all keys with empty values
	foreach ( $options as $k=>$v ) {
		//if ( $k == 'show_date' )
			//$output .= $k . '=> ' . $v;
		if( $v == '' ) {
			//$output .= 'unset: ' . $k . '=> ' . $v;
			unset( $options[$k] );
			
		}
	}
	//$output .= '<pre>OPTIONS:' . print_r( $options, true ) . '</pre>';
	//return $output;
		
	// and merge them with the defaults
	$defaults = array_merge( mstw_gs_get_defaults( ), mstw_gs_get_dtg_defaults( ) );
	$args = wp_parse_args( $options, $defaults );
	//$output .= '<pre>ARGS:' . print_r( $args, true ) . '</pre>';
	//return $output;
		
	// then merge the parameters passed to the shortcode with the result									
	$attribs = shortcode_atts( $args, $atts );
	//$output .= '<pre>ATTRIBS:' . print_r( $attribs, true ) . '</pre>';
	//return $output;
	
	/*
	// get the options set in the admin screen
	$options = get_option( 'mstw_gs_options' );
	//$output = '<pre>OPTIONS:' . print_r( $options, true ) . '</pre>';
	
	// and merge them with the defaults
	$args = wp_parse_args( $options, mstw_gs_get_defaults( ) );
	//$output .= '<pre>ARGS (options+defaults): ' . print_r( $args, true ) . '</pre>';
	
	// then merge the parameters passed to the shortcode with the result									
	$attribs = shortcode_atts( $args, $atts );
	//$output .= '<pre>ARGS:' . print_r( $args, true ) . '</pre>';
	//$output .= '<pre>ATTRIBS (args+atts): ' . print_r( $attribs, true ) . '</pre>';
	//return $output;
	*/
	
	$mstw_gs_countdown = mstw_gs_build_countdown( $attribs  );
	
	return $mstw_gs_countdown;
}

// --------------------------------------------------------------------------------------
// Called by:	mstw_gs_countdown_handler
// Builds the countdown display as a string (to replace the [shortcode] in a page or post.
// Loops through the specified schedule, finds the next game, and builds the countdown.
//
// $sched -> schedule ID, defaults to 1
// $intro -> text before countdown, defaults "Time to kickoff:" 
// $home_only -> countdown to home games only, defaults to false (all games)
// --------------------------------------------------------------------------------------
function mstw_gs_build_countdown( $attribs ) { 
	
	// For legacy compatibility
	$sched = $attribs['sched'];
	$intro = $attribs['intro'];
	$home_only = $attribs['home_only'];
	
	//$output = '<pre>' . print_r( $attribs, true ) . '</pre>';
	//return $output;
	
	$ret_str = '';
	
	// First get all the games for the specified schedule id.
	$game_posts = get_posts( array( 'numberposts' => -1,
							  'post_type' => 'scheduled_games',
							  'meta_query' => array(
												array(
													'key' => '_mstw_gs_sched_id',
													'value' => $sched,
													'compare' => '='
												)
											),
							  
							  'orderby' => 'meta_value', 
							  'meta_key' => '_mstw_gs_unix_dtg',
							  'order' => 'ASC' 
							));
							
	// Set some local variables  	
	$current_dtg = current_time( 'timestamp' );  // Get the current (WordPress) date-time stamp
	
	$have_games = false;	// indicates there are no games after the current time
	
	// loop thru the game posts to find the first game in the future
	foreach( $game_posts as $game ) {
		// Find first game time after the current time, and (just to be sure) has no result
				
		if ( get_post_meta( $game->ID, '_mstw_gs_unix_dtg', true ) > $current_dtg && 
				get_post_meta( $game->ID, '_mstw_gs_game_result', true ) == '' ) {
			if ( !$home_only || ( $home_only && get_post_meta( $game->ID, '_mstw_gs_home_game', true ) == 'home' ) ) {
				// Ding, ding, ding, we have a winner
				// Grab the data needed and stop looping through the games
				$have_games = true;
				//$game_date = get_post_meta( $game->ID, '_mstw_gs_unix_date', true );
				$game_dtg = get_post_meta( $game->ID, '_mstw_gs_unix_dtg', true );
				$opponent = get_post_meta( $game->ID, '_mstw_gs_opponent', true );
				$opponent_link = get_post_meta( $game->ID, '_mstw_gs_opponent_link', true );
				$gl_location = get_post_meta( $game->ID, '_mstw_gs_gl_location', true );
				$gl_loc_title = get_post_meta( $game->ID, '_mstw_gs_gl_loc_title', true );
				$location = get_post_meta( $game->ID, '_mstw_gs_location', true );
				$location_link = get_post_meta( $game->ID, '_mstw_gs_location_link', true );
				$game_time_tba = get_post_meta( $game->ID, '_mstw_gs_game_time_tba', true );
				break; 
			}
		}
	}
	
	// see what was found
	if ( ! $have_games ) {
		// No games scheduled after the current time
		if ( $home_only ) {
			$ret_msg = __( 'No home games found.', 'mstw-loc-domain' );
		}
		else {
			$ret_msg = __( 'No games found.', 'mstw-loc-domain' );
		}
		$ret_str .= '<span class="mstw-gs-cdt-intro">' . $ret_msg . '</span>';
	}
	else {
		// we found a game, so build the countdown display
		
		//full date-time group format 
		$cdt_dtg_format = ( $attribs['cdt_dtg_format'] == 'custom' ? $attribs['custom_cdt_dtg_format'] : $attribs['cdt_dtg_format'] ); 
		
		//date only format
		$cdt_date_format = ( $attribs['cdt_date_format'] == 'custom' ? $attribs['custom_cdt_date_format'] : $attribs['cdt_date_format'] ); 
		
		// Game day, date, time; need to handle a TBD time
		if ( $game_time_tba != '' ) { 
			$dtg_str = mstw_date_loc( $cdt_date_format, (int)$game_dtg ) . ' Time ' . $game_time_tba; 
			//$game_date is the UNIX timestamp DATE only
		}
		else {
			$dtg_str = mstw_date_loc( $cdt_dtg_format, (int)$game_dtg ); 
			//$dtg_str = "fmt: $cdt_date_format timestamp: $game_dtg";
			//$game_dtg is the full UNIX timestamp (DATE & TIME)  
        }
		
		$dtg_span = "<span class='mstw-gs-cdt-dtg mstw-gs-cdt-dtg_$sched'>";
		$ret_str .= $dtg_span . $dtg_str . '</span><br/>';
		
		// Add the opponent & location
		$opponent_entry = mstw_gs_build_opponent_entry( $game, $attribs, 'table' );
		//$post - the game post
		//$options - the combined base and dtg options, args, atts
		//$entry_type - "slider" or "table" controls image used/image size
		//Defaults to "table", which is the smaller size);
		
		$location_entry = mstw_gs_build_location_entry( $game, $attribs );
		
		$opp_span = "<span class='mstw-gs-cdt-opponent mstw-gs-cdt-opponent_$sched'>";
		$loc_span = "<span class='mstw-gs-cdt-location mstw-gs-cdt-location_$sched'>";
	
		$ret_str .= $opp_span . $opponent_entry . '</span>' . $loc_span . ' @ ' . $location_entry .  '</span><br/>';
		
		// Add the intro text set in shortcut arg or widget setting
		$intro_span = "<span class='mstw-gs-cdt-intro mstw-gs-cdt-intro_$sched'>";
		$ret_str .= $intro_span . $intro . '</span><br/>';
		
		// Add the countdown
		settype($game_dtg, 'integer');
		$countdown_span = "<span class='mstw-gs-cdt-countdown mstw-gs-cdt-countdown_$sched'>";
		$ret_str .= $countdown_span . time_difference( $game_dtg - $current_dtg ) . '</span>';
		 
		//$ret_str .= '<span class="mstw-gs-cdt-countdown">' . time_difference( $game_dtg - $current_dtg ) . '</span>';
	}
						
	return $ret_str;
	
}

// --------------------------------------------------------------------------------------
// Add the shortcode handler, which will create the Schedule Slider on the user side.
// Handles the shortcode parameters, if there were any, 
// then calls mstw_gs_build_slider( ) to create the output
// --------------------------------------------------------------------------------------
	add_shortcode( 'mstw_gs_slider', 'mstw_gs_slider_handler' );

	function mstw_gs_slider_handler( $atts ) {
	
		//return '<pre>' . print_r( $atts, true ) . '</pre>';
	
		//$atts = shortcode_atts( array(
		//			'sched' => '1',
		//			'show_links' => 1,
		//			),
		//			$atts );
		
		// NEED TO ADD DEFAULTS >>
		
		// get the options set in the admin screen
		$base_options = get_option( 'mstw_gs_options' );
		$dtg_options = get_option( 'mstw_gs_dtg_options' );
		$options = array_merge( (array)$base_options, (array)$dtg_options );
		//$output = '<pre>OPTIONS:' . print_r( $options, true ) . '</pre>';
		//return $output;
	
		// and merge them with the defaults
		$defaults = array_merge( mstw_gs_get_defaults( ), mstw_gs_get_dtg_defaults( ) );
		$args = wp_parse_args( $options, $defaults );
		//$output .= '<pre>ARGS=$options+$defaults:' . print_r( $args, true ) . '</pre>';
		//return $output;
		
		// then merge the parameters passed to the shortcode with the result									
		$attribs = shortcode_atts( $args, $atts );
		//$output .= '<pre>ATTS:' . print_r( $atts, true ) . '</pre>';
		//$output .= '<pre>ATTRIBS:' . print_r( $attribs, true ) . '</pre>';
		//return $output;
		
		//get the schedule slug
		$sched_slug = $attribs['sched'];
		
		if ( $sched_slug ==  "" ) {
			return '<h3>No Schedule Specified </h3>';
		}
		
		$sched_slugs = explode( ',', $attribs['sched'] );
		//return '<pre>' . print_r($sched_slugs) . '</pre>';
		
		if ( $sched_slugs[0] ==  "" ) {
			return '<h3>No Schedule Specified </h3>';
		}
			
		// Get the posts
		$posts = get_posts( array( 	'numberposts' => -1,
									'post_type' => 'scheduled_games',
									'meta_query' => array(
														array(
															'key' => '_mstw_gs_sched_id',
															'value' => $sched_slugs,
															'compare' => 'IN'
														)
													),
							  
									'orderby' => 'meta_value', 
									'meta_key' => '_mstw_gs_unix_dtg',
									'order' => 'ASC' 
									)
							);	
					
		if( $posts ) {
			// find the next game
			$next_game = mstw_gs_get_next_game( $posts, current_time( 'timestamp' ) );

			//pulls $next_game_number and $next_game_id
			extract( $next_game, EXTR_OVERWRITE );
			
			$games_to_show = $attribs['games_to_show'];
			
			$games_to_show = ( $games_to_show == '' or $games_to_show == -1 ) ? 3 : $games_to_show;
			$nbr_of_games = count( $posts );
			
			// if $next_game_id == -2 no games were found in the schedule, which should never happen due to the if( $posts ) check above
			// if $next_game_id == -1 no games were found in the schedule after the current time
			if ( $next_game_id == -2 ) {
				return "<h3>" . __( 'No games found on schedule ', 'mstw-loc-domain' ) . $sched_slug . "</h3>\n";
			} 
			else if ($next_game_id == -1 ) {
				$next_game_number = $nbr_of_games - $games_to_show;
			}
			
			//Ya never know when there's only 2 games on a schedule
			$next_game_number = max( 0, min( $next_game_number, $nbr_of_games - $games_to_show ) );
			
			$mstw_gs_slider = mstw_gs_build_slider( $posts, $attribs, $next_game_number+1 );
				
		} 
		else {
			return "<h3>" . __( 'No games found on schedule ', 'mstw-loc-domain' ) . $sched_slug . "</h3>\n";
		}
		
		return $mstw_gs_slider;
	}
	
	//================================================================================
	// MSTW_GS_BUILD_SLIDER
	//	Does the heavy lifting to build the game schedule slider for the [mstw_gs_slider] shortcode
	// ARGS:
	//	$games = an array of game posts
	//	$atts => the game schedules plug-in display settings and the shortcode arguments combined
	//	$game_number => the first game to be displayed (slider offset is calculated based on this value)
	//	$schedule_title_label => the label for the schedule title (defaults to "Schedule")
	// RETURN:
	//	an HTML string containing the slider to be displayed
	//================================================================================
	function mstw_gs_build_slider( $games, $atts, $game_number ) { // $schedule_title_label ) {
	
	    //return '<pre>' . print_r( $atts, true ) . '</pre>';
		
		$sched_ids = explode( ',', $atts['sched'] );
		
		//return '<p>' . print_r( $sched_ids, true ) . '</p>';
		
		$css_tag = '_' . $sched_ids[0];
		
		$slider_title = $atts['title'];
		( $slider_title == '' ? $show_slider_title = 0 : $show_slider_title = 1 );
		
		//$show_slider_schedule_link = 1;
		
		if ( $atts['link'] == '' or $atts['link_label'] == '' ) {
			$show_slider_schedule_link = 0;
		}
		else {
			$show_slider_schedule_link = 1;
			$slider_link = $atts['link'];
			$slider_link_label = $atts['link_label'];
		}
		
		//return '<pre>' . print_r( $atts, true ) . '</pre>';
		
		$game_block_width = 187;
		//$schedule_slider_width = 3000; //DEFAULT ONLY. CALCULATED BELOW BASED ON THE # OF GAMES
		$schedule_view_width = 584; //DEFAULT. CALCULATED BELOW BASED ON GAMES_TO_SHOW

		$nbr_of_games = sizeof( $games );
	
		$games_to_show = $atts['games_to_show'];
		$games_to_show = ( $games_to_show == '' or $games_to_show == -1 ) ? 3 : $games_to_show;
		$slider_view_width = $games_to_show*$game_block_width+10 . 'px';
		
		$slider_view_height = ( $atts['show_slider_logos'] == 'name-only' ? '197px' : '250px' );
		
		// this is the entire width the 10 accounts for the size of the right arrow bar
		$schedule_slider_width = $nbr_of_games*$game_block_width+10 . 'px';
		// postions the next game on the left
		$game_number = min( $game_number, $nbr_of_games-$games_to_show+1 );
		$schedule_slider_offset = ($game_number > 0 ? (-1)*($game_number-1)*$game_block_width : 0) . 'px';
		
		$output = '';
		//$output = '<pre>' . print_r( $atts, true ) . '</pre>';
		//return $output;
		
		
		$output .= "<div class='gs-slider-area gs-slider-area$css_tag' style='width:$slider_view_width;'>\n"; //height:$slider_view_height;'>\n";
		$output .= "<div class='gs-slider gs-one-edge-shadow gs-one-edge-shadow" . $css_tag . "'>\n";
		$output .= "<div class='border border" . $css_tag . "'>\n";
		$output .= "<div class='box box" . $css_tag . "'>\n";
			if ( $show_slider_title ) {
				$output .= "<div class='title title" . $css_tag . "'>\n";
					$output .= $slider_title;
				$output .= "</div> <!--end .title-->\n";
			
				if ( $show_slider_schedule_link ) {
					$output .= "<div class='full-schedule-link full-schedule-link" . $css_tag . "'>\n";
						$output .= "<a href='" . $slider_link . "' target='_blank' >" . $slider_link_label . "</a>\n";
					$output .= "</div> <!--end .full-schedule-link-->\n";
				}
				
				$output .= "<div class='gs-clear'></div>\n";
				$output .= "<div class='gs-divider gs-divider" . $css_tag . "'></div>\n";
			}
		
		$output .= "<div class='content' style='height:$slider_view_height;'>\n";
		$output .= "<div id='schedule-slider' class='schedule-slider  schedule-slider$css_tag' style='width:$schedule_slider_width; height:$slider_view_height; left: $schedule_slider_offset; position:absolute;'>\n";
		
		
			//$output .= "This is the output from mstw_gs_build_slider( )\n";
			foreach ( $games as $game ) {
				$output .= mstw_gs_build_game_block( $game, $atts, $css_tag );
			}
		$output .= "</div> <!--end .schedule-slider-->\n";
		
		// Add the scroll controls - right and left arrows
		$output .= "<div class='gs-clear'></div>\n";
		$output .= "<div id='gs-slider-right-arrow' class='gs-slider-right-arrow $css_tag' style='height:$slider_view_height; line-height:$slider_view_height;'>&rsaquo;</div>\n";
		$output .= "<div id='gs-slider-left-arrow' class='gs-slider-left-arrow $css_tag'  style='height:$slider_view_height; line-height:$slider_view_height;'>&lsaquo;</div>\n";
		
		$output .= "</div> <!--end .content-->\n";
		
		$output .= "</div> <!--end .box-->\n";
		$output .= "</div> <!--end .border-->\n";
		$output .= "</div> <!--end .gs-slider-->\n";
		$output .= "</div> <!--end .gs-slider-area-->\n";
		
		return $output;
	}

//================================================================================
// MSTW_GS_BUILD_GAME_BLOCK
//	Called by mstw_gs_build_slider() to build the html for ONE game block
//	Returns an HTML string
// 	
	function mstw_gs_build_game_block( $game, $options, $css_tag ) {
		
		//$options should include both the base options (mstw_gs_options and the dtg options mstw_gs_dtg_options
		extract( $options );
		
		$slider_date_format = ( $slider_date_format == 'custom' ? $custom_slider_date_format : $slider_date_format ); 
		
		$slider_time_format = ( $slider_time_format == 'custom' ? $custom_slider_time_format : $slider_time_format );
		
		$ret = '';
		$ret .= "<div class='game-block'>\n";
			$ret .= "<div class='date date" . $css_tag . " pad'>\n";
				$ret .= mstw_date_loc( $slider_date_format, (int)get_post_meta( $game->ID, '_mstw_gs_unix_dtg', true ) );
			$ret .= "</div> <!--end .date-->\n";
			
			$ret .= "<div class='opponent opponent" . $css_tag . " pad'>\n";
				$ret .= mstw_gs_build_opponent_entry( $game, $options, "slider" );
			$ret .= "</div> <!--end .opponent-->\n";
			
			//$location = get_post_meta( $game->ID, '_mstw_gs_location', true );
			//$location_link = get_post_meta( $game->ID, '_mstw_gs_location', true );
			
			$location_entry = mstw_gs_build_location_entry( $game, $options );
			
			if( trim( $location_entry ) != '' and !empty( $location_entry ) ) {
				$ret .= "<div class='location location" . $css_tag . " pad'>\n";
					//$ret .= "@ " . get_post_meta( $game->ID, '_mstw_gs_location', true );
				if( $location_entry[0] != '&' ) {
					$ret .= "@ ";
				}
					
				$ret .= $location_entry;
					
				$ret .= "</div> <!--end .location-->\n";
			}
			
			$ret .= "<div class='time-result time-result" . $css_tag . " pad'>\n";
			
			$game_result = get_post_meta( $game->ID, '_mstw_gs_game_result', true );
			$game_time_tba = get_post_meta( $game->ID, '_mstw_gs_game_time_tba', true );
			
			if ( $game_result != '' ) {
				$ret .= $game_result;
			} 
			else if ( $game_time_tba != '' ) {
				$ret .= $game_time_tba;
			}
			else {
				$ret .= date( $slider_time_format, get_post_meta( $game->ID, '_mstw_gs_unix_dtg', true ) );
			}
				
			$ret .= "</div> <!--end .time-result-->\n";
			
			$ret .= "<div class='links pad'>\n";
				$ret .= mstw_gs_build_media_links( $game ); //"<a href='http://shoalsummitsolutions.com'>Links Area</a>\n";
			$ret .= "</div> <!--end .links-->\n";
			
		$ret .= "</div> <!--end .game-block-->\n";
		return $ret;
	}
	
	function mstw_gs_build_media_links( $post ) {
		$media_links = ''; // return string
		
		// testing with label 1 for now
		$label_1 = trim( get_post_meta( $post->ID, '_mstw_gs_media_label_1', true ) );
		if (  $label_1 <> '' ) {
			$url_1 = trim( get_post_meta( $post->ID, '_mstw_gs_media_url_1', true ) );
			if ( $url_1 <> '' ) {
				// build the link
				$media_links .= '<a href="' . $url_1 . '" target="_blank">' . $label_1 . '</a>';
			}
			else {
				// label without the link
				$media_links .= $label_1; 
			}
			
			if ( ( $label_2 = trim( get_post_meta( $post->ID, '_mstw_gs_media_label_2', true ) ) ) <> '' ) {
				if ( ( $url_2 = trim( get_post_meta( $post->ID, '_mstw_gs_media_url_2', true ) ) ) <> '' ) {
					// build the link
					$media_links .= ' | <a href="' . $url_2 . '" target="_blank">' . $label_2 . '</a>';
				}
				else {
					// label without the link
					$media_links .= ' | ' . $label_2;
				}
			}
		
		}
		
		//$media_links = 'label_l: ' . $label_1 . ' url_1: ' . $url_1;
		
		return $media_links;
	}
	
	//================================================================================
	// MSTW_GS_GET_NEXT_GAME
	//	Finds the next game AFTER a specified date time group
	// Args:
	//	$games = an array of game posts
	//	dtg => a php time stamp
	// Return:
	//	An array of information:
	//	next_game_id 
	//		WP ID for next game if found, otherwise
	//		-1 if no game was found with a start DTG after the dtg argument
	//		-2 if $games is empty
	//	next_game_number
	//		number of next game [ 0 to sizeof($games)-1 ] 
	//		(if found, otherwise see next_game_id)
	//	next_game_dtg
	//		PHP time stamp (date-time-group) for next game 
	//		(if found, otherwise see next_game_id)
	//	next_game_opponent
	//		PHP time stamp for next game 
	//		(if found, otherwise see next_game_id)
	//================================================================================
	function mstw_gs_get_next_game( $games, $dtg ) {
		// No game has been found (yet)
		$retval = array( 'next_game_id' 	=> -1,
						 'next_game_number'	=> -1,
						 'next_game_dtg'	=> -1,
						 'next_game_opponent'	=> '-1',
						 ); 

		// loop thru the game posts to find the first game in the future
		$next_game_number = 0;
		if ( $games ) {
			foreach( $games as $game ) {
				// Find first game time after the current time, and (just to be sure) has no result		
				if ( get_post_meta( $game->ID, '_mstw_gs_unix_dtg', true ) > $dtg ) {
					// Ding, ding, ding, we have a winner
					// Grab the data needed and stop looping through the games
					$retval['next_game_id'] = $game->ID;
					$retval['next_game_number'] = $next_game_number;
					$retval['next_game_dtg'] = get_post_meta( $game->ID, '_mstw_gs_unix_dtg', true );
					$retval['next_game_opponent'] = get_post_meta( $game->ID, '_mstw_gs_opponent', true );
					
					//return $game->ID;
					break;
				}
				$next_game_number++;
			}
		} else {  
			$retval['next_game_id'] =  -2; 
		}
		
		return $retval;

	}

/*--------------------------------------------------------------------------
 *	Build the string showing the countdown time (in years, months, days,
 *	hours, minutes, seconds) based on the difference between two UNIX
 *	timestamps (delta in seconds).
 *	Replaces the datediff() function used in previous versions of the plugin
 *--------------------------------------------------------------------------*/
	function time_difference( $endtime ) {
		$days = (date("j",$endtime)-1);
		$months = (date("n",$endtime)-1);
		$years = (date("Y",$endtime)-1970);
		$hours = date("G",$endtime);
		$mins = date("i",$endtime);
		$secs = date("s",$endtime);
		$diff = '';
		
		if ($years > 0 )
			$diff .= $years . ' ' . __('years', 'mstw-loc-domain') . ', ';
		if ($months > 0 )
			$diff .= $months . ' ' . __('months', 'mstw-loc-domain') . ', ';
		if ($days > 0 )
			$diff .= $days . ' ' . __('days', 'mstw-loc-domain') . ', ';
		if ($hours > 0 )
			$diff .= $hours . ' ' . __('hours', 'mstw-loc-domain') . ', ';
		
		$diff .= $mins . ' ' . __('minutes', 'mstw-loc-domain');
		
		return $diff;
}

/* ------------------------------------------------------------------------
 *
 * Game Schedule Widgets
 *	- mstw_gs_sched_widget - displays the schedule as a simple 
 *		date-opponent table
 *	- mstw_gs_countdown_widget - counts down the time from now 
 *		until the next scheduled game
 *
 *-----------------------------------------------------------------------*/

// ----------------------------------------------------------------
// First, use 'widgets_init' hook to register the widgets
// ----------------------------------------------------------------

add_action( 'widgets_init', 'mstw_gs_register_widgets' );

 //register our widgets
function mstw_gs_register_widgets() {
    register_widget( 'mstw_gs_sched_widget' );
	register_widget( 'mstw_gs_countdown_widget' );
}

/*--------------------------------------------------------------------
 *
 * mstw_gs_sched_widget
 *	- displays a simple schedule (table) with date and opponent columns
 *  - does NOT include opponent links (no particular reason other than K.I.S.S.)
 *
 *------------------------------------------------------------------*/
class mstw_gs_sched_widget extends WP_Widget {

    //process the new widget
    function mstw_gs_sched_widget( ) {
        $widget_ops = array( 
			'classname' => 'mstw_gs_sched_widget_class', 
			'description' => 'Display a team schedule.' 
			); 
        $this->WP_Widget( 'mstw_gs_sched_widget', 'Game Schedule', $widget_ops );
    }
 
     //build the widget settings form
    function form( $instance ) {
        $defaults = array(	'sched_title' => 'Schedule', 
							'sched_id' => '1', 
							'sched_yr' => date('Y'),
							'sched_start_date' => 0, 
							'sched_end_date' => PHP_INT_MAX, //strtotime( '2999-12-31'), 
							'sched_max_to_show' => -1, 
							); 
							
        $instance = wp_parse_args( (array) $instance, $defaults );
		$sched_title = $instance['sched_title'];
		$sched_id = $instance['sched_id'];
		
		//$sched_start_date = $instance['sched_start_date'];
		if ( $instance['sched_start_date'] == 'now' ) {
			$sched_start_date = 'now';
		}
		else {
			$sched_start_date = date( 'Y-m-d H:i', (int)esc_attr( $instance['sched_start_date'] ) );
		}
		
		$sched_end_date = $instance['sched_end_date'];
		$sched_max_to_show = $instance['sched_max_to_show'];
		
        ?>
        <p>Schedule Title: <input class="widefat" name="<?php echo $this->get_field_name( 'sched_title' ); ?>"  
            					type="text" value="<?php echo esc_attr( $sched_title ); ?>" /></p>
        <p>Schedule ID: <input class="widefat" name="<?php echo $this->get_field_name( 'sched_id' ); ?>"  
        						type="text" value="<?php echo esc_attr( $sched_id ); ?>" /></p>
		<p>The dates below MUST be in the format yyyy-mm-dd hh:mm. (You can omit the hh:mm for 00:00.) Otherwise, you can expect unexpected results. Use 'now' as the start date to show only future games.</p>
		<p>Display Start Date: <input class="widefat" name="<?php echo $this->get_field_name( 'sched_start_date' ); ?>"	type="text" value="<?php echo $sched_start_date; ?>" />
		</p>
        <p>Display End Date: <input class="widefat" name="<?php echo $this->get_field_name( 'sched_end_date' ); ?>"  type="text" value="<?php echo date('Y-m-d H:i', (int)esc_attr( $sched_end_date ) ); ?>" />
		</p>
		<p>Maximum # of games to show (-1 to show all games): <input class="widefat" name="<?php echo $this->get_field_name( 'sched_max_to_show' ); ?>" type="text" value="<?php echo esc_attr( $sched_max_to_show ); ?>" />
		</p>
        <?php
    }
 
    //save the widget settings
    function update($new_instance, $old_instance) {
		
        $instance = $old_instance;
		
		$instance['sched_title'] = strip_tags( $new_instance['sched_title'] );

		$instance['sched_id'] = strip_tags( $new_instance['sched_id'] );
		
		// 'now' means use the current date
		if ( $new_instance['sched_start_date'] == 'now' ) {
			$instance['sched_start_date'] = $new_instance['sched_start_date'];
		}
		else {
			$instance['sched_start_date'] = strtotime( strip_tags( $new_instance['sched_start_date'] ) );
		}
		
		$instance['sched_end_date'] = strtotime( strip_tags( $new_instance['sched_end_date'] ) );
		
		$instance['sched_max_to_show'] = strip_tags( $new_instance['sched_max_to_show'] );
 
        return $instance;
		
    }
 
	//--------------------------------------------------------------------
	// displays the schedule widget
	//
	function widget( $args, $instance ) {
		// $args holds the global theme variables, such as $before_widget
		extract( $args );
		
		// get the options set in the admin display settings screen
		$base_options = get_option( 'mstw_gs_options' );
		$dtg_options = get_option( 'mstw_gs_dtg_options' );
		//$options = get_option( 'mstw_gs_options' );
		$options = array_merge( $base_options, $dtg_options );
		
		// Remove all keys with empty values
		foreach ( $options as $k=>$v ) {
			if( $v == '' ) {
				unset( $options[$k] );
			}
		}
		
		// and merge them with the defaults
		$defaults = array_merge( mstw_gs_get_defaults( ), mstw_gs_get_dtg_defaults( ) );
		$options = wp_parse_args( $options, $defaults );
		
		//echo "<pre>" . print_r( $options ) . "</pre>";
		//echo "<pre>" . print_r( $instance ) . "</pre>";
		//return;
		
		// then merge the parameters passed to the widget with the result									
		//$attribs = shortcode_atts( $args, $atts );
		
		//$options = wp_parse_args( $options, mstw_gs_get_defaults() );
		
		//Build the date format from the display settings
		$date_format = ( $options['table_widget_date_format'] == 'custom' ? $options['custom_table_widget_date_format'] : $options['table_widget_date_format'] );
		
		echo $before_widget;
		
		$title = apply_filters( 'widget_title', $instance['sched_title'] );
		
		// Get the parameters for get_posts() below
		$sched_id = $instance['sched_id'];
		
		if ( $instance['sched_start_date'] == 'now' ) {
			$first_dtg = time( );
		}
		else {
			$first_dtg = $instance['sched_start_date'];
		}
		
		$last_dtg = $instance['sched_end_date'];
		
		$max_to_show = $instance['sched_max_to_show']; 
		
		// show the widget title, if there is one
		if( !empty( $title ) ) {
			echo  $before_title . $title . $after_title;
		}
		
		// Get the game posts for $sched_id 
		$posts = get_posts(array( 'numberposts' => $max_to_show,
								  'relation' => 'AND',
							  	  'post_type' => 'scheduled_games',
							  	  'meta_query' => array(
												array(
													'key' => '_mstw_gs_sched_id',
													'value' => $sched_id,
													'compare' => '='
												),
												array(
													'key' => '_mstw_gs_unix_dtg',
													'value' => array( $first_dtg, $last_dtg),
													'type' => 'NUMERIC',
													'compare' => 'BETWEEN'
												)
											),						  
							  	  'orderby' => 'meta_value', 
							  	  'meta_key' => '_mstw_gs_unix_dtg',
							      'order' => 'ASC' 
							));						
	
   	 	// Make table of posts
		if($posts) {	
			// Start with the table header
        	$output = ''; ?>
        
        	<table class="mstw-gs-sw-tab mstw-gs-sw-tab-<?php echo $sched_id; ?>">
        	<thead class="mstw-gs-sw-tab-head mstw-gs-sw-tab-head-<?php echo $sched_id; ?>"><tr>
				<?php if( $options['show_date'] == 1 ) { ?>
					<th><?php _e( $options['date_label'], 'mstw-loc-domain' ); ?></th>
				<?php } ?>
				<?php if( $options['opponent_label'] == "" ) { ?>
					<th><?php _e( 'Opponent', 'mstw-loc-domain' ); ?></th>
				<?php } else {?>
					<th><?php _e( $options['opponent_label'], 'mstw-loc-domain' ); ?></th>
				<?php } ?>
				
					
			</tr></thead>
        
			<?php
			// Loop through the posts and make the rows
			$even_and_odd = array('even', 'odd');
			$row_cnt = 1; // Keeps track of even and odd rows. Start with row 1 = odd.
		
			foreach( $posts as $post ) {
				// set up some housekeeping to make styling in the loop easier
				$is_home_game = get_post_meta($post->ID, '_mstw_gs_home_game', true );
				$even_or_odd_row = $even_and_odd[$row_cnt]; 
				$row_class = "mstw-gs-sw-$even_or_odd_row mstw-gs-sw-$even_or_odd_row" . "_$sched_id";
				if ( $is_home_game == 'home' ) 
					$row_class = $row_class . ' mstw-gs-sw-home';
			
				$row_tr = '<tr class="' . $row_class . '">';
				//$row_tr = '<tr>';
				$row_td = '<td class="' . $row_class . '">'; 
			
				// create the row
				$row_string = $row_tr;		
			
				// column 1: Build the game date in a specified format
				if( $options['show_date'] == 1 ) { 
					$date_string = mstw_date_loc( $date_format, (int)get_post_meta( $post->ID, '_mstw_gs_unix_dtg', true ) );
				
					$row_string = $row_string. $row_td . $date_string . '</td>';
				}
				// column 2: create the opponent entry
				//$opponent = get_post_meta( $post->ID, '_mstw_gs_opponent', true);
				$opponent = mstw_gs_build_opponent_entry( $post, $options, 'table' );
				
				if ( $is_home_game != 'home' ) {
					$opponent = '@ ' . $opponent;
				}
				
				$row_string =  $row_string . $row_td . $opponent . '</td>';
			
				/*
				// Might want to add this at some point
				// column 4: create the time/results entry
				if ( get_post_meta( $post->ID, '_mstw_gs_game_result', true) != '' ) {
					$row_string =  $row_string . $row_td . get_post_meta( $post->ID, '_mstw_gs_game_result', true) . '</td>';
				}	
				else {	
					$row_string =  $row_string . $row_td . get_post_meta( $post->ID, '_mstw_gs_game_time', true) . '</td>';
				}
				*/
		
				echo $row_string . '</tr>';
			
				$row_cnt = 1- $row_cnt;  // Get the styles right
			
			} // end of foreach post

			echo '</table>';
		}
		else { // No posts were found

			_e( 'No Scheduled Games Found', 'mstw-loc-domain' );

		} // End of if ($posts)
		
		echo $after_widget;
	
	} // end of function widget( )
} // End of class mstw_gs_sched_widget


/*--------------------------------------------------------------------
 *
 * mstw_gs_countdown_widget
 *	- displays a countdown timer to the next scheduled game
 *
 *------------------------------------------------------------------*/

class mstw_gs_countdown_widget extends WP_Widget {

/*--------------------------------------------------------------------
 * construct the widget
 *------------------------------------------------------------------*/	
	function mstw_gs_countdown_widget( ) {
		// processes the widget
		 $widget_ops = array( 
			'classname' => 'mstw_gs_countdown_widget_class', 
			'description' => 'Display a countdown timer to the next scheduled game.' 
			); 
        $this->WP_Widget( 'mstw_gs_countdown_widget', 'Schedule Countdown Timer', $widget_ops );
	}
	
/*--------------------------------------------------------------------
 * display/manage the countdown widget settings form
 *------------------------------------------------------------------*/
	
	function form( $instance ) {
	
        $defaults = array(	'cd_title' => 'Countdown', 
							'sched' => '1', 
							'intro' => 'Time to kickoff:', 
							'home_only' => '', 
							); 
							
		$instance = wp_parse_args( (array) $instance, $defaults );
							
		//$options = get_option( 'mstw_gs_options' );
		//$output = '<pre>OPTIONS:' . print_r( $options, true ) . '</pre>';
	
		// and merge them with the defaults
		//$new_args = wp_parse_args( $options, mstw_gs_get_defaults( ) );
		//$output .= '<pre>ARGS:' . print_r( $args, true ) . '</pre>';
	
		// then merge the parameters passed to the shortcode with the result									
		//$attribs = wp_parse_args( $new_args, (array) $instance );					
		
        
		$cd_title = $instance['cd_title'];
		$sched = $instance['sched'];
		$home_only = $instance['home_only'];
		$intro = $instance['intro'];
		
        ?>
        <p>Countdown Title: <input class="widefat" name="<?php echo $this->get_field_name( 'cd_title' ); ?>"  
            					type="text" value="<?php echo esc_attr( $cd_title ); ?>" /></p>
        
        <p>Schedule ID: <input class="widefat" name="<?php echo $this->get_field_name( 'sched' ); ?>"  
        						type="text" value="<?php echo esc_attr( $sched ); ?>" /></p> 
		
		<p><input class="checkbox" type="checkbox" <?php checked( $attribs['home_only'], 'on' ); ?> id="<?php echo $this->get_field_id( 'home_only' ); ?>" name="<?php echo $this->get_field_name( 'home_only' ); ?>" /> 
		<label for="<?php echo $this->get_field_id( 'home_only' ); ?>">Use home games only?</label></p>
		
        <p>Countdown Intro Text: <input class="widefat" name="<?php echo $this->get_field_name( 'intro' ); ?>"
        						type="text" value="<?php echo esc_attr( $intro ); ?>" /></p>
            
        <?php 
    }
	
/*--------------------------------------------------------------------
 * saves the countdown widget settings
 *------------------------------------------------------------------*/	
   function update($new_instance, $old_instance) {
		
        $instance = $old_instance;
		
		$instance['cd_title'] = strip_tags( $new_instance['cd_title'] );

		$instance['sched'] = strip_tags( $new_instance['sched'] );
		
		$instance['home_only'] = strip_tags( $new_instance['home_only'] );
		
		$instance['intro'] = strip_tags( $new_instance['intro'] );
 
        return $instance;
		
    }
	
/*--------------------------------------------------------------------
 * displays the countdown widget
 *------------------------------------------------------------------*/		

	function widget( $args, $instance ) {
		
		// $args holds the global theme variables, such as $before_widget
		extract( $args );
		
		echo $before_widget;
		
		$title = apply_filters( 'widget_title', $instance['cd_title'] );
		
		// get the options set in the admin screen
		$dtg_options = get_option( 'mstw_gs_dtg_options' );
		$base_options = get_option( 'mstw_gs_options' );
		$options = array_merge( $base_options, $dtg_options );
		//$output = '<pre>OPTIONS:' . print_r( $options, true ) . '</pre>';
		
		// Remove all keys with empty values
		foreach ( $options as $k=>$v ) {
			if( $v == '' ) {
				unset( $options[$k] );
			}
		}
	
		// and merge them with the defaults
		$new_args = wp_parse_args( $options, mstw_gs_get_defaults( ) );
		//$output .= '<pre>ARGS:' . print_r( $args, true ) . '</pre>';
	
		// then merge the parameters passed to the shortcode with the result									
		$attribs = wp_parse_args( (array) $instance, $new_args );
		//$output = '<pre>NEW ARGS:' . print_r( $new_args, true ) . '</pre>';
		//$output .= '<pre>INSTANCE:' . print_r( $instance, true ) . '</pre>';
		//echo $output;
		
		// Get the parameters for get_posts() below
		//$sched = $instance['sched'];
		//$home_only = $instance['home_only'];
		//$intro = $instance['intro'];
		
		if( !empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}
			
        echo mstw_gs_build_countdown( $attribs ); 
		
		//echo '<pre>' . print_r( $attribs, true ) . '</pre>';
		//return;
        
        //echo $cd_str;
		
		echo $after_widget;
      	
	} // end of function widget()
	
} // end of class mstw_gs_countdown_widget

/*------------------------------------------------------------------------------------
 * This is a modification of the date function (line 997 for example) for use
 * in WP internationalization/localization. Ff you have created a translation the plugin 
 * and set the WP_LANG variable in the wp-config.php file, this will work (at least for
 * most date formats). If you don't understand WordPress internationalization, you would
 * be well advised to read the codex before jumping in to this pool.
--------------------------------------------------------------------------------------*/
function mstw_date_loc($format, $timestamp = null) {
	
	$param_D = array( '', 
						__( 'Mon', 'mstw-loc-domain' ), 
						__( 'Tue', 'mstw-loc-domain' ), 
						__( 'Wed', 'mstw-loc-domain' ), 
						__( 'Thr', 'mstw-loc-domain' ), 
						__( 'Fri', 'mstw-loc-domain' ), 
						__( 'Sat', 'mstw-loc-domain' ), 
						__( 'Sun', 'mstw-loc-domain' ) );
	
	//$param_l = array('', 'lunes', 'martes', 'mi&eacute;rcoles', 'jueves', 'viernes', 's&aacute;bado', 'domingo');
	$param_l = array( '', 
						__( 'Monday', 'mstw-loc-domain' ), 
						__( 'Tuesday', 'mstw-loc-domain' ), 
						__( 'Wednesday', 'mstw-loc-domain' ), 
						__( 'Thursday', 'mstw-loc-domain' ), 
						__( 'Friday', 'mstw-loc-domain' ), 
						__( 'Saturday', 'mstw-loc-domain' ), 
						__( 'Sunday', 'mstw-loc-domain' ) );
						
	//$param_F = array('', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'Septembre', 'Octobre', 'Novembre', 'D&eacute;cembre');
	$param_F = array( '', 
						__( 'January', 'mstw-loc-domain' ), 
						__( 'February', 'mstw-loc-domain' ), 
						__( 'March', 'mstw-loc-domain' ), 
						__( 'April', 'mstw-loc-domain' ), 
						__( 'May', 'mstw-loc-domain' ), 
						__( 'June', 'mstw-loc-domain' ),
						__( 'July', 'mstw-loc-domain' ),
						__( 'August', 'mstw-loc-domain' ),
						__( 'September', 'mstw-loc-domain' ),
						__( 'October', 'mstw-loc-domain' ),
						__( 'November', 'mstw-loc-domain' ),
						__( 'December', 'mstw-loc-domain' ) );
						
	//$param_M = array('', 'enero', 'feb.', 'marzo', 'abr.', 'mayo', 'jun.', 'jul.', 'agosto', 'sept.', 'oct.', 'nov.', 'dic.');
	$param_M = array( '', 
						__( 'Jan', 'mstw-loc-domain' ), 
						__( 'Feb', 'mstw-loc-domain' ), 
						__( 'Mar', 'mstw-loc-domain' ), 
						__( 'Apr', 'mstw-loc-domain' ), 
						__( 'May', 'mstw-loc-domain' ), 
						__( 'Jun', 'mstw-loc-domain' ),
						__( 'Jul', 'mstw-loc-domain' ),
						__( 'Aug', 'mstw-loc-domain' ),
						__( 'Sep', 'mstw-loc-domain' ),
						__( 'Oct', 'mstw-loc-domain' ),
						__( 'Nov', 'mstw-loc-domain' ),
						__( 'Dec', 'mstw-loc-domain' ) );
	

	$return = '';
	
	if ( is_null( $timestamp ) ) { 
		$timestamp = current_time( 'timestamp' ); 
	}
	
	for( $i = 0, $len = strlen( $format ); $i < $len; $i++ ) {
		switch($format[$i]) {
			case '\\' : // double.slashes
				$i++;
				$return .= isset($format[$i]) ? $format[$i] : '';
				break;
			case 'D' :
				$return .= $param_D[date('N', $timestamp)];
				break;
			case 'l' :
				$return .= $param_l[date('N', $timestamp)];
				break;
			case 'F' :
				$return .= $param_F[date('n', $timestamp)];
				break;
			case 'M' :
				$return .= $param_M[date('n', $timestamp)];
				break;
			default :
				$return .= date($format[$i], $timestamp);
				break;
		}
	}
	return $return;
}
?>