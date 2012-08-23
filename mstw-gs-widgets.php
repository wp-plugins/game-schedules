<?php
/*--------------------------------------------------------------------
 * Game Schedule Widgets [file mstw-gs-widgets.php]
 *	- mstw_gs_sched_widget
 *	- mstw_gs_countdown_widget
 *------------------------------------------------------------------*/

/*--------------------------------------------------------------------
 *
 * mstw_gs_sched_widget
 *	- displays a simple schedule (table) with date and opponent columns
 *
 *------------------------------------------------------------------*/

//mstw_gs_sched_widget class
class mstw_gs_sched_widget extends WP_Widget {

    //process the new widget
    function mstw_gs_sched_widget() {
        $widget_ops = array( 
			'classname' => 'mstw_gs_sched_widget_class', 
			'description' => 'Display a team schedule.' 
			); 
        $this->WP_Widget( 'mstw_gs_sched_widget', 'Game Schedule Widget', $widget_ops );
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
		$sched_yr = $instance['sched_yr'];
		
        ?>
        <p>Schedule Title: <input class="widefat" name="<?php echo $this->get_field_name( 'sched_title' ); ?>"  
            					type="text" value="<?php echo esc_attr( $sched_title ); ?>" /></p>
        <p>Schedule ID: <input class="widefat" name="<?php echo $this->get_field_name( 'sched_id' ); ?>"  
        						type="text" value="<?php echo esc_attr( $sched_id ); ?>" /></p>
        <p>Sched Year: <input class="widefat" name="<?php echo $this->get_field_name( 'sched_yr' ); ?>"
        						type="text" value="<?php echo esc_attr( $sched_yr ); ?>" /></p>
            
        <?php
    }
 
    //save the widget settings
    function update($new_instance, $old_instance) {
		
        $instance = $old_instance;
		
		$instance['sched_title'] = strip_tags( $new_instance['sched_title'] );
		$instance['sched_id'] = strip_tags( $new_instance['sched_id'] );
		$instance['sched_yr'] = strip_tags( $new_instance['sched_yr'] );
 
        return $instance;
    }
 
/*   //display the widget
    function widget($args, $instance) {
        extract($args);
 
        echo $before_widget;
		
        $title = apply_filters( 'widget_title', $instance['sched_title'] );
        $sched_id = empty( $instance['sched_id'] ) ? '&nbsp;' : $instance['sched_id'];
        $sched_yr = empty( $instance['sched_yr'] ) ? '&nbsp;' : $instance['sched_yr']; 
 
        if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
        echo '<p>Schedule ID: ' . $sched_id . '</p>';
        echo '<p>Schedule Year: ' . $sched_yr . '</p>';
		
        echo $after_widget;
    }
}
*/

/*--------------------------------------------------------------------
 * displays the widget
 *------------------------------------------------------------------*/	
	function widget( $args, $instance ) {
		// $args holds the global theme variables, such as $before_widget
		extract( $args );
		
		echo $before_widget;
		
		$title = apply_filters( 'widget_title', $instance['sched_title'] );
		
		// Get the parameters for get_posts() below
		$sched_id = $instance['sched_id'];
		$sched_yr = $instance['sched_yr'];
		
		// show the widget title, if there is one
		if( !empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}
		
		// Get the game posts for $sched_id and $sched_yr
		$posts = get_posts(array( 'numberposts' => -1,
							  	  'post_type' => 'scheduled_games',
							  	  'meta_query' => array(
												array(
													'key' => '_mstw_gs_sched_id', //**
													'value' => $sched_id,
													'compare' => '='
												),
												array(
													'key' => '_mstw_gs_sched_year', //**
													'value' => $sched_yr,
													'compare' => '='
												)
											),						  
							  	  'orderby' => 'meta_value', 
							  	  'meta_key' => '_mstw_gs_unix_date',
							      'order' => 'ASC' 
							));						
	
   	 	// Make table of posts
		if($posts) {
			$mstw_gs_sw_dtg_format = 'd M'; //format for the date column
					
			// Start with the table header
        	$output = ''; ?>
        
        	<table class="mstw-gs-sw-tab">
        	<thead class="mstw-gs-sw-tab-head"><tr>
            	<th>Date</th>
            	<th>Opponent</th>	
			</tr></thead>
        
			<?php
			// WILL WANT TO SET UP THE ROW BASED ON THE HOME GAME ENTRY
			// Loop through the posts and make the rows
			$even_and_odd = array('even', 'odd');
			$row_cnt = 1; // Keeps track of even and odd rows. Start with row 1 = odd.
		
			foreach( $posts as $post ) {
				// set up some housekeeping to make styling in the loop easier
				$is_home_game = get_post_meta($post->ID, '_mstw_gs_home_game', true );
				$even_or_odd_row = $even_and_odd[$row_cnt]; 
				$row_class = 'mstw-gs-sw-' . $even_or_odd_row;
				if ( get_post_meta($post->ID, '_mstw_gs_home_game', true ) == 'home' ) 
					$row_class = $row_class . ' mstw-gs-sw-home';
			
				$row_tr = '<tr class="' . $row_class . '">';
				$row_td = '<td>'; 
			
				// create the row
				$row_string = $row_tr;		
			
				// column 1: Build the game date in a specified format			
				$date_string = date( $mstw_gs_sw_dtg_format, get_post_meta( $post->ID, '_mstw_gs_unix_date', true) );
			
				$row_string = $row_string. $row_td . $date_string . '</td>';
			
				// column 2: create the opponent entry
				$row_string =  $row_string . $row_td . get_post_meta( $post->ID, '_mstw_gs_opponent', true) . '</td>';
			
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

			echo 'No games scheduled for ' . $sched_yr;

		} // End of if ($posts)
	
	} // end of function widget( )

} // End of class mstw_gs_sched_widget

/*--------------------------------------------------------------------
 *
 * mstw_gs_countdown_widget
 *	- displays a simple schedule (table) with date and opponent columns
 *
 *------------------------------------------------------------------*/

class mstw_gs_countdown_widget extends WP_Widget {
	
	function mstw_gs_countdown_widget( ) {
		// processes the widget
		
	}
	
	function form( $instance ) {
		// displays the widget form in the admin dashboard
		
	}
	
	function update( $new_instance, $old_instance ) {
		// process widget options to save
		
	}
	
	function widget( $args, $instance ) {
		// displays the widget
		
	}
	
}
?>