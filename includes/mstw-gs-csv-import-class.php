<?php
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
					<table class='form-table'>				
						<tr>  <!-- Team ID input field -->
							<td><label for="opt_sched_id">Select a team/schedule (ID) to input:</label></td>
							<td><input size="8" name="csv_importer_sched_id" id="opt_sched_id" type="text" value="<?php echo esc_attr( $opt_sched_id ); ?>"/>
							<br/><span class='description'>Use an existing team ID or a new one. Team ID will be created if it does not exist.</span></td>
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