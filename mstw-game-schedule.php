<?php
/*
Plugin Name: Game Schedule
Plugin URI: http://wordpress.org/extend/plugins/
Description: The Game Schedule Plugin defines a custom type - Scheduled Games - for use in the MySportTeamWebite framework. Generations a game schedule (html table) using a shortcode.
Version: 3.0
Author: Mark O'Donnell
Author URI: http://shoalsummitsolutions.com
*/

/*
Game Schedule (Wordpress Plugin)
Copyright (C) 2012 Mark O'Donnell
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

/* ------------------------------------------------------------------------
 * 20120821-MAO: Updated for use in MC Football website
 *	(1) Changed mstw_gs_remove_view to mstw_gs_remove_the_view to avoid a collision
 *		with mstw-game-locations plugin (a bug that needs repair in that plugin
 *		... should be mstw_gl_remove_view there)
 *	(2)	Removed GameDayInBerkeley specific changes to countdown shortcode handler
 *
 * 20120928-MAO:
 *	(1)	Corrected a bug in the shortcode for displaying a schedule [essentially 
 *		function mstw_gs_build_sched_tab( $sched, $year )] to allow multiple shortcodes
 *		to be used [multiple schedules to be displayed] on a single page.
 *
 * 20121003-MAO:
 *	(1) Began work on adding an opponent link (mstw_gs_opponent_link) that will allow
 *		the user to link to any URL from the opponent field in the table and/or the 
 *		opponent field in the widget.
 *
 * 20121011-MAO:
 *	(1)	Began adding localization functions _e() and __().
 *		Added global variable for the localization domain $mstw_domain = 'mstw-loc-domain';
 *		Working first on displays to user. Assuming developer/admin can understand English (for now).
 *		Added action 'mstw_load_localization' 'after_theme_setup' [May need in the widget as well?]
 *
 * 20121014-MAO:
 *	(1) Finished Rev 2.0. 
 *	(2)	"Year" is not used to define schedules, it is just part of each
 *		games date now. This is a big deal because schedules can go across years now.
 *	(3)	Lot's of localization was done, but some challenges remain with date() and with
 *		the countdown timer (due to the diff function.) I only localized the 'user' side for now,
 *		the adminstration pages are still English. 
 *	(4) Opponent_link field was added.
 *	(5)	See readme for more details.
 *
 * 20121103-MAO:
 *	(1)	Fixed bug with opponent links. 
 *	(2) Fixed bug with enqueueing stylesheet.
 *
 * 20121104-MAO:
 *	(1) Added location_link field. Links from the location entries of games.
 *
 * 20121109-MAO:
 *	(1) Changed date() to mstw_date_loc() for internationalization in the
 *		table [shortcode] and widget.
 *	(2) Removed all remnants of the test current time - cd_test_now.
 *	(3)	Cleaned up the comments in the mstw_date_loc() function header.
 *
 * 20121117-MAO:
 *	(1) Changed date() to mstw_date_loc() - forgot part of the shortcode.
 *	(2)	Added $mstw_gs_time_format to support changing the date format on 
 *		the schedule table [shortcode].
 *	(3)	Updated the Croatian translation. 
 *
 * 20121127-MAO:
 *	(1) Removed an extraneous line that added "Sept" in addition to "Sep"
 *		in the function mstw_date_loc(). This line was causing strange behavior
 *		of game dates when users upgraded.
 *
 * 201212023-MAO: 
 *	(1)	Added error checking to handle TBD game times in function mstw_gs_build_sched_tab()
 *
 * ------------------------------------------------------------------------
 * 20130206-MAO:  BEGAN VERSION VERSION 3.0
 *	(1) Replaced $mstw_domain with the hard-coded string value
 *	 	'mstw-loc-domain' having learned that using the global variable 
 *		can create problems.
 *	(2) Added logic to display new game location field and Google maps 
 *		link from the Game Locations plugin.
 *	(3) Added argument to shortcode to hide media column
 *	(4) Added check for gs_hide_media admin setting, which overrides
 *		the shortcode argument IF it's set to hide-media
 *	(5) Changed globals for date-time formats to use new admin settings
 *
 * 20130302-MAO: FIXES TO COUNTDOWN TIMER 
 *	(1) Changed the current time to get the WORDPRESS time instead of the
 *		system time as previous.
 *	(2)	Changed the actual countdown time (time to next game) construction
 *		due to some 'anomalies' with the previous version.
 * ------------------------------------------------------------------------*/

/* ------------------------------------------------------------------------
// PLUGIN PREFIX:                                                          
// 'mstw_gs_'   derived from mysportsteamwebsite game schedule
// -----------------------------------------------------------------------*/ 
/*---------------------------------------------------------------
 * Load the mstw-utility-functions if necessary
---------------------------------------------------------------*/
	//if ( !function_exists( 'mstw_set_wp_default_timezone' ) ) {
	//	require_once plugin_dir_path( __FILE__) . 'includes/mstw-utility-functions.php';
	//} 

	// This is important, have to see if it has global scope
	//mstw_set_wp_default_timezone( );

	// Get the admin options
	$options = get_option( 'mstw_gs_options' );
	
// Debug messages - used throughout	
	//$mstw_debug_str = '';
	
// ----------------------------------------------------------------
// If an admin, load the admin functions (once)
	if ( is_admin( ) ) {
		// we're in wp-admin
		require_once ( dirname( __FILE__ ) . '/includes/mstw-game-schedules-admin.php' );
    }
	
// ----------------------------------------------------------------
// Set up localization
add_action( 'init', 'mstw_load_localization' );
	
function mstw_load_localization( ) {
    load_plugin_textdomain( 'mstw-loc-domain', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
} // end custom_theme_setup
	
// ----------------------------------------------------------------
// Deactivate, request upgrade, and exit if WP version is not right
add_action( 'admin_init', 'mstw_gs_requires_wp_ver' );

// ----------------------------------------------------------------
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
}

// --------------------------------------------------------------------------------------
// Set-up Action and Filter Hooks for the Settings on the admin side
// --------------------------------------------------------------------------------------
register_activation_hook(__FILE__, 'mstw_gs_set_defaults');
register_uninstall_hook(__FILE__, 'mstw_gs_delete_plugin_options');
add_action('admin_init', 'mstw_gs_register_settings' );
// add_action('admin_menu', 'mstw_gs_add_options_page'); Code is still in place
//add_filter( 'plugin_action_links', 'mstw_plugin_action_links', 10, 2 );

// --------------------------------------------------------------------------------------
// Callback for: register_uninstall_hook(__FILE__, 'mstw_gs_delete_plugin_options')
// --------------------------------------------------------------------------------------
// It runs when the user deactivates AND DELETES the plugin. 
// It deletes the plugin options DB entry, which is an array storing all the plugin options
// --------------------------------------------------------------------------------------
function mstw_gs_delete_plugin_options() {
	delete_option('mstw_gs_options');
}

// --------------------------------------------------------------------------------------


/* Queue up the necessary CSS */
/* add_action( 'wp_head', 'mstw_gs_enqueue_styles' ); */
add_action( 'wp_enqueue_scripts', 'mstw_gs_enqueue_styles' );

// ---------------------------------------------------------------------
// Callback for: add_action( 'wp_head', 'mstw_gs_enqueue_styles' );
// ---------------------------------------------------------------------
// Loads the Cascading Style Sheet for the [mstw-gl-table] shortcode
// ---------------------------------------------------------------------
function mstw_gs_enqueue_styles () {
	
	/* Find the full path to the css file */
	$mstw_gs_style_url = plugins_url('/css/mstw-gs-styles.css', __FILE__);
	$mstw_gs_style_file = WP_PLUGIN_DIR . '/game-schedules/css/mstw-gs-styles.css';
	
	wp_register_style( 'mstw_gs_style', plugins_url('/css/mstw-gs-styles.css', __FILE__) );
	
	//echo 'file url: ' . $mstw_gs_style_url . "\n";
	//echo 'file name: ' . $mstw_gs_style_file . "\n";
	
	/* If stylesheet exists, enqueue the style */
	if ( file_exists( $mstw_gs_style_file ) ) {	
		wp_enqueue_style( 'mstw_gs_style' );			
		
	} 

}


// --------------------------------------------------------------------------------------
// GAME SCHEDULES CUSTOM POST TYPE STUFF
// --------------------------------------------------------------------------------------
// Set-up Action Hooks & Filters for the Game Locations custom post type
// ACTIONS
// 		'init'											mstw_gs_register_post_type
//		'add_metaboxes'									mstw_gs_add_meta
//		'save_posts'									mstw_gs_save_meta
//		'manage_game_schedule_posts_custom_column'		mstw_gs_manage_columns

// FILTERS
// 		'manage_edit-game_schedule_columns'				mstw_gs_edit_columns
//		'post_row_actions'								mstw_gs_remove_the_view
//		
// --------------------------------------------------------------------------------------

// --------------------------------------------------------------------------------------
add_action( 'init', 'mstw_gs_register_post_type' );
// --------------------------------------------------------------------------------------
function mstw_gs_register_post_type() {
	/* Set up the arguments for the Game Schedules post type */
	$args = array(
    	'public' => true,
        'query_var' => 'scheduled_games',
        'rewrite' => array(
            'slug' => 'scheduled-games',
            'with_front' => false,
        ),
        'supports' => array(
            'title'
        ),
        'labels' => array(
            'name' => 'Games',
            'singular_name' => 'Game',
            'add_new' => 'Add New Game',
            'add_new_item' => 'Add Game',
            'edit_item' => 'Edit Game',
            'new_item' => 'New Game',
			//'View Game Schedule' needs a custom page template that is of no value.
			'view_item' => null, 
            'search_items' => 'Search Games',
            'not_found' => 'No Games Found',
            'not_found_in_trash' => 'No Games Found In Trash'
        	)
		);
		
	register_post_type( 'scheduled_games', $args);
}

// --------------------------------------------------------------------------------------
add_shortcode( 'mstw_gs_table', 'mstw_gs_shortcode_handler' );
// --------------------------------------------------------------------------------------
// Add the shortcode handler, which will create the Game Locations table on the user side.
// Handles the shortcode parameters, if there were any, 
// then calls mstw_gs_build_loc_tab() to create the output
// --------------------------------------------------------------------------------------
function mstw_gs_shortcode_handler( $atts ){
	
	extract( shortcode_atts( array(
				'sched' => '1',
				'show_media' => true,
				'first_dtg' => '1970:01:01 00:00:00',	// first php dtg
				'last_dtg' => '2038:01:19 00:00:00', 	// last php dtg (roughly)
				'games_to_show' => -1,
				),
				$atts ) );
		
	$mstw_gs_sched_tab = mstw_gs_build_sched_tab( $sched, $show_media, $first_dtg, $last_dtg, $games_to_show );
	
	return $mstw_gs_sched_tab;
}

/*--------------------------------------------------------------------------------------
 * Called by:	mstw_gs_shortcode_handler
 * Builds the Game Schedules table as a string (to replace the [shortcode] in a page or post.
 * Loops through the Game Schedules Custom posts and formats them into a pretty table.
 * ARGUMENTS:
 * $sched -> schedule ID, defaults to 1
 * $show_media -> Show (default) or hide ( if false ) the media column
 * $last_dtg -> Games after this date-time are not considered. 
 *		Format: YYYY-MM-DD HH:MM[:SS] (24-hour clock)
 * $games_to_show -> Max number of games to display - defaults to -1 show all
 *--------------------------------------------------------------------------------------*/
	function mstw_gs_build_sched_tab( $sched, $show_media, $first_dtg_str, $last_dtg_str, $games_to_show ) {
		// Get the admin options settings
		
		$options = get_option( 'mstw_gs_options' );
		
		// Check the hide_media admin setting.
		// It is always honored if it's set to hide-media.
		if ( $options['gs_hide_media'] == 'hide-media' ) {
			$show_media = false;
		}
		$dtg_format = $options['gs_tab_shortcode_dtg_format'];
		if ( $dtg_format == '' ) {
			$dtg_format = 'Y-m-d';
		}
		$time_format = $options['gs_tab_shortcode_time_format'];
		if ( $time_format == '' ) {
			$time_format = 'H:i';
		}
		
		// Need to set $first_dtg and $last_dtg by converting strings
		$first_dtg = strtotime( $first_dtg_str );
		//echo '<p> first_dtg_str: ' . $first_dtg_str . ' first_dtg_int: ' . $first_dtg. '</p>';
		//echo '<p> reverse it: ' . date( 'Y m d' , $last_dtg ) . '</p>';
		if ( $first_dtg <= 0 ) {  //strtotime() failed
			$first_dtg = 1;
		}
		
		$last_dtg = strtotime( $last_dtg_str );
		//echo '<p> last_dtg_str: ' . $last_dtg_str . ' last_dtg_int: ' . $last_dtg. '</p>';
		//echo '<p> reverse it: ' . date( 'Y m d' , $last_dtg ) . '</p>';
		if ( $last_dtg <= 0 ) {  //strtotime() failed
			$last_dtg = PHP_INT_MAX;
		}	
		
		// Get the games posts
		$posts = get_posts(array( 'numberposts' => $games_to_show,
								  'post_type' => 'scheduled_games',
								  'meta_query' => array(
													'relation' => 'AND',
													array(
														'key' => '_mstw_gs_sched_id',
														'value' => $sched,
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
		
		if ( $posts ) {
			// Make table of posts
			// Start with the table header
			$output = '<table class="mstw-gs-table">'; 
			$output = $output . '<thead class="mstw-gs-table-head"><tr>';
			$output = $output . '<th>'. __( 'Date', 'mstw-loc-domain' ) . '</th>';
			$output = $output . '<th>'. __( 'Opponent', 'mstw-loc-domain' ) . '</th>';
			$output = $output . '<th>'. __( 'Location', 'mstw-loc-domain' ) . '</th>';
			$output = $output . '<th>'. __( 'Time/Result', 'mstw-loc-domain' ) . '</th>';
			if ( $show_media ) {
				$output = $output . '<th>'.  __( 'Media', 'mstw-loc-domain' ) . '</th>';
			}
			$output = $output . '</tr></thead>';
			   
			// Keeps track of even and odd rows. Start with row 1 = odd.
			$even_and_odd = array('even', 'odd');
			$row_cnt = 1; 
		
			// Loop through the posts and make the rows
			foreach($posts as $post){
				// set up some housekeeping to make styling in the loop easier
				$is_home_game = get_post_meta($post->ID, '_mstw_gs_home_game', true );
				$even_or_odd_row = $even_and_odd[$row_cnt]; 
				$row_class = 'mstw-gs-' . $even_or_odd_row;
				if ( $is_home_game == 'home' ) 
					$row_class = $row_class . ' mstw-gs-home';
				
				$row_tr = '<tr class="' . $row_class . '">';
				$row_td = '<td>'; 
				
				// create the row
				$row_string = $row_tr;			
				
				// column 1: Build the game date in a specified format			
				$new_date_string = mstw_date_loc( $dtg_format, get_post_meta( $post->ID, '_mstw_gs_unix_dtg', true ) );
				//$new_date_string = $mstw_gs_dtg_format;
				
				//$new_date_string = date( $mstw_gs_dtg_format, get_post_meta( $post->ID, '_mstw_gs_unix_date', true) );
				
				$row_string = $row_string. $row_td . $new_date_string . '</td>';
				//$row_string = $row_string. $row_td . get_post_meta( $post->ID, '_mstw_gs_unix_date', true ) . '</td>';
				
				// column 2: create the opponent entry
				$mstw_gs_opponent_entry = get_post_meta( $post->ID, '_mstw_gs_opponent', true );
				
				// Check to see if you have to add the link
				if ( ( $mstw_gs_opponent_link = get_post_meta( $post->ID, '_mstw_gs_opponent_link', true ) ) != '' ) {
					$mstw_gs_opponent_entry = '<a href="' . $mstw_gs_opponent_link . '" target="_blank" >' . $mstw_gs_opponent_entry . '</a>';
				}
				
				$row_string =  $row_string . $row_td . $mstw_gs_opponent_entry . '</td>';
				
				// column 3: create the location entry
				// 20120210-MAO: New code to integrate Game Locations Plugin
				// 1. Check to see if there's a custom location, if so use it
				// 2. Then check to see if there's a location from GL Plugin, if so use it 
				// 3. Finally display 'No Location' 
				
				$gl_location = get_post_meta( $post->ID, '_mstw_gs_gl_location', true );
				$gl_loc_title = get_post_meta( $post->ID, '_mstw_gs_gl_loc_title', true );
				$location = get_post_meta( $post->ID, '_mstw_gs_location', true );
				$location_link = get_post_meta( $post->ID, '_mstw_gs_location_link', true );
				
				$location_entry = __( 'None found.', 'mstw-loc-domain' );
			
				if ($location != '' ) {  // case 1
					$location_entry = $location;
			
					//Check to see if you have to add the location link
					if ( $location_link != '' ) {
						$location_entry = '<a href="' . $location_link . '" target="_blank" >' . $location_entry . '</a>';
					}
				}
				else if ( $gl_location != '' ) { // case 2
					$custom_url = trim( get_post_meta( $gl_location, '_mstw_gl_custom_url', true) );
				
					if ( empty( $custom_url ) ) {  // build the url from the address fields
						$center_string = get_the_title( $gl_location ). "," .
							get_post_meta( $gl_location, '_mstw_gl_street', true ) . ', ' .
							get_post_meta( $gl_location, '_mstw_gl_city', true ) . ', ' .
							get_post_meta( $gl_location, '_mstw_gl_state', true ) . ', ' . 
							get_post_meta( $gl_location, '_mstw_gl_zip', true );
							
						$location_entry = '<a href="https://maps.google.com?q=' .$center_string . '" target="_blank" >'; 
					}
					else {
						$location_entry = '<a href="' . $custom_url . '" target="_blank">';
					}
					$location_entry .= get_the_title( $gl_location ) . '</a>';
				}
				
				$row_string =  $row_string . $row_td . $location_entry . '</td>';
				
				// column 4: create the time/results entry
				// 20120221-MAO: Rewritten to handle new game time entry logic
				//		and to use time format settings
				
				// If there is a game result, stick it in and we're done
				$game_result = get_post_meta( $post->ID, '_mstw_gs_game_result', true); 
				if ( $game_result != '' ) {
					$row_string .=  $row_td . $game_result . '</td>';
				}
				else {	
					// There's no game result, so add a game time
					// Check if the game time is TBA
					$time_is_tba = get_post_meta( $post->ID, '_mstw_gs_game_time_tba', true );
					
					if ( $time_is_tba != '' ) {	
						//Time is TBA. Stick it in and we're done
						$row_string .=  $row_td . $time_is_tba . '</td>';
					}
					else {	
						//Time is not TBA. Build the time string from the unix timestamp
						$unix_dtg = get_post_meta( $post->ID, '_mstw_gs_unix_dtg', true );
						$time_str = date( $time_format, $unix_dtg );
						//$row_string .=  $row_td . $unix_dtg . '</td>';
						$row_string .=  $row_td . $time_str . '</td>';
					}	
				}
				
				// column 5: create the media listings in a pretty format 
				
				if ( $show_media ) {
					$media_links = $row_td . "";
					
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
						if ( $mstw_media_label_2 <> "" ) {
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
							if ( $mstw_media_label_3 <> "" ) {
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
					
					$row_string = $row_string . $media_links . '</td>';  //			Should have a </tr> here??
				}
				
				$output = $output . $row_string;
				
				$row_cnt = 1- $row_cnt;  // Get the styles right
				
			} // end of foreach post
			
			$output = $output . '</table>';
		}
		else { // No posts were found
		
			$output =  '<h3>' . __( 'No Scheduled Games Found.', 'mstw-loc-domain' ) . '</h3>';
			
		}
		return $output;

	} /*End function mstw_gs_build_sched_tab*/

// --------------------------------------------------------------------------------------
add_shortcode( 'mstw_gs_countdown', 'mstw_gs_countdown_handler' );
// --------------------------------------------------------------------------------------
// Add the countdown shortcode handler, parses the args, and calls mstw_gs_build_countdown(),
// which creates the countdown timer display/output
// --------------------------------------------------------------------------------------
function mstw_gs_countdown_handler( $atts ){
	
	extract( shortcode_atts( array(
				'sched' => '1',
				'intro' => 'Time to kickoff:',
				'home_only' => false,
				), $atts ) );
		
	$mstw_gs_countdown = mstw_gs_build_countdown( $sched, $intro, $home_only );
	
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
function mstw_gs_build_countdown( $sched, $intro, $home_only ) {
	
	// General debug string used throughout
	//global $mstw_debug_str;
	
	//$ret_str = "<p><strong>Current WP DTG: </strong>" . date("D M d, Y G:i a") . '<strong> Zone: </strong>' . get_option('timezone_string') . '</p>';
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
		$options = get_option('mstw_gs_options');
		$cdt_time_format = $options['gs_cdt_time_format'];
		$cdt_tbd_format = $options['gs_cdt_tbd_format'];
		
		
		// Game day, date, time; need to handle a TBD time
		if ( $game_time_tba != '' ) {
			$dtg_str = mstw_date_loc( $cdt_tbd_format, $game_dtg ) . ' Time ' . $game_time_tba; 
			//$game_date is the UNIX timestamp DATE only
		}
		else {
			$dtg_str = mstw_date_loc( $cdt_time_format, $game_dtg ); 
			//$game_dtg is the full UNIX timestamp (DATE & TIME)  
        }
		
		$ret_str .= '<span class="mstw-gs-cdt-dtg">' . $dtg_str .  '</span><br/>';
		
		// Add the opponent & location
		// 20120821-MAO: Location display should be an option.
		
		$opponent_entry = $opponent;
		
		//Check to see if you have to add the opponent link
		if ( $opponent_link != '' ) {
			$opponent_entry = '<a href="' . $opponent_link . '" target="_blank" >' . $opponent_entry . '</a>';
		}
		
		// 20120210-MAO: New code to integrate Game Locations Plugin
		// 1. Check to see if there's a custom location, if so use it	
		// 2. Then check to see if there's a location from GL Plugin, if so use it 
		// 3. Finally display 'No Location' 
		
		$location_entry = __( 'None found.', 'mstw-loc-domain' );
		
		if ($location != '' ) {  // case 1
			$location_entry = $location;
		
			//Check to see if you have to add the location link
			if ( $location_link != '' ) {
				$location_entry = '<a href="' . $location_link . '" target="_blank" >' . $location_entry . '</a>';
			}
		}
		else if ( $gl_location != '' ) { // case 2
			$location_entry = $gl_loc_title;
		}
		
		$ret_str .= '<span class="mstw-gs-cdt-opponent">' . $opponent_entry . ' @ ' . $location_entry .  '</span><br/>';
		
		// Add the intro text set in shortcut arg or widget setting
		$ret_str .= '<span class="mstw-gs-cdt-intro">' . $intro .  '</span><br/>';
		
		settype($game_dtg, 'integer');
		 
		$ret_str .= '<span class="mstw-gs-cdt-countdown">' . time_difference( $game_dtg - $current_dtg ) . '</span>';
	}
						
	return $ret_str;
	
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
 * Game Schedule Widgets [file mstw-schedule-widgets.php]
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
    function form($instance) {
        $defaults = array(	'sched_title' => 'Schedule', 
							'sched_id' => '1', 
							'sched_yr' => date('Y'),
							'sched_start_date' => 0, 
							'sched_end_date' => strtotime( '2999-12-31'), 
							'sched_max_to_show' => -1, 
							); 
							
        $instance = wp_parse_args( (array) $instance, $defaults );
		$sched_title = $instance['sched_title'];
		$sched_id = $instance['sched_id'];
		$sched_start_date = $instance['sched_start_date'];
		$sched_end_date = $instance['sched_end_date'];
		$sched_max_to_show = $instance['sched_max_to_show'];
		
        ?>
        <p>Schedule Title: <input class="widefat" name="<?php echo $this->get_field_name( 'sched_title' ); ?>"  
            					type="text" value="<?php echo esc_attr( $sched_title ); ?>" /></p>
        <p>Schedule ID: <input class="widefat" name="<?php echo $this->get_field_name( 'sched_id' ); ?>"  
        						type="text" value="<?php echo esc_attr( $sched_id ); ?>" /></p>
		<p>The dates below MUST be in the format yyyy-mm-dd hh:mm. (You can omit the hh:mm for 00:00.) Otherwise, you can expect unexpected results.</p>
		<p>Display Start Date: <input class="widefat" name="<?php echo $this->get_field_name( 'sched_start_date' ); ?>"	type="text" value="<?php echo date('Y-m-d H:i', esc_attr( $sched_start_date ) ); ?>" />
		</p>
        <p>Display End Date: <input class="widefat" name="<?php echo $this->get_field_name( 'sched_end_date' ); ?>"  type="text" value="<?php echo date('Y-m-d H:i', esc_attr( $sched_end_date ) ); ?>" />
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
		
		$instance['sched_start_date'] = strtotime( strip_tags( $new_instance['sched_start_date'] ) );
		
		$instance['sched_end_date'] = strtotime( strip_tags( $new_instance['sched_end_date'] ) );
		
		$instance['sched_max_to_show'] = strip_tags( $new_instance['sched_max_to_show'] );
 
        return $instance;
		
    }
 
 /*--------------------------------------------------------------------
 * displays the widget
 *------------------------------------------------------------------*/	
 
function widget( $args, $instance ) {

		//Date column of the widget's table
		$options = get_option('mstw_gs_options');
		$tab_widget_dtg_format = $options['gs_tab_widget_dtg_format'];
		if ( $tab_widget_dtg_format == '' ) {
			$tab_widget_dtg_format =  'Y-m-d';
		}
		
		// $args holds the global theme variables, such as $before_widget
		extract( $args );
		
		echo $before_widget;
		
		$title = apply_filters( 'widget_title', $instance['sched_title'] );
		
		// Get the parameters for get_posts() below
		$sched_id = $instance['sched_id'];
		$first_dtg = $instance['sched_start_date'];
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
													'key' => '_mstw_gs_sched_id', //**
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
        
        	<table class="mstw-gs-sw-tab">
        	<thead class="mstw-gs-sw-tab-head"><tr>
            	<th><?php _e( 'DATE', 'mstw-loc-domain' ); ?></th>
            	<th><?php _e( 'OPPONENT', 'mstw-loc-domain' ); ?></th>	
			</tr></thead>
        
			<?php
			// Loop through the posts and make the rows
			$even_and_odd = array('even', 'odd');
			$row_cnt = 1; // Keeps track of even and odd rows. Start with row 1 = odd.
		
			foreach( $posts as $post ) {
				// set up some housekeeping to make styling in the loop easier
				$is_home_game = get_post_meta($post->ID, '_mstw_gs_home_game', true );
				$even_or_odd_row = $even_and_odd[$row_cnt]; 
				$row_class = 'mstw-gs-sw-' . $even_or_odd_row;
				if ( $is_home_game == 'home' ) 
					$row_class = $row_class . ' mstw-gs-sw-home';
			
				$row_tr = '<tr class="' . $row_class . '">';
				//$row_tr = '<tr>';
				$row_td = '<td>'; 
				//$row_td = '<td class="' . $row_class . '">';
			
				// create the row
				$row_string = $row_tr;		
			
				// column 1: Build the game date in a specified format			
				$date_string = mstw_date_loc( $tab_widget_dtg_format, get_post_meta( $post->ID, '_mstw_gs_unix_dtg', true ) );
			
				$row_string = $row_string. $row_td . $date_string . '</td>';
			
				// column 2: create the opponent entry
				$opponent = get_post_meta( $post->ID, '_mstw_gs_opponent', true);
				
				if ( $is_home_game != 'home' ) {
					$opponent = '@' . $opponent;
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
							'cd_sched_id' => '1', 
							'cd_intro_text' => 'Time to kickoff:', 
							'cd_home_only' => '', 
							); 
							
        $instance = wp_parse_args( (array) $instance, $defaults );
		
        $cd_title = $instance['cd_title'];
		$cd_sched_id = $instance['cd_sched_id'];
		$cd_home_only = $instance['cd_home_only'];
		
        ?>
        <p>Countdown Title: <input class="widefat" name="<?php echo $this->get_field_name( 'cd_title' ); ?>"  
            					type="text" value="<?php echo esc_attr( $cd_title ); ?>" /></p>
        
        <p>Schedule ID: <input class="widefat" name="<?php echo $this->get_field_name( 'cd_sched_id' ); ?>"  
        						type="text" value="<?php echo esc_attr( $cd_sched_id ); ?>" /></p> 
		
		<p><input class="checkbox" type="checkbox" <?php checked( $instance['cd_home_only'], 'on' ); ?> id="<?php echo $this->get_field_id( 'cd_home_only' ); ?>" name="<?php echo $this->get_field_name( 'cd_home_only' ); ?>" /> 
		<label for="<?php echo $this->get_field_id( 'cd_home_only' ); ?>">Use home games only?</label></p>
		
        <p>Countdown Intro Text: <input class="widefat" name="<?php echo $this->get_field_name( 'cd_intro_text' ); ?>"
        						type="text" value="<?php echo esc_attr( $cd_intro_text ); ?>" /></p>
            
        <?php
    }
	
/*--------------------------------------------------------------------
 * saves the countdown widget settings
 *------------------------------------------------------------------*/	
   function update($new_instance, $old_instance) {
		
        $instance = $old_instance;
		
		$instance['cd_title'] = strip_tags( $new_instance['cd_title'] );

		$instance['cd_sched_id'] = strip_tags( $new_instance['cd_sched_id'] );
		
		$instance['cd_home_only'] = strip_tags( $new_instance['cd_home_only'] );
		
		$instance['cd_intro_text'] = strip_tags( $new_instance['cd_intro_text'] );
 
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
		
		// Get the parameters for get_posts() below
		$cd_sched_id = $instance['cd_sched_id'];
		$cd_home_only = $instance['cd_home_only'];
		$cd_intro_text = $instance['cd_intro_text'];
		
		if( !empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}
			
        $cd_str = mstw_gs_build_countdown( $cd_sched_id, $cd_intro_text,  $cd_home_only );
        
        echo $cd_str;
		
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
	
	if(is_null($timestamp)) { $timestamp = mktime(); }
	
	for($i = 0, $len = strlen($format); $i < $len; $i++) {
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