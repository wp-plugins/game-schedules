<?php
/*
 *	This is the admin portion of the MSTW Game Schedules Plugin
 *	It is loaded conditioned on is_admin() in mstw-game-schedule.php 
 */

/*  Copyright 2013  Mark O'Donnell  (email : mark@shoalsummitsolutions.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


	// ----------------------------------------------------------------
	// Load the MSTW Admin Utility Functions if necessary
	if ( !function_exists( 'mstw_admin_utils_loaded' ) ) {
			require_once  plugin_dir_path( __FILE__ ) . 'mstw-admin-utils.php';
	}

	// ----------------------------------------------------------------
	// Load the MSTW Admin Date-Time Utilities if necessary
	if ( !function_exists( 'mstw_utl_date_format_ctrl' ) ) {
		require_once 'mstw-dtg-admin-utils.php';
	}
	
	// ----------------------------------------------------------------	
	// Add styles and scripts for the color picker. 
	add_action( 'admin_enqueue_scripts', 'mstw_gs_enqueue_color_picker' );
	
	function mstw_gs_enqueue_color_picker( $hook_suffix ) {
		// Enqueue stylesheet and JS for WP color picker
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'mstw-gs-color-picker', plugins_url( 'game-schedules/js/gs-color-settings.js' ), array( 'wp-color-picker' ), false, true ); 
	}
	
	// ----------------------------------------------------------------
	// Add the custom MSTW icon to CPT pages
	add_action('admin_head', 'mstw_gs_custom_css');
	
	function mstw_gs_custom_css() { ?>
		<style type="text/css">
			#icon-mstw-gs-main-menu.icon32 {
				background: url(<?php echo plugins_url( '/game-schedules/images/mstw-logo-32x32.png', 'game-schedules' );?>) transparent no-repeat;
			}
			#icon-scheduled_game.icon32 {
				background: url(<?php echo plugins_url( '/game-schedules/images/mstw-logo-32x32.png', 'game-schedules' );?>) transparent no-repeat;
			}
			#icon-edit.icon32-posts-scheduled_games {
				background: url(<?php echo plugins_url( '/game-schedules/images/mstw-logo-32x32.png', 'game-schedules' );?>) transparent no-repeat;
			}
			#menu-posts-scheduled_game .wp-menu-image {
				background-image: url(<?php echo plugins_url( '/game-schedules/images/mstw-admin-menu-icon.png', 'game-schedules' );?>) no-repeat 6px -17px !important;
			}
			
		</style>
	<?php }
		
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
	// Remove the Bulk Actions pull-down - Edit only
	add_filter( 'bulk_actions-edit-scheduled_games', 'mstw_gs_bulk_actions' );

    function mstw_gs_bulk_actions( $actions ){
        unset( $actions['edit'] );
        return $actions;
    }
	
	// ----------------------------------------------------------------
	// Create the meta box for the Game Schedules custom post type
	add_action( 'add_meta_boxes', 'mstw_gs_add_meta' );

	function mstw_gs_add_meta () {
		add_meta_box('mstw-gs-meta', 'Game', 'mstw_gs_create_ui', 
						'scheduled_games', 'normal', 'high' );
	}

	// ----------------------------------------------------------------
	// Creates the UI form for entering a Game Schedules in the Admin page
	// Callback for: add_meta_box('mstw-gl-meta', 'Game', ... )

	function mstw_gs_create_ui( $post ) {
		// mstw_set_wp_default_timezone( ); Not needed??
		// Months array for <select>/<option> statement in UI
		$mstw_gs_months = array ( 	'01', '02', '03', '04',
									'05', '06', '07', '08',
									'09', '10', '11', '12',
								);
								
		// Days array for <select>/<option> statement in UI
		$mstw_gs_days = array ( '01', '02', '03', '04', '05', '06', '07', '08',
								'09', '10', '11', '12', '13', '14', '15', '16',
								'17', '18', '19', '20', '21', '22', '23', '24',
								'25', '26', '27', '28', '29', '30', '31'
								);
							
		$game_time_mins = array( '00' => '00', '05' => '05',
								 '10' => '10', '15' => '15',
								 '20' => '20', '25' => '25',
								 '30' => '30', '35' => '35',
								 '40' => '40', '45' => '45',
								 '50' => '50', '55' => '55',
								);
		$game_time_hrs = array(  '00' => '00', '01' => '01', 
								 '02' => '02', '03' => '03',
								 '04' => '04', '05' => '05',
								 '06' => '06', '07' => '07', 
								 '08' => '08', '09' => '09',
								 '10' => '10', '11' => '11', 
								 '12' => '12', '13' => '13', 
								 '14' => '14', '15' => '15',
								 '16' => '16', '17' => '17',
								 '18' => '18', '19' => '19',
								 '20' => '20', '21' => '21',
								 '22' => '22', '23' => '23',
								);
		$game_time_tba = array( '---' => '',
								__('TBA', 'mstw-loc-domain') => 'TBA',
								__('T.B.A.', 'mstw-loc-domain') => 'T.B.A.',
								__('TBD', 'mstw-loc-domain') => 'TBD',
								__('T.B.D.', 'mstw-loc-domain') => 'T.B.D.',
								);
		
		// Months array for <select>/<option> statement in UI
		//global $mstw_gs_months; 
		//global $mstw_gs_days; 
							  				  
		// Retrieve the metadata values if they exist
		$mstw_gs_sched_id = get_post_meta( $post->ID, '_mstw_gs_sched_id', true );
		
		//$mstw_gs_sched_year  = get_post_meta( $post->ID, '_mstw_gs_sched_year', true );	// alphanumeric year string
		//$mstw_gs_game_month = get_post_meta( $post->ID, '_mstw_gs_game_month', true );	// alpahnumeric month string
		//$mstw_gs_game_day = get_post_meta( $post->ID, '_mstw_gs_game_day', true );  	// alphanumeric day string
		
		$mstw_gs_game_time_tba = get_post_meta( $post->ID, '_mstw_gs_game_time_tba', true );  // game time is TBA
		
		// UNIX timestamp date & time. Used to generate year, month, and day
		if ( get_post_meta( $post->ID, '_mstw_gs_unix_dtg', true ) != '' ) {	
			$mstw_gs_unix_dtg = get_post_meta( $post->ID, '_mstw_gs_unix_dtg', true );
		}
		else {
			$mstw_gs_unix_dtg = current_time( 'timestamp' );
		}
		
		$mstw_gs_opponent = get_post_meta( $post->ID, '_mstw_gs_opponent', true );
		$mstw_gs_opponent_link = get_post_meta( $post->ID, '_mstw_gs_opponent_link', true );
		$mstw_gs_gl_location = get_post_meta( $post->ID, '_mstw_gs_gl_location', true );
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
			<td><input maxlength="64" size="20" name="mstw_gs_sched_id"
				value="<?php echo esc_attr( $mstw_gs_sched_id ); ?>"/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="mstw_gs_sched_year" >Game Year:</label></th>
			<td><input maxlength="4" size="5" name="mstw_gs_sched_year"
				value="<?php echo date( 'Y', (int)esc_attr( $mstw_gs_unix_dtg ) ); ?>"/></td>
		</tr>
		
		<tr valign="top">
			<th scope="row"><label for="mstw_gs_game_day" >Game Day:</label></th>
			<td>
			<select name="mstw_gs_game_day">    
				<?php foreach ( $mstw_gs_days as $label ) {  ?>
						<option value="<?php echo $label ?>" <?php selected( date( 'd', (int)$mstw_gs_unix_dtg ), $label );?>>
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
						<option value="<?php echo $label; ?>" <?php selected( date( 'm', (int)$mstw_gs_unix_dtg ), $label );?>>
							<?php echo $label; ?>
						 </option>              
				<?php } ?> 
			</select>   
			</td>
		</tr>
		
		<!-- New version of time with pull-downs -->
		<?php
		$curr_hrs = date( 'H', (int)esc_attr( $mstw_gs_unix_dtg ) );
		$curr_mins = date( 'i', (int)esc_attr( $mstw_gs_unix_dtg ) );
		$curr_tba = $mstw_gs_game_time_tba;
		if ( $curr_tba == '' ) {
			$curr_tba = '---';
		}	
		?>
		<tr valign="top">
			<th scope="row"><label for="game_time_hrs" >Game Time [hh:mm]:</label></th>
			<td>
				<select id='game_time_hrs' name='game_time_hrs'>
					<?php 
					foreach( $game_time_hrs as $key=>$value ) {
						$selected = ( $curr_hrs == trim( $value ) ) ? 'selected="selected"' : '';
						echo "<option value='$value' $selected>$key</option>";
					}
					?>
				</select>
				:
				<select id='game_time_mins' name='game_time_mins'>
					<?php 
					foreach( $game_time_mins as $key=>$value ) {
						$selected = ( $curr_mins == $value ) ? 'selected="selected"' : '';
						echo "<option value='$value' $selected>$key</option>";
					}
					?>
				</select>
				&nbsp;or&nbsp;
				<select id='game_time_tba' name='game_time_tba'>
					<?php 
					foreach( $game_time_tba as $key=>$value ) {
						$selected = ( $curr_tba == $value ) ? 'selected="selected"' : '';
						echo "<option value='$value' $selected>$key</option>";
					}
					?>
				</select>
			</td>
			<td>Note: if TBA is anything other than '---', then it is used for the game time whether or not a time is entered.</td>
			<!--<td><?php //echo '$curr_hrs:$curr_mins: ' . $curr_hrs .':' . $curr_mins; ?> </td>-->
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
		 
		<?php $plugin_active = 'Inactive';
		if( is_plugin_active('game-locations/mstw-game-locations.php') ) { 
			$plugin_active = 'Active';
			$locations = get_posts(array( 'numberposts' => -1,
							  'post_type' => 'game_locations',
							  'orderby' => 'title',
							  'order' => 'ASC' 
							));						
	
			if( $locations ) {
				echo '<tr valign="top">';
				echo '<th>Select Location from Game Locations:</th>';
				echo "<td><select id='mstw_gs_gl_location' name='mstw_gs_gl_location'>";
				foreach( $locations as $loc ) {
					$selected = ( $mstw_gs_gl_location == $loc->ID ) ? 'selected="selected"' : '';
					echo "<option value='" . $loc->ID . "'" . $selected . ">" . get_the_title( $loc->ID ) . "</option>";
				}
				echo "</select></td>";
				echo "<td>Note: this setting requires that the Game Locations plugin is activated. It is preferred to using the custom location and link settings below.</td>";
				echo "</tr>";
				
			}
		} //End: if (is_plugin_active) 
		else {
			echo '<tr valign="top">';
			echo '<th scope="row">Game Locations Plugin:</th>';
			echo "<td>Please activate the <a href='http://wordpress.org/extend/plugins/game-locations/' title='Game Locations Plugin'>Game Locations Plugin</a> to use this feature. It makes life a lot simpler for 'normal' Game Schedules use.</td>";
			echo '</tr>';
		} ?>
		
		<tr valign="top">
			<th scope="row"><label for="mstw_gs_location" >Game Location:</label></th>
			<td><input maxlength="64" size="30" name="mstw_gs_location"
				value="<?php echo esc_attr( $mstw_gs_location ); ?>"/></td>
			<td>Note: this setting is not needed if location is selected from Game Locations dropdown AND it will override any selection from the Game Locations dropdown.</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="mstw_gs_location_link" >Location Link:</label></th>
			<td><input maxlength="256" size="30" name="mstw_gs_location_link"
				value="<?php echo esc_attr( $mstw_gs_location_link ); ?>"/></td>
			<td>Note: this setting will override the Game Locations map link.</td>
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
			<th scope="row"><label for="mstw_gs_unix_date" >UNIX Date (Info Only):</label></th>
			<td>
				<?php echo mstw_date_loc( 'Y-m-d', (int)esc_attr( $mstw_gs_unix_dtg ) );?>
				<!--<input maxlength="60" size="30" name="mstw_gs_unix_date"
				value="<?php //echo date( 'Y-m-d', esc_attr( $mstw_gs_unix_date ) ); ?>"/>-->
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row"><label for="mstw_gs_unix_dtg" >UNIX Date-Time (Info Only):</label></th>
			<td>
				<?php echo mstw_date_loc( 'Y-m-d H:i', (int)esc_attr( $mstw_gs_unix_dtg ) ); ?>
				<!--<input maxlength="60" size="30" name="mstw_gs_unix_dtg"
				value="<?php echo date( 'l, Y-m-d h:i a', (int)esc_attr( $mstw_gs_unix_dtg ) ); ?>"/>-->
			</td>
		</tr>
		
		</table>
		
	<?php        	
	}

// ----------------------------------------------------------------
// Save the Game Schedules Meta Data
// ----------------------------------------------------------------
	add_action( 'save_post', 'mstw_gs_save_meta' );

	function mstw_gs_save_meta( $post_id ) {
		// set the date_default_timezone_set() to the WP (general) setting
		// mstw_set_wp_default_timezone( );
		
		//First verify the metadata required by the shortcode is set. If not, set defaults
		
		// SCHEDULE ID
		// If schedule id was not set, default to 1 :: could happen!
		if ( isset( $_POST['mstw_gs_sched_id'] ) ) {
			$mstw_id = strip_tags( trim( $_POST['mstw_gs_sched_id'] ) );
			if ( $mstw_id == "" ) {
				$mstw_id = '1';
			}
		}
		else {  //$_POST['mstw_gs_sched_id'] is not set
			$mstw_id = '1';
		}
		update_post_meta( $post_id, '_mstw_gs_sched_id', $mstw_id );
			
		// YEAR
		// If game year was not set, default to the current year :: could happen!
		$mstw_year = strip_tags( trim( $_POST[ 'mstw_gs_sched_year' ] ) );
		if ($mstw_year == '') {
			$mstw_year = date('Y');
		}
		//update_post_meta( $post_id, '_mstw_gs_sched_year', $mstw_year );
		
		// MONTH
		// Month is a pulldown, we should be good
		$mstw_month = strip_tags( trim( $_POST[ 'mstw_gs_game_month' ] ) );
		//update_post_meta( $post_id, '_mstw_gs_game_month', $mstw_month );
		
		// DAY
		// Day is a pulldown, we should be good!
		$mstw_day = strip_tags( trim( $_POST[ 'mstw_gs_game_day' ] ) );
		//update_post_meta( $post_id, '_mstw_gs_game_day', $mstw_day );
		
		$date_only_str = $mstw_year . '-' . $mstw_month . '-' . $mstw_day;
		//$unix_date = strtotime( $date_only_str );
		//update_post_meta( $post_id, '_mstw_gs_unix_date', $unix_date );
		
		$game_time_hrs = strip_tags( trim( $_POST[ 'game_time_hrs' ] ) );
		$game_time_mins = strip_tags( trim( $_POST[ 'game_time_mins' ] ) );
		
		$game_time_tba = strip_tags( trim( $_POST[ 'game_time_tba' ] ) );
		update_post_meta( $post_id, '_mstw_gs_game_time_tba', $game_time_tba );
		
		if ( $game_time_tba != '' ) {
			$game_time_hrs = $game_time_mins = '00';
		}
		
		$mstw_time = $game_time_hrs . ':' . $game_time_mins;
		
		$full_dtg_str = $date_only_str . ' ' . $mstw_time;
		$unix_dtg = strtotime( $full_dtg_str );
		update_post_meta( $post_id, '_mstw_gs_unix_dtg', $unix_dtg );
					
		// Okay, we should be good to update the database
				
		update_post_meta( $post_id, '_mstw_gs_opponent', 
				strip_tags( $_POST['mstw_gs_opponent'] ) );
				
		update_post_meta( $post_id, '_mstw_gs_opponent_link', 
				strip_tags( $_POST['mstw_gs_opponent_link'] ) );
				
		update_post_meta( $post_id, '_mstw_gs_gl_location', 
				strip_tags( $_POST['mstw_gs_gl_location'] ) );
				
		update_post_meta( $post_id, '_mstw_gs_gl_loc_title', 
				strip_tags( get_the_title( $_POST['mstw_gs_gl_location'] ) ) );
				
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

// ----------------------------------------------------------------
// Set up the Game Schedules 'view all' columns
// ----------------------------------------------------------------
add_filter( 'manage_edit-scheduled_games_columns', 'mstw_gs_edit_columns' ) ;

	function mstw_gs_edit_columns( $columns ) {	
		
		//mstw_set_wp_default_timezone( ); 

		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Title' ),
			'sched_id' => __( 'Schedule' ),
			'sched_year' => __( 'Year' ),
			'game_date' => __( 'Date' ),
			'opponent' => __( 'Opponent' ),
			'opponent_link' => __( 'Opponent Link' ),
			'gl_location' => __( 'Location' ),
			'location' => __( 'Custom Location' ),
			'location_link' => __( 'Custom Location Link' ),
			'game_time' => __( 'Time' ),
			'game_result' => __( 'Result' )
			/* 'debug' => __('Debug-Remove') */
		);

		return $columns;
	}

// ----------------------------------------------------------------
// Display the Game Locations 'view all' columns
// ----------------------------------------------------------------
	add_action( 'manage_scheduled_games_posts_custom_column', 'mstw_gs_manage_columns', 10, 2 );

	function mstw_gs_manage_columns( $column, $post_id ) {
		global $post;
		
		$options = get_option( 'mstw_gs_options' );
		$mstw_admin_date_format = $options['gs_admin_dtg_fmt'];
		$mstw_admin_time_format = $options['gs_admin_time_fmt'];
		
		$game_timestamp = get_post_meta( $post_id, '_mstw_gs_unix_dtg', true );

		switch( $column ) {
			/* Debug Column */
			/*case 'debug' :
				$debug_str = "";
				echo (	$debug_str );	
				break;
			*/
			
			// If displaying the 'sched_year' column.
			case 'sched_year' :
				// Build from unix timestamp
				if ( empty( $game_timestamp ) ) 
					_e( 'No Game Year', 'mstw-loc-domain' );
				else
					printf( '%s', date( 'Y', $game_timestamp ) );
				break;
				
			// If displaying the 'sched_id' column.
			case 'sched_id' :
				// Get the post meta
				$mstw_gs_sched_id = get_post_meta( $post_id, '_mstw_gs_sched_id', true );

				if ( empty( $mstw_gs_sched_id ) )
					_e( 'No Schedule Defined', 'mstw-loc-domain' );
				else
					printf( '%s', $mstw_gs_sched_id );

				break;
			

			// If displaying the 'game_date' column
			case 'game_date' :
				// Build from unix timestamp
				if ( empty( $game_timestamp ) )
					_e( 'No Game Date', 'mstw-loc-domain' );
				else
					echo( date( $mstw_admin_date_format, $game_timestamp ) );

				break;
			
					
			// If displaying the 'opponent' column
			case 'opponent' :
				// Get the post meta
				$mstw_gs_opponent = get_post_meta( $post_id, '_mstw_gs_opponent', true );

				if ( empty( $mstw_gs_opponent ) )
					_e( 'No Opponent', 'mstw-loc-domain' );
				else
					printf( '%s', $mstw_gs_opponent );

				break;

			// If displaying the 'opponent_link' column
			case 'opponent_link' :
				// Get the post meta.
				$mstw_gs_opponent_link = get_post_meta( $post_id, '_mstw_gs_opponent_link', true );

				if ( empty( $mstw_gs_opponent_link ) )
					_e( 'No Opponent Link', 'mstw-loc-domain' );
				else
					printf( '%s', $mstw_gs_opponent_link );

				break;
				
			//If displaying the 'gl_location' column. */
			case 'gl_location' :
				// Get the post meta
				$mstw_gs_gl_location = get_post_meta( $post_id, '_mstw_gs_gl_location', true );

				if ( empty( $mstw_gs_gl_location ) )
					_e( 'No Location from Game Locations Plugin', 'mstw-loc-domain' );
				else
					printf( '%s', get_the_title( $mstw_gs_gl_location ) );

				break;	
				
			// If displaying the 'location' column
			case 'location' :
				// Get the post meta
				$mstw_gs_location = get_post_meta( $post_id, '_mstw_gs_location', true );

				if ( empty( $mstw_gs_location ) )
					_e( 'No Location', 'mstw-loc-domain' );
				else
					printf( '%s', $mstw_gs_location );

				break;	
				
			// If displaying the 'location_link' column
				case 'location_link' :
				// Get the post meta
				$mstw_gs_location_link = get_post_meta( $post_id, '_mstw_gs_location_link', true );

				if ( empty( $mstw_gs_location_link ) )
					_e( 'No Location Link', 'mstw-loc-domain' );
				else
					printf( '%s', $mstw_gs_location_link );

				break;
				
			//If displaying the 'time' column
			case 'game_time' :
				// Build from UNIX timestamp
				$mstw_gs_game_time_tba = get_post_meta( $post_id, '_mstw_gs_game_time_tba', true );

				if ( $mstw_gs_game_time_tba != '' )
					printf( '%s', $mstw_gs_game_time_tba );
				else
					printf( '%s', date( $mstw_admin_time_format, $game_timestamp ) );
				
				break;	

			//If displaying the 'result' column
			case 'game_result' :
				// Get the post meta
				$mstw_gs_game_result = get_post_meta( $post_id, '_mstw_gs_game_result', true );

				if ( empty( $mstw_gs_game_result ) )
					_e( 'No Game Result', 'mstw-loc-domain' );
				else
					printf( '%s', $mstw_gs_game_result );

				break;
				
			/* Just break out of the switch statement for everything else. */
			default :
				break;
		}
	}
// ----------------------------------------------------------------
// Remove the "View Post" option
// ----------------------------------------------------------------
	if ( is_admin( ) ) {
		add_filter( 'post_row_actions', 'mstw_gs_remove_the_view', 10, 2 );
	}			

	function mstw_gs_remove_the_view( $actions ) {
		global $post;
		if( $post->post_type == 'scheduled_games' ) {
			unset( $actions['view'] );
		}
		return $actions;
	}

// ----------------------------------------------------------------
//	CODE FOR GAME SCHEDULES SETTINGS PAGES
// ----------------------------------------------------------------

// ----------------------------------------------------------------	
// Add a menus for the settings pages
// ----------------------------------------------------------------
	add_action( 'admin_menu', 'mstw_gs_add_page' );

	
	function mstw_gs_add_page( ) {
		
		// Decided to add the settings page to the Games menu rather than
		// the settings menu
		$page = add_submenu_page( 	'edit.php?post_type=scheduled_games', 
							'Game Schedule Settings', 	//page title
							'Display Settings', 		//menu title
							'manage_options', 			// Capability required to see this option.
							'mstw_gs_settings', 		// Slug name to refer to this menu
							'mstw_gs_option_page' );	// Callback to output content
							
		$page = add_submenu_page( 	'edit.php?post_type=scheduled_games', 
							'Game Schedule Color Settings', 	//page title
							'Color Settings', 		//menu title
							'manage_options', 			// Capability required to see this option.
							'mstw_gs_colors', 		// Slug name to refer to this menu
							'mstw_gs_colors_page' );	// Callback to output content
							
		// Does the importing work
		$plugin = new MSTW_GS_ImporterPlugin;
		
		add_submenu_page(	'edit.php?post_type=scheduled_games',
							'Import Schedule from CSV File',		//page title
							'CSV Schedule Import',					//menu title
							'manage_options',						//capability to access
							'mstw_gs_csv_import',					//slug name for menu
							array( $plugin, 'form' )				//callback to display menu
						);
							
		// Now also add action to load java scripts ONLY when you're on this page
		// add_action( 'admin_print_styles-' . $page, mstw_gs_load_scripts );
	}

// ----------------------------------------------------------------	
// 	Render the option page
// ----------------------------------------------------------------
	function mstw_gs_option_page() {
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2>Game Schedule Plugin Settings</h2>
			<?php //settings_errors(); ?>
			<form action="options.php" method="post">
				<?php settings_fields( 'mstw_gs_options_group' ); ?>
				<?php do_settings_sections( 'mstw_gs_settings' ); ?>
				<p>
				<input name="Submit" type="submit" class="button-primary" value="Save Changes" />
				</p>
			</form>
		</div>
		<?php
	}
	
// ----------------------------------------------------------------	
// 	Render the option page
// ----------------------------------------------------------------

	function mstw_gs_colors_page( ) {
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2>Game Schedule Color Settings</h2>
			<?php //settings_errors(); ?>
			<form action="options.php" method="post">
				<?php settings_fields( 'mstw_gs_options_group' ); ?>
				<?php do_settings_sections( 'mstw_gs_colors' ); ?>
				<p>
				<input name="Submit" type="submit" class="button-primary" value="Save Changes" />
				</p>
			</form>
		</div>
		<?php
	}

// ----------------------------------------------------------------	
// 	Register and define the settings
// ----------------------------------------------------------------
	add_action('admin_init', 'mstw_gs_admin_init');
	
	function mstw_gs_admin_init( ) {
		//$options = get_option( 'mstw_gs_options' );
		//$options - wp_parse_args( $options, mstw_gs_get_defaults( ) );
		//print_r( $options );
		
		register_setting(
			'mstw_gs_options_group',  	// settings group name
			'mstw_gs_options',  		// options (array) to validate
			'mstw_gs_validate_options'  // validation function
			);
			
		// Data Fields (& columns) Settings
		mstw_gs_data_fields_setup( );
		
		// Date & Time Format Settings
		mstw_gs_dtg_format_setup( );
		
		// Colors Settings
		mstw_gs_colors_setup( );
	
	}
	
// ----------------------------------------------------------------	
// 	Colors settings page setup	
// ----------------------------------------------------------------		
	function mstw_gs_colors_setup( ) {
		// DTG format section
		// Data fields/columns -- show/hide and labels
		//$display_on_page = 'mstw_gs_colors';
		//$page_section = 'mstw_gs_colors_main';
		
		//$options = get_option( 'mstw_gs_options' );
		
		mstw_gs_table_colors_section_setup( );
		
		mstw_gs_cdt_colors_section_setup( );
		
		mstw_gs_slider_colors_section_setup( );	
			
	}
	
/ ----------------------------------------------------------------	
// 	Slider colors section setup	
// ----------------------------------------------------------------	
	function mstw_gs_slider_colors_section_setup( ) {
		
		$display_on_page = 'mstw_gs_colors';
		$page_section = 'mstw_gs_slider_colors';
		
		$options = get_option( 'mstw_gs_options' );
		
		add_settings_section(
			$page_section,
			'Schedule Slider Colors',
			'mstw_gs_colors_slider_inst',
			$display_on_page
			);	
	}
	
// ----------------------------------------------------------------	
// 	Table (shortcode and widget) colors section setup	
// ----------------------------------------------------------------
	function mstw_gs_table_colors_section_setup( ) {
	
		$display_on_page = 'mstw_gs_colors';
		$page_section = 'mstw_gs_table_colors';
		
		$options = get_option( 'mstw_gs_options' );
		
		add_settings_section(
			$page_section,
			'Schedule Table Colors',
			'mstw_gs_colors_table_inst',
			$display_on_page
			);
	
		// Header background color
		$args = array( 	'id' => 'gs_tbl_hdr_bkgd_color',
						'name' => 'mstw_gs_options[gs_tbl_hdr_bkgd_color]',
						'value' => $options['gs_tbl_hdr_bkgd_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'gs_tbl_hdr_bkgd_color',
			__( 'Header Background Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_on_page,				//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Header text color
		$args = array( 	'id' => 'gs_tbl_hdr_text_color',
						'name' => 'mstw_gs_options[gs_tbl_hdr_text_color]',
						'value' => $options['gs_tbl_hdr_text_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'gs_tbl_hdr_text_color',
			__( 'Header Text Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_on_page,				//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Odd row background color
		$args = array( 	'id' => 'gs_tbl_odd_bkgd_color',
						'name' => 'mstw_gs_options[gs_tbl_odd_bkgd_color]',
						'value' => $options['gs_tbl_odd_bkgd_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'gs_tbl_odd_bkgd_color',
			__( 'Odd Row Background Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_on_page,				//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Odd row text color
		$args = array( 	'id' => 'gs_tbl_odd_text_color',
						'name' => 'mstw_gs_options[gs_tbl_odd_text_color]',
						'value' => $options['gs_tbl_odd_text_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'gs_tbl_odd_text_color',
			__( 'Odd Row Text Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_on_page,				//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Even row background color
		$args = array( 	'id' => 'gs_tbl_even_bkgd_color',
						'name' => 'mstw_gs_options[gs_tbl_even_bkgd_color]',
						'value' => $options['gs_tbl_even_bkgd_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'gs_tbl_even_bkgd_color',
			__( 'Even Row Background Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_on_page,				//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Even row text color
		$args = array( 	'id' => 'gs_tbl_even_text_color',
						'name' => 'mstw_gs_options[gs_tbl_even_text_color]',
						'value' => $options['gs_tbl_even_text_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'gs_tbl_even_text_color',
			__( 'Even Row Text Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_on_page,				//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Home game (row) background color
		$args = array( 	'id' => 'gs_tbl_home_bkgd_color',
						'name' => 'mstw_gs_options[gs_tbl_home_bkgd_color]',
						'value' => $options['gs_tbl_home_bkgd_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'gs_tbl_home_bkgd_color',
			__( 'Home Game (Row) Background Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_on_page,				//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Home game (row) text color
		$args = array( 	'id' => 'gs_tbl_home_text_color',
						'name' => 'mstw_gs_options[gs_tbl_home_text_color]',
						'value' => $options['gs_tbl_home_text_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'gs_tbl_home_text_color',
			__( 'Home Game (Row) Text Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_on_page,				//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
	
	}
	
// ----------------------------------------------------------------	
// 	Colors table section instructions	
// ----------------------------------------------------------------	
	function mstw_gs_colors_table_inst( ) {
		echo '<p>' . __( "Enter the default colors for your Schedule Table shortcodes and widgets. NOTE: These settings will override the default colors in the plugin's stylsheet." , 'mstw-loc-domain' ) . '</p>';
	}
	
// ----------------------------------------------------------------	
// 	CDT (shortcode and widget) colors section setup	
// ----------------------------------------------------------------	
	
	function mstw_gs_cdt_colors_section_setup( ) {
	
		$display_on_page = 'mstw_gs_colors';
		$page_section = 'mstw_gs_cdt_colors';
		
		$options = get_option( 'mstw_gs_options' );
		
		add_settings_section(
			$page_section,
			'Countdown Timer Colors',
			'mstw_gs_colors_cdt_inst',
			$display_on_page
		);
			
		// Game time text color
		$args = array( 	'id' => 'gs_cdt_game_time_color',
						'name' => 'mstw_gs_options[gs_cdt_game_time_color]',
						'value' => $options['gs_cdt_game_time_color'],
						'label' => ''
					 );					 
		add_settings_field(
			'gs_cdt_game_time_color',
			__( 'Game Time Text Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_on_page,				//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Opponent text color
		$args = array( 	'id' => 'gs_cdt_opponent_color',
						'name' => 'mstw_gs_options[gs_cdt_opponent_color]',
						'value' => $options['gs_cdt_opponent_color'],
						'label' => ''
					 );					 
		add_settings_field(
			'gs_cdt_opponent_color',
			__( 'Opponent Text Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_on_page,				//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Location text color
		$args = array( 	'id' => 'gs_cdt_location_color',
						'name' => 'mstw_gs_options[gs_cdt_location_color]',
						'value' => $options['gs_cdt_location_color'],
						'label' => ''
					 );				 
		add_settings_field(
			'gs_cdt_location_color',
			__( 'Location Text Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_on_page,				//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Intro text color
		$args = array( 	'id' => 'gs_cdt_intro_color',
						'name' => 'mstw_gs_options[gs_cdt_intro_color]',
						'value' => $options['gs_cdt_intro_color'],
						'label' => ''
					 );				 
		add_settings_field(
			'gs_cdt_intro_color',
			__( 'Intro Text Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_on_page,				//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Countdown text color
		$args = array( 	'id' => 'gs_cdt_countdown_color',
						'name' => 'mstw_gs_options[gs_cdt_countdown_color]',
						'value' => $options['gs_cdt_countdown_color'],
						'label' => ''
					 );
		add_settings_field(
			'gs_cdt_countdown_color',
			__( 'Countdown Text Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_on_page,				//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Countdown background color
		$args = array( 	'id' => 'gs_cdt_countdown_bkgd_color',
						'name' => 'mstw_gs_options[gs_cdt_countdown_bkgd_color]',
						'value' => $options['gs_cdt_countdown_bkgd_color'],
						'label' => ''
					 );
		add_settings_field(
			'gs_cdt_countdown_bkgd_color',
			__( 'Countdown Background Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_on_page,				//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
	}
	
// ----------------------------------------------------------------	
// 	Colors CDT section instructions	
// ----------------------------------------------------------------	
	function mstw_gs_colors_cdt_inst( ) {
		echo '<p>' . __( "Enter the default colors for your countdown timer shortcodes and widgets. NOTE: These settings will override the default colors in the plugin's stylsheet.", 'mstw-loc-domain' ) . '</p>';
	}
	
// ----------------------------------------------------------------	
// 	Colors Slider section instructions	
// ----------------------------------------------------------------	
	function mstw_gs_colors_slider_inst( ) {
		echo '<p>' . __( "Enter the default colors for your Schedule Slider shortcodes and widgets. NOTE: These settings will override the default colors in the plugin's stylsheet.", 'mstw-loc-domain' ) . '</p>';
		echo '<p>
		SLIDER<br>
		Slider blook bkgd<br>
		Slider header text<br>
		Slider header divider<br>
		Slider date text<br>
		Slider opponent text<br>
		Slider location text<br>
		Slider date text<br>
		Slider links text<br>
		</p>';
	}
	
	function mstw_gs_dtg_format_setup( ) {
		// DTG format section
		// Data fields/columns -- show/hide and labels
		$display_on_page = 'mstw_gs_settings';
		$page_section = 'mstw_gs_dtg_format_settings';
		
		$options = get_option( 'mstw_gs_options' );
		
		add_settings_section(
			$page_section,
			__( 'Date and Time Formats', 'mstw-loc-domain' ),
			'mstw_gs_date_time_inst',
			$display_on_page
			);
			
		// Date format for admin add/edit game screen
		$args = array(	'opt_name' => 'mstw_gs_options',
						'set_name' => 'admin_date_format',
						'set_default' => 'Y-m-d',
						'cdt' => false,
						);						
		add_settings_field(
			'admin_date_format',
			__( 'Admin Table Date Format:', 'mstw-loc-domain' ),
			'mstw_utl_date_format_ctrl',
			'mstw_gs_settings',
			'mstw_gs_dtg_format_settings',
			$args
		);

		//Custom date format for admin add/edit game screen
		$args = array(	'id' => 'custom_admin_date_format',
						'name'	=> 'mstw_gs_options[custom_admin_date_format]',
						'value'	=> $options['custom_admin_date_format'],
						'label'	=> __( 'Enter a PHP date() format string for a custom format. You probably should know what you are doing before selecting the custom option. (Default: "")', 'mstw-loc-domain' )
						);						
		add_settings_field(
			'custom_admin_date_format',
			__( 'Custom Admin Table Date Format:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',
			$display_on_page,
			$page_section,
			$args
		);	

		// Time format for admin add/edit game screen
		$args = array(	'opt_name' => 'mstw_gs_options',
						'set_name' => 'admin_time_format',
						'set_default' => 'H:i',
						);						
		add_settings_field(
			'admin_time_format',
			__( 'Admin Table Time Format:', 'mstw-loc-domain' ),
			'mstw_utl_time_format_ctrl',
			'mstw_gs_settings',
			'mstw_gs_dtg_format_settings',
			$args
		);	
		
		//Custom time format for admin add/edit game screen
		$args = array(	'id' => 'custom_admin_time_format',
						'name'	=> 'mstw_gs_options[custom_admin_time_format]',
						'value'	=> $options['custom_admin_time_format'],
						'label'	=> __( 'Enter a PHP date() format string for a custom format. You probably should know what you are doing before selecting the custom option. (Default: "")', 'mstw-loc-domain' )
						);						
		add_settings_field(
			'custom_admin_time_format',
			__( 'Custom Admin Table Time Format:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',
			$display_on_page,
			$page_section,
			$args
		);	
		
		// Date format for schedule table shortcode
		$args = array(	'opt_name' => 'mstw_gs_options',
						'set_name' => 'table_date_format',
						'set_default' => 'Y m d',
						'cdt' => false,
						);						
		add_settings_field(
			'table_date_format',
			__( 'Schedule Table [shortcode] Date Format:', 'mstw-loc-domain' ),
			'mstw_utl_date_format_ctrl',
			'mstw_gs_settings',
			'mstw_gs_dtg_format_settings',
			$args
		);
		
		//Custom date format for schedule table shortcode
		$args = array(	'id' => 'custom_table_date_format',
						'name'	=> 'mstw_gs_options[custom_table_date_format]',
						'value'	=> $options['custom_table_date_format'],
						'label'	=> __( 'Enter a PHP date() format string for a custom format. You probably should know what you are doing before selecting the custom option. (Default: "")', 'mstw-loc-domain' )
						);						
		add_settings_field(
			'custom_table_date_format',
			__( 'Custom Schedule Table [shortcode] Date Format:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',
			$display_on_page,
			$page_section,
			$args
		);	
		
		// Time format for schedule table shortcode
		$args = array(	'opt_name' => 'mstw_gs_options',
						'set_name' => 'table_time_format',
						'set_default' => 'H:i',
						);						
		add_settings_field(
			'table_time_format',
			__( 'Schedule Table [shortcode] Time Format:', 'mstw-loc-domain' ),
			'mstw_utl_time_format_ctrl',
			'mstw_gs_settings',
			'mstw_gs_dtg_format_settings',
			$args
		);
		
		//Custom time format for schedule table shortcode
		$args = array(	'id' => 'custom_table_time_format',
						'name'	=> 'mstw_gs_options[custom_table_time_format]',
						'value'	=> $options['custom_table_time_format'],
						'label'	=> __( 'Enter a PHP date() format string for a custom format. You probably should know what you are doing before selecting the custom option. (Default: "")', 'mstw-loc-domain' )
						);						
		add_settings_field(
			'custom_table_time_format',
			__( 'Custom Schedule Table [shortcode] Time Format:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',
			$display_on_page,
			$page_section,
			$args
		);	
		
		// Date format for table widget
		$args = array(	'opt_name' => 'mstw_gs_options',
						'set_name' => 'table_widget_date_format',
						'set_default' => 'j M y',
						'cdt' => false,
						);						
		add_settings_field(
			'table_widget_date_format',
			__( 'Schedule Table (widget) Date Format:', 'mstw-loc-domain' ),
			'mstw_utl_date_format_ctrl',
			'mstw_gs_settings',
			'mstw_gs_dtg_format_settings',
			$args
		);
		
		//Custom date format for schedule table widget
		$args = array(	'id' => 'custom_table_widget_date_format',
						'name'	=> 'mstw_gs_options[custom_table_widget_date_format]',
						'value'	=> $options['custom_table_widget_date_format'],
						'label'	=> __( 'Enter a PHP date() format string for a custom format. You probably should know what you are doing before selecting the custom option. (Default: "")', 'mstw-loc-domain' )
						);						
		add_settings_field(
			'custom_table_widget_date_format',
			__( 'Custom Schedule Table (widget) Date Format:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',
			$display_on_page,
			$page_section,
			$args
		);
		
		// DTG format for countdown timer
		$args = array(	'opt_name' => 'mstw_gs_options',
						'set_name' => 'cdt_dtg_format',
						'set_default' => 'l, j M g:i a',
						'cdt' => true,
						);			
		add_settings_field(
			'cdt_dtg_format',
			__( 'Countdown Timer (widget & [shortcode]) Date & Time Format:', 'mstw-loc-domain' ),
			'mstw_utl_date_format_ctrl',
			'mstw_gs_settings',
			'mstw_gs_dtg_format_settings',
			$args
		);
		
		//Custom DTG format for countdown timer table & widget
		$args = array(	'id' => 'custom_cdt_dtg_format',
						'name'	=> 'mstw_gs_options[custom_cdt_dtg_format]',
						'value'	=> $options['custom_cdt_dtg_format'],
						'label'	=> __( 'Enter a PHP date() format string for a custom format. You probably should know what you are doing before selecting the custom option. (Default: "")', 'mstw-loc-domain' )
						);						
		add_settings_field(
			'custom_cdt_dtg_format',
			__( 'Custom Countdown Timer (widget & [shortcode]) Date-Time Format:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',
			$display_on_page,
			$page_section,
			$args
		);	
		
		// Date format for countdown timer - with no time [TBA]
		$args = array(	'opt_name' => 'mstw_gs_options',
						'set_name' => 'cdt_date_format',
						'set_default' => 'l, j M',
						'cdt' => false,
						);						
		add_settings_field(
			'cdt_date_format',
			__( 'Countdown Timer (widget & [shortcode]) Date Format (game time is TBA):', 'mstw-loc-domain' ),
			'mstw_utl_date_format_ctrl',
			'mstw_gs_settings',
			'mstw_gs_dtg_format_settings',
			$args
		);
		
		//Custom date format for countdown timer
		$args = array(	'id' => 'custom_cdt_date_format',
						'name'	=> 'mstw_gs_options[custom_cdt_date_format]',
						'value'	=> $options['custom_cdt_date_format'],
						'label'	=> __( 'Enter a PHP date() format string for a custom format. You probably should know what you are doing before selecting the custom option. (Default: "")', 'mstw-loc-domain' )
						);						
		add_settings_field(
			'custom_cdt_date_format',
			__( 'Custom Countdown Timer (widget & [shortcode]) Date Format:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',
			$display_on_page,
			$page_section,
			$args
		);
		
		// Date format schedule slider
		$args = array(	'opt_name' => 'mstw_gs_options',
						'set_name' => 'slider_date_format',
						'set_default' => 'D, j M',
						'cdt' => false,
						);						
		add_settings_field(
			'slider_date_format',
			__( 'Schedule Slider Date Format:', 'mstw-loc-domain' ),
			'mstw_utl_date_format_ctrl',
			$display_on_page,
			$page_section,
			$args
		);
		
		//Custom date format for schedule slider
		$args = array(	'id' => 'custom_slider_date_format',
						'name'	=> 'mstw_gs_options[custom_slider_date_format]',
						'value'	=> $options['custom_slider_date_format'],
						'label'	=> __( 'Enter a PHP date() format string for a custom format. You probably should know what you are doing before selecting the custom option. (Default: "")', 'mstw-loc-domain' )
						);						
		add_settings_field(
			'custom_slider_date_format',
			__( 'Custom Schedule Slider Date Format:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',
			$display_on_page,
			$page_section,
			$args
		);
		
		// Time format schedule slider
		$args = array(	'opt_name' => 'mstw_gs_options',
						'set_name' => 'slider_time_format',
						'set_default' => 'g:i A',
						'cdt' => false,
						);						
		add_settings_field(
			'slider_time_format',
			__( 'Schedule Slider Time Format:', 'mstw-loc-domain' ),
			'mstw_utl_time_format_ctrl',
			$display_on_page,
			$page_section,
			$args
		);
		
		//Custom date format for schedule slider
		$args = array(	'id' => 'custom_slider_time_format',
						'name'	=> 'mstw_gs_options[custom_slider_time_format]',
						'value'	=> $options['custom_slider_time_format'],
						'label'	=> __( 'Enter a PHP date() format string for a custom format. You probably should know what you are doing before selecting the custom option. (Default: "")', 'mstw-loc-domain' )
						);						
		add_settings_field(
			'custom_slider_time_format',
			__( 'Custom Schedule Slider Time Format:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',
			$display_on_page,
			$page_section,
			$args
		);

	}
	

	function mstw_gs_data_fields_setup( ) {
		// Data fields/columns -- show/hide and labels
		$display_on_page = 'mstw_gs_settings';
		$page_section = 'mstw_gs_fields_columns_settings';
		
		$options = get_option( 'mstw_gs_options' );
		
		add_settings_section(
			$page_section,  //id attribute of tags
			__( 'Data Field and Table Column Settings', 'mstw-loc-domain' ),	//title of the section
			'mstw_gs_data_fields_inst',		//callback to fill section with desired output - should echo
			$display_on_page				//menu page slug on which to display
		);
			
		//time/result, media
		
		// Show/hide DATE column
		$args = array( 	'id' => 'show_date',
						'name'	=> 'mstw_gs_options[show_date]',
						'value'	=> $options['show_date'],
						'label'	=> __( 'Show or hide the Date field/column. (Default: Show)', 'mstw-loc-domain' )
						//'label' => 'show_number: ' . $options['show_number'] . '::'
						);						
		add_settings_field(
			'gs_show_date',
			__( 'Show Date Column:', 'mstw-loc-domain' ),
			'mstw_utl_show_hide_ctrl',
			$display_on_page,
			$page_section,
			$args
		);	
		
			
		// DATE field/column label
		$args = array( 	'id' => 'date_label',
						'name'	=> 'mstw_gs_options[date_label]',
						'value'	=> $options['date_label'],
						'label'	=> __( 'Set label for date data field or column. (Default: "Date")', 'mstw-loc-domain' )
						//'label' => 'number_label: ' . $options['number_label'] . '::'
						);						
		add_settings_field(
			'gs_date_label',
			__( 'Date Column Label:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',
			$display_on_page,
			$page_section,
			$args
		);
		
		// OPPONENT field/column must be shown
		
		// OPPONENT field/column label
		$args = array( 	'id' => 'opponent_label',
						'name'	=> 'mstw_gs_options[opponent_label]',
						'value'	=> $options['opponent_label'],
						'label'	=> __( 'Set label for opponent data field or column. (Default: "Opponent") NOTE: THE OPPONENT FIELD MUST  BE SHOWN.', 'mstw-loc-domain' )
						//'label' => 'number_label: ' . $options['number_label'] . '::'
						);						
		add_settings_field(
			'gs_opponent_label',
			__( 'Opponent Column Label:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',
			$display_on_page,
			$page_section,
			$args
		);
		
		// Show/hide LOCATION column
		$args = array( 	'id' => 'show_location',
						'name'	=> 'mstw_gs_options[show_location]',
						'value'	=> $options['show_location'],
						'label'	=> __( 'Show or hide the Location field/column. (Default: Show)', 'mstw-loc-domain' )
						//'label' => 'show_number: ' . $options['show_number'] . '::'
						);						
		add_settings_field(
			'gs_show_location',
			__( 'Show Location Column:', 'mstw-loc-domain' ),
			'mstw_utl_show_hide_ctrl',
			$display_on_page,
			$page_section,
			$args
		);	
		
			
		// LOCATION field/column label
		$args = array( 	'id' => 'location_label',
						'name'	=> 'mstw_gs_options[location_label]',
						'value'	=> $options['location_label'],
						'label'	=> __( 'Set label for location data field or column. (Default: "Location")', 'mstw-loc-domain' )
						//'label' => 'number_label: ' . $options['number_label'] . '::'
						);						
		add_settings_field(
			'gs_location_label',
			__( 'Location Column Label:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',
			$display_on_page,
			$page_section,
			$args
		);		
		
		// Show/hide TIME/RESULT column
		$args = array( 	'id' => 'show_time',
						'name'	=> 'mstw_gs_options[show_time]',
						'value'	=> $options['show_time'],
						'label'	=> __( 'Show or hide the Time/Result field or column. (Default: Show)', 'mstw-loc-domain' )
						//'label' => 'show_number: ' . $options['show_number'] . '::'
						);						
		add_settings_field(
			'gs_show_time',
			__( 'Show Time Column:', 'mstw-loc-domain' ),
			'mstw_utl_show_hide_ctrl',
			$display_on_page,
			$page_section,
			$args
		);	
		
			
		// TIME/RESULT field/column label
		$args = array( 	'id' => 'time_label',
						'name'	=> 'mstw_gs_options[time_label]',
						'value'	=> $options['time_label'],
						'label'	=> __( 'Set label for Time/Result data field or column. (Default: "Time/Result")', 'mstw-loc-domain' )
						//'label' => 'number_label: ' . $options['number_label'] . '::'
						);						
		add_settings_field(
			'gs_time_label',
			__( 'Time/Result Column Label:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',
			$display_on_page,
			$page_section,
			$args
		);

		// Show/hide MEDIA column
		$args = array( 	'options' => array( 'Hide' => 0,
											'Show 1' => 1,
											'Show 2' => 2,
											'Show 3' => 3,
											),
						'id' => 'show_media',
						'name'	=> 'mstw_gs_options[show_media]',
						'value'	=> $options['show_media'],
						'label'	=> __( 'Show a number of media fields (1-3) or hide the Media field or column. (Default: Show all 3)', 'mstw-loc-domain' )
						);						
		add_settings_field(
			'gs_show_media',
			__( 'Show Media Column:', 'mstw-loc-domain' ),
			'mstw_utl_select_option_ctrl', //'mstw_utl_show_hide_ctrl',
			$display_on_page,
			$page_section,
			$args
		);	
		
		// MEDIA field/column label
		$args = array( 	'id' => 'media_label',
						'name'	=> 'mstw_gs_options[media_label]',
						'value'	=> $options['media_label'],
						'label'	=> __( 'Set label for Media data field or column. (Default: "Time/Result")', 'mstw-loc-domain' )
						);						
		add_settings_field(
			'gs_media_label',
			__( 'Media Column Label:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',
			$display_on_page,
			$page_section,
			$args
		);				
	
	} //End of data_fields_setup()
	
// ----------------------------------------------------------------	
// 	Data Fields Section Instructions	
// ----------------------------------------------------------------	
	function mstw_gs_data_fields_inst( ) {
		echo '<p>' . __( 'Enter the default settings for Schedule Table data fields and columns. These settings will apply to the [shortcode] schedules, where they can be overridden by [shortcode] arguments.', 'mstw-loc-domain' ) .  '</p>';
		echo '<p>' . __('NOTE: THE OPPONENT FIELD MUST BE SHOWN.', 'mstw-loc-domain' ) . '</p>';
	} //End of data_fields_inst()

// ----------------------------------------------------------------	
// 	Date-time format section instructions and controls	
// ----------------------------------------------------------------	

	function mstw_gs_date_time_inst( ) {
		echo '<p>' . __( 'Enter the date-time formats for your shortcodes and widgets. ', 'mstw-loc-domain' ) . '</p>';
		echo '<p>' . __( "NOTE that if 'Custom' is selected as the format a valid PHP date() format string must be entered in the corresponding format text field.", 'mstw-loc-domain' ) . '</p>';
	}
	
// ----------------------------------------------------------------	
//	Validate user input (we want text only)
 
	function mstw_gs_validate_options( $input ) {
		// Create our array for storing the validated options
		$output = array();
		
		// Pull the previous (last good) options
		$options = get_option( 'mstw_gs_options' );
		
		// Loop through each of the incoming options
		foreach( $input as $key => $value ) {
			// Check to see if the current option has a value. If so, process it.
			if( isset( $input[$key] ) ) {
				switch ( $key ) {
					// add the hex colors
					case 'gs_table_head_text_color':
					case 'gs_table_head_bkgd_color':
					case 'gs_table_title_text_color':
					case 'gs_table_links_color':
					case 'gs_table_even_row_color':
					case 'gs_table_even_row_bkgd':
					case 'gs_table_odd_row_color':
					case 'gs_table_odd_row_bkgd':
					case 'sp_main_bkgd_color':
					case 'sp_main_text_color':
						
						// validate the color for proper hex format
						//$sanitized_color = mstw_utl_sanitize_hex_color( $input[$key] ); // {mstw_sanitize_hex_color( $input[$key] );
						$sanitized_color = $input[$key];
						//$sanitized_color = $input[$key];
						// decide what to do - save new setting 
						// or display error & revert to last setting
						if ( isset( $sanitized_color ) ) {
							// blank input is valid
							$output[$key] = $sanitized_color;
						}
						else  {
							// there's an error. Reset to the last stored value
							$output[$key] = $options[$key];
							// add error message
							add_settings_error( 'mstw_gs_' . $key,
												'mstw_gs_hex_color_error',
												'Invalid hex color entered in: ' . $key,
												'error');
						}
						break;
					
					default:
						// There should not be user/accidental errors in these fields
						//case 'gs_hide_media':
						$output[$key] = sanitize_text_field( $input[$key] );
						//$output[$key] = $input[$key];
						break;
					
				} // end switch
			} // end if
		} // end foreach
		
		// Return the array processing any additional functions filtered by this action
		return apply_filters( 'sandbox_theme_validate_input_examples', $output, $input );
		//return $output;
	}	
	
	function mstw_gs_admin_notices() {
		settings_errors( );
	}
	add_action( 'admin_notices', 'mstw_gs_admin_notices' );


// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 	CSV Game Schedule Importer Class
//		- Modified from CSVImporter by ???
//		- All rights flow down.
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
class MSTW_GS_ImporterPlugin {
    var $defaults = array(
        'csv_post_title'      => null,
        'csv_post_post'       => null,
        'csv_post_type'       => null,
        'csv_post_excerpt'    => null,
        'csv_post_date'       => null,
        'csv_post_tags'       => null,
        'csv_post_categories' => null,
        'csv_post_author'     => null,
        'csv_post_slug'       => null,
        'csv_post_parent'     => 0,
    );

    var $log = array();

    /**
     * Determine value of option $name from database, $default value or $params,
     * save it to the db if needed and return it.*/
     
    function process_option( $name, $default, $params ) {
        if ( array_key_exists( $name, $params ) ) {
            $value = stripslashes( $params[$name] );
        } elseif ( array_key_exists( '_'.$name, $params ) ) {
            // unchecked checkbox value
            $value = stripslashes( $params['_'.$name] );
        } else {
            $value = null;
        }
        $stored_value = get_option( $name );
        if ( $value == null ) {
            if ($stored_value === false) {
                if (is_callable($default) &&
                    method_exists($default[0], $default[1])) {
                    $value = call_user_func($default);
                } else {
                    $value = $default;
                }
                add_option($name, $value);
            } else {
                $value = $stored_value;
            }
        } else {
            if ($stored_value === false) {
                add_option($name, $value);
            } elseif ($stored_value != $value) {
                update_option($name, $value);
            }
        }
        return $value;
    } //End function process_option()

    /*-------------------------------------------------------------
     * Builds the user interface for CSV Import screen
     *-----------------------------------------------------------*/
	function form( ) {
        
        $opt_sched_id = $this->process_option( 'csv_importer_sched_id', 0, $_POST );

        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            $this->post(compact('opt_draft', 'opt_sched_id'));
        }

        // form HTML {{{
		?>

		<div class="wrap">
			<?php echo get_screen_icon(); ?>
			<h2>Import CSV</h2>
			<form class="add:the-list: validate" method="post" enctype="multipart/form-data">
				<!-- Enter the schedule ID via text ... for now -->
				<table>				
					<tr>  <!-- Team ID input field -->
						<td><label for="opt_sched_id">Select a team/schedule (ID) to input:</label></td>
						<td><input size="20" name="csv_importer_sched_id" id="opt_sched_id" type="text" value="<?php echo esc_attr( $opt_sched_id ); ?>"/></td>
						<td><strong>Use an existing team ID or a new one. Team ID will be created if it does not exist.</strong></td>
					</tr>
					<tr>  <!-- CSV file selection field -->
						<td><label for="csv_import">Upload file:</label></td>
						<td><input name="csv_import" id="csv_import" type="file" value="" aria-required="true" /></td>
					</tr>
					<tr> <!-- Submit button -->
					<td colspan="2" class="submit"><input type="submit" class="button" name="submit" value="Import" /></td>
					</tr>
				</table>
			</form>
		</div><!-- end wrap -->
		<!-- end of form HTML -->
	<?php
    } //End of function form()

    
	/*-------------------------------------------------------------
	 *	Print Message Log
	 *-----------------------------------------------------------*/
	function print_messages() {
        if (!empty($this->log)) {

        // messages HTML {{{
?>

<div class="wrap">
    <?php if (!empty($this->log['error'])): ?>

    <div class="error">

        <?php foreach ($this->log['error'] as $error): ?>
            <p><?php echo $error; ?></p>
        <?php endforeach; ?>

    </div>

    <?php endif; ?>

    <?php if (!empty($this->log['notice'])): ?>

    <div class="updated fade">

        <?php foreach ($this->log['notice'] as $notice): ?>
            <p><?php echo $notice; ?></p>
        <?php endforeach; ?>

    </div>

    <?php endif; ?>
</div><!-- end wrap -->

<?php
        // end messages HTML }}}

            $this->log = array();
        }
    } //End function print_messages()

    /*-------------------------------------------------------------
     * Handle POST submission
     *-----------------------------------------------------------*/
    function post( $options ) {
	
		extract( $options );
		
		// Check that a team has been selected
		//echo '<p>$opt_sched_id(ID): ' . $opt_sched_id;
		if ( !isset( $opt_sched_id ) || trim( $opt_sched_id )==='' ) {
			$this->log['error'][] = 'Please specify a team ID. Exiting.';
            $this->print_messages();
            return;
		} 
		// Check that a file has been uploaded
        if ( empty($_FILES['csv_import']['tmp_name']) ) {
            $this->log['error'][] = 'Please select a file. Exiting.';
            $this->print_messages();
            return;
        }

        echo '<p> Loading DataSource ... </p>';
		if ( !class_exists( 'File_CSV_DataSource' ) ) {
			require_once 'DataSource.php';
			echo '<p> Done. </p>';
		} else {
			echo '<p> Already loaded. </p>';
		}

        $time_start = microtime( true );
        $csv = new File_CSV_DataSource;
        $file = $_FILES['csv_import']['tmp_name'];
        $this->stripBOM( $file );

        if ( !$csv->load( $file ) ) {
            $this->log['error'][] = 'Failed to load file, aborting.';
            $this->print_messages( );
            return;
        }

        // pad shorter rows with empty values
        $csv->symmetrize();

        // WordPress sets the correct timezone for date functions 
        // somewhere in the bowels of wp_insert_post(). We need 
        // strtotime() to return correct time before the call to
		// wp_insert_post().
        // mstw_set_wp_default_timezone( ); 

        $skipped = 0;
        $imported = 0;
        $comments = 0;
        foreach ( $csv->connect( ) as $csv_data ) {
			// First try to create the post from the row
            if ( $post_id = $this->create_post( $csv_data, $options, $imported+1 )) {
                $imported++;
				//Insert the custom fields, which is most everything
                $this->create_custom_fields( $post_id, $csv_data );
            } else {
                $skipped++;
            }
        }

        if ( file_exists($file) ) {
            @unlink( $file );
        }

        $exec_time = microtime( true ) - $time_start;

        if ($skipped) {
            $this->log['notice'][] = "<b>Skipped {$skipped} posts (most likely due to empty title, body and excerpt).</b>";
        }
        $this->log['notice'][] = sprintf("<b>Imported {$imported} posts to {$term->slug} in %.2f seconds.</b>", $exec_time);
        $this->print_messages();
    }
	
	/*-------------------------------------------------------------
	 *	Build a post from a row of CSV data
	 *-----------------------------------------------------------*/
    function create_post( $data, $options, $cntr ) {
        extract( $options );

        $data = array_merge( $this->defaults, $data );

		// The post type is hardwired for this plugin's custom post type
		$type = 'scheduled_games';
		
        $valid_type = ( function_exists( 'post_type_exists' ) &&
            post_type_exists( $type )) || in_array( $type, array('post', 'page' ));

        if ( !$valid_type ) {
            $this->log['error']["type-{$type}"] = sprintf(
                'Unknown post type "%s".', $type );
        }
		
		// Temp title will be Schedule_ID-game_nbr Opponent
		// E.g., 2013Cal-03 Ohio State
		// First get the schedule ID, and make sure it's not empty
		echo '<p>Schedule ID: ' . $opt_sched_id;
		if ( $opt_sched_id == '' ) {
			$this->log['error'][] = "Unknown team. Are you sure you specified one?";
		}
		$temp_title = $opt_sched_id . '-' . sprintf( "%1$02d", $cntr );
		
		// Next get the opponent; if empty fill in a default
		$opponent = $data[__('Opponent', 'mstw-loc-domain')];
		//echo '<p>Opponent: ' . $opponent . '</p>';
		if ( trim( $opponent == '' ) ) {
			$opponent = __( "Unknown", "mstw-loc-domain" );
		}
		$temp_title .= " " . $opponent;
		
		// Create a slug from the newly constructed title
		$temp_slug = sanitize_title( $temp_title );
		echo ' Title: ' . $temp_title . '</p>';
		
		// Build the (mostly empty) post
        $new_post = array(
            'post_title'   => convert_chars( $temp_title ),
            'post_content' => '', //wpautop(convert_chars($data['Bio'])),
            'post_status'  => 'publish',
            'post_type'    => $type,
            'post_name'    => $temp_slug,
        );
        // create it
        $post_id = wp_insert_post( $new_post );
		
		if ( $post_id ) {
			//$term = get_term_by( 'id', $opt_sched_id, 'teams' );
			//wp_set_object_terms( $id, $term->slug, 'teams');
			update_post_meta( $post_id, '_mstw_gs_sched_id', $opt_sched_id );
		} 
		
        return $post_id;
    } //End function create_post()

	
	/*-------------------------------------------------------------
	 *	Add the fields from a row of CSV data to a newly created post
	 *-----------------------------------------------------------*/
    function create_custom_fields( $post_id, $data ) {
	
		// Going to try to combine date and time fields when possible
		$game_date_stamp = 0;
		$game_time_stamp = 0;
		
        foreach ( $data as $k => $v ) {
            // anything that doesn't start with csv_ is a custom field
            if (!preg_match('/^csv_/', $k) && $v != '') {
				switch ( strtolower( $k ) ) {
					case __( "sched", "mstw-loc-domain" ):
					case __( "id", "mstw-loc-domain" ):
					case __( "schedule", "mstw-loc-domain" ):
					case __( "schedule id", "mstw-loc-domain" ):
						// Not using this; must be set on admin screen right now
						break;
						
					case __( "opponent", "mstw-loc-domain" ):
						$k = '_mstw_gs_opponent';
						break;
						
					case __( "opponent link", "mstw-loc-domain" ):
						$k = '_mstw_gs_opponent_link';
						break;
						
					case __( "date", "mstw-loc-domain" ):
					case __( "game date", "mstw-loc-domain" ):
						$k = '_mstw_gs_unix_dtg';
						//save the date string for later use with the time string
						$date_str = $v;
						
						//echo '<p>We found a date: ' . $date_str;
						
						// Need to convert to a UNIX dtg stamp and store
						$v = strtotime( $date_str );
					    //echo '<p>strtotime( ' . $date_str .  ' ) = ' . $v . '</p>';
						if ( $v <= 0 or $v === false ) { //bad date string
							$v = time( );	// default time to now (close enough)
							$date_str = date( 'Y-m-d' ); // default string to today
							//echo '<p>DATE ERROR: ' . $date_str . '</p>';
						}
						//echo " And now it's: " . $date_str . '</p>';
						
						// Now need to break out and store game year, day, month
						// _mstw_gs_sched_year, _mstw_gs_game_day, _mstw_gs_game_month
						//update_post_meta( $post_id, '_mstw_gs_sched_year', 
						//			date( 'Y', $v ) ); //4-digit year
						//update_post_meta( $post_id, '_mstw_gs_game_month', 
						//			date( 'M', $v ) ); //3-letter abbreviation for month
						//update_post_meta( $post_id, '_mstw_gs_game_day', 
						//			date( 'd', $v ) ); //2-digit day with leading zero
									
						
						break;
						
					case __( "time", "mstw-loc-domain" ):
					case __( "game time", "mstw-loc-domain" ):
						// Try to combine with date, convert to a UNIX dtg stamp, and store
						
						$k = '_mstw_gs_unix_dtg';   // DB field: UNIX DTG timestamp
						$time_str = $v;   		 	// basic time string
						
						//echo '<p>We found a time: ' . $time_str . '</p>';
						
						// Will need the UNIX game date, which MUST COME BEFORE THE TIME
						$unix_date = get_post_meta( $post_id, '_mstw_gs_unix_dtg', true );
						
						//echo '<p>And we pulled the date: ' . date( 'Y-m-d', $unix_date ) . ' (' . $unix_date . ')';
						
						if ( $unix_date == '' ) {
							// Didn't put the date before the time, so we're going with today's date
							$date_str = date( 'Y-m-d' );
							$unix_date = strtotime( $date_str );
						} else {
							// This should be what happens
							$date_str = date( 'Y-m-d', $unix_date );
						}
						//echo ' Then we changed it to: ' . $date_str . '</p>';
						
						// First check for TBD game time
						if ( $v == "TBD" or $v == "TBA" or $v == "T.B.D." or $v == "T.B.A." ) {
							// default the UNIX DTG to the date, which should be set  
							// update_post_meta( $post_id, '_mstw_gs_unix_dtg', $unix_date );
							$k = '_mstw_gs_game_time_tba';   // DB field: UNIX DTG timestamp
						}
						else { 
							//otherwise build the game time from the date and time fields
							//$dtg_str = $date_str . ' ' . $time_str;
							// update_post_meta( $post_id, '_mstw_gs_unix_dtg', strtotime( $dtg_str ) );
							$v = strtotime( $date_str . ' ' . $time_str );
							$k = '_mstw_gs_unix_dtg';   // DB field: UNIX DTG timestamp	
						}
						
						//echo '<p>DTG String: ' . $date_str . ' ' . $time_str . '</p>';
						break;
						
					case __( "home", "mstw-loc-domain" ):
					case __( "home game", "mstw-loc-domain" ):
						$k = '_mstw_gs_home_game';
						//echo '<p> Home Game: ' . $v . '</p>';
						if ( $v == "1" or $v == "Home" or $v == 'home' ) {
							$v ="home";
						}
						else {
							$v == "0";
						}
						break;
						
					case __( "location", "mstw-loc-domain" ):
					case __( "game location", "mstw-loc-domain" ):
						$k = '_mstw_gs_location';
						break;
						
					case __( "location link", "mstw-loc-domain" ):
					case __( "game location link", "mstw-loc-domain" ):
						$k = '_mstw_gs_location_link';
						break;
						
					case __( "result", "mstw-loc-domain" ):
					case __( "final score", "mstw-loc-domain" ):
						$k = '_mstw_gs_game_result';
						break;
						
					case __( "media label 1", "mstw-loc-domain" ):
						$k = '_mstw_gs_media_label_1';
						break;
						
					case __( "media url 1", "mstw-loc-domain" ):
						$k = '_mstw_gs_media_url_1';
						break;
						
					case __( "media label 2", "mstw-loc-domain" ):
						$k = '_mstw_gs_media_label_2';
						break;
						
					case __( "media url 2", "mstw-loc-domain" ):
						$k = '_mstw_gs_media_url_2';
						break;
					case __( "media label 3", "mstw-loc-domain" ):
						$k = '_mstw_gs_media_label_3';
						break;
						
					case __( "media url 3", "mstw-loc-domain" ):
						$k = '_mstw_gs_media_url_3';
						break;
						
				}
				
				// debug stuff
				// if ( $k == '_mstw_gs_unix_dtg' or $k == '_mstw_gs_game_time_tba' ) {
				//  	echo '<p>ID: ' . $post_id . ' Key: ' . $k . ' Value: ' . $v ;
				//}
				
				$ret = update_post_meta( $post_id, $k, $v );
            }
        }
    } //End of function create_custom_fields()

    /*-------------------------------------------------------------
	 *	Add the fields from a row of CSV data to a newly created post
	 *-----------------------------------------------------------*/
    function stripBOM($fname) {
        $res = fopen($fname, 'rb');
        if (false !== $res) {
            $bytes = fread($res, 3);
            if ($bytes == pack('CCC', 0xef, 0xbb, 0xbf)) {
                $this->log['notice'][] = 'Getting rid of byte order mark...';
                fclose($res);

                $contents = file_get_contents($fname);
                if (false === $contents) {
                    trigger_error('Failed to get file contents.', E_USER_WARNING);
                }
                $contents = substr($contents, 3);
                $success = file_put_contents($fname, $contents);
                if (false === $success) {
                    trigger_error('Failed to put file contents.', E_USER_WARNING);
                }
            } else {
                fclose($res);
            }
        } else {
            $this->log['error'][] = 'Failed to open file, aborting.';
        }
    }
}
?>