<?php
/*
Plugin Name: Game Schedule
Plugin URI: http://wordpress.org/extend/plugins/
Description: The Game Schedule Plugin defines a custom type - Scheduled Games - for use in the MySportTeamWebite framework. Generations a game schedule (html table) using a shortcode.
Version: 2.4
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
 * ------------------------------------------------------------------------*/

/* ------------------------------------------------------------------------
// PLUGIN PREFIX:                                                          
// 'mstw_gs_'   derived from mysportsteamwebsite game schedule
// -----------------------------------------------------------------------*/ 

// This is important, have to see if it has global scope
	date_default_timezone_set('America/Los_Angeles');

// This is temporary until we create options (someday)
	//Date column of the widget's table
	$mstw_gs_dtg_format =  'j M y';
	// For the dashboard/metabox.
	$mstw_dash_dtg_format =  'j M y'; 	
	// For the countdown timer; game time with a time
	$mstw_gs_cdt_time_format = "l, j M g:i a";
	// For the countdown timer; game time with only a game date (no time)
	$mstw_gs_cdt_tbd_format = "l, j M";
	//Date column of the widget's table
	$mstw_gs_sw_dtg_format = 'd M y'; 
	//Time column for the [shortcode]'s schedule table
	//$mstw_gs_time_format = "H.i";
	$mstw_gs_time_format = "g:i a";
	
// Months array for <select>/<option> statement in UI
	$mstw_gs_months = array ( 	'Jan', 'Feb', 'Mar', 'Apr',
								'May', 'Jun', 'Jul', 'Aug',
								'Sep', 'Oct', 'Nov', 'Dec',
							);
							
// Days array for <select>/<option> statement in UI
	$mstw_gs_days = array ( '01', '02', '03', '04', '05', '06', '07', '08',
							'09', '10', '11', '12', '13', '14', '15', '16',
							'17', '18', '19', '20', '21', '22', '23', '24',
							'25', '26', '27', '28', '29', '30', '31'
							);
	
// Debug messages - used throughout	
	//$mstw_debug_str = '';
	
//	MSTW localization domain
	$mstw_domain = 'mstw-loc-domain';
	
// ----------------------------------------------------------------
// Set up localization
add_action( 'init', 'mstw_load_localization' );
	
function mstw_load_localization( ) {
	global $mstw_domain;
    load_plugin_textdomain( $mstw_domain, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
} // end custom_theme_setup
	
// ----------------------------------------------------------------
// Remove Quick Edit Menu	
add_filter( 'post_row_actions', 'mstw_gs_remove_quick_edit', 10, 2 );

function mstw_gs_remove_quick_edit( $actions, $post ) {
    if( $post->post_type == 'scheduled_games' ) {
        unset( $actions['inline hide-if-no-js'] );
    }
    return $actions;
}
	
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
// Callback for: register_activation_hook(__FILE__, 'mstw_gs_set_defaults')
// --------------------------------------------------------------------------------------
// This function runs when the plugin is activated. If there are no options currently set, 
// or the user has selected the checkbox to reset the options to their defaults,
// then the options are set/reset. Otherwise the options remain unchanged.
// --------------------------------------------------------------------------------------
function mstw_gs_set_defaults() {
	$tmp = get_option('mstw_gs_options');
    if(($tmp['chk_default_options_db']=='1')||(!is_array($tmp))) {
		delete_option('mstw_gs_options'); // so we don't have to reset all the 'off' checkboxes too! 
		$arr = array(	"mstw_gs_hdr_bkgd" => "#000000",
						"mstw_gs_hdr_text" => "#FFFFFF",
						"mstw_gs_even_bkgd" => "#DBE5F1",
						"mstw_gs_even_text" => "#000000",
						"mstw_gs_odd_bkgd" => "#FFFFFF",
						"mstw_gs_odd_text" => "#000000",
						"mstw_gs_brdr_width" => "2",  //px
						"mstw_gs_brdr_color" => "#F481BD",
						"mstw_gs_default_opts" => "",
		);
		update_option('mstw_gs_options', $arr);
	}
}

// --------------------------------------------------------------------------------------
// Callback for: add_action('admin_init', 'mstw_gs_register_settings' )
// --------------------------------------------------------------------------------------
// Registers plugin settings with the WP Setting API. Nothing works unless this happens.
// --------------------------------------------------------------------------------------
function mstw_gs_register_settings( ) { //whitelist options
	register_setting( 'mstw_gs_options_group', 'mstw_gs_options', 'mstw_gs_valid_options' );
	add_settings_section( 'mstw_gs_main_section', 'Game Locations Table Style', 'mstw_gs_main_section_text', 
							basename(__FILE__) );
	add_settings_field( 'mstw_gs_hdr_bkgd', 'Header Background Color', 'mstw_gs_hdr_bkgd_cb', 
						basename(__FILE__), 'mstw_gs_main_section');

}

// ------------------------------------------------------------------------------
// Callback for: add_action('admin_menu', 'mstw_gs_add_options_page');
// ------------------------------------------------------------------------------
// Adds a new Settings Page into the plugin menu.
// ------------------------------------------------------------------------------
function mstw_gs_add_options_page( ) { 
	add_submenu_page('edit.php?post_type=game_schedule', 'Game Locations Settings', 'Settings',
					 'edit_posts', basename(__FILE__), 'mstw_gs_render_settings_ui');
}

/* Queue up the necessary CSS */
/* add_action( 'wp_head', 'mstw_gs_enqueue_styles' ); */
add_action( 'wp_enqueue_scripts', 'mstw_gs_enqueue_styles' );

// ------------------------------------------------------------------------------
// Callback for: add_action( 'wp_head', 'mstw_gs_enqueue_styles' );
// ------------------------------------------------------------------------------
// Loads the Cascading Style Sheet for the [mstw-gl-table] shortcode
// ------------------------------------------------------------------------------
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
	/*else {
		echo $mstw_gs_style_file . ' does not exist' . "\n";
	} */

}


// --------------------------------------------------------------------------------------
// GAME LOCATIONS CUSTOM POST TYPE STUFF
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
	/* Set up the arguments for the Game Locations post type */
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
add_action( 'add_meta_boxes', 'mstw_gs_add_meta' );
// --------------------------------------------------------------------------------------
// Create the meta box for the Game Locations custom post type
// --------------------------------------------------------------------------------------
function mstw_gs_add_meta () {
	add_meta_box('mstw-gs-meta', 'Game', 'mstw_gs_create_ui', 
					'scheduled_games', 'normal', 'high' );
}

// --------------------------------------------------------------------------------------
// Callback for: add_meta_box('mstw-gl-meta', 'Game Location', 'mstw_gs_create_ui', ... )
// --------------------------------------------------------------------------------------
// Creates the UI form for entering a Game Location in the Admin page
// --------------------------------------------------------------------------------------
function mstw_gs_create_ui( $post ) {
	//date_default_timezone_set('America/Los_Angeles');
	
	// Months array for <select>/<option> statement in UI
	global $mstw_gs_months; 
	global $mstw_gs_days; 
						  
	/* foreach ( $mstw_gs_months as $int_key => $str_val ) {
        	echo $int_key . " => " . $str_val . "<br/>";
		}
	*/
						  
	// Retrieve the metadata values if they exist
	$mstw_gs_sched_id = get_post_meta( $post->ID, '_mstw_gs_sched_id', true );
	
	$mstw_gs_sched_year  = get_post_meta( $post->ID, '_mstw_gs_sched_year', true );	// alphanumeric year string
	$mstw_gs_game_month = get_post_meta( $post->ID, '_mstw_gs_game_month', true );	// alpahnumeric month string
	$mstw_gs_game_day = get_post_meta( $post->ID, '_mstw_gs_game_day', true );  	// alphanumeric day string
	$mstw_gs_game_time = get_post_meta( $post->ID, '_mstw_gs_game_time', true );	// alphanumeric time string
	
	$mstw_gs_unix_date = get_post_meta( $post->ID, '_mstw_gs_unix_date', true );	// UNIX timestamp date only
	$mstw_gs_unix_dtg = get_post_meta( $post->ID, '_mstw_gs_unix_dtg', true );		// UNIX timestamp date & time
	
	$mstw_gs_opponent = get_post_meta( $post->ID, '_mstw_gs_opponent', true );
	$mstw_gs_opponent_link = get_post_meta( $post->ID, '_mstw_gs_opponent_link', true );
	$mstw_gs_location = get_post_meta( $post->ID, '_mstw_gs_location', true );
	$mstw_gs_location_link = get_post_meta( $post->ID, '_mstw_gs_location_link', true );
	$mstw_gs_home_game = get_post_meta( $post->ID, '_mstw_gs_home_game', true );
	$mstw_gs_game_result = get_post_meta( $post->ID, '_mstw_gs_game_result', true );
	 
	
	$mstw_gs_media_label_1  = get_post_meta( $post->ID, '_mstw_gs_media_label_1', true );
	$mstw_gs_media_label_2  = get_post_meta( $post->ID, '_mstw_gs_media_label_2', true );
	$mstw_gs_media_label_3  = get_post_meta( $post->ID, '_mstw_gs_media_label_3', true );
	
	$mstw_gs_media_url_1  = get_post_meta($post->ID, '_mstw_gs_media_url_1', true );
	$mstw_gs_media_url_2  = get_post_meta($post->ID, '_mstw_gs_media_url_2', true );
	$mstw_gs_media_url_3  = get_post_meta($post->ID, '_mstw_gs_media_url_3', true );
	   
	?>	
	
   <table class="form-table">
    <tr valign="top">
    	<th scope="row"><label for="mstw_gs_sched_id" >Schedule ID:</label></th>
        <td><input maxlength="8" size="8" name="mstw_gs_sched_id"
        	value="<?php echo esc_attr( $mstw_gs_sched_id ); ?>"/></td>
    </tr>
    <tr valign="top">
    	<th scope="row"><label for="mstw_gs_sched_year" >Game Year:</label></th>
        <td><input maxlength="4" size="5" name="mstw_gs_sched_year"
        	value="<?php echo esc_attr( $mstw_gs_sched_year ); ?>"/></td>
    </tr>
    
    <tr valign="top">
    	<th scope="row"><label for="mstw_gs_game_day" >Game Day:</label></th>
        <td>
        <select name="mstw_gs_game_day">    
			<?php foreach ( $mstw_gs_days as $label ) {  ?>
          			<option value="<?php echo $label ?>" <?php selected( $mstw_gs_game_day, $label );?>>
          				<?php echo $label; ?>
                     </option>              
     		<?php } ?> 
        </select>   
        </td>
    </tr>
    
    <tr valign="top">
    	<th scope="row"><label for="mstw_gs_game_month" >Game Month:</label></th>
        <td>
        <select name="mstw_gs_game_month">    
			<?php foreach ( $mstw_gs_months as $label ) {  ?>
          			<option value="<?php echo $label; ?>" <?php selected( $mstw_gs_game_month, $label );?>>
          				<?php echo $label; ?>
                     </option>              
     		<?php } ?> 
        </select>   
        </td>
    </tr>
  
    <tr valign="top">
    	<th scope="row"><label for="mstw_gs_opponent" >Opponent:</label></th>
        <td><input maxlength="64" size="30" name="mstw_gs_opponent"
        	value="<?php echo esc_attr( $mstw_gs_opponent ); ?>"/></td>
    </tr>
	
	<tr valign="top">
    	<th scope="row"><label for="mstw_gs_opponent_link" >Opponent Link:</label></th>
        <td><input maxlength="256" size="30" name="mstw_gs_opponent_link"
        	value="<?php echo esc_attr( $mstw_gs_opponent_link ); ?>"/></td>
    </tr>
	
    <tr valign="top">
    	<th scope="row"><label for="mstw_gs_home_game" >Home Game?</label></th>
        <td><input type="checkbox" name="mstw_gs_home_game" value="home" <?php checked( $mstw_gs_home_game, 'home', true )?> /></td>
    </tr>
    <tr valign="top">
    	<th scope="row"><label for="mstw_gs_location" >Location:</label></th>
        <td><input maxlength="64" size="30" name="mstw_gs_location"
        	value="<?php echo esc_attr( $mstw_gs_location ); ?>"/></td>
    </tr> 
	
	<tr valign="top">
    	<th scope="row"><label for="mstw_gs_location_link" >Location Link:</label></th>
        <td><input maxlength="256" size="30" name="mstw_gs_location_link"
        	value="<?php echo esc_attr( $mstw_gs_location_link ); ?>"/></td>
    </tr>
		
    <tr valign="top">
    	<th scope="row"><label for="mstw_gs_game_time" >Game Time:<br/>Use hh:mm am </label></th>
        <td><input maxlength="16" size="10" name="mstw_gs_game_time"
        	value="<?php echo esc_attr( $mstw_gs_game_time ); ?>"/></td>
    </tr>
    <tr valign="top">
    	<th scope="row"><label for="mstw_gs_game_result" >Game Result: </label></th>
        <td><input maxlength="16" size="10" name="mstw_gs_game_result"
        	value="<?php echo esc_attr( $mstw_gs_game_result ); ?>"/></td>
    </tr>
    <tr valign="top">
    	<th scope="row"><label for="mstw_gs_media_label_1" >Media 1 Label:</label></th>
        <td><input maxlength="64" size="30" name="mstw_gs_media_label_1"
        	value="<?php echo esc_attr( $mstw_gs_media_label_1 ); ?>"/></td>
    </tr>
    <tr valign="top">
    	<th scope="row"><label for="mstw_gs_media_url_1" >Media 1 URL:</label></th>
        <td><input maxlength="256" size="30" name="mstw_gs_media_url_1"
        	value="<?php echo esc_attr( $mstw_gs_media_url_1 ); ?>"/></td>
    </tr>
    <tr valign="top">
    	<th scope="row"><label for="mstw_gs_media_label_2" >Media 2 Label:</label></th>
        <td><input maxlength="64" size="30" name="mstw_gs_media_label_2"
        	value="<?php echo esc_attr( $mstw_gs_media_label_2 ); ?>"/></td>
    </tr>
    <tr valign="top">
    	<th scope="row"><label for="mstw_gs_media_url_2" >Media 2 URL:</label></th>
        <td><input maxlength="256" size="30" name="mstw_gs_media_url_2"
        	value="<?php echo esc_attr( $mstw_gs_media_url_2 ); ?>"/></td>
    </tr>
    <tr valign="top">
    	<th scope="row"><label for="mstw_gs_media_label_3" >Media 3 Label:</label></th>
        <td><input maxlength="64" size="30" name="mstw_gs_media_label_3"
        	value="<?php echo esc_attr( $mstw_gs_media_label_3 ); ?>"/></td>
    </tr>
    <tr valign="top">
    	<th scope="row"><label for="mstw_gs_media_url_3" >Media 3 URL:</label></th>
        <td><input maxlength="256" size="30" name="mstw_gs_media_url_3"
        	value="<?php echo esc_attr( $mstw_gs_media_url_3 ); ?>"/></td>
    </tr>
    
    <tr valign="top">
    	<th scope="row"><label for="mstw_gs_unix_date" >UNIX Date Only (Don't Enter):</label></th>
        <td><input maxlength="60" size="30" name="mstw_gs_unix_date"
        	value="<?php echo date( 'Y-m-d', esc_attr( $mstw_gs_unix_date ) ); ?>"/></td>
    </tr>
    
    <tr valign="top">
    	<th scope="row"><label for="mstw_gs_unix_dtg" >UNIX Date-Time (Don't Enter):</label></th>
        <td><input maxlength="60" size="30" name="mstw_gs_unix_dtg"
        	value="<?php echo date( 'l, Y-m-d h:i a', esc_attr( $mstw_gs_unix_dtg ) ); ?>"/></td>
    </tr>
    
    </table>
    
<?php        	
}

// --------------------------------------------------------------------------------------
add_action( 'save_post', 'mstw_gs_save_meta' );
// --------------------------------------------------------------------------------------
// Save the Game Locations Meta Data
// --------------------------------------------------------------------------------------
function mstw_gs_save_meta( $post_id ) {
	date_default_timezone_set('America/Los_Angeles');
	
	global $mstw_msg_str;
	//global $mstw_debug_str;
	
	//First verify the metadata required by the shortcode is set. If not, set defaults
	
	// SCHEDULE ID
	// If schedule id was not set, default to 1
	$mstw_id = strip_tags( trim( $_POST['mstw_gs_sched_id'] ) );
	if ( $mstw_id == "" ) {
		$mstw_id = '1';
	}
	update_post_meta( $post_id, '_mstw_gs_sched_id', $mstw_id );
		
	// YEAR
	// If game year was not set, default to the current year
	// Someday we'll do a better check here, or use a pulldown in the UI
	$mstw_year = strip_tags( trim( $_POST[ 'mstw_gs_sched_year' ] ) );
	if ($mstw_year == '') {
		$mstw_year = date('Y');
	}
	update_post_meta( $post_id, '_mstw_gs_sched_year', $mstw_year );
	
	// DAY
	// If schedule day is not set, default to 1
	// Someday we'll do a better check here, or use a pulldown in the UI
	$mstw_day = strip_tags( trim( $_POST[ 'mstw_gs_game_day' ] ) );
	if ( $mstw_day == "" ) {
		$mstw_day = '1';
	}
	update_post_meta( $post_id, '_mstw_gs_game_day', $mstw_day );
	
	$mstw_month = strip_tags( trim( $_POST[ 'mstw_gs_game_month' ] ) );
	$mstw_time = strip_tags( trim( $_POST[ 'mstw_gs_game_time' ] ) );
	$mstw_result = strip_tags( trim( $_POST[ 'mstw_gs_game_result' ] ) );
	
	$date_only_str = $mstw_year . '-' . $mstw_month . '-' . $mstw_day;
	$unix_date = strtotime( $date_only_str );
	update_post_meta( $post_id, '_mstw_gs_unix_date', $unix_date );
	
	$full_dtg_str = $date_only_str . ' ' . $mstw_time;
	$unix_dtg = strtotime( $full_dtg_str );
	update_post_meta( $post_id, '_mstw_gs_unix_dtg', $unix_dtg );
	

	//$mstw_debug_str =   'Constructed date string: ' . $date_only_str. '<br/>' . 
						'UNIX date: ' . $unix_date . '<br/>' .
						'Date(UNIX date): ' . date( 'Y-m-d', $unix_date ) . '<br/>' .
						'Constructed DTG string: ' . $full_dtg_str. '<br/>' . 
						'UNIX DTG: ' . $unix_dtg . '<br/>' .
						'Date(UNIX DTG): ' . date('Y-m-d h:i a', $unix_dtg);
	
	//update_post_meta( $post_id, '_mstw_gs_debug', $mstw_debug_str );
	
	
	// MONTH
	// Month is a pulldown, we should be good
	$mstw_month = strip_tags( trim( $_POST[ 'mstw_gs_game_month' ] ) );
	update_post_meta( $post_id, '_mstw_gs_game_month', $mstw_month );
				
	// Okay, we should be good to update the database
			
	update_post_meta( $post_id, '_mstw_gs_opponent', 
			strip_tags( $_POST['mstw_gs_opponent'] ) );
			
	update_post_meta( $post_id, '_mstw_gs_opponent_link', 
			strip_tags( $_POST['mstw_gs_opponent_link'] ) );
			
	update_post_meta( $post_id, '_mstw_gs_location', 
			strip_tags( $_POST['mstw_gs_location'] ) );
			
	update_post_meta( $post_id, '_mstw_gs_location_link', 
			strip_tags( $_POST['mstw_gs_location_link'] ) );		
			
	update_post_meta( $post_id, '_mstw_gs_home_game', 
			strip_tags( $_POST['mstw_gs_home_game'] ) );
			
	update_post_meta( $post_id, '_mstw_gs_game_time',  
			strip_tags( $_POST['mstw_gs_game_time'] ) );
			
	update_post_meta( $post_id, '_mstw_gs_game_result',  
			strip_tags( $_POST['mstw_gs_game_result'] ) );
			
	update_post_meta( $post_id, '_mstw_gs_media_label_1',
			strip_tags( $_POST['mstw_gs_media_label_1'] ) );
			
	update_post_meta( $post_id, '_mstw_gs_media_label_2',
			strip_tags( $_POST['mstw_gs_media_label_2'] ) );
			
	update_post_meta( $post_id, '_mstw_gs_media_label_3',
			strip_tags( $_POST['mstw_gs_media_label_3'] ) );
			
	update_post_meta( $post_id, '_mstw_gs_media_url_1',
			esc_url( $_POST['mstw_gs_media_url_1'] ) );
			
	update_post_meta( $post_id, '_mstw_gs_media_url_2',
			esc_url( $_POST['mstw_gs_media_url_2'] ) );
			
	update_post_meta( $post_id, '_mstw_gs_media_url_3',
			esc_url( $_POST['mstw_gs_media_url_3'] ) );
}

// --------------------------------------------------------------------------------------
add_filter( 'manage_edit-scheduled_games_columns', 'mstw_gs_edit_columns' ) ;
// --------------------------------------------------------------------------------------
// Set up the Game Locations 'view all' columns
// --------------------------------------------------------------------------------------
function mstw_gs_edit_columns( $columns ) {
	date_default_timezone_set('America/Los_Angeles');
	
	global $mstw_gs_dtg_format;

	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __( 'Title' ),
		'sched_id' => __( 'Schedule' ),
		'sched_year' => __( 'Year' ),
		'game_date' => __( 'Date' ),
		'opponent' => __( 'Opponent' ),
		'opponent_link' => __( 'Opponent Link' ),
		'location' => __( 'Location' ),
		'location_link' => __( 'Location Link' ),
		'game_time' => __( 'Time' ),
		'game_result' => __( 'Result' )
		/* 'debug' => __('Debug-Remove') */
	);

	return $columns;
}

// --------------------------------------------------------------------------------------
add_action( 'manage_scheduled_games_posts_custom_column', 'mstw_gs_manage_columns', 10, 2 );
// --------------------------------------------------------------------------------------
// Display the Game Locations 'view all' columns
// --------------------------------------------------------------------------------------
function mstw_gs_manage_columns( $column, $post_id ) {
	global $post;
	global $mstw_dash_dtg_format;
	global $mstw_domain; //Important for localization functions!
	
	/* echo 'column: ' . $column . " Post ID: " . $post_id; */

	switch( $column ) {
		/* Debug Column */
		/*case 'debug' :
			$debug_str = "";
			echo (	$debug_str );	
			break;
		*/
		
		/* If displaying the 'sched_year' column. */
		case 'sched_year' :

			/* Get the post meta. */
			$mstw_gs_sched_year = get_post_meta( $post_id, '_mstw_gs_sched_year', true );

			if ( empty( $mstw_gs_sched_year ) )
				_e(  'No Game Year', $mstw_domain );
			else
				printf( '%s', $mstw_gs_sched_year );

			break;
			
		/* If displaying the 'sched_id' column. */
		case 'sched_id' :
		
			/* Get the post meta. */
			$mstw_gs_sched_id = get_post_meta( $post_id, '_mstw_gs_sched_id', true );

			if ( empty( $mstw_gs_sched_id ) )
				_e( 'No Schedule Defined', $mstw_domain );
			else
				printf( __( '%s' ), $mstw_gs_sched_id );

			break;
		

		/* If displaying the 'game_date' column. */
		case 'game_date' :
		
			/* Get the post meta. */
			$mstw_gs_unix_date = get_post_meta( $post_id, '_mstw_gs_unix_date', true );

			if ( empty( $mstw_gs_unix_date ) )
				_e( 'No Game Date', $mstw_domain );
			else
				echo( date( $mstw_dash_dtg_format, $mstw_gs_unix_date ) );

			break;
		
				
		/* If displaying the 'opponent' column. */
		case 'opponent' :

			/* Get the post meta. */
			$mstw_gs_opponent = get_post_meta( $post_id, '_mstw_gs_opponent', true );


			if ( empty( $mstw_gs_opponent ) )
				_e( 'No Opponent', $mstw_domain );
			else
				printf( __( '%s' ), $mstw_gs_opponent );

			break;

		/* If displaying the 'opponent_link' column. */
		case 'opponent_link' :

			/* Get the post meta. */
			$mstw_gs_opponent_link = get_post_meta( $post_id, '_mstw_gs_opponent_link', true );


			if ( empty( $mstw_gs_opponent_link ) )
				_e( 'No Opponent Link', $mstw_domain );
			else
				printf( '%s', $mstw_gs_opponent_link );

			break;
			
		/* If displaying the 'location' column. */
		case 'location' :

			/* Get the post meta. */
			$mstw_gs_location = get_post_meta( $post_id, '_mstw_gs_location', true );

			if ( empty( $mstw_gs_location ) )
				_e( 'No Location', $mstw_domain );
			else
				printf( __( '%s' ), $mstw_gs_location );

			break;	
			
		/* If displaying the 'location_link' column. */
			case 'location_link' :

			/* Get the post meta. */
			$mstw_gs_location_link = get_post_meta( $post_id, '_mstw_gs_location_link', true );


			if ( empty( $mstw_gs_location_link ) )
				_e( 'No Location Link', $mstw_domain );
			else
				printf( '%s', $mstw_gs_location_link );

			break;
			
		/* If displaying the 'time' column. */
		case 'game_time' :

			/* Get the post meta. */
			$mstw_gs_game_time = get_post_meta( $post_id, '_mstw_gs_game_time', true );

			if ( empty( $mstw_gs_game_time ) )
				_e( 'No Game Time', $mstw_domain );
			else
				printf( '%s', $mstw_gs_game_time );

			break;	

		/* If displaying the 'result' column. */
		case 'game_result' :

			/* Get the post meta. */
			$mstw_gs_game_result = get_post_meta( $post_id, '_mstw_gs_game_result', true );

			if ( empty( $mstw_gs_game_result ) )
				_e( 'No Game Result', $mstw_domain );
			else
				printf( __( '%s' ), $mstw_gs_game_result );

			break;
			
		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}

// --------------------------------------------------------------------------------------
if (is_admin()) {
	add_filter('post_row_actions','mstw_gs_remove_the_view',10,2);
}			
// --------------------------------------------------------------------------------------
//removes view from mstw_scheduled_games list
function mstw_gs_remove_the_view( $actions ) {
	global $post;
    if( $post->post_type == 'scheduled_games' ) {
		unset( $actions['view'] );
	}
    return $actions;
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
				), $atts ) );
		
	$mstw_gs_sched_tab = mstw_gs_build_sched_tab( $sched );
	
	return $mstw_gs_sched_tab;
}

// --------------------------------------------------------------------------------------
// Called by:	mstw_gs_shortcode_handler
// Builds the Game Locations table as a string (to replace the [shortcode] in a page or post.
// Loops through the Game Locations Custom posts and formats them into a pretty table.
// --------------------------------------------------------------------------------------
function mstw_gs_build_sched_tab( $sched ) {
	global $mstw_domain;
	
	// These will come from options ... someday
	global $mstw_gs_dtg_format;
	global $mstw_gs_time_format;
	
	// Get the games posts
	$posts = get_posts(array( 'numberposts' => -1,
							  'post_type' => 'scheduled_games',
							  'meta_query' => array(
												array(
													'key' => '_mstw_gs_sched_id',
													'value' => $sched,
													'compare' => '='
												)
											),
							  
							  'orderby' => 'meta_value', 
							  'meta_key' => '_mstw_gs_unix_date',
							  'order' => 'ASC' 
							));						
	
    if($posts) {
		// Make table of posts
		// Start with the table header
        $output = '<table class="mstw-gs-table">'; 
        $output = $output . '<thead class="mstw-gs-table-head"><tr>';
		$output = $output . '<th>'. __( 'Date', $mstw_domain ) . '</th>';
		$output = $output . '<th>'. __( 'Opponent', $mstw_domain ) . '</th>';
		$output = $output . '<th>'. __( 'Location', $mstw_domain ) . '</th>';
		$output = $output . '<th>'. __( 'Time/Result', $mstw_domain ) . '</th>';
		$output = $output . '<th>'. __( 'Media', $mstw_domain ) . '</th>';
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
			$new_date_string = mstw_date_loc( $mstw_gs_dtg_format, get_post_meta( $post->ID, '_mstw_gs_unix_date', true ) );
			
			// $new_date_string = date( $mstw_gs_dtg_format, get_post_meta( $post->ID, '_mstw_gs_unix_date', true) );
			
			$row_string = $row_string. $row_td . $new_date_string . '</td>';
			
			// column 2: create the opponent entry
			$mstw_gs_opponent_entry = get_post_meta( $post->ID, '_mstw_gs_opponent', true );
			
			// Check to see if you have to add the link
			if ( ( $mstw_gs_opponent_link = get_post_meta( $post->ID, '_mstw_gs_opponent_link', true ) ) != '' ) {
				$mstw_gs_opponent_entry = '<a href="' . $mstw_gs_opponent_link . '" target="_blank" >' . $mstw_gs_opponent_entry . '</a>';
			}
			
			$row_string =  $row_string . $row_td . $mstw_gs_opponent_entry . '</td>';
			
			// column 3: create the location entry
			$mstw_gs_location_entry = get_post_meta( $post->ID, '_mstw_gs_location', true );
			
			// Check to see if you have to add the link
			if ( ( $mstw_gs_location_link = get_post_meta( $post->ID, '_mstw_gs_location_link', true ) ) != '' ) {
				$mstw_gs_location_entry = '<a href="' . $mstw_gs_location_link . '" target="_blank" >' . $mstw_gs_location_entry . '</a>';
			}
			
			$row_string =  $row_string . $row_td . $mstw_gs_location_entry . '</td>';
			
			// column 4: create the time/results entry
			if ( get_post_meta( $post->ID, '_mstw_gs_game_result', true) != '' ) {
				$row_string =  $row_string . $row_td . get_post_meta( $post->ID, '_mstw_gs_game_result', true) . '</td>';
			}
			else {	
				// This line formats the time before adding it to the output string
				$temp_time_str = get_post_meta( $post->ID, '_mstw_gs_game_time', true );
				
				// 201212023-MAO: Added error checking to handle TBD game times
				if ( $temp_time_str == "TBA" or $temp_time_str == "T.B.A." or $temp_time_str == "TBD" 
						or $temp_time_str == "T.B.D." or $temp_time_str == "" ) {
					$new_time_str = $temp_time_str;
				}
				// This check could generate a E_WARNING message, which will display unless you turn off warning messages in wp_config
				else if ( !($new_time_str = date( $mstw_gs_time_format, get_post_meta( $post->ID, '_mstw_gs_unix_dtg', true ) ) ) ) {
					$new_time_str = $temp_time_str;
				}
					
				$row_string =  $row_string . $row_td . $new_time_str . '</td>';
				
			}
			
			// column 5: create the media listings in a pretty format // this is just a placeholder, need links
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
			
			$row_string = $row_string . $media_links . '</td>';  // Should have a </tr> here??
			
			$output = $output . $row_string;
			
			$row_cnt = 1- $row_cnt;  // Get the styles right
			
		} // end of foreach post
		$output = $output . '</table>';
	}
	else { // No posts were found
	
		$output =  '<h3>' . __( 'No Scheduled Games Found.', $mstw_domain ) . '</h3>';
		
	}
	
	return $output;
	
}

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
				'home_only' => false
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
// $current -> FOR DEBUG ONLY. This defaults to the current time, and that's what it should be
// $home_only -> countdown to home games only, defaults to false (all games)
// --------------------------------------------------------------------------------------
function mstw_gs_build_countdown( $sched, $intro, $home_only ) {
	
	// General debug string used throughout
	//global $mstw_debug_str;
	
	// For localization
	global $mstw_domain;
	
	// The format for the next game time; will be an option set in admin someday
	global $mstw_gs_cdt_time_format;
	global $mstw_gs_cdt_tbd_format;
	
	// First get all the games for the specified schedule id.
	$game_posts = get_posts(array( 'numberposts' => -1,
							  'post_type' => 'scheduled_games',
							  'meta_query' => array(
												array(
													'key' => '_mstw_gs_sched_id',
													'value' => $sched,
													'compare' => '='
												)
											),
							  
							  'orderby' => 'meta_value', 
							  'meta_key' => '_mstw_gs_unix_date',
							  'order' => 'ASC' 
							));
							
	// Set some local variables
	$current_dtg = time( );  	// Get the current date-time stamp
	
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
				$game_date = get_post_meta( $game->ID, '_mstw_gs_unix_date', true );
				$game_dtg = get_post_meta( $game->ID, '_mstw_gs_unix_dtg', true );
				$opponent = get_post_meta( $game->ID, '_mstw_gs_opponent', true );
				$opponent_link = get_post_meta( $game->ID, '_mstw_gs_opponent_link', true );
				$location = get_post_meta( $game->ID, '_mstw_gs_location', true );
				$location_link = get_post_meta( $game->ID, '_mstw_gs_location_link', true );
				$game_time = get_post_meta( $game->ID, '_mstw_gs_game_time', true );
				break; 
			}
		}
	}
	
	// see what was found
	if ( ! $have_games ) {
		// No games scheduled after the current time
		if ( $home_only ) {
			$ret_msg = __( 'No home games found.', $mstw_domain );
		}
		else {
			$ret_msg = __( 'No games found.', $mstw_domain );
		}
		$ret_str = '<span class="mstw-gs-cdt-intro">' . $ret_msg . '</span>';
	}
	else {
		// we found a game, so build the countdown display
		
		// Game day, date, time; need to handle a TBD time
		if ( $game_time == 'TBD' or $game_time == 'T.B.D.' or $game_time == 'T.B.A.' or $game_time == 'TBA' ) {
			$dtg_str = mstw_date_loc( $mstw_gs_cdt_tbd_format, $game_date ) . ' Time TBA'; //$game_date is the UNIX timestamp DATE only
		}
		else {
			$dtg_str = mstw_date_loc( $mstw_gs_cdt_time_format, $game_dtg ); //get_post_meta( $game->ID, '_mstw_gs_unix_dtg', true ) );  
        }
		
		$ret_str = $ret_str . '<span class="mstw-gs-cdt-dtg">' . $dtg_str .  '</span><br/>';
		
		// Add the opponent & location
		// 20120821-MAO: Location display should be an option.
		
		$opponent_entry = $opponent;
		
		//Check to see if you have to add the opponent link
		if ( $opponent_link != '' ) {
			$opponent_entry = '<a href="' . $opponent_link . '" target="_blank" >' . $opponent_entry . '</a>';
		}
		
		$location_entry = $location;
		
		//Check to see if you have to add the opponent link
		if ( $location_link != '' ) {
			$location_entry = '<a href="' . $location_link . '" target="_blank" >' . $location_entry . '</a>';
		}
		
		$ret_str = $ret_str . '<span class="mstw-gs-cdt-opponent">' . $opponent_entry . ' @ ' . $location_entry .  '</span><br/>';
		//$ret_str = $ret_str . '<span class="mstw-gs-cdt-opponent">' . $opponent . '</span><br/>';
		
		// Add the intro text set in shortcut arg or widget setting
		$ret_str = $ret_str . '<span class="mstw-gs-cdt-intro">' . $intro .  '</span><br/>';
		
		// Add the countdown
		// argument types need to be set or dateDiff() does not work
		settype($game_date, 'integer');
		settype($game_dtg, 'integer');
		if ( $game_time == 'TBD' or $game_time == 'T.B.D.' or $game_time == 'T.B.A.' or $game_time == 'TBA' ) {
			$countdown = dateDiff( $game_date, $current_dtg );
		}
		else {
			$countdown = dateDiff( $game_dtg, $current_dtg );
		}
		
		$ret_str = $ret_str . '<span class="mstw-gs-cdt-countdown">' . $countdown . '</span>'; 
		
	}
						
	return $ret_str;
	
}

/****************************************************************/
// Time format is UNIX timestamp or
// PHP strtotime compatible strings
/****************************************************************/
  function dateDiff($time1, $time2, $precision = 4) {
  
	// required for localization
	global $mstw_domain;
	
	// $strings is not used in code. It triggers the localization in poedit.
	$strings = array(   __( 'year', $mstw_domain ), __( 'years', $mstw_domain ), 
						__( 'month', $mstw_domain ), __( 'months', $mstw_domain ),
						__( 'week', $mstw_domain ), __( 'weeks', $mstw_domain ),
						__( 'day', $mstw_domain ), __( 'days', $mstw_domain ),
						__( 'hour', $mstw_domain ), __( 'hours', $mstw_domain ),
						__( 'minute', $mstw_domain ), __( 'minutes', $mstw_domain ),
						__( 'second', $mstw_domain ), __( 'seconds', $mstw_domain ) );

	
    // If not numeric then convert texts to unix timestamps
	// echo ( 'arg1: ' . $time1 . ' arg2: ' . $time2 . '<br/>' );
    if (!is_int($time1)) {
      $time1 = strtotime($time1);
    }
    if (!is_int($time2)) {
      $time2 = strtotime($time2);
    }
 
    // If time1 is bigger than time2
    // Then swap time1 and time2
    if ($time1 > $time2) {
      $ttime = $time1;
      $time1 = $time2;
      $time2 = $ttime;
    }
 
    // Set up intervals and diffs arrays
    /*$intervals = array( __('year', $mstw_domain ), __('month', $mstw_domain ), __('day', $mstw_domain ), 
						__('hour', $mstw_domain ), __('minute', $mstw_domain ), __('second', $mstw_domain ) );*/
	$intervals = array( 'year' , 'month', 'day', 'hour', 'minute', 'second' );
    $diffs = array();
 
    // Loop thru all intervals
    foreach ($intervals as $interval) {
      // Set default diff to 0
      $diffs[$interval] = 0;
      // Create temp time from time1 and interval
      $ttime = strtotime("+1 " . $interval, $time1);
      // Loop until temp time is smaller than time2
      while ($time2 >= $ttime) {
	$time1 = $ttime;
	$diffs[$interval]++;
	// Create new temp time from time1 and interval
	$ttime = strtotime("+1 " . $interval, $time1);
      }
    }
 
    $count = 0;
    $times = array();
    // Loop thru all diffs
    foreach ($diffs as $interval => $value) {
      // Break if we have needed precission
      if ($count >= $precision) {
	break;
      }
      // Add value and interval 
      // if value is bigger than 0
      if ($value > 0) {
			// Add s if value is not 1
			if ($value != 1) {
				$interval .= "s";
			}
			// Add value and interval to times array
			$times[] = $value . " " . $interval;
			$count++;
      }
    }
	
	// Now for the localization - explode the return string
	// Replace the exploded strings with localizations
	$debug = "size of times: " . sizeof( $times ) . " times[0]= *" . $times[0] . "*";
	for ( $i = 0; $i < sizeof( $times ); $i++ ) {
		// $times_elts should look like "1 month" or "3 days"
		$times_elts = explode( " ", $times[$i] );
		// should have $times_elts[] = [1, month] or [3, days]
		for ( $j = 0; $j < sizeof( $times_elts ); $j++ ) {
			$times_elts[$j] = __( $times_elts[$j], $mstw_domain );
		}
		// This should put us back with "1 mes" or "3 dias"
		$times[$i] = implode( " ", $times_elts );
	}
	// Put the return string back together
	return implode( ", ", $times );
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
    function mstw_gs_sched_widget() {
        $widget_ops = array( 
			'classname' => 'mstw_gs_sched_widget_class', 
			'description' => 'Display a team schedule.' 
			); 
        $this->WP_Widget( 'mstw_gs_sched_widget', 'Game Schedule', $widget_ops );
    }
 
     //build the widget settings form
    function form($instance) {
        $defaults = array( 'title' => 'My Info', 'movie' => '', 'song' => '',
							'sched_title' => 'Schedule', 'sched_id' => '1', 'sched_yr' => date('Y') ); 
        $instance = wp_parse_args( (array) $instance, $defaults );
        $title = $instance['title'];
        $movie = $instance['movie'];
        $song = $instance['song'];
		
		$sched_title = $instance['sched_title'];
		$sched_id = $instance['sched_id'];
		
        ?>
        <p>Schedule Title: <input class="widefat" name="<?php echo $this->get_field_name( 'sched_title' ); ?>"  
            					type="text" value="<?php echo esc_attr( $sched_title ); ?>" /></p>
        <p>Schedule ID: <input class="widefat" name="<?php echo $this->get_field_name( 'sched_id' ); ?>"  
        						type="text" value="<?php echo esc_attr( $sched_id ); ?>" /></p>
            
        <?php
    }
 
    //save the widget settings
    function update($new_instance, $old_instance) {
		
        $instance = $old_instance;
		
		$instance['sched_title'] = strip_tags( $new_instance['sched_title'] );

		$instance['sched_id'] = strip_tags( $new_instance['sched_id'] );
 
        return $instance;
		
    }
 
 /*--------------------------------------------------------------------
 * displays the widget
 *------------------------------------------------------------------*/	
	function widget( $args, $instance ) {
	
		global $mstw_domain; //Important for localization functions!
		global $mstw_gs_sw_dtg_format; //format for the date column
	
		// $args holds the global theme variables, such as $before_widget
		extract( $args );
		
		echo $before_widget;
		
		$title = apply_filters( 'widget_title', $instance['sched_title'] );
		
		// Get the parameters for get_posts() below
		$sched_id = $instance['sched_id'];
		
		// show the widget title, if there is one
		if( !empty( $title ) ) {
			echo  $before_title . $title . $after_title;
		}
		
		// Get the game posts for $sched_id 
		$posts = get_posts(array( 'numberposts' => -1,
							  	  'post_type' => 'scheduled_games',
							  	  'meta_query' => array(
												array(
													'key' => '_mstw_gs_sched_id', //**
													'value' => $sched_id,
													'compare' => '='
												)
											),						  
							  	  'orderby' => 'meta_value', 
							  	  'meta_key' => '_mstw_gs_unix_date',
							      'order' => 'ASC' 
							));						
	
   	 	// Make table of posts
		if($posts) {
					
			// Start with the table header
        	$output = ''; ?>
        
        	<table class="mstw-gs-sw-tab">
        	<thead class="mstw-gs-sw-tab-head"><tr>
            	<th><?php _e( 'DATE', $mstw_domain ); ?></th>
            	<th><?php _e( 'OPPONENT', $mstw_domain ); ?></th>	
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
				$date_string = mstw_date_loc( $mstw_gs_sw_dtg_format, get_post_meta( $post->ID, '_mstw_gs_unix_date', true) );
				
				//$date_string = date( $mstw_gs_sw_dtg_format, get_post_meta( $post->ID, '_mstw_gs_unix_date', true) );
			
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

			_e( 'No Scheduled Games Found', $mstw_domain );

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
        $this->WP_Widget( 'mstw_gs_countdown_widget', 'Schedule Coundown Timer', $widget_ops );
	}
	
/*--------------------------------------------------------------------
 * display/manage the countdown widget settings form
 *------------------------------------------------------------------*/
	
	function form($instance) {
	
		global $mstw_domain; //Important for localization functions!
	
        $defaults = array( 'cd_title' => 'Countdown', 
							'cd_sched_id' => '1', 'cd_intro_text' => 'Time to kickoff:', 'cd_home_only' => '' ); 
							
        $instance = wp_parse_args( (array) $instance, $defaults );
		
        $cd_title = $instance['cd_title'];
		$cd_sched_id = $instance['cd_sched_id'];
		$cd_home_only = $instance['cd_home_only'];
		$cd_intro_text = $instance['cd_intro_text'];
		
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

	global $mstw_domain;
	
	//$param_D = array('', 'Lun', 'Mar', 'Mi&eacute;r', 'Jue', 'Vi&eacute;r', 'S&aacute;b', 'Dom');
	$param_D = array( '', 
						__( 'Mon', $mstw_domain ), 
						__( 'Tue', $mstw_domain ), 
						__( 'Wed', $mstw_domain ), 
						__( 'Thr', $mstw_domain ), 
						__( 'Fri', $mstw_domain ), 
						__( 'Sat', $mstw_domain ), 
						__( 'Sun', $mstw_domain ) );
	
	//$param_l = array('', 'lunes', 'martes', 'mi&eacute;rcoles', 'jueves', 'viernes', 's&aacute;bado', 'domingo');
	$param_l = array( '', 
						__( 'Monday', $mstw_domain ), 
						__( 'Tuesday', $mstw_domain ), 
						__( 'Wednesday', $mstw_domain ), 
						__( 'Thursday', $mstw_domain ), 
						__( 'Friday', $mstw_domain ), 
						__( 'Saturday', $mstw_domain ), 
						__( 'Sunday', $mstw_domain ) );
						
	//$param_F = array('', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'Septembre', 'Octobre', 'Novembre', 'D&eacute;cembre');
	$param_F = array( '', 
						__( 'January', $mstw_domain ), 
						__( 'February', $mstw_domain ), 
						__( 'March', $mstw_domain ), 
						__( 'April', $mstw_domain ), 
						__( 'May', $mstw_domain ), 
						__( 'June', $mstw_domain ),
						__( 'July', $mstw_domain ),
						__( 'August', $mstw_domain ),
						__( 'September', $mstw_domain ),
						__( 'October', $mstw_domain ),
						__( 'November', $mstw_domain ),
						__( 'December', $mstw_domain ) );
						
	//$param_M = array('', 'enero', 'feb.', 'marzo', 'abr.', 'mayo', 'jun.', 'jul.', 'agosto', 'sept.', 'oct.', 'nov.', 'dic.');
	$param_M = array( '', 
						__( 'Jan', $mstw_domain ), 
						__( 'Feb', $mstw_domain ), 
						__( 'Mar', $mstw_domain ), 
						__( 'Apr', $mstw_domain ), 
						__( 'May', $mstw_domain ), 
						__( 'Jun', $mstw_domain ),
						__( 'Jul', $mstw_domain ),
						__( 'Aug', $mstw_domain ),
						__( 'Sep', $mstw_domain ),
						__( 'Oct', $mstw_domain ),
						__( 'Nov', $mstw_domain ),
						__( 'Dec', $mstw_domain ) );
	

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