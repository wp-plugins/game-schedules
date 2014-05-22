//Set up the color pickers to work with our text input field
jQuery(document).ready(function($){

	$('#gs_tbl_hdr_bkgd_color').wpColorPicker();
	$('#gs_tbl_hdr_text_color').wpColorPicker();
	
	$('#gs_tbl_border_color').wpColorPicker();
	
	$('#gs_tbl_odd_text_color').wpColorPicker();
	$('#gs_tbl_odd_bkgd_color').wpColorPicker();
	
	$('#gs_tbl_even_text_color').wpColorPicker();
	$('#gs_tbl_even_bkgd_color').wpColorPicker();
	
	$('#gs_tbl_home_text_color').wpColorPicker();
	$('#gs_tbl_home_bkgd_color').wpColorPicker();
	
	$('#gs_cdt_bkgd_color').wpColorPicker();
	$('#gs_cdt_game_time_color').wpColorPicker();
	$('#gs_cdt_opponent_color').wpColorPicker();
	$('#gs_cdt_location_color').wpColorPicker();
	$('#gs_cdt_intro_color').wpColorPicker();
	$('#gs_cdt_countdown_color').wpColorPicker();
	$('#gs_cdt_countdown_bkgd_color').wpColorPicker();
	
	
	$('#gs_sldr_game_block_bkgd_color').wpColorPicker();
	$('#gs_sldr_hdr_bkgd_color').wpColorPicker();
	$('#gs_sldr_hdr_text_color').wpColorPicker();
	$('#gs_sldr_hdr_divider_color').wpColorPicker();
	$('#gs_sldr_game_date_color').wpColorPicker();
	$('#gs_sldr_game_opponent_color').wpColorPicker();
	$('#gs_sldr_game_location_color').wpColorPicker();
	$('#gs_sldr_game_time_color').wpColorPicker();
	$('#gs_sldr_game_links_color').wpColorPicker();

	$('#gs_game_date').datepicker({
        dateFormat : 'yy-mm-dd'
    });
});