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
// Load the mstw_utility_functions if necessary
	if ( !function_exists( 'mstw_sanitize_hex_color' ) ) {
		require_once 'mstw_utility_functions.php';
	}

// ----------------------------------------------------------------
// Remove Quick Edit Menu	
	add_filter( 'post_row_actions', 'mstw_gs_remove_quick_edit', 10, 2 );

	function mstw_gs_remove_quick_edit( $actions, $post ) {
		if( $post->post_type == 'scheduled_games' ) {
			unset( $actions['inline hide-if-no-js'] );
		}
		return $actions;
	}

?>