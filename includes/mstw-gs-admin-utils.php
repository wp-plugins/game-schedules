<?php
/*
 * mstw-gs-admin-utils.php
 * 	Admin utility functions for MSTW Game Schedules Plugin
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
 *	MSTW-GS-ADMIN-UTILS	
 *	0. 	mstw_gs_admin_utils_loaded() - indicator that the file is loaded 
 *	1. 	mstw_gs_color_ctrl() - 
 *	2. 	mstw_gs_text_ctrl() -
 *	3. 	mstw_gs_checkbox_ctrl() - 
 *  4. 	mstw_gs_show_hide_ctrl() - 
 *  5. 	mstw_gs_select_option_ctrl() - 
 *  6. 	mstw_gs_date_format_ctrl() -
 *  7. 	mstw_gs_time_format_ctrl() -
 *  8. 	mstw_gs_sanitize_hex_color() - 
 *  9. 	mstw_gs_build_form_field() - helper function for registering admin form field settings
 *  10.	mstw_gs_display_form_field() - helper function for building HTML for all admin form fields
 *  11. mstw_gs_get_admin_defaults() - admin defaults differ from front side defaults
 *  12. mstw_gs_admin_safe_ref() - stops errors with using undefined associative array references

 *---------------------------------------------------------*/
 
 /*----------------------------------------------------------------	
 *	0. MSTW_ADMIN_UTILS_LOADED - DO NOT REMOVE THIS FUNCTION!
 *		This function is used by require_once statements to figure
 *		out whether or not to load the utils.
 *---------------------------------------------------------------*/
	function mstw_gs_admin_utils_loaded( ) {
		return( true );
	}

 /*----------------------------------------------------------------	
 *	1. MSTW_GS_COLOR_CTRL
 *	Builds color selector controls for the admin UI
 *
 * 	Arguments:
 *		$args['id'] 	(string) ID of input field 
 *		$args['name'] 	(string) Name of input field
 *		$args['class'] 	(string) Name of input field
 *		$args['value'] 	(string) Current value of input field
 *		$args['label'] 	(string) Instructions displayed after the field
 *
 *	return - none. Control is displayed.
 *---------------------------------------------------------------*/
	function mstw_gs_color_ctrl( $args ) { 
		$id = $args['id'];
		$name = $args['name'];
		//$class = $args['class'];
		$value = $args['value'];
		$label = $args['label'];
		
		echo "<input type='text' id='$id' name='$name' value='$value' /> \n";
		//echo "<label for='$id'>$label</label> \n";

	} //End: mstw_gs_color_ctrl
 
 //----------------------------------------------------------------	
 //	2. MSTW_GS_TEXT_CTRL
 //		Builds text format controls for the admin UI
 //
 // 	Arguments:
 //		$args['id'] 	(string) ID of input field 
 //		$args['name'] 	(string) Name of input field
 //		$args['value'] 	(string) Current value of input field
 //		$args['label'] 	(string) Instructions displayed after the field
 //
 //	return - none. Control is displayed.
 //-----------------------------------------------------------------
	function mstw_gs_text_ctrl( $args ) { 
		$id = $args['id'];
		$name = $args['name'];
		$value = $args['value'];
		$label = $args['label'];
		
		echo "<input type='text' id='$id' name='$name' value='$value' /> \n";
		echo "<label for='$id'>$label</label> \n";
		
	} //End: mstw_gs_text_ctrl
	
/*----------------------------------------------------------------	
 *	3. MSTW_GS_CHECKBOX_CTRL
 *		Builds checkbox format controls for the admin UI
 *
 * 	Arguments:
 *		$args['id'] 	(string) ID of input field 
 *		$args['name'] 	(string) Name of input field
 *		$args['value'] 	(string) Current value of input field
 *		$args['label'] 	(string) Instructions displayed after the field
 *
 *	NOTE that the checked value is always '1'.
 *
 *	Return - none. Control is displayed.
 *---------------------------------------------------------------*/
	/*
	function mstw_gs_checkbox_ctrl( $args ) { 
		$id = 		$args['id'];
		$name = 	$args['name'];
		$value = 	$args['value'];
		$label = 	$args['label'];
		$checked =	$args['checked'];
		
		echo "<input type='checkbox' id='$id' name='$name' value='$value' " . 
				checked( $checked, $value, false ) . "/> \n";  
		echo "<label for='$id'>$label</label> \n";
		
	}	//End: mstw_gs_checkbox_ctrl
	*/
	
//----------------------------------------------------------------	
// 4. MSTW_GS_SHOW_HIDE_CTRL
//		Builds 'Show-Hide' controls for the admin UI
//		Calls mstw_gs_select_option_ctrl() wiht Show => 1, Hide => 0
//
// Arguments: 
//	 $args['id'] (string)		Setting name from option array
//	 $args['name'] (string)		Name of input field
//	 $args['value'] (string)	Current value of setting
//	 $args['label'] (string)	Default to use of setting is blank
//
//	Return - none. Output is echoed.
//-----------------------------------------------------------------	
	function mstw_gs_show_hide_ctrl( $args ) {
	
		$new_args = array(	'options' => array(	__( 'Show', 'mstw-loc-domain' ) => 1, 
												__( 'Hide', 'mstw-loc-domain' ) => 0, 
												),
						'id' => $args['id'],
						'name' => $args['name'],
						'value' => $args['value'],
						'label' => $args['label']
						);
		
		mstw_gs_select_option_ctrl( $new_args );
		
	}  //End: mstw_gs_show_hide_ctrl
	
//----------------------------------------------------------------	
// 5. MSTW_GS_SELECT_OPTION_CTRL
//		Builds Select-Option controls for the admin UI
//
// 	Arguments:
//	 $args['options'] (array)	Key/value pairs for the options 
//	 $args['id'] (string)		Setting name from option array
//	 $args['name'] (string)		Name of input field
//	 $args['value'] (string)	Current value of setting
//	 $args['label'] (string)	Default to use of setting is blank
//
//	Return - none. Output is echoed.
//-----------------------------------------------------------------
	function mstw_gs_select_option_ctrl( $args ) {
		
		$options = $args['options'];
		$name = $args['name'];
		$id = $args['id'];
		$curr_value = $args['value'];
		$label = $args['label'];
		
		echo "<select id='$id' name='$name' style='width: 160px' >";
		foreach( $options as $key=>$value ) {
			//echo '<p> key: ' . $key . ' value: ' . $value .'</p>';
			$selected = ( $curr_value == $value ) ? 'selected="selected"' : '';
			echo "<option value='$value' $selected>$key</option>";
		}
		echo "</select> \n";
		//echo "<label for='$id'>". $label . "</label> \n";
		echo "<label for='$id'>$label</label> \n";
		
	}  //End: mstw_gs_select_option_ctrl

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
	function mstw_gs_date_format_ctrl( $args ) {
	
		//extract( $args );
		$name = $args['name'];
		$id = $args['id'];
		$curr_value = $args['curr_value'];
		$dtg = $args['dtg'];
		
		//echo '<pre>'; print_r( $args ); echo '</pre>';

		$mstw_gs_date_formats = array ( 
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
			__( 'Tuesday, 07 Apr', 'mstw-loc-domain' ) => 'l, d M',
			__( 'Tues, 7 Apr', 'mstw-loc-domain' ) => 'D, j M',
			__( 'Tuesday, 7 Apr', 'mstw-loc-domain' ) => 'l, j M',
			__( '07 Apr', 'mstw-loc-domain' ) => 'd M',
			__( '7 Apr', 'mstw-loc-domain' ) => 'j M',
			);
			
		$mstw_gs_date_time_formats = array ( 
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
			$loop_array = $mstw_gs_date_time_formats;
			$label = __( 'Formats for', 'mstw-loc-domain' ) . " " . __( '7 April 2013 13:15', 'mstw-loc-domain' );
		}
		else {
			$loop_array = $mstw_gs_date_formats;
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
	function mstw_gs_time_format_ctrl( $args ) {
		$mstw_gs_time_formats = array ( 
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
		
		$loop_array = $mstw_gs_time_formats;
		$label = __( 'Formats for eight in the morning', 'mstw-loc-domain' ) . " 08:00";
		
		echo "<select id='$id' name='$name' style='width: 160px' >";
		foreach( $loop_array as $key=>$value ) {
			$selected = ( $curr_value == $value ) ? 'selected="selected"' : '';
			echo "<option value='$value' $selected>$key</option>";
		}
		echo "</select> \n";
		echo "<label for='$id'>$label</label> \n";	
	}
	
/*----------------------------------------------------------------	
 *	Sanitization Functions
 *---------------------------------------------------------------*/	
	function mstw_gs_sanitize_hex_color( $color ) {
		// Check $color for proper hex color format (3 or 6 digits) or the empty string.
		// Returns corrected string if valid hex color, returns null otherwise
		
		if ( '' === $color )
			return '';

		// 3 or 6 hex digits, or the empty string.
		if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) )
			return $color;

		return null;
	}
	
//----------------------------------------------------------------	
// 9. MSTW_GS_BUILD_FORM_FIELD
//		Helper function for registering admin form field settings
//----------------------------------------------------------------
function mstw_gs_build_form_field( $args ) {
	// default array to overwrite when calling the function
	
	$defaults = array(
		'id'      => 'default_field', // the ID of the setting in our options array, and the ID of the HTML form element
		'title'   => 'Default Field',  // the label for the HTML form element
		'desc'    => '', // the description displayed under the HTML form element
		'default'     => '',  // the default value for this setting
		'type'    => 'text', // the HTML form element to use
		'section' => '', // settings section to which this setting belongs
		'page' => '', //page on which the section belongs
		'options' => array(), // (optional): the values in radio buttons or a drop-down menu
		'name' => '', //name of HTML form element. should be options_array[option]
		'class'   => '',  // the HTML form element class. Also used for validation purposes!
		'value' => ''  // the current value of the setting
	);
	
	// "extract" to be able to use the array keys as variables in our function output below
	extract( wp_parse_args( $args, $defaults ) );
	//extract( $args );
	
	switch ( $type ) {
		case 'show-hide':
			$type = 'select-option';
			$options = array(	__( 'Show', 'mstw-loc-domain' ) => 1, 
								__( 'Hide', 'mstw-loc-domain' ) => 0, 
							  );
			break;
		case 'date-time':
			$type = 'select-option';
			$options = array ( 	__( 'Custom', 'mstw-loc-domain' ) => 'custom',
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
			if ( $desc == '' ) {
				$desc = __( 'Formats for 7 April 2013 13:15.', 'mstw-loc-domain' );
			}
			break;
		case 'date-only':
			$type = 'select-option';
			$options = array ( 	__( 'Custom', 'mstw-loc-domain' ) => 'custom',
								'2013-04-07' => 'Y-m-d',
								'13-04-07' => 'y-m-d',
								'04/07/13' => 'm/d/y',
								'4/7/13' => 'n/j/y',
								__( '07 Apr 2013', 'mstw-loc-domain' ) => 'd M Y',
								__( '7 Apr 2013', 'mstw-loc-domain' ) => 'j M Y',
								__( 'Tues, 07 Apr 2013', 'mstw-loc-domain' ) => 'D, d M Y',
								__( 'Tues, 7 Apr 13', 'mstw-loc-domain' ) => 'D, j M y',
								__( 'Tuesday, 7 Apr', 'mstw-loc-domain' ) => 'l, j M',
								__( 'Tuesday, 07 April 2013', 'mstw-loc-domain' ) => 'l, d F Y',
								__( 'Tuesday, 7 April 2013', 'mstw-loc-domain' ) => 'l, j F Y',
								__( 'Tues, 07 Apr', 'mstw-loc-domain' ) => 'D, d M',
								__( 'Tues, 7 Apr', 'mstw-loc-domain' ) => 'D, j M',
								__( '07 Apr', 'mstw-loc-domain' ) => 'd M',
								__( '7 Apr', 'mstw-loc-domain' ) => 'j M',
								);
			if ( $desc == '' ) {
				$desc = __( 'Formats for 7 Apr 2013. Default: 2013-04-07', 'mstw-loc-domain' );
			}
			break;
		case 'time-only':
			$type = 'select-option';
			$options = array ( 	__( 'Custom', 'mstw-loc-domain' ) 		=> 'custom',
								__( '08:00 (24hr)', 'mstw-loc-domain' ) => 'H:i',
								__( '8:00 (24hr)', 'mstw-loc-domain' ) 	=> 'G:i',
								__( '08:00 am', 'mstw-loc-domain' ) 	=> 'h:i a',
								__( '08:00 AM', 'mstw-loc-domain' ) 	=> 'h:i A',
								__( '8:00 am', 'mstw-loc-domain' ) 		=> 'g:i a',
								__( '8:00 AM', 'mstw-loc-domain' ) 		=> 'g:i A',
								);
			if ( $desc == '' ) {
				$desc = __( 'Formats for eight in the morning. Default: 08:00', 'mstw-loc-domain' );
			}
			break;
		default:
			break;
							
	}
	// additional arguments for use in form field output in the function mstw_gs_display_form_field()
	
	$field_args = array(
		'type'       => $type,
		'id'         => $id,
		'desc'       => $desc,
		'curr_value' => $value,
		'options'    => $options,
		'label_for'  => $id,
		'class'      => $class,
		'name'		 => $name,
	);
	
	//echo '<p>Build form field</p>';
	//return;

	add_settings_field( $id, 
						$title, 
						'mstw_gs_display_form_field', 
						$page, 
						$section, 
						$field_args 
						);
}

//----------------------------------------------------------------
// 10. MSTW_GS_DISPLAY_FORM_FIELD	
// 		Helper function for building HTML for all admin form fields
// 		Callback for add_settings_field() in mstw_gs_build_ctrl()
//		Echoes output
//----------------------------------------------------------------

	function mstw_gs_display_form_field( $args ) {
		extract( $args );
		// current value will be $curr_value NOT $value
		
		
		//echo '<pre>'; print_r( $args ); echo '</pre>';
		//echo '<pre>'; print_r( $options ); echo '</pre>';
		//echo '<pre>'; print_r( $args['options'] ); echo '</pre>';
		//return;
		
		
		// pass the standard value if the option is not yet set in the database
		if ( !isset( $options[$id] ) && 'type' != 'checkbox' ) {
			$options[$id] = ( isset( $default ) ? $default : 'default_field' );
		}
		
		// Additional field class. Output only if the class is defined in the $args()
		$field_class = ($class != '') ? ' ' . $class : '';
		
		
		// switch html display based on the setting type.
		switch ( $type ) {
		
			//TEXT CONTROL
			case 'text':
			case 'color':
				//this conditional keeps echo'ed markup tidy 
				$class_str = ( $field_class == '' ) ? '' : "class='text$field_class'";
				
				echo "<input $class_str type='text' id='$id' name='$name' value='$curr_value' /> \n";
				
				echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";
				
			break;
			
			//SELECT OPTION CONTROL
			case 'select-option':
				//not sure why this is needed given the extract() above
				//but without it you get an extra option with the 
				//'option-name' displayed (huh??)
				$options = $args['options'];
				//this conditional keeps echo'ed markup tidy 
				$class_str = ( $field_class == '' ) ? '' : "class='select$field_class'";
				
				echo "<select id='$id' $class_str name='$name'>";
					foreach( $options as $key=>$value ) {
						//echo '<p> key: ' . $key . ' value: ' . $value .'</p>';
						$selected = ( $curr_value == $value ) ? 'selected="selected"' : '';
						echo "<option value='$value' $selected>$key</option>";
					}
				echo "</select>";
				
				echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";
				
			break;

	//-----------------------------------------------------------------------------
	// THE FOLLOWING CASES HAVE NOT BEEN TESTED/USED
			
			case "multi-text":
				foreach($options as $item) {
					$item = explode("|",$item); // cat_name|cat_slug
					$item[0] = esc_html__($item[0], 'wptuts_textdomain');
					if (!empty($options[$id])) {
						foreach ($options[$id] as $option_key => $option_val){
							if ($item[1] == $option_key) {
								$value = $option_val;
							}
						}
					} else {
						$value = '';
					}
					echo "<span>$item[0]:</span> <input class='$field_class' type='text' id='$id|$item[1]' name='" . $wptuts_option_name . "[$id|$item[1]]' value='$value' /><br/>";
				}
				echo ($desc != '') ? "<span class='description'>$desc</span>" : "";
			break;
			
			case 'textarea':
				$options[$id] = stripslashes($options[$id]);
				$options[$id] = esc_html( $options[$id]);
				echo "<textarea class='textarea$field_class' type='text' id='$id' name='" . $wptuts_option_name . "[$id]' rows='5' cols='30'>$options[$id]</textarea>";
				echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : ""; 		
			break;

			case 'select2':
				echo "<select id='$id' class='select$field_class' name='" . $wptuts_option_name . "[$id]'>";
				foreach($options as $item) {
					
					$item = explode("|",$item);
					$item[0] = esc_html($item[0], 'wptuts_textdomain');
					
					$selected = ($options[$id]==$item[1]) ? 'selected="selected"' : '';
					echo "<option value='$item[1]' $selected>$item[0]</option>";
				}
				echo "</select>";
				echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";
			break;

			case 'checkbox':
				echo "<input class='checkbox$field_class' type='checkbox' id='$id' name='$name' value='$value' " . checked( $value, 1, false ) . " />";
				echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";
			break;

			case "multi-checkbox":
				foreach($options as $item) {
					
					$item = explode("|",$item);
					$item[0] = esc_html($item[0], 'wptuts_textdomain');
					
					$checked = '';
					
					if ( isset($options[$id][$item[1]]) ) {
						if ( $options[$id][$item[1]] == 'true') {
							$checked = 'checked="checked"';
						}
					}
					
					echo "<input class='checkbox$field_class' type='checkbox' id='$id|$item[1]' name='" . $wptuts_option_name . "[$id|$item[1]]' value='1' $checked /> $item[0] <br/>";
				}
				echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";
			break;
			
			default:
				echo "CONTROL TYPE $type NOT RECOGNIZED.";
			break;
			
		}
		
	}

//----------------------------------------------------------------
// 11. MSTW_GS_GET_ADMIN_DEFAULTS	
// 		Helper function resetting admin mstw_gs_options[]
// 		Differs from mstw_gs_get_options() because many shortcode args
//			are not needed; especially the labels
//----------------------------------------------------------------	

	function mstw_gs_get_admin_defaults( ) {
		//Base defaults
		$defaults = array(			
				//show/hide date fields (default labels are blank)
				'show_date'				=> 1,
				'date_label'			=> '',
				'opponent_label'		=> '',
				'show_location'			=> 1,
				'location_label'		=> '',
				'show_time'				=> 1,
				'time_label'			=> '',
				'show_media'			=> 3,
				'media_label'			=> '',
				
				'table_opponent_format'	=> 'full-name',
				'slider_opponent_format'	=> 'full-name',
				'show_table_logos'		=> 'name-only', //Hide Logos
				'show_slider_logos'		=> 'name-only', //Hide Logos
				'venue_format'			=> 'city-name-state', //Show (location) name only
				'venue_link_format'		=> 'no-link', //No Link
				);
				
		return $defaults;
	}
?>
