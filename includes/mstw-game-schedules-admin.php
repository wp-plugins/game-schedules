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
	// Turn off error reporting
	//error_reporting(0);
	
	// ----------------------------------------------------------------
	// Prevents uninitialized string errors 
	//
	function mstw_gs_safe_ref( $array, $index ) {
		return ( isset( $array[$index] ) ? $array[$index] : '' );
	}
	
	// ----------------------------------------------------------------
	// Load the admin utils if necessary; die if file can't be loaded
	//
	if ( is_admin( ) and !function_exists( 'mstw_gs_admin_utils_loaded' ) ) {
		require_once 'mstw-gs-admin-utils.php';
	}
	
	// ----------------------------------------------------------------
	// Load the the CSV Importer; don't die if file can't be loaded
	//
	if( is_admin( ) ) {
		include_once 'mstw-gs-csv-import-class.php';
	}
	
	// ----------------------------------------------------------------	
	// Add filter to set the capabilities required to access custom post types. 
	// add_filter( 'mstw_gs_user_capability', 'mstw_gs_set_user_capability', 1, 2 );
	//		1 is the priority, we want this to fire before developers
	//		2 is the number of arguments passed to callback
	
	function mstw_gs_set_user_capability( $capability, $filter4 ) {
		// Only allow the admin role to access the display settings menu 
		if ( $filter4 == 'display_settings_menu_item' )
			return 'manage_options';
		// For everthing else, just pass the capability back
		else 	
			return $capability;  
	}
	
	// ----------------------------------------------------------------	
	// Add styles and scripts for the color picker. 
	//
	add_action( 'admin_enqueue_scripts', 'mstw_gs_enqueue_javascript' );
	
	function mstw_gs_enqueue_javascript( $hook_suffix ) {
		//enqueue the color-picker script & stylesheet
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'mstw-gs-color-picker', plugins_url( 'game-schedules/js/gs-color-settings.js' ), array( 'wp-color-picker' ), false, true );
		
		//enqueue the datepicker script & stylesheet
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
	}
	
	// ----------------------------------------------------------------
	// Add the custom MSTW icon to CPT pages
	//
	add_action('admin_head', 'mstw_gs_custom_css');
	
	function mstw_gs_custom_css() { ?>
		<style type="text/css">
			#icon-mstw-gs-main-menu.icon32 {
				background: url(<?php echo plugins_url( '/game-schedules/images/mstw-logo-32x32.png', 'game-schedules' );?>) transparent no-repeat;
			}
			#menu-posts-scheduled_game .wp-menu-image {
				background-image: url(<?php echo plugins_url( '/game-schedules/images/mstw-admin-menu-icon.png', 'game-schedules' );?>) no-repeat 6px -17px !important;
			}
			
			#icon-scheduled_game.icon32 {
				background: url(<?php echo plugins_url( '/game-schedules/images/mstw-logo-32x32.png', 'game-schedules' );?>) transparent no-repeat;
			}
			#icon-mstw_gs_teams.icon32 {
				background: url(<?php echo plugins_url( '/game-schedules/images/mstw-logo-32x32.png', 'game-schedules' );?>) transparent no-repeat;
			}
			#icon-mstw_gs_schedules.icon32 {
				background: url(<?php echo plugins_url( '/game-schedules/images/mstw-logo-32x32.png', 'game-schedules' );?>) transparent no-repeat;
			}
			
			
			
			#icon-edit.icon32-posts-scheduled_games {
				background: url(<?php echo plugins_url( '/game-schedules/images/mstw-logo-32x32.png', 'game-schedules' );?>) transparent no-repeat;
			}
			#icon-edit.icon32-posts-mstw_gs_teams {
				background: url(<?php echo plugins_url( '/game-schedules/images/mstw-logo-32x32.png', 'game-schedules' );?>) transparent no-repeat;
			}
			#icon-edit.icon32-posts-mstw_gs_schedules {
				background: url(<?php echo plugins_url( '/game-schedules/images/mstw-logo-32x32.png', 'game-schedules' );?>) transparent no-repeat;
			}
			
		</style>
	<?php }
	
	// ----------------------------------------------------------------
	// Remove Quick Edit Menu	
	add_filter( 'post_row_actions', 'mstw_gs_remove_quick_edit', 10, 2 );

	function mstw_gs_remove_quick_edit( $actions, $post ) {
		if ( $post->post_type == 'scheduled_games' or 
			$post->post_type == 'mstw_gs_teams' or 
			$post->post_type == 'mstw_gs_schedules' ) {
			
				unset( $actions['inline hide-if-no-js'] );
		}
		 
		return $actions;
	}
	
	// ----------------------------------------------------------------
	// Remove the Bulk Actions pull-down - Edit only
	//
	if (is_admin( ) ) {
		add_filter( 'bulk_actions-edit-scheduled_games', 'mstw_gs_remove_bulk_actions' );
		add_filter( 'bulk_actions-edit-mstw_gs_teams', 'mstw_gs_remove_bulk_actions' );
		add_filter( 'bulk_actions-edit-mstw_gs_schedules', 'mstw_gs_remove_bulk_actions' );
	}
	
    function mstw_gs_remove_bulk_actions( $actions ) {
        unset( $actions['edit'] );
        return $actions;
    }
	
	// ----------------------------------------------------------------
	// Remove the "View Post" option
	//
	if ( is_admin( ) ) {
		add_filter( 'post_row_actions', 'mstw_gs_remove_the_view', 10, 2 );
	}			

	function mstw_gs_remove_the_view( $actions ) {
		global $post;
		
		if( $post->post_type == 'scheduled_games' or 
			$post->post_type == 'mstw_gs_teams' or 
			$post->post_type == 'mstw_gs_schedules' ) {
			
			unset( $actions['view'] );
			
		}
		
		return $actions;
	}
	
	// ----------------------------------------------------------------
	// Create the meta box for the Game Schedules custom post type
	//
	add_action( 'add_meta_boxes', 'mstw_gs_add_meta' );

	function mstw_gs_add_meta () {
		add_meta_box('mstw-gs-meta', 'Game', 'mstw_gs_create_games_ui', 
						'scheduled_games', 'normal', 'high' );
		
		add_meta_box('mstw-gs-meta', 'Team', 'mstw_gs_create_teams_ui', 
						'mstw_gs_teams', 'normal', 'high' );
						
		add_meta_box('mstw-gs-meta', 'Schedule', 'mstw_gs_create_schedules_ui', 
						'mstw_gs_schedules', 'normal', 'high' );
	}
	
	// ----------------------------------------------------------------
	// Create admin page tabs
	//
	function mstw_gs_admin_tabs( $current_tab = 'fields-columns-tab' ) {
		$tabs = array( 	'files-columns-tab' => 'Fields/Columns', 
						'date-time-tab' => 'Date/Time Formats', 
						'colors-tab' => 'Colors', 
						);
		//echo '<div id="icon-themes" class="icon32"><br></div>';
		echo '<h2 class="nav-tab-wrapper">';
		foreach( $tabs as $tab => $name ){
			$class = ( $tab == $current_tab ) ? ' nav-tab-active' : '';
			echo "<a class='nav-tab$class' href='edit.php?post_type=scheduled_games&page=mstw_gs_settings&tab=$tab'>$name</a>";
			
		}
		
		echo '</h2>';
	}
	
	// ----------------------------------------------------------------
	// Add a filter the All Games screen based on the Schedule ID
	//
	add_action( 'restrict_manage_posts','mstw_restrict_games_by_schedID' );
	
	function mstw_restrict_games_by_schedID( ) {
		global $wpdb;
		global $typenow;
		
		//echo '<p> In mstw_restrict_games_by_schedID() $typenow= ' . $typenow . '</p>';
		
		if( isset( $typenow ) && $typenow != "" && $typenow == "scheduled_games" ) {
			$meta_values = $wpdb->get_col("
				SELECT DISTINCT meta_value
				FROM ". $wpdb->postmeta ."
				WHERE meta_key = '_mstw_gs_sched_id'
				ORDER BY meta_value
			");
			//echo "</pre>" . print_r( $wpdb->postmeta ) . "</pre>";
			//return;
			?>
			<select name="_mstw_gs_sched_id" id="issue">
				<option value="">Show All Schedules</option>
				
				<?php foreach ($meta_values as $meta_value) { ?>
					<option value="<?php echo esc_attr( $meta_value ); ?>" <?php if( isset($_GET['_mstw_gs_sched_id']) && !empty($_GET['_mstw_gs_sched_id']) ) selected($_GET['_mstw_gs_sched_id'], $meta_value ); ?>>
					<?php
					  echo $meta_value;
					?>
					</option>
				<?php } ?>
			</select>
		<?php
		}
	}  //End of mstw_restrict_games_by_schedID()

	// ----------------------------------------------------------------
	// Add a filter to the where clause in mstw_restrict_games_by_schedID()
	//
	add_filter( 'posts_where' , 'mstw_gs_posts_where_metavalue' );
	
	function mstw_gs_posts_where_metavalue( $where ) {
		if( is_admin( ) ) {
			global $wpdb;       
			if ( isset( $_GET['_mstw_gs_sched_id'] ) && !empty( $_GET['_mstw_gs_sched_id'] ) ) {
				$meta_number = $_GET['_mstw_gs_sched_id'];
				$where .= " AND ID IN (SELECT post_id FROM " . $wpdb->postmeta . " WHERE meta_key='_mstw_gs_sched_id' AND meta_value='$meta_number' )";
			}
		}   
		return $where;
	}
	
	// ----------------------------------------------------------------
	// Add a filter to sort all games table on the schedule column
	//
	add_filter("manage_edit-scheduled_games_sortable_columns", 'mstw_gs_columns_sort');
	
	function mstw_gs_columns_sort( $columns ) {
		$custom = array(
			'sched_id' 	=> 'sched_id'
		);
		return wp_parse_args( $custom, $columns );
	}

	// ----------------------------------------------------------------
	// Sort show all games by schedule. See:
	// http://scribu.net/wordpress/custom-sortable-columns.html#comment-4732
	//
	add_filter( 'request', 'mstw_gs_schedule_column_order' );
	
	function mstw_gs_schedule_column_order( $vars ) {
		if ( isset( $vars['orderby'] ) && 'sched_id' == $vars['orderby'] ) {
			$vars = array_merge( $vars, array(
									'meta_key' => '_mstw_gs_sched_id',
									//'orderby' => 'meta_value_num', // does not work
									'orderby' => 'meta_value'
									//'order' => 'asc' // don't use this; blocks toggle UI
									) 
								);
		}
		
		return $vars;
		
	} //End mstw_gs_schedule_column_order( )
	
	// ----------------------------------------------------------------
	// Create Team data entry page
	//
	function mstw_gs_create_teams_ui( $post ) {
		// pull the team data from the UI
		$team_full_name = get_post_meta( $post->ID, 'team_full_name', true );
		$team_short_name = get_post_meta( $post->ID, 'team_short_name', true );
		$team_full_mascot = get_post_meta( $post->ID, 'team_full_mascot', true );
		$team_short_mascot = get_post_meta( $post->ID, 'team_short_mascot', true );
		$team_home_venue = get_post_meta( $post->ID, 'team_home_venue', true );
		$team_link = get_post_meta( $post->ID, 'team_link', true );
		$team_logo = get_post_meta( $post->ID, 'team_logo', true );
		$team_alt_logo = get_post_meta( $post->ID, 'team_alt_logo', true );
		
		$std_length = 128;
		$std_size = 30;
		
		$admin_fields = array(  'team_full_name' => array (
									'type'	=> 'text',
									'value' => $team_full_name,
									'label' => __( 'Team Full Name:', 'mstw-loc-domain' ),
									'maxlength' => $std_length,
									'size' => $std_size,
									'notes' => 'E.g., "San Francisco" or "California"',
									),
								'team_short_name' => array (
									'type'	=> 'text',
									'value' => $team_short_name,
									'label' => 'Team Short Name:',
									'maxlength' => $std_length,
									'size' => $std_size,
									'notes' => 'E.g., "SF" or "Cal". If not specified, full name will be used in it\'s place.',
									),
								'team_full_mascot' => array (
									'type'	=> 'text',
									'value' => $team_full_mascot,
									'label' => 'Team Full Mascot:',
									'maxlength' => $std_length,
									'size' => $std_size,
									'notes' => 'E.g., "49ers" or "Golden Bears"',
									),
								'team_short_mascot' => array (
									'type'	=> 'text',
									'value' => $team_short_mascot,
									'label' => 'Team Short Mascot:',
									'maxlength' => $std_length,
									'size' => $std_size,
									'notes' => 'E.g., "Niners" or "Bears". If not specified, full mascot name will be used in it\'s place.',
								),
								'team_link' => array (
									'type'	=> 'text',
									'value' => $team_link,
									'label' => 'Team Link:',
									'maxlength' => 256,
									'size' => $std_size,
									'notes' => 'E.g., "http://49ers.com" or "http://calbears.com"',
									),
								'team_logo' => array (
									'type'	=> 'text',
									'value' => $team_logo,
									'label' => 'Team Table Logo:',
									'maxlength' => 256,
									'size' => $std_size,
									'notes' => 'Provide full path to file (uploaded to media library, for example). Recommended size 41x28px.',
									),
								'team_alt_logo' => array (
									'type'	=> 'text',
									'value' => $team_alt_logo,
									'label' => 'Team Slider Logo:',
									'maxlength' => 256,
									'size' => $std_size,
									'notes' => 'Provide full path to file (uploaded to media library, for example). Recommended size 125x125px.',
									),
							);
							
		?> 
		<table class="form-table">
		
		<?php mstw_gs_build_admin_edit_screen( $admin_fields ); 
		
		if( is_plugin_active('game-locations/mstw-game-locations.php') ) { 
			$plugin_active = 'Active';
			$locations = get_posts(array( 'numberposts' => -1,
							  'post_type' => 'game_locations',
							  'orderby' => 'title',
							  'order' => 'ASC' 
							));						
	
			if( $locations ) {
				echo '<tr valign="top">';
				echo '<th>Team Home Venue:</th>';
				echo "<td><select id='team_home_venue' name='team_home_venue'>";
				$selected = ( empty( $team_home_venue ) or $team_home_venue == -1 ) ? 'selected="selected"' : '';
				echo "<option value='-1' " . $selected . "> ---- </option>";
					
				foreach( $locations as $loc ) {
					$selected = ( $team_home_venue == $loc->ID ) ? 'selected="selected"' : '';
					echo "<option value='" . $loc->ID . "'" . $selected . ">" . get_the_title( $loc->ID ) . "</option>";
				}
				echo "</select>\n";     
				echo "<br><span class='description'>Note: this setting requires that the Game Locations plugin is activated.</span></td>";
				echo "</tr>";
				
			}
		} //End: if (is_plugin_active) 
		?>
		
		</table>
	<?php }
	
	
	// ----------------------------------------------------------------
	// Create Schedule data entry page
	//
	function mstw_gs_create_schedules_ui( $post ) {
		// pull the team data from the UI
		$schedule_id = get_post_meta( $post->ID, 'schedule_id', true );
		$schedule_team = get_post_meta( $post->ID, 'schedule_team', true );
		
		$std_length = 128;
		$std_size = 30;
		
		$admin_fields = array(  'schedule_id' => array (
									'type' 		=> 'text',
									'value' 	=> $schedule_id,
									'label' 	=> __( 'Unique Schedule ID:', 'mstw-loc-domain' ),
									'maxlength' => $std_length,
									'size' 		=> $std_size,
									'notes' 	=> 'Will be converted to WP \'slug\' format(128 character max) E.g., "2013 Varsity Football" will be converted to "2013-varsity-football"',
									),
							);
							
		
		echo "<table class='form-table'>\n";
		
		mstw_gs_build_admin_edit_screen( $admin_fields ); 
		
		$teams = get_posts(array( 'numberposts' => -1,
						  'post_type' => 'mstw_gs_teams',
						  'orderby' => 'title',
						  'order' => 'ASC' 
						));						
	
		if( $teams ) {
			echo "<tr valign='top'>\n";
			echo "<th>Schedule For:</th>\n";
			echo "<td><select id='schedule_team' name='schedule_team'>\n";
			
			$selected = ( empty( $schedule_team ) or $schedule_team == -1 ) ? 'selected="selected"' : '';
			echo "<option value='-1' " . $selected . "> ---- </option>\n";
				
			foreach( $teams as $team ) {
				$selected = ( $schedule_team == $team->ID ) ? 'selected="selected"' : '';
				echo "<option value='" . $team->ID . "'" . $selected . ">" . get_the_title( $team->ID ) . "</option>\n";
			}
			
			echo "</select>\n";
			echo "<br/><span class='description'>Selected team (from the Teams DB) will be the home team for this schedule.</span></td>\n";
			echo "</tr>\n";
			
		}
		?>
		
		</table>
	<?php 
	}
	
//----------------------------------------------------------------
//Convenience function to build admin UI data entry screens
//
	
	function mstw_gs_build_admin_edit_screen( $fields ) {
		
		foreach( $fields as $field=>$data ) {
			echo "<tr valign='top'> \n";
			echo "<th scope='row'><label for=" . $field . ">";
			echo $data['label'] . "</label></th>\n";
			
			$value = $data['value'];
			
			switch ( $data['type'] ) {
				case 'checkbox':
					echo "<td><input type='checkbox' name='$field' id='$field' value='$value' " . checked( $data['checked'], $value, false ) .  "/> \n"; 
					break;
				default:
					echo "<td><input type=" . $data['type'] . " maxlength=" . $data['maxlength'] . " size=" . $data['size'] . " name='" . $field ;
					echo "' id='" . $field . "' value='" . esc_attr( $data['value'] ) . "' />\n";
					break;
			}
			
			if( $data['notes'] != '' ) {
				echo "<br /><span class='description'>" . $data['notes'] . "</span></td>\n";
			}
			echo "</tr> \n";
		}
	}
	
// ----------------------------------------------------------------
// Creates the UI form for entering a Games in the Admin page
// Callback for: add_meta_box('mstw-gl-meta', 'Game', ... )
//

	function mstw_gs_create_games_ui( $post ) {
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
		
		$options = get_option( 'mstw_gs_options' );
							  				  
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
		
		//New for team custom post type
		$mstw_gs_opponent_team = get_post_meta( $post->ID, 'gs_opponent_team', true );
		
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
		
		$game_date_label = ( $options['date_label'] == '' ? __( 'Game Date', 'mstw-loc-domain' ) : $options['date_label'] );
		$game_time_label = ( $options['time_label'] == '' ? __( 'Game Time', 'mstw-loc-domain' ) : $options['time_label'] );
		$opponent_label = ( $options['opponent_label'] == '' ? __( 'Opponent', 'mstw-loc-domain' ) : $options['opponent_label'] );
		$media_label = ( $options['media_label'] == '' ? __( 'Media', 'mstw-loc-domain' ) : $options['media_label'] );
		
		?>
		
	   <table class="form-table">
	   
	   <?php
	   $admin_fields = array( 	'mstw_gs_sched_id' => array (
									'type' => 'text',
									'value' => $mstw_gs_sched_id,
									'label' => __( 'Schedule ID:', 'mstw-loc-domain' ),
									'maxlength' => 128,
									'size' => 30,
									'notes' => 'Will be converted to WP \'slug\' format. E.g., "2013 Varsity Football" will be converted to "2013-varsity-football". (128 character max)',
								),
								'gs_game_date' => array (
									'type' => 'text',
									'value' => date( 'Y-m-d', $mstw_gs_unix_dtg ),
									'label' => $game_date_label,
									'maxlength' => 128,
									'size' => 30,
									'notes' => '',
								),
							);
		
	
		mstw_gs_build_admin_edit_screen( $admin_fields );
		?>
		
		<!-- Game Time -->
		<?php
		$curr_hrs = date( 'H', (int)esc_attr( $mstw_gs_unix_dtg ) );
		$curr_mins = date( 'i', (int)esc_attr( $mstw_gs_unix_dtg ) );
		$curr_tba = $mstw_gs_game_time_tba;
		if ( $curr_tba == '' ) {
			$curr_tba = '---';
		}
		?>
		
		<tr valign="top">
			<th scope="row"><label for="game_time_hrs" ><?php echo $game_time_label . ' [hh:mm]:';?></label></th>
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
			<br/><span class='description'>If TBA is anything other than '---', then it is used for the game time whether or not a time is entered.</span></td>
			<!--<td><?php //echo '$curr_hrs:$curr_mins: ' . $curr_hrs .':' . $curr_mins; ?> </td>-->
		</tr>
		
		<!-- This is the new stuff for the MSTW teams CPT-->
		<?php mstw_gs_build_teams_input( $mstw_gs_opponent_team ); ?>
		
		<?php
		$admin_fields = array( 	'mstw_gs_opponent' => array (
									'type' => 'text',
									'value' => $mstw_gs_opponent,
									'label' => $opponent_label  . ":",
									'maxlength' => 128,
									'size' => 30,
									'notes' => 'Name of opponent (your choice of format).',
								),
								'mstw_gs_opponent_link' => array (
									'type' => 'text',
									'value' => $mstw_gs_opponent_link,
									'label' =>  $opponent_label . ' ' . __( 'Link:', 'mstw-loc-domain' ),
									'maxlength' => 256,
									'size' => 30,
									'notes' => 'Link to a website for the opponent (your choice, maybe the team website or school website.',
								),
								//
								//'mstw_gs_home_game' => array (
								//	'type' => 'checkbox',
								//	'value' => $mstw_gs_home_game,
								//	'checked' => 'home',
								//	'label' =>  __( 'Home Game?:', 'mstw-loc-domain' ),
									//'maxlength' => 256,
									//'size' => 30,
								//	'notes' => 'Check if game is a home game. Note: if using Teams DB, you can change the games location from the normal home venue by using the Location fields below.',
								//),
							);
		
	
		mstw_gs_build_admin_edit_screen( $admin_fields );
		?>
		
		<tr valign="top">
			<th scope="row"><label for="mstw_gs_home_game" >Home Game?</label></th>
			<td><input type="checkbox" name="mstw_gs_home_game" value="home" <?php checked( $mstw_gs_home_game, 'home', true )?> /></td>
		</tr>
		 
		<?php 
		$location_label = ( $options['location_label'] == '' ? __( 'Location', 'mstw-loc-domain' ) : $options['location_label'] );
			
		$plugin_active = 'Inactive';
		if( is_plugin_active('game-locations/mstw-game-locations.php') ) { 
			$plugin_active = 'Active';
			$locations = get_posts(array( 'numberposts' => -1,
							  'post_type' => 'game_locations',
							  'orderby' => 'title',
							  'order' => 'ASC' 
							));						
	
			if( $locations ) {
				echo '<tr valign="top">';
				echo "<th>" . __(' Select ', 'mstw-loc-domain' ) . $location_label . __( ' from Game Locations:', 'mstw-loc-domain' ) . "</th>";
				echo "<td><select id='mstw_gs_gl_location' name='mstw_gs_gl_location'>";
				// trying to add --- option
				echo "<option value=''>----</option>";
				
				foreach( $locations as $loc ) {
					$selected = ( $mstw_gs_gl_location == $loc->ID ) ? 'selected="selected"' : '';
					echo "<option value='" . $loc->ID . "'" . $selected . ">" . get_the_title( $loc->ID ) . "</option>";
				}
				echo "</select>\n";
				echo "<br/><span class='description'>Note: this setting requires that the Game Locations plugin is activated. It is preferred to using the custom location and link settings below.</span></td>\n";
				echo "</tr>\n";
				
			}
		} //End: if (is_plugin_active) 
		else {
			echo '<tr valign="top">';
			echo '<th scope="row">Game Locations Plugin:</th>';
			echo "<td>Please activate the <a href='http://wordpress.org/extend/plugins/game-locations/' title='Game Locations Plugin'>Game Locations Plugin</a> to use this feature. It makes life a lot simpler for 'normal' Game Schedules use.</td>";
			echo '</tr>';
		} 
		
		$admin_fields = array( 	'mstw_gs_location' => array (
									'type' => 'text',
									'value' => $mstw_gs_location,
									'label' => $location_label  . ":",
									'maxlength' => 128,
									'size' => 30,
									'notes' => __( 'This setting WILL OVERRIDE any selection from the Game Locations dropdown. Therefore it should not be used if location is selected from Game Locations dropdown.', 'mstw-loc-domain' ),
								),
								'mstw_gs_location_link' => array (
									'type' => 'text',
									'value' => $mstw_gs_location_link,
									'label' =>  $location_label . ' ' . __( 'Link:', 'mstw-loc-domain' ),
									'maxlength' => 256,
									'size' => 30,
									'notes' => __( 'This could be a link to a map or to a venue website. It will override the Game Locations map link. ', 'mstw-loc-domain' ),
								),
								'mstw_gs_game_result' => array (
									'type' => 'text',
									'value' => $mstw_gs_game_result,
									'label' =>  __( 'Game Result:', 'mstw-loc-domain' ),
									'maxlength' => 128,
									'size' => 30,
									'notes' => __( 'If a result is entered here, it will replace the game time in all front end displays.', 'mstw-loc-domain' ),
								),
								'mstw_gs_media_label_1' => array (
									'type' => 'text',
									'value' => $mstw_gs_media_label_1,
									'label' =>  $media_label . ' ' . __( 'Label 1:', 'mstw-loc-domain' ),
									'maxlength' => 128,
									'size' => 30,
									'notes' => __( 'This text will be displayed for ', 'mstw-loc-domain' ) . $media_label . __( ' link 1. If it is blank, NO LINKS WILL BE DISPLAYED.', 'mstw-loc-domain' ),
								),
								'mstw_gs_media_url_1' => array (
									'type' => 'text',
									'value' => $mstw_gs_media_url_1,
									'label' =>  $media_label . ' ' . __( 'URL 1:', 'mstw-loc-domain' ),
									'maxlength' => 256,
									'size' => 30,
									'notes' => __( 'URL for ', 'mstw-loc-domain' ) . $media_label . __( ' link 1.', 'mstw-loc-domain' ),
								),
								'mstw_gs_media_label_2' => array (
									'type' => 'text',
									'value' => $mstw_gs_media_label_2,
									'label' =>  $media_label . ' ' . __( 'Label 2:', 'mstw-loc-domain' ),
									'maxlength' => 128,
									'size' => 30,
									'notes' => __( 'This text will be displayed for ', 'mstw-loc-domain' ) . $media_label . __( ' link 2. If it is blank, #3 below will be ignored.', 'mstw-loc-domain' ),
								),
								'mstw_gs_media_url_2' => array (
									'type' => 'text',
									'value' => $mstw_gs_media_url_2,
									'label' =>  $media_label . ' ' . __( 'URL 2:', 'mstw-loc-domain' ),
									'maxlength' => 256,
									'size' => 30,
									'notes' => __( 'URL for ', 'mstw-loc-domain' ) . $media_label . __( ' link 2.', 'mstw-loc-domain' ),
								),
								'mstw_gs_media_label_3' => array (
									'type' => 'text',
									'value' => $mstw_gs_media_label_3,
									'label' =>  $media_label . ' ' . __( 'Label 3:', 'mstw-loc-domain' ),
									'maxlength' => 128,
									'size' => 30,
									'notes' => __( 'This text will be displayed for ', 'mstw-loc-domain' ). $media_label . __( ' link 3.', 'mstw-loc-domain' ),
								),
								'mstw_gs_media_url_3' => array (
									'type' => 'text',
									'value' => $mstw_gs_media_url_3,
									'label' =>  $media_label . ' ' . __( 'URL 3:', 'mstw-loc-domain' ),
									'maxlength' => 256,
									'size' => 30,
									'notes' => __( 'URL for ', 'mstw-loc-domain' ) . $media_label . __( ' link 3.', 'mstw-loc-domain' ),
								),
							);
		
	
		mstw_gs_build_admin_edit_screen( $admin_fields );
		?>
		
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
	
	function mstw_gs_build_teams_input( $current_team ) {
			
		$teams = get_posts(array( 'numberposts' => -1,
						  'post_type' => 'mstw_gs_teams',
						  'orderby' => 'title',
						  'order' => 'ASC' 
						));						

		if( $teams ) {
			echo '<tr valign="top">';
			echo '<th>' . __( 'Select Opponent:', 'mstw-loc-domain' ) . '</th>';
			echo "<td><select id='gs_opponent_team' name='gs_opponent_team'>";
			//$selected = ( empty( $gs_opponent_team ) or $gs_opponent_team == -1 ) ? 'selected="selected"' : '';
			
			//echo "<option value='-1' " . $selected . "> ---- </option>";
			echo "<option value='-1'> ---- </option>";
			foreach( $teams as $team ) {
				$selected = ( $current_team == $team->ID ) ? 'selected="selected"' : '';
				echo "<option value='" . $team->ID . "'" . $selected . ">" . get_the_title( $team->ID ) . "</option>";
			}
			
			echo "</select>\n";
			echo "<br/><span class='description'>If set, this setting will override Opponent and Opponent Link. It is also the only way to add logos to the various displays.</span></td>\n";
			echo "</tr>\n";
			
		}
		
	} //End: function build_teams

	// ----------------------------------------------------------------
	// Save the Game Schedules Meta Data
	//
	add_action( 'save_post', 'mstw_gs_save_meta' );

	function mstw_gs_save_meta( $post_id ) {
		// Process scheduled_game updates
		if( isset( $_POST['post_type'] ) ) {
			// SCHEDULED_GAMES POST TYPE
			//echo '$_POST:<pre>';
			//print_r( $_POST );
			//echo '</pre>';
			//die("Done");
			if ( $_POST['post_type'] == 'scheduled_games' ) {
				//First verify the metadata required by the shortcode is set. If not, set defaults
				
				// SCHEDULE ID
				// If schedule id was not set, default to 1 :: could happen!
				if ( isset( $_POST['mstw_gs_sched_id'] ) ) {
					$mstw_id = sanitize_title( $_POST['mstw_gs_sched_id'], '1' );
				}
				else {  //$_POST['mstw_gs_sched_id'] is not set
					$mstw_id = '1';
				}
				update_post_meta( $post_id, '_mstw_gs_sched_id', $mstw_id );
				
				$date_only_str = strip_tags( trim( $_POST[ 'gs_game_date' ] ) );
				//$unix_date = strtotime( $date_only_str );
				//update_post_meta( $post_id, '_mstw_gs_unix_date', $unix_date );
				$game_time_hrs = mstw_gs_safe_ref( $_POST, 'game_time_hrs' );
				$game_time_hrs = strip_tags( trim( $game_time_hrs ) );
				$game_time_mins = mstw_gs_safe_ref( $_POST, 'game_time_mins' );
				$game_time_mins = strip_tags( trim( $game_time_mins ) );
				$game_time_tba = mstw_gs_safe_ref( $_POST, 'game_time_tba' );
				$game_time_tba = strip_tags( trim( $game_time_tba ) );
				update_post_meta( $post_id, '_mstw_gs_game_time_tba', $game_time_tba );
				
				if ( $game_time_tba != '' ) {
					$game_time_hrs = $game_time_mins = '00';
				}
				
				$mstw_time = $game_time_hrs . ':' . $game_time_mins;
				
				$full_dtg_str = $date_only_str . ' ' . $mstw_time;
				$unix_dtg = strtotime( $full_dtg_str );
				update_post_meta( $post_id, '_mstw_gs_unix_dtg', $unix_dtg );
							
				// Okay, we should be good to update the database
				$mstw_gs_opponent = mstw_gs_safe_ref( $_POST, 'mstw_gs_opponent' );		
				
				update_post_meta( $post_id, '_mstw_gs_opponent', sanitize_text_field( $mstw_gs_opponent ) );
			
				// New in 4.0 for MSTW Teams CPT entries
				//
				$gs_opponent_team = mstw_gs_safe_ref( $_POST, 'gs_opponent_team' );
				update_post_meta( $post_id, 'gs_opponent_team', $gs_opponent_team );
				
				$mstw_gs_opponent_link = mstw_gs_safe_ref( $_POST, 'mstw_gs_opponent_link' );				
				update_post_meta( $post_id, '_mstw_gs_opponent_link', esc_url( $mstw_gs_opponent_link ) );

				$mstw_gs_gl_location = mstw_gs_safe_ref( $_POST, 'mstw_gs_gl_location' );
				update_post_meta( $post_id, '_mstw_gs_gl_location', $mstw_gs_gl_location );
				
				$game_location_title = mstw_gs_safe_ref( $_POST, 'game_location_title' );
				//$game_location_title = ( $_POST['_mstw_gs_gl_location'] != '' ? sanitize_text_field( get_the_title( $_POST['mstw_gs_gl_location'] ) ) : '' );
				update_post_meta( $post_id, '_mstw_gs_gl_loc_title', sanitize_text_field( $game_location_title ) );
				
				$mstw_gs_location = mstw_gs_safe_ref( $_POST, 'mstw_gs_location' );
				update_post_meta( $post_id, '_mstw_gs_location', sanitize_text_field( $mstw_gs_location ) );
				
				$mstw_gs_location_link = mstw_gs_safe_ref( $_POST, 'mstw_gs_location_link' );
				update_post_meta( $post_id, '_mstw_gs_location_link', esc_url( $mstw_gs_location_link ) );		
				
				$mstw_gs_home_game = mstw_gs_safe_ref( $_POST, 'mstw_gs_home_game' );
				update_post_meta( $post_id, '_mstw_gs_home_game', strip_tags( $mstw_gs_home_game ) );
				
				$mstw_gs_game_time = mstw_gs_safe_ref( $_POST, 'mstw_gs_game_time' );				
				update_post_meta( $post_id, '_mstw_gs_game_time',  strip_tags( $mstw_gs_game_time ) );
				
				$mstw_gs_game_result = mstw_gs_safe_ref( $_POST, 'mstw_gs_game_result' );
				update_post_meta( $post_id, '_mstw_gs_game_result', sanitize_text_field( $mstw_gs_game_result ) );
		
				$mstw_gs_media_label_1 = mstw_gs_safe_ref( $_POST, 'mstw_gs_media_label_1' );
				update_post_meta( $post_id, '_mstw_gs_media_label_1', sanitize_text_field( $mstw_gs_media_label_1 ) );
			
				$mstw_gs_media_label_2 = mstw_gs_safe_ref( $_POST, 'mstw_gs_media_label_2' );
				update_post_meta( $post_id, '_mstw_gs_media_label_2', sanitize_text_field( $mstw_gs_media_label_2 ) );
			
				$mstw_gs_media_label_3 = mstw_gs_safe_ref( $_POST, 'mstw_gs_media_label_3' );
				update_post_meta( $post_id, '_mstw_gs_media_label_3', sanitize_text_field( $mstw_gs_media_label_3 ) );
			
				$mstw_gs_media_url_1 = mstw_gs_safe_ref( $_POST, 'mstw_gs_media_url_1' );
				update_post_meta( $post_id, '_mstw_gs_media_url_1', esc_url( $mstw_gs_media_url_1 ) );
			
				$mstw_gs_media_url_2 = mstw_gs_safe_ref( $_POST, 'mstw_gs_media_url_2' );
				update_post_meta( $post_id, '_mstw_gs_media_url_2', esc_url( $mstw_gs_media_url_2 ) );
			
				$mstw_gs_media_url_3 = mstw_gs_safe_ref( $_POST, 'mstw_gs_media_url_3' );
				update_post_meta( $post_id, '_mstw_gs_media_url_3', esc_url( $mstw_gs_media_url_3 ) );
			}
			
			// MSTW_GS_TEAMS POST TYPE
			else if ( $_POST['post_type'] == 'mstw_gs_teams' ) {
			
				update_post_meta( $post_id, 'team_full_name', sanitize_text_field( $_POST['team_full_name'] ) );
		
				update_post_meta( $post_id, 'team_short_name', sanitize_text_field( $_POST['team_short_name'] ) );
			
				update_post_meta( $post_id, 'team_full_mascot', sanitize_text_field( $_POST['team_full_mascot'] ) );
			
				update_post_meta( $post_id, 'team_short_mascot', sanitize_text_field( $_POST['team_short_mascot'] ) );
				
				update_post_meta( $post_id, 'team_home_venue', $_POST['team_home_venue'] );
						
				update_post_meta( $post_id, 'team_link', esc_url( $_POST['team_link'] ) );

				update_post_meta( $post_id, 'team_logo', esc_url( $_POST['team_logo'] ) );
						
				update_post_meta( $post_id, 'team_alt_logo', esc_url( $_POST['team_alt_logo'] ) );

			}
		
			// MSTW_GS_SCHEDULES POST TYPE
			else if ( $_POST['post_type'] == 'mstw_gs_schedules' ) {
			
				update_post_meta( $post_id, 'schedule_id', sanitize_title( $_POST['schedule_id'], 'not-specified' ) );
				
				update_post_meta( $post_id, 'schedule_team', $_POST['schedule_team'] );
			}
		} //End: if( isset( _$POST['post_type] )
	
		return;
		
	} //End: mstw_gs_save_meta()

// ----------------------------------------------------------------
// Set up the All Games table
//
add_filter( 'manage_edit-scheduled_games_columns', 'mstw_gs_edit_games_columns' ) ;

	function mstw_gs_edit_games_columns( $columns ) {	
		
		$options = get_option( 'mstw_gs_options' );
		
		$date_label = ( $options['date_label'] == '' ? __( 'Date', 'mstw-loc-domain' ) : $options['date_label'] );
		$opponent_label = ( $options['opponent_label'] == '' ? __( 'Opponent', 'mstw-loc-domain' ) : $options['opponent_label'] );
		$location_label = ( $options['location_label'] == '' ? __( 'Location', 'mstw-loc-domain' ) : $options['location_label'] );
		$time_label = ( $options['time_label'] == '' ? __( 'Time', 'mstw-loc-domain' ) : $options['time_label'] );
		$media_label = ( $options['media_label'] == '' ? __( 'Media', 'mstw-loc-domain' ) : $options['media_label'] );

		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Title', 'mstw-loc-domain' ),
			'sched_id' => __( 'Schedule', 'mstw-loc-domain' ),
			//'sched_year' => __( 'Year', 'mstw-loc-domain' ),
			'game_date' => $date_label,
			'game_time' => $time_label,
			'opponent' => $opponent_label,
			'opponent_link' => $opponent_label . ' ' . __( 'Link', 'mstw-loc-domain' ),
			'game_result' => __( 'Result', 'mstw-loc-domain' ),
			'gl_location' => $location_label,
			'location' => __( 'Custom', 'mstw-loc-domain' ) . ' ' . $location_label,
			'location_link' => __( 'Custom', 'mstw-loc-domain' ) . ' ' .  $location_label . ' ' . __( 'Link', 'mstw-loc-domain' ),
			
			/* 'debug' => __('Debug-Remove') */
		);

		return $columns;
	}

// ----------------------------------------------------------------
// Display the Games 'view all' columns
// 
	add_action( 'manage_scheduled_games_posts_custom_column', 'mstw_gs_manage_games_columns', 10, 2 );

	function mstw_gs_manage_games_columns( $column, $post_id ) {
		global $post;
		
		$options = get_option( 'mstw_gs_dtg_options' );
		$mstw_admin_date_format = $options['admin_date_format'];
		$mstw_admin_time_format = $options['admin_time_format'];
		
		$game_timestamp = get_post_meta( $post_id, '_mstw_gs_unix_dtg', true );

		switch( $column ) {
			// Debug Column */
			//case 'debug' :
			//	$debug_str = "";
			//	echo (	$debug_str );	
			//	break;
			//
			
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
				if ( empty( $game_timestamp ) ) {
					_e( 'No Game Date', 'mstw-loc-domain' );
				}
				else {
					//echo $game_timestamp;
					echo( date( $mstw_admin_date_format, intval( $game_timestamp ) ) );
				}
				break;
			
			//If displaying the 'time' column
			case 'game_time' :
				// Build from UNIX timestamp
				$mstw_gs_game_time_tba = get_post_meta( $post_id, '_mstw_gs_game_time_tba', true );

				if ( $mstw_gs_game_time_tba != '' ) {
					printf( '%s', $mstw_gs_game_time_tba );
				}
				else {
					//echo $game_timestamp;
					//printf( '%s', date( $mstw_admin_time_format, $game_timestamp ) );
					echo( date( $mstw_admin_time_format, intval( $game_timestamp ) ) );
				}
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
				
			//If displaying the 'gl_location' column.
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
				
			/* Just break out of the switch statement for everything else. */
			default :
				break;
		}
	}

	
// ----------------------------------------------------------------
// Set up the All Teams table
//
	add_filter( 'manage_edit-mstw_gs_teams_columns', 'mstw_gs_edit_teams_columns' ) ;

	function mstw_gs_edit_teams_columns( $columns ) {	
		
		$options = get_option( 'mstw_gs_options' );

		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Title', 'mstw-loc-domain' ),
			'team_full_name' => __( 'Team Full Name', 'mstw-loc-domain' ),
			'team_short_name' => __( 'Team Short Name', 'mstw-loc-domain' ),
			'team_full_mascot' => __( 'Mascot Full Name', 'mstw-loc-domain' ),
			'team_short_mascot' => __( 'Mascot Short Name', 'mstw-loc-domain' ),
			'team_link' => __( 'Team Link', 'mstw-loc-domain' ),
			'team_logo' => __( 'Team Table Logo', 'mstw-loc-domain' ),
			'team_alt_logo' => __( 'Team Slider Logo', 'mstw-loc-domain' ),
			'team_home_venue' => __( 'Team Home Venue', 'mstw-loc-domain' ),
			/* 'debug' => __('Debug-Remove') */
		);

		return $columns;
	}


	
// ----------------------------------------------------------------
// Display the Teams 'view all' columns
// 
	add_action( 'manage_mstw_gs_teams_posts_custom_column', 'mstw_gs_manage_teams_columns', 10, 2 );

	function mstw_gs_manage_teams_columns( $column, $post_id ) {
		global $post;
		
		switch( $column ) {
			/* Debug Column */
			/*case 'debug' :
				$debug_str = "";
				echo (	$debug_str );	
				break;
			*/
				
			// FULL NAME column
			case 'team_full_name' :
				$full_name = get_post_meta( $post_id, 'team_full_name', true );
				if( $full_name != '' )
					echo( $full_name );
				else
					_e( 'No Full Name', 'mstw-loc-domain' ); 
				break;
			
			// SHORT NAME column
			case 'team_short_name' :
				$short_name = get_post_meta( $post_id, 'team_short_name', true );
				if( $short_name != '' )
					echo( $short_name );
				else
					_e( 'No Short Name', 'mstw-loc-domain' ); 
				break;
			
			// FULL MASCOT column
			case 'team_full_mascot' :
				$name = get_post_meta( $post_id, 'team_full_mascot', true );
				if( $name != '' )
					echo( $name );
				else
					_e( 'No Full Mascot', 'mstw-loc-domain' ); 
				break;

			// SHORT MASCOT column
			case 'team_short_mascot' :
				$name = get_post_meta( $post_id, 'team_short_mascot', true );
				if( $name != '' )
					echo( $name );
				else
					_e( 'No Short Mascot', 'mstw-loc-domain' ); 
				break;
				
			// TEAM LINK column
			case 'team_link' :
				$name = get_post_meta( $post_id, 'team_link', true );
				if( $name != '' )
					echo( $name );
				else
					_e( 'No Team Link', 'mstw-loc-domain' ); 
				break;

			// TEAM (TABLE) LOGO column
			case 'team_logo' :
				$name = get_post_meta( $post_id, 'team_logo', true );
				if( $name != '' )
					echo( $name );
				else
					_e( 'No Table Logo', 'mstw-loc-domain' ); 
				break;
				
			// TEAM ALT (SLIDER) LOGO column
			case 'team_alt_logo' :
				$name = get_post_meta( $post_id, 'team_alt_logo', true );
				if( $name != '' )
					echo( $name );
				else
					_e( 'No Slider Logo', 'mstw-loc-domain' ); 
				break;	
				
			// TEAM HOME VENUE column
			case 'team_home_venue' :
				// Get the post meta
				$venue = get_post_meta( $post_id, 'team_home_venue', true );

				if ( empty( $venue ) )
					_e( 'No Home Venue', 'mstw-loc-domain' );
				else
					echo get_the_title( $venue );

				break;	
				
			/* Just break out of the switch statement for everything else. */
			default :
				break;
		}
	}
	
// ----------------------------------------------------------------
// Set up the All Schedules table
//
	add_filter( 'manage_edit-mstw_gs_schedules_columns', 'mstw_gs_edit_schedules_columns' ) ;

	function mstw_gs_edit_schedules_columns( $columns ) {	
		
		$options = get_option( 'mstw_gs_options' );

		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Title', 'mstw-loc-domain' ),
			'schedule_id' => __( 'Unique Schedule ID', 'mstw-loc-domain' ),
			'schedule_team' => __( 'Schedule for Team', 'mstw-loc-domain' ),
			// 'debug' => __('Debug-Remove')
		);

		return $columns;
	}


	
// ----------------------------------------------------------------
// Display the Schedules 'view all' columns
// 
	add_action( 'manage_mstw_gs_schedules_posts_custom_column', 'mstw_gs_manage_schedules_columns', 10, 2 );

	function mstw_gs_manage_schedules_columns( $column, $post_id ) {
		global $post;
		
		switch( $column ) {
			/* Debug Column */
			/*case 'debug' :
				$debug_str = "";
				echo (	$debug_str );	
				break;
			*/
				
			// SCHEDULE ID column
			case 'schedule_id' :
				$id = get_post_meta( $post_id, 'schedule_id', true );
				if( $id != '' )
					echo ( $id );
				else
					_e( 'No Schedule ID', 'mstw-loc-domain' ); 
					
				break;
			
			// SCHEDULE FOR column
			case 'schedule_team' :
				$team = get_post_meta( $post_id, 'schedule_team', true );
				if( $team != '' )
					echo( get_the_title( $team ) );
				else
					_e( 'No Team for Schedule', 'mstw-loc-domain' ); 
					
				break;
			
			/* Just break out of the switch statement for everything else. */
			default :
				break;
		}
	}

// ----------------------------------------------------------------
//	CODE FOR GAME SCHEDULES SETTINGS PAGES
// ----------------------------------------------------------------

// ----------------------------------------------------------------	
// Add a menus for the settings pages
//
	add_action( 'admin_menu', 'mstw_gs_add_page' );

	function mstw_gs_add_page( ) {
	
		//remove_submenu_page( 'post-new.php?post_type=scheduled_games' );
		global $submenu;
		
		unset( $submenu['edit.php?post_type=scheduled_games'][10] );
		
		// Decided to add the settings page to the Games menu rather than
		// the settings menu
		$capability = apply_filters( 'mstw_gs_user_capability', 'edit_others_posts', 'display_settings_menu_item' );
		$page = add_submenu_page( 	'edit.php?post_type=scheduled_games', 
							'Game Schedule Settings', 	//page title
							'Display Settings', 		//menu title
							$capability, 			// Capability required to see this option.
							'mstw_gs_settings', 		// Slug name to refer to this menu
							'mstw_gs_option_page' );	// Callback to output content
							
		/*$page = add_submenu_page( 	'edit.php?post_type=scheduled_games', 
							'Game Schedule Color Settings', 	//page title
							'Color Settings', 		//menu title
							'edit_posts', 			// Capability required to see this option.
							'mstw_gs_colors', 		// Slug name to refer to this menu
							'mstw_gs_colors_page' );	// Callback to output content
		*/
							
							
		// Does the importing work
		$plugin = new MSTW_GS_ImporterPlugin;
		$capability = apply_filters( 'mstw_gs_user_capability', 'edit_others_posts', 'csv_import_menu_item' );
		add_submenu_page(	'edit.php?post_type=scheduled_games',
							'Import Schedule from CSV File',	//page title
							'CSV Schedule Import',				//menu title
							$capability,						//capability to access
							'mstw_gs_csv_import',				//slug name for menu
							array( $plugin, 'form' )			//callback to display menu
						);
	}

// ----------------------------------------------------------------	
// 	Render the Display Setttings page
// ----------------------------------------------------------------
	function mstw_gs_option_page() {
		global $pagenow;
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2>Game Schedule Plugin Settings</h2>
			<?php //settings_errors(); ?> 
			
			<?php 
			//Get or set the current tab - default to first/main settings tab
			$current_tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : 'files-columns-tab' );
			
			//Display the tabs, showing the current tab
			mstw_gs_admin_tabs( $current_tab );  
			?>
			
			<form action="options.php" method="post">
			
			<?php 
			//echo '<h2>pagenow = ' . $pagenow . ' page = ' . $_GET['page'] . '</h2>';
			//WHY DO WE NEED THIS CONDITIONAL, REALLY?
			if ( $pagenow == 'edit.php' && $_GET['page'] == 'mstw_gs_settings' ) {
				switch ( $current_tab ) {
					case 'files-columns-tab':
						settings_fields( 'mstw_gs_options' );
						do_settings_sections( 'mstw_gs_settings' );
						$options_name = 'mstw_gs_options[reset]';
						break;
					case 'date-time-tab';
						settings_fields( 'mstw_gs_dtg_options' );
						do_settings_sections( 'mstw_gs_dtg_settings' );
						$options_name = 'mstw_gs_dtg_options[reset]';
						break;
					case 'colors-tab':
						settings_fields( 'mstw_gs_color_options' );
						do_settings_sections( 'mstw_gs_colors' );
						$options_name = 'mstw_gs_color_options[reset]';
						break;
				}
				?>
				<table class="form-table">
				<tr>
					<td>
						<input name="Submit" type="submit" class="button-primary" value=<?php _e( "Save Changes", "mstw-loc-domain" ) ?> /><br/><span="description">&nbsp;&nbsp;</span>
					</td>
					<td>
						<input type="submit" name="<?php echo $options_name ?>" value=<?php _e( "Reset Default Values", "mstw-loc-domain" ) ?> /> <br/>
						<strong><span class="description"><?php _e( "WARNING! Reset Default Values will do so without further warning!", "mstw-loc-domain" ); ?></span></strong>
					</td>
				</tr>
				</table>
				<?php
			}
			
			//submit_button();
			//submit_button( "Reset" );
			?>
				
			</form>
		</div>
		<?php
	}
	
// ----------------------------------------------------------------	
// 	Register and define the settings
// ----------------------------------------------------------------
	add_action( 'admin_init', 'mstw_gs_admin_init' );
	
	function mstw_gs_admin_init( ) {
		//If options do not exist, add them
		if( false == get_option( 'mstw_gs_options' ) ) {    
			add_option( 'mstw_gs_options' );  
		}  
		if( false == get_option( 'mstw_gs_dtg_options' ) ) {    
			add_option( 'mstw_gs_dtg_options' );  
		} 
		if( false == get_option( 'mstw_gs_color_options' ) ) {    
			add_option( 'mstw_gs_color_options' );  
		}
		
		// Data Fields (& columns) Settings
		mstw_gs_data_fields_setup( );
		
		// Date & Time Format Settings
		mstw_gs_dtg_format_setup( );
		
		// Colors Settings
		mstw_gs_colors_setup( );
		
		register_setting(
			'mstw_gs_options',  	// settings group name
			'mstw_gs_options',  		// options (array) to validate
			'mstw_gs_validate_options'  // validation function
			);
			
		register_setting(
			'mstw_gs_dtg_options',  	// settings group name
			'mstw_gs_dtg_options',  		// options (array) to validate
			'mstw_gs_validate_dtg_options'  // validation function
			);
			
		register_setting(
			'mstw_gs_color_options',  	// settings group name
			'mstw_gs_color_options', 		// options (array) to validate
			'mstw_gs_validate_color_options'  // validation function
			);
	}
	
// ----------------------------------------------------------------	
// 	Colors settings page setup	
// ----------------------------------------------------------------		
	function mstw_gs_colors_setup( ) {
		
		mstw_gs_table_colors_section_setup( );
		
		mstw_gs_cdt_colors_section_setup( );
		
		mstw_gs_slider_colors_section_setup( );	
			
	}
	
	// ----------------------------------------------------------------	
	// 	Table (shortcode and widget) colors section setup	
	// 
	function mstw_gs_table_colors_section_setup( ) {
	
		$display_on_page = 'mstw_gs_colors';
		$page_section = 'mstw_gs_table_colors';
		
		$options = get_option( 'mstw_gs_color_options' );
		
		add_settings_section(
			$page_section,
			'Schedule Table Colors',
			'mstw_gs_colors_table_inst',
			$display_on_page
			);
	
		$arguments = array(
			array( 	// TABLE HEADER BACKGROUND COLOR
				'type' => 'color', 
				'id' => 'gs_tbl_hdr_bkgd_color',
				'name' => 'mstw_gs_color_options[gs_tbl_hdr_bkgd_color]',
				'value' => mstw_gs_safe_ref( $options, 'gs_tbl_hdr_bkgd_color' ), //$options['gs_tbl_hdr_bkgd_color'], 
				'title'	=> __( 'Header Background Color:', 'mstw-loc-domain' ),
				'page' => $display_on_page,
				'section' => $page_section,
			),array( 	// TABLE HEADER TEXT COLOR
				'type' => 'color', 
				'id' => 'gs_tbl_hdr_text_color',
				'name' => 'mstw_gs_color_options[gs_tbl_hdr_text_color]',
				'value' => mstw_gs_safe_ref( $options, 'gs_tbl_hdr_text_color' ), //$options['gs_tbl_hdr_text_color'],
				'title'	=> __( 'Header Text Color:', 'mstw-loc-domain' ),
				'page' => $display_on_page,
				'section' => $page_section,
			),
			array( 	// TABLE BORDER COLOR
				'type' => 'color', 
				'id' => 'gs_tbl_border_color',
				'name' => 'mstw_gs_color_options[gs_tbl_border_color]',
				'value' => mstw_gs_safe_ref( $options, 'gs_tbl_border_color' ), //$options['gs_tbl_border_color'],
				'title'	=> __( 'Table Border Color:', 'mstw-loc-domain' ),
				'page' => $display_on_page,
				'section' => $page_section,
			),
			array( 	// ODD ROW BACKGROUND COLOR
				'type' => 'color', 
				'id' => 'gs_tbl_odd_bkgd_color',
				'name' => 'mstw_gs_color_options[gs_tbl_odd_bkgd_color]',
				'value' => mstw_gs_safe_ref( $options, 'gs_tbl_odd_bkgd_color' ), //$options['gs_tbl_odd_bkgd_color'],
				'title'	=> __( 'Odd Row Background Color:', 'mstw-loc-domain' ),
				'page' => $display_on_page,
				'section' => $page_section,
			),
			array( 	// ODD ROW TEXT COLOR
				'type' => 'color', 
				'id' => 'gs_tbl_odd_text_color',
				'name' => 'mstw_gs_color_options[gs_tbl_odd_text_color]',
				'value' => mstw_gs_safe_ref( $options, 'gs_tbl_odd_text_color' ), //$options['gs_tbl_odd_text_color'],
				'title'	=> __( 'Odd Row Text Color:', 'mstw-loc-domain' ),
				'page' => $display_on_page,
				'section' => $page_section,
			),
			array( 	// EVEN ROW BACKGROUND COLOR
				'type' => 'color', 
				'id' => 'gs_tbl_even_bkgd_color',
				'name' => 'mstw_gs_color_options[gs_tbl_even_bkgd_color]',
				'value' => mstw_gs_safe_ref( $options, 'gs_tbl_even_bkgd_color' ), //$options['gs_tbl_even_bkgd_color'],
				'title'	=> __( 'Even Row Background Color:', 'mstw-loc-domain' ),
				'page' => $display_on_page,
				'section' => $page_section,
			),
			array( 	// EVEN ROW TEXT COLOR
				'type' => 'color', 
				'id' => 'gs_tbl_even_text_color',
				'name' => 'mstw_gs_color_options[gs_tbl_even_text_color]',
				'value' => mstw_gs_safe_ref( $options, 'gs_tbl_even_text_color' ), //$options['gs_tbl_even_text_color'],
				'title'	=> __( 'Even Row Text Color:', 'mstw-loc-domain' ),
				'page' => $display_on_page,
				'section' => $page_section,
			),
			array( 	// HOME GAME ROW BACKGROUND COLOR
				'type' => 'color', 
				'id' => 'gs_tbl_home_bkgd_color',
				'name' => 'mstw_gs_color_options[gs_tbl_home_bkgd_color]',
				'value' => mstw_gs_safe_ref( $options, 'gs_tbl_home_bkgd_color' ), //$options['gs_tbl_home_bkgd_color'],
				'title'	=> __( 'Home Game (row) Background Color:', 'mstw-loc-domain' ),
				'page' => $display_on_page,
				'section' => $page_section,
			),
			array( 	// HOME GAME ROW TEXT COLOR
				'type' => 'color', 
				'id' => 'gs_tbl_home_text_color',
				'name' => 'mstw_gs_color_options[gs_tbl_home_text_color]',
				'value' => mstw_gs_safe_ref( $options, 'gs_tbl_home_text_color' ), //$options['gs_tbl_home_text_color'],
				'title'	=> __( 'Home Game (row) Text Color:', 'mstw-loc-domain' ),
				'page' => $display_on_page,
				'section' => $page_section,
			),
		);
		
		foreach ( $arguments as $args ) {
			mstw_gs_build_form_field( $args );
		}
	
	}	
	
// ----------------------------------------------------------------	
// 	CDT (shortcode and widget) colors section setup	
// ----------------------------------------------------------------	
	function mstw_gs_cdt_colors_section_setup( ) {
	
		$display_on_page = 'mstw_gs_colors';
		$page_section = 'mstw_gs_cdt_colors';
		
		$options = get_option( 'mstw_gs_color_options' );
		
		add_settings_section(
			$page_section,
			'Countdown Timer Colors',
			'mstw_gs_colors_cdt_inst',
			$display_on_page
		);
		
		$arguments = array(
			array( 	
			'type' => 'color', 
			'id' => 'gs_cdt_game_time_color',
			'name' => 'mstw_gs_color_options[gs_cdt_game_time_color]',
			'value' => mstw_gs_safe_ref( $options, 'gs_cdt_game_time_color' ), //$options['gs_cdt_game_time_color'],
			'title'	=> __( 'Game Time Text Color:', 'mstw-loc-domain' ),
			'desc'	=> '',
			'default' => '',
			'options' => '',
			'page' => $display_on_page,
			'section' => $page_section,
			),
			array( 	
			'type' => 'color', 
			'id' => 'gs_cdt_opponent_color',
			'name' => 'mstw_gs_color_options[gs_cdt_opponent_color]',
			'value' => mstw_gs_safe_ref( $options, 'gs_cdt_opponent_color' ), //$options['gs_cdt_opponent_color'],
			'title'	=> __( 'Opponent Text Color:', 'mstw-loc-domain' ),
			'desc'	=> '',
			'default' => '',
			'options' => '',
			'page' => $display_on_page,
			'section' => $page_section,
			),
			array( 	
			'type' => 'color', 
			'id' => 'gs_cdt_location_color',
			'name' => 'mstw_gs_color_options[gs_cdt_location_color]',
			'value' => mstw_gs_safe_ref( $options, 'gs_cdt_location_color' ), //$options['gs_cdt_location_color'],
			'title'	=> __( 'Location Text Color:', 'mstw-loc-domain' ),
			'desc'	=> '',
			'default' => '',
			'options' => '',
			'page' => $display_on_page,
			'section' => $page_section,
			),
			array( 	
			'type' => 'color',
			'id' => 'gs_cdt_intro_color',
			'name' => 'mstw_gs_color_options[gs_cdt_intro_color]',
			'value' => mstw_gs_safe_ref( $options, 'gs_cdt_intro_color' ), //$options['gs_cdt_intro_color'],
			'title'	=> __( 'Intro Text Color:', 'mstw-loc-domain' ),
			'desc'	=> '',
			'default' => '',
			'options' => '',
			'page' => $display_on_page,
			'section' => $page_section,
			),
			array( 	
			'type' => 'color', 
			'id' => 'gs_cdt_countdown_color',
			'name' => 'mstw_gs_color_options[gs_cdt_countdown_color]',
			'value' => mstw_gs_safe_ref( $options, 'gs_cdt_countdown_color' ), //$options['gs_cdt_countdown_color'],
			'title'	=> __( 'Countdown Text Color:', 'mstw-loc-domain' ),
			'desc'	=> '',
			'default' => '',
			'options' => '',
			'page' => $display_on_page,
			'section' => $page_section,
			),
			array( 	
			'type' => 'color', 
			'id' => 'gs_cdt_countdown_bkgd_color',
			'name' => 'mstw_gs_color_options[gs_cdt_countdown_bkgd_color]',
			'value' => mstw_gs_safe_ref( $options, 'gs_cdt_countdown_bkgd_color' ), //$options['gs_cdt_countdown_bkgd_color'],
			'title'	=> __( 'Countdown Background Color:', 'mstw-loc-domain' ),
			'desc'	=> '',
			'default' => '',
			'options' => '',
			'page' => $display_on_page,
			'section' => $page_section,
			),
		);
		foreach ( $arguments as $args ) {
			mstw_gs_build_form_field( $args );
		}
	}
	
// ----------------------------------------------------------------	
// 	Slider colors section setup	
// ----------------------------------------------------------------	
	function mstw_gs_slider_colors_section_setup( ) {
		
		$display_on_page = 'mstw_gs_colors';
		$page_section = 'mstw_gs_slider_colors';
		
		$options = get_option( 'mstw_gs_color_options' );
		
		add_settings_section(
			$page_section,
			'Schedule Slider Colors',
			'mstw_gs_colors_slider_inst',
			$display_on_page
			);	
		

		$arguments = array(
			array( 	
			'type' => 'color', 
			'id' => 'gs_sldr_hdr_bkgd_color',
			'name' => 'mstw_gs_color_options[gs_sldr_hdr_bkgd_color]',
			'value' => mstw_gs_safe_ref( $options, 'gs_sldr_hdr_bkgd_color' ), //$options['gs_sldr_hdr_bkgd_color'],
			'title'	=> __( 'Header Background Color:', 'mstw-loc-domain' ),
			'desc'	=> '',
			'default' => '',
			'options' => '',
			'page' => $display_on_page,
			'section' => $page_section,
			),
			array( 	
			'type' => 'color', 
			'id' => 'gs_sldr_game_block_bkgd_color',
			'name' => 'mstw_gs_color_options[gs_sldr_game_block_bkgd_color]',
			'value' => mstw_gs_safe_ref( $options, 'gs_sldr_game_block_bkgd_color' ), //$options['gs_sldr_game_block_bkgd_color'],
			'title'	=> __( 'Game Block Background Color:', 'mstw-loc-domain' ),
			'desc'	=> '',
			'default' => '',
			'options' => '',
			'page' => $display_on_page,
			'section' => $page_section,
			),
			array( 	
			'type' => 'color', 
			'id' => 'gs_sldr_hdr_text_color',
			'name' => 'mstw_gs_color_options[gs_sldr_hdr_text_color]',
			'value' => mstw_gs_safe_ref( $options, 'gs_sldr_hdr_text_color' ), //$options['gs_sldr_hdr_text_color'],
			'title'	=> __( 'Header Text Color:', 'mstw-loc-domain' ),
			'desc'	=> '',
			'default' => '',
			'options' => '',
			'page' => $display_on_page,
			'section' => $page_section,
			),
			array( 	
			'type' => 'color', 
			'id' => 'gs_sldr_hdr_divider_color',
			'name' => 'mstw_gs_color_options[gs_sldr_hdr_divider_color]',
			'value' => mstw_gs_safe_ref( $options, 'gs_sldr_hdr_divider_color' ), //$options['gs_sldr_hdr_divider_color'],
			'title'	=> __( 'Header Divider (line) Color:', 'mstw-loc-domain' ),
			'desc'	=> '',
			'default' => '',
			'options' => '',
			'page' => $display_on_page,
			'section' => $page_section,
			),
			array( 	
			'type' => 'color', 
			'id' => 'gs_sldr_game_date_color',
			'name' => 'mstw_gs_color_options[gs_sldr_game_date_color]',
			'value' => mstw_gs_safe_ref( $options, 'gs_sldr_game_date_color' ), //$options['gs_sldr_game_date_color'],
			'title'	=> __( 'Game Date Color:', 'mstw-loc-domain' ),
			'desc'	=> '',
			'default' => '',
			'options' => '',
			'page' => $display_on_page,
			'section' => $page_section,
			),
			array( 	
			'type' => 'color', 
			'id' => 'gs_sldr_game_opponent_color',
			'name' => 'mstw_gs_color_options[gs_sldr_game_opponent_color]',
			'value' => mstw_gs_safe_ref( $options, 'gs_sldr_game_opponent_color' ), //$options['gs_sldr_game_opponent_color'],
			'title'	=> __( 'Opponent Color:', 'mstw-loc-domain' ),
			'desc'	=> '',
			'default' => '',
			'options' => '',
			'page' => $display_on_page,
			'section' => $page_section,
			),
			array( 	
			'type' => 'color', 
			'id' => 'gs_sldr_game_location_color',
			'name' => 'mstw_gs_color_options[gs_sldr_game_location_color]',
			'value' => mstw_gs_safe_ref( $options, 'gs_sldr_game_location_color' ), //$options['gs_sldr_game_location_color'],
			'title'	=> __( 'Game Location Color:', 'mstw-loc-domain' ),
			'desc'	=> '',
			'default' => '',
			'options' => '',
			'page' => $display_on_page,
			'section' => $page_section,
			),
			array( 	
			'type' => 'color', 
			'id' => 'gs_sldr_game_time_color',
			'name' => 'mstw_gs_color_options[gs_sldr_game_time_color]',
			'value' => mstw_gs_safe_ref( $options, 'gs_sldr_game_time_color' ), //$options['gs_sldr_game_time_color'],
			'title'	=> __( 'Game Time Color:', 'mstw-loc-domain' ),
			'desc'	=> '',
			'default' => '',
			'options' => '',
			'page' => $display_on_page,
			'section' => $page_section,
			),
			array( 	
			'type' => 'color', 
			'id' => 'gs_sldr_game_links_color',
			'name' => 'mstw_gs_color_options[gs_sldr_game_links_color]',
			'value' => mstw_gs_safe_ref( $options, 'gs_sldr_game_links_color' ), //$options['gs_sldr_game_links_color'],
			'title'	=> __( 'Game Links Color:', 'mstw-loc-domain' ),
			'desc'	=> '',
			'default' => '',
			'options' => '',
			'page' => $display_on_page,
			'section' => $page_section,
			),
		);
		
		foreach ( $arguments as $args ) {
			mstw_gs_build_form_field( $args );
		}
			
	}	

// ----------------------------------------------------------------	
// 	Colors table section instructions	
// ----------------------------------------------------------------	
	function mstw_gs_colors_table_inst( ) {
		echo '<p>' . __( "Enter the default colors for your Schedule Table shortcodes and widgets. NOTE: These settings will override the default colors in the plugin's stylsheet." , 'mstw-loc-domain' ) . '</p>';
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
	}
	
// ----------------------------------------------------------------	
// 	Team Logo section instructions	
// ----------------------------------------------------------------	
	function mstw_gs_team_logo_inst( ) {
		echo '<p>' . __( "Control the display of team names & logos. NOTE: THESE SETTINGS ONLY APPLY WHEN SELECTING OPPONENTS FROM THE MSTW TEAMS DATABASE.", 'mstw-loc-domain' ) . '</p>';
	}	
	
	function mstw_gs_dtg_format_setup( ) {
		// DTG format section
		// Data fields/columns -- show/hide and labels
		$display_on_page =  'mstw_gs_dtg_settings';
		
		$options = wp_parse_args( get_option( 'mstw_gs_dtg_options' ), mstw_gs_get_dtg_defaults( ) );
		
		/*echo 'mstw_gs_dtg_options:<pre>';
		print_r( get_option('mstw_gs_dtg_options') );
		echo '</pre>';
		
		echo 'options parsed with defaults:<pre>'; print_r( $options ); echo '</pre>';
		*/
		
		//----------------------------------------------------------
		// Admin format settings section
		//
		$page_section = 'mstw_gs_admin_dtg_format_settings';
		
		add_settings_section(
			$page_section,
			__( 'Admin Page Formats', 'mstw-loc-domain' ),
			'mstw_gs_admin_dtg_inst',
			$display_on_page
			);
		
		$arguments = array(
						array( 	// ADMIN DATE FORMAT
								'type'    => 'date-only', 
								'id' => 'admin_date_format',
								'name'	=> 'mstw_gs_dtg_options[admin_date_format]',
								'value'	=> $options['admin_date_format'],
								'desc'	=> '',
								'title'	=> __( 'Admin Table Date Format:', 'mstw-loc-domain' ),
								'default' => '',
								'options' => '',
								'page' => $display_on_page,
								'section' => $page_section,
							),
						array( 	// ADMIN CUSTOM DATE FORMAT
								'type'    => 'text', 
								'id' => 'custom_admin_date_format',
								'name'	=> 'mstw_gs_dtg_options[custom_admin_date_format]',
								'value'	=> $options['custom_admin_date_format'],
								'desc'	=> __( 'Enter a PHP date() format string for a custom format. You probably should know what you are doing before selecting the custom option.', 'mstw-loc-domain' ),
								'title'	=> __( 'Admin Table Custom Date Format:', 'mstw-loc-domain' ),
								'default' => '',
								'options' => '',
								'page' => $display_on_page,
								'section' => $page_section,
							),
						array( 	// ADMIN TIME FORMAT
								'type'    => 'time-only', 
								'id' => 'admin_time_format',
								'name' => 'mstw_gs_dtg_options[admin_time_format]',
								'value' => $options['admin_time_format'],
								'desc'	=> '',
								'title'	=> __( 'Admin Table Time Format:', 'mstw-loc-domain' ),
								'default' => '',
								'options' => '',
								'page' => $display_on_page,
								'section' => $page_section,
							),
						array( 	// ADMIN CUSTOM TIME FORMAT
								'type'    => 'text', 
								'id' => 'custom_admin_time_format',
								'name'	=> 'mstw_gs_dtg_options[custom_admin_time_format]',
								'value'	=> $options['custom_admin_time_format'],
								'desc'	=> __( 'Enter a PHP date() format string for a custom format. You probably should know what you are doing before selecting the custom option.', 'mstw-loc-domain' ),
								'title'	=> __( 'Custom Admin Table Time Format:', 'mstw-loc-domain' ),
								'default' => '',
								'options' => '',
								'page' => $display_on_page,
								'section' => $page_section,
							),
						);

		foreach ( $arguments as $args ) {
			mstw_gs_build_form_field( $args );
		}		
		
		//----------------------------------------------------------
		// Table format settings section
		//
		$page_section = 'mstw_gs_table_dtg_format_settings';
		
		add_settings_section(
			$page_section,
			__( 'Table Shortcode & Widget Formats', 'mstw-loc-domain' ),
			'mstw_gs_table_dtg_inst',
			$display_on_page
			);
		
		$arguments = array(
						array( 	// DATE FORMAT FOR SCHEDULE TABLE SHORTCODE
								'type'    => 'date-only', 
								'id' => 'table_date_format',
								'name'	=> 'mstw_gs_dtg_options[table_date_format]',
								'value'	=> $options['table_date_format'],
								'desc'	=> '',
								'title'	=> __( 'Schedule Table [shortcode] Date Format:', 'mstw-loc-domain' ),
								'default' => '',
								'options' => '',
								'page' => $display_on_page,
								'section' => $page_section,
							),
						array( 	// CUSTOM DATE FORMAT FOR SCHEDULE TABLE SHORTCODE
								'type'    => 'text', 
								'id' => 'custom_table_date_format',
								'name'	=> 'mstw_gs_dtg_options[custom_table_date_format]',
								'value'	=> $options['custom_table_date_format'],
								'desc'	=> __( 'Enter a PHP date() format string for a custom format. You probably should know what you are doing before selecting the custom option.', 'mstw-loc-domain' ),
								'title'	=> __( 'Schedule Table [shortcode] Custom Date Format:', 'mstw-loc-domain' ),
								'default' => '',
								'options' => '',
								'page' => $display_on_page,
								'section' => $page_section,
							),
						array( 	// SCHEDULE TABLE SHORTCODE TIME FORMAT
								'type'    => 'time-only', 
								'name' => 'mstw_gs_dtg_options[table_time_format]',
								'id' => 'table_time_format',
								'value' => $options['table_time_format'],
								'title'	=> __( 'Schedule Table [shortcode] Time Format:', 'mstw-loc-domain' ),
								'desc'	=> '',
								'default' => '',
								'options' => '',
								'page' => $display_on_page,
								'section' => $page_section,
							),
						array( 	// SCHEDULE TABLE SHORTCODE CUSTOM TIME FORMAT
								'type'    => 'text', 
								'id' => 'custom_table_time_format',
								'name'	=> 'mstw_gs_dtg_options[custom_table_time_format]',
								'value'	=> $options['custom_table_time_format'],
								'desc'	=> __( 'Enter a PHP date() format string for a custom format. You probably should know what you are doing before selecting the custom option.', 'mstw-loc-domain' ),
								'title'	=> __( 'Admin Table Custom Time Format:', 'mstw-loc-domain' ),
								'default' => '',
								'options' => '',
								'page' => $display_on_page,
								'section' => $page_section,
							),
						array( 	// DATE FORMAT FOR SCHEDULE TABLE WIDGET
								'type'    => 'date-only', 
								'id' => 'table_widget_date_format',
								'name'	=> 'mstw_gs_dtg_options[table_widget_date_format]',
								'value'	=> $options['table_widget_date_format'],
								'desc'	=> '',
								'title'	=> __( 'Schedule Table (widget) Date Format:', 'mstw-loc-domain' ),
								'default' => '',
								'options' => '',
								'page' => $display_on_page,
								'section' => $page_section,
							),
						array( 	// CUSTOM DATE FORMAT FOR SCHEDULE TABLE WIDGET
								'type'    => 'text', 
								'id' => 'custom_table_widget_date_format',
								'name'	=> 'mstw_gs_dtg_options[custom_table_widget_date_format]',
								'value'	=> $options['custom_table_widget_date_format'],
								'desc'	=> __( 'Enter a PHP date() format string for a custom format. You probably should know what you are doing before selecting the custom option.', 'mstw-loc-domain' ),
								'title'	=> __( 'Schedule Table Widget Custom Date Format:', 'mstw-loc-domain' ),
								'default' => '',
								'options' => '',
								'page' => $display_on_page,
								'section' => $page_section,
							),
						);
						
		foreach ( $arguments as $args ) {
			mstw_gs_build_form_field( $args );
		}	
		
		//----------------------------------------------------------
		// Countdown timer dtg settings section
		//
		$page_section = 'mstw_gs_cdt_dtg_format_settings';
		
		add_settings_section(
			$page_section,
			__( 'Countdown Timer Formats', 'mstw-loc-domain' ),
			'mstw_gs_cdt_dtg_inst',
			$display_on_page
			);
		
		$arguments = array(
						array( 	// DATE-TIME FORMAT FOR COUNTDOWN TIMER
								'type'    => 'date-time', 
								'id' => 'cdt_dtg_format',
								'name'	=> 'mstw_gs_dtg_options[cdt_dtg_format]',
								'value'	=> $options['cdt_dtg_format'],
								'desc'	=> '',
								'title'	=> __( 'Countdown Timer (widget & [shortcode]) Date & Time Format:', 'mstw-loc-domain' ),
								'default' => '',
								'options' => '',
								'page' => $display_on_page,
								'section' => $page_section,
							),
						array( 	// CUSTOM DATE-TIME FORMAT FOR COUNTDOWN TIMER
								'type'    => 'text', 
								'id' => 'custom_cdt_dtg_format',
								'name'	=> 'mstw_gs_dtg_options[custom_cdt_dtg_format]',
								'value'	=> $options['custom_cdt_dtg_format'],
								'desc'	=> __( 'Enter a PHP date() format string for a custom format. You probably should know what you are doing before selecting the custom option.', 'mstw-loc-domain' ),
								'title'	=>  __( 'Countdown Timer (widget & [shortcode]) Custom Date & Time Format:', 'mstw-loc-domain' ),
								'default' => '',
								'options' => '',
								'page' => $display_on_page,
								'section' => $page_section,
							),
						array( 	// DATE (ONLY) FORMAT FOR COUNTDOWN TIMER
								'type'    => 'date-only', 
								'id' => 'cdt_date_format',
								'name'	=> 'mstw_gs_dtg_options[cdt_date_format]',
								'value'	=> $options['cdt_date_format'],
								'desc'	=> __( 'Used when game time is TBA.', 'mstw-loc-domain' ),
								'title'	=> __( 'Countdown Timer (widget & [shortcode]) Date Format:', 'mstw-loc-domain' ),
								'default' => '',
								'options' => '',
								'page' => $display_on_page,
								'section' => $page_section,
							),
						array( 	// CUSTOM DATE (ONLY) FORMAT FOR COUNTDOWN TIMER
								'type'    => 'text', 
								'id' => 'custom_cdt_date_format',
								'name'	=> 'mstw_gs_dtg_options[custom_cdt_date_format]',
								'value'	=> $options['custom_cdt_date_format'],
								'desc'	=> __( 'Used when game time is TBA. Enter a PHP date() format string for a custom format. You probably should know what you are doing before selecting the custom option.', 'mstw-loc-domain' ),
								'title'	=> __( 'Countdown Timer (widget & [shortcode]) Custom Date Format:', 'mstw-loc-domain' ),
								'default' => '',
								'options' => '',
								'page' => $display_on_page,
								'section' => $page_section,
							),
						);
		
		foreach ( $arguments as $args ) {
			mstw_gs_build_form_field( $args );
		}	
		
		//----------------------------------------------------------
		// Slider dtg settings section
		//
		$page_section = 'mstw_gs_admin_slider_format_settings';
		
		add_settings_section(
			$page_section,
			__( 'Schedule Slider Formats', 'mstw-loc-domain' ),
			'mstw_gs_slider_dtg_inst',
			$display_on_page
			);
			
		
		$arguments = array(
						array( 	// DATE FORMAT FOR SCHEDULE SLIDER
								'type'    => 'date-only', 
								'id' => 'slider_date_format',
								'name'	=> 'mstw_gs_dtg_options[slider_date_format]',
								'value'	=> $options['slider_date_format'],
								'desc'	=> '',
								'title'	=> __( 'Schedule Slider Date Format:', 'mstw-loc-domain' ),
								'default' => '',
								'options' => '',
								'page' => $display_on_page,
								'section' => $page_section,
							),
						array( 	// CUSTOM DATE FORMAT FOR SCHEDULE SLIDER
								'type'    => 'text', 
								'id' => 'custom_slider_date_format',
								'name'	=> 'mstw_gs_dtg_options[custom_slider_date_format]',
								'value'	=> $options['custom_slider_date_format'],
								'desc'	=> __( 'Enter a PHP date() format string for a custom format. You probably should know what you are doing before selecting the custom option. (Default: "")', 'mstw-loc-domain' ),
								'title'	=>  __( 'Schedule Slider Custom Date Format:', 'mstw-loc-domain' ),
								'default' => '',
								'options' => '',
								'page' => $display_on_page,
								'section' => $page_section,
							),	
						array( 	// TIME FORMAT FOR SCHEDULE SLIDER
								'type'    => 'time-only', 
								'name' => 'mstw_gs_dtg_options[slider_time_format]',
								'id' => 'slider_time_format',
								'value' => $options['slider_time_format'],
								'desc'	=> '',
								'title'	=> __( 'Schedule Slider Time Format:', 'mstw-loc-domain' ),
								'default' => '',
								'options' => '',
								'page' => $display_on_page,
								'section' => $page_section,
							),
						array( 	// CUSTOM TIME FORMAT FOR SCHEDULE SLIDER
								'type'    => 'text', 
								'id' => 'custom_slider_time_format',
								'name'	=> 'mstw_gs_dtg_options[custom_slider_time_format]',
								'value'	=> $options['custom_slider_time_format'],
								'desc'	=> __( 'Enter a PHP date() format string for a custom format. You probably should know what you are doing before selecting the custom option. (Default: "")', 'mstw-loc-domain' ),
								'title'	=>  __( 'Schedule Slider Custom Time Format:', 'mstw-loc-domain' ),
								'default' => '',
								'options' => '',
								'page' => $display_on_page,
								'section' => $page_section,
							),
						);
						
		foreach ( $arguments as $args ) {
			mstw_gs_build_form_field( $args );
		}	
		
	}
	
	function mstw_gs_data_fields_setup( ) {
		// Data fields/columns -- show/hide and labels
		$display_on_page = 'mstw_gs_settings';
		$page_section = 'mstw_gs_fields_columns_settings';
		
		//$options = get_option( 'mstw_gs_options' );
		$options = wp_parse_args( 	get_option( 'mstw_gs_options' ), 
									mstw_gs_get_defaults() 
								);
		
		add_settings_section(
			$page_section,  //id attribute of tags
			__( 'Data Field and Table Column Settings', 'mstw-loc-domain' ),	//title of the section
			'mstw_gs_data_fields_inst',		//callback to fill section with desired output - should echo
			$display_on_page				//menu page slug on which to display
		);
		
		$arguments = array(
						// Show/Hide Date Column
						array( 	// the HTML form element to use
							'type'    => 'show-hide', 
							// the ID of the setting in options array, 
							// and the ID of the HTML form element
							'id' => 'show_date',
							// the label for the HTML form element
							'title'	=> __( 'Show Date Column:', 'mstw-loc-domain' ),
							// the description displayed under the HTML form element
							'desc'	=> __( 'Show or hide the Date field/column. (Default: Show)', 'mstw-loc-domain' ),
							// the default value for this setting
							'default' => '1', //show
							// only used for select-option and ..
							'options' => "",
							// name of HTML form element
							'name'	=> 'mstw_gs_options[show_date]',
							// current value of field
							'value'	=> $options['show_date'],
							// page on which to display HTML control
							'page' => $display_on_page,
							// page section in which to display HTML control
							'section' => $page_section,
							),
							
						// DATE field/column label
						array( 	// the HTML form element to use
							'type'    => 'text', 
							// the ID of the setting in options array, 
							// and the ID of the HTML form element
							'id' => 'date_label',
							// the label for the HTML form element
							'title'	=> __( 'Date Column Label:', 'mstw-loc-domain' ), 
							// the description displayed under the HTML form element
							'desc'	=> __( 'Set the label/title for date data field and/or column. (Default: "Date")', 'mstw-loc-domain' ),
							// the default value for this setting
							'default' => '', 
							// only used for select-option and ..
							'options' => "",
							// name of HTML form element
							'name'	=> 'mstw_gs_options[date_label]',
							// current value of field
							'value'	=> $options['date_label'],
							// page on which to display HTML control
							'page' => $display_on_page,
							// page section in which to display HTML control
							'section' => $page_section,
							),
							
							// OPPONENT field/column label
							array( 	// the HTML form element to use
							'type'    => 'text', 
							// the ID of the setting in options array, 
							// and the ID of the HTML form element
							'id' => 'opponent_label',
							// the label for the HTML form element
							'title'	=>__( 'Opponent Column Label:', 'mstw-loc-domain' ), 
							// the description displayed under the HTML form element
							'desc'	=> __( 'Set label for opponent data field or column. (Default: "Opponent") NOTE: THE OPPONENT FIELD MUST  BE SHOWN.', 'mstw-loc-domain' ),
							// the default value for this setting
							'default' => '', 
							// only used for select-option and ..
							'options' => "",
							// name of HTML form element
							'name'	=> 'mstw_gs_options[opponent_label]',
							// current value of field
							'value'	=> $options['opponent_label'],
							// page on which to display HTML control
							'page' => $display_on_page,
							// page section in which to display HTML control
							'section' => $page_section,
							),
							
						// Show/hide LOCATION column
						array( 	// the HTML form element to use
							'type'    => 'show-hide', 
							// the ID of the setting in options array, 
							// and the ID of the HTML form element
							'id' => 'show_location',
							// the label for the HTML form element
							'title'	=> __( 'Show Location Column:', 'mstw-loc-domain' ),
							// the description displayed under the HTML form element
							'desc'	=> __( 'Show or hide the Location field/column. (Default: Show)', 'mstw-loc-domain' ),
							// the default value for this setting
							'default' => '1', //show
							// only used for select-option and ..
							'options' => "",
							// name of HTML form element
							'name'	=> 'mstw_gs_options[show_location]',
							// current value of field
							'value'	=> $options['show_location'],
							// page on which to display HTML control
							'page' => $display_on_page,
							// page section in which to display HTML control
							'section' => $page_section,
							),
							
							// LOCATION field/column label
							array( 	// the HTML form element to use
							'type'    => 'text', 
							// the ID of the setting in options array, 
							// and the ID of the HTML form element
							'id' => 'location_label',
							// the label for the HTML form element
							'title'	=>__( 'Location Column Label:', 'mstw-loc-domain' ), 
							// the description displayed under the HTML form element
							'desc'	=> __( 'Set label for location data field or column. (Default: "Location")', 'mstw-loc-domain' ),
							// the default value for this setting
							'default' => '', 
							// only used for select-option and ..
							'options' => "",
							// name of HTML form element
							'name'	=> 'mstw_gs_options[location_label]',
							// current value of field
							'value'	=> $options['location_label'],
							// page on which to display HTML control
							'page' => $display_on_page,
							// page section in which to display HTML control
							'section' => $page_section,
							),
							
							// Show/hide TIME/RESULT column
							array( 	// the HTML form element to use
							'type'    => 'show-hide', 
							// the ID of the setting in options array, 
							// and the ID of the HTML form element
							'id' => 'show_time',
							// the label for the HTML form element
							'title'	=> __( 'Show Time/Result Column:', 'mstw-loc-domain' ),
							// the description displayed under the HTML form element
							'desc'	=> __( 'Show or hide the Time/Result field or column. (Default: Show)', 'mstw-loc-domain' ),
							// the default value for this setting
							'default' => '1', //show
							// only used for select-option and ..
							'options' => "",
							// name of HTML form element
							'name'	=> 'mstw_gs_options[show_time]',
							// current value of field
							'value'	=> $options['show_time'],
							// page on which to display HTML control
							'page' => $display_on_page,
							// page section in which to display HTML control
							'section' => $page_section,
							),
							
							// TIME/RESULT field/column label
							array( 	// the HTML form element to use
							'type'    => 'text', 
							// the ID of the setting in options array, 
							// and the ID of the HTML form element
							'id' => 'time_label',
							// the label for the HTML form element
							'title'	=>__( 'Time/Result Column Label:', 'mstw-loc-domain' ), 
							// the description displayed under the HTML form element
							'desc'	=> __( 'Set label for time/result data field or column. (Default: "Time/Result")', 'mstw-loc-domain' ),
							// the default value for this setting
							'default' => '', 
							// only used for select-option and ..
							'options' => "",
							// name of HTML form element
							'name'	=> 'mstw_gs_options[time_label]',
							// current value of field
							'value'	=> $options['time_label'],
							// page on which to display HTML control
							'page' => $display_on_page,
							// page section in which to display HTML control
							'section' => $page_section,
							),
							
							// Show/hide MEDIA column
							array( 	// the HTML form element to use
								'type'    => 'select-option', 
								// the ID of the setting in options array, 
								// and the ID of the HTML form element
								'id' => 'show_media',
								// the label for the HTML form element
								'title'	=> __( 'Show Media Column:', 'mstw-loc-domain' ),
								// the description displayed under the HTML form element
								'desc'	=> __( 'Show a number of media fields (1-3) or hide the Media field or column. (Default: Show all 3)', 'mstw-loc-domain' ),
								// the default value for this setting
								'default' => '',
								// only used for select-option and ..
								'options' => array( 'Hide' => 0,
													'Show 1' => 1,
													'Show 2' => 2,
													'Show 3' => 3,
													),
								// name of HTML form element
								'name'	=> 'mstw_gs_options[show_media]',
								// current value of field
								'value'	=> $options['show_media'],
								// page on which to display HTML control
								'page' => $display_on_page,
								// page section in which to display HTML control
								'section' => $page_section,
							),
							
							// MEDIA field/column label
							array( 	// the HTML form element to use
							'type'    => 'text', 
							// the ID of the setting in options array, 
							// and the ID of the HTML form element
							'id' => 'media_label',
							// the label for the HTML form element
							'title'	=>__( 'Media Column Label:', 'mstw-loc-domain' ), 
							// the description displayed under the HTML form element
							'desc'	=> __( 'Set label for media data field or column. (Default: "Media Links")', 'mstw-loc-domain' ),
							// the default value for this setting
							'default' => '', 
							// only used for select-option and ..
							'options' => "",
							// name of HTML form element
							'name'	=> 'mstw_gs_options[media_label]',
							// current value of field
							'value'	=> $options['media_label'],
							// page on which to display HTML control
							'page' => $display_on_page,
							// page section in which to display HTML control
							'section' => $page_section,
							),
							
						);
						
		foreach ( $arguments as $args ) {
			mstw_gs_build_form_field( $args );
		}

		//---------------------------------------------------------------
		// TEAM LOGOS SECTION
		//
		$page_section = 'mstw-gs-team-logos-section';
		
		add_settings_section(
			$page_section,	//id attribute of tags
			__( 'MSTW Team Database Settings', 'mstw-loc-domain' ),	//title of the section
			'mstw_gs_team_logo_inst',	//callback to fill section with desired output - should echo
			$display_on_page	//menu page slug on which to display
		);

		// Opponent name format
		$arguments = array (
						// OPPONENT NAME FORMAT FOR SCHEDULE TABLES
						array( 	// the HTML form element to use
								'type'    => 'select-option', 
								// the ID of the setting in options array, 
								// and the ID of the HTML form element
								'id' => 'table_opponent_format',
								// the label for the HTML form element
								'title'	=> __( 'Opponent Name Format in Schedule TABLES:', 'mstw-loc-domain' ),
								// the description displayed under the HTML form element
								'desc'	=> __( '(Default: Full Name Only)', 'mstw-loc-domain' ),
								// the default value for this setting
								'default' => '',
								// only used for select-option and ..
								'options' => array( __( 'Short Name Only', 'mstw-loc-domain' ) => 'short-name',
													__( 'Full Name Only', 'mstw-loc-domain' ) => 'full-name',
													__( 'Short Name & Mascot', 'mstw-loc-domain' ) => 'short-name-mascot',
													__( 'Full Name & Mascot', 'mstw-loc-domain' ) => 'full-name-mascot',
													),
								// name of HTML form element
								'name'	=> 'mstw_gs_options[table_opponent_format]',
								// current value of field
								'value'	=> $options['table_opponent_format'],
								// page on which to display HTML control
								'page' => $display_on_page,
								// page section in which to display HTML control
								'section' => $page_section,
							),
						// OPPONENT NAME FORMAT FOR SLIDERS
						array( 	// the HTML form element to use
								'type'    => 'select-option', 
								// the ID of the setting in options array, 
								// and the ID of the HTML form element
								'id' => 'slider_opponent_format',
								// the label for the HTML form element
								'title'	=> __( 'Opponent Name Format in Schedule SLIDERS:', 'mstw-loc-domain' ),
								// the description displayed under the HTML form element
								'desc'	=> __( '(Default: Full Name Only)', 'mstw-loc-domain' ),
								// the default value for this setting
								'default' => '',
								// only used for select-option and ..
								'options' => array( __( 'Short Name Only', 'mstw-loc-domain' ) => 'short-name',
													__( 'Full Name Only', 'mstw-loc-domain' ) => 'full-name',
													__( 'Short Name & Mascot', 'mstw-loc-domain' ) => 'short-name-mascot',
													__( 'Full Name & Mascot', 'mstw-loc-domain' ) => 'full-name-mascot',
													),
								// name of HTML form element
								'name'	=> 'mstw_gs_options[slider_opponent_format]',
								// current value of field
								'value'	=> $options['slider_opponent_format'],
								// page on which to display HTML control
								'page' => $display_on_page,
								// page section in which to display HTML control
								'section' => $page_section,
							),
						// SHOW/HIDE LOGO IN SCHEDULE TABLES
						array( 	'type'    => 'select-option',
								'id' => 'show_table_logos',
								'name'	=> 'mstw_gs_options[show_table_logos]',
								'value'	=> $options['show_table_logos'],
								'desc'	=> __( 'NOTE: this setting only applies if scheduled opponents are selected from the MSTW Teams database. . (Default: Show Name Only)', 'mstw-loc-domain' ),
								'title'	=> __( 'Show Team Logos in Schedule TABLES:', 'mstw-loc-domain' ),
								'default' => '',
								'options' => array( __( 'Show Name Only', 'mstw-loc-domain' ) => 'name-only',
													__( 'Show Logo & Name', 'mstw-loc-domain' ) => 'logo-name',
													__( 'Show Logo Only', 'mstw-loc-domain' ) => 'logo-only',
													),
								'page' => $display_on_page,
								'section' => $page_section,
							),
						// SHOW/HIDE LOGO IN SCHEDULE SLIDERS
						array( 	'type'    => 'select-option',
								'id' => 'show_slider_logos',
								'name'	=> 'mstw_gs_options[show_slider_logos]',
								'value'	=> $options['show_slider_logos'],
								'desc'	=> __( 'NOTE: this setting only applies if scheduled opponents are selected from the MSTW Teams database. (Default: Hide-Show Name Only)', 'mstw-loc-domain' ),
								'title'	=> __( 'Show Team Logos in Schedule SLIDERS:', 'mstw-loc-domain' ),
								'default' => '',
								'options' => array( __( 'Show Name Only', 'mstw-loc-domain' ) => 'name-only',
													__( 'Show Logo & Name', 'mstw-loc-domain' ) => 'logo-name',
													__( 'Show Logo Only', 'mstw-loc-domain' ) => 'logo-only',
													),
								'page' => $display_on_page,
								'section' => $page_section,
							),
						// FORMAT LOCATION
						array( 	'type'    => 'select-option',
								'id' => 'venue_format',
								'name'	=> 'mstw_gs_options[venue_format]',
								'value'	=> $options['venue_format'],
								'desc'	=> __( 'NOTE: this setting only applies if scheduled opponents are selected from the MSTW Teams database. (Default: Name Only)', 'mstw-loc-domain' ),
								'title'	=> __( 'Format for Location:', 'mstw-loc-domain' ),
								'default' => '',
								'options' => array( 
												__( 'Show Name Only', 'mstw-loc-domain' ) => 'name-only',
												__( 'Show City, State (Name)', 'mstw-loc-domain' ) => 'city-state-name',
											),
								'page' => $display_on_page,
								'section' => $page_section,
							),
						// LINK FROM LOCATION
						array( 	'type'    => 'select-option',
								'id' => 'venue_link_format',
								'name'	=> 'mstw_gs_options[venue_link_format]',
								'value'	=> $options['venue_link_format'],
								'desc'	=> __( 'NOTE: this setting only applies if scheduled opponents are selected from the MSTW Teams database. (Default: No Link)', 'mstw-loc-domain' ),
								'title'	=> __( 'Link from Location:', 'mstw-loc-domain' ),
								'default' => '',
								'options' => array( 
												__( 'No Link', 'mstw-loc-domain' ) => 'no-link',
												__( 'Link to Venue URL', 'mstw-loc-domain' ) => 'link-to-venue',
												__( 'Link to Map URL', 'mstw-loc-domain' ) => 'link-to-map',
											),
								'page' => $display_on_page,
								'section' => $page_section,
							),
					);
		
		foreach ( $arguments as $args ) {
			mstw_gs_build_form_field( $args );
		}			
	
	} //End of data_fields_setup()
	
	//-----------------------------------------------------------------	
	// 	Main section instructions and controls	
	// 	
	function mstw_gs_data_fields_inst( ) {
		echo '<p>' . __( 'Settings to control the visibility of data fields & table columns as well as to change their labels to "re-purpose" the fields. ', 'mstw-loc-domain' ) .'</p>';
		/* Just in case we add some colors someday
		'<br/>' . __( 'All color values are in hex, starting with a hash(#), followed by either 3 or 6 hex digits. For example, #123abd or #1a2.', 'mstw-loc-domain' ) .  '</p>';
		*/
	}

	//----------------------------------------------------------------------	
	// 	Date-time format sections instructions
	// 
	function mstw_gs_admin_dtg_inst( ) {
		echo '<p>' . __( 'Enter the date-time formats for the admin pages. ', 'mstw-loc-domain' ) . '<br>' .  __( "NOTE that if 'Custom' is selected as the format a valid PHP date() format string must be entered in the corresponding format text field.", 'mstw-loc-domain' ) . '</p>';;
	}
	
	function mstw_gs_table_dtg_inst( ) {
		echo '<p>' . __( 'Enter the date-time formats for the schedule tables [shortcodes] and widgets. ', 'mstw-loc-domain' ) . '<br>' .  __( "NOTE that if 'Custom' is selected as the format a valid PHP date() format string must be entered in the corresponding format text field.", 'mstw-loc-domain' ) . '</p>';;
	}
	
	function mstw_gs_cdt_dtg_inst( ) {
		echo '<p>' . __( 'Enter the date-time formats for the countdown timer [shortcode] & widgets. ', 'mstw-loc-domain' ) . '<br>' .  __( "NOTE that if 'Custom' is selected as the format a valid PHP date() format string must be entered in the corresponding format text field.", 'mstw-loc-domain' ) . '</p>';;
	}
	
	function mstw_gs_slider_dtg_inst( ) {
		echo '<p>' . __( 'Enter the date-time formats for the schedule slider [shortcode]', 'mstw-loc-domain' ) . '<br>' .  __( "NOTE that if 'Custom' is selected as the format a valid PHP date() format string must be entered in the corresponding format text field.", 'mstw-loc-domain' ) . '</p>';;
	}
	
	// ----------------------------------------------------------------	
	//	Validate user input (we want text only)
	//
	function mstw_gs_validate_options( $input ) {
		// Create our array for storing the validated options
		$output = array();
		
		// Pull the previous (last good) options
		$options = get_option( 'mstw_gs_options' );
		
		// This handles the RESET button
		if ( array_key_exists( 'reset', $input ) ) {
			if ( $input['reset'] ) {
					$output = mstw_gs_get_admin_defaults( );
					return $output;
			}
		}
		
		// Loop through each of the incoming options
		foreach( $input as $key => $value ) {
			// Check to see if the current option has a value. If so, process it.
			if( isset( $input[$key] ) ) {
				switch ( $key ) {
					// The default format is assumed text
					
					// Add the numbers
					// No numbers
					
					// Add the urls
					// None
					
					// Add the hexcolors
					// Now handled by mstw_gs_validate_color_options()
					
					default:
						// Insure "safe" text
						$output[$key] = sanitize_text_field( $input[$key] );
						break;
					
				} // end switch
			} // end if
		} // end foreach
		
		// Return the array processing any additional functions filtered by this action
		return apply_filters( 'mstw_gs_sanitize_options', $output, $input );
		//return $output;
	}	

	// ----------------------------------------------------------------	
	//	Validate user dtg settings input
	//	
	function mstw_gs_validate_dtg_options( $input ) {
		// Create our array for storing the validated options
		$output = array();
		
		// This handles the RESET button
		if ( array_key_exists( 'reset', $input ) ) {
			if ( $input['reset'] ) {
					//echo "<pre>input: <br/>" . print_r( $input, true ) . "</pre>";
					$output = mstw_gs_get_dtg_defaults( );
					//echo "<pre>output: <br/>" . print_r( $output, true ) . "</pre>";
					//die();
					return $output;
			}
		}
		
		//echo "<pre>input: <br/>" . print_r( $input, true ) . "</pre>";
		
		// Pull the previous (last good) options
		$options = get_option( 'mstw_gs_dtg_options' );
		
		// Loop through each of the incoming options
		foreach( $input as $key => $value ) {
			// Check to see if the current option has a value. If so, process it.
			if( isset( $input[$key] ) ) {
				$output[$key] = sanitize_text_field( $input[$key] );
			} // end if
		} // end foreach
		
		// Return the array processing any additional functions filtered by this action
		return apply_filters( 'mstw_gs_sanitize_dtg_options', $output, $input );
		//return $output;
	}
	
	// ----------------------------------------------------------------	
	//	Validate user color settings input
	// 
	function mstw_gs_validate_color_options( $input ) {
		// Create our array for storing the validated options
		$output = array();
		
		// This handles the RESET button
		if ( array_key_exists( 'reset', $input ) ) {
			if ( $input['reset'] ) {
					//echo "<pre>input: <br/>" . print_r( $input, true ) . "</pre>";
					$output = mstw_gs_get_color_defaults( );
					//echo "<pre>output: <br/>" . print_r( $output, true ) . "</pre>";
					//die();
					return $output;
			}
		}
		
		//die( "<pre>input: <br/>" . print_r( $input, true ) . "</pre>" );
		
		// Pull the previous (last good) options
		$options = get_option( 'mstw_gs_color_options' );
		
		// Loop through each of the incoming options
		foreach( $input as $key => $value ) {
			// Check to see if the current option has a value. If so, process it.
			if( isset( $input[$key] ) ) {
				// validate the color for proper hex format
				// there should NEVER be a problem; js color selector should error check
				$sanitized_color = mstw_gs_sanitize_hex_color( $input[$key] );
				
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
			} // end if
		} // end foreach
		
		// Return the array processing any additional functions filtered by this action
		return apply_filters( 'mstw_gs_sanitize_color_options', $output, $input );
	}	
	
?>