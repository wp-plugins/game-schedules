jQuery(function($) {
	var left_indent = parseInt($('#schedule-slider').css('left'));
	var block_width = $('.game-block').outerWidth();
	var slider_width = $('#schedule-slider').outerWidth();
	var left_stop = 0;
	var view_width = $('.gs-slider-area').outerWidth();
	var nbr_blocks = Math.floor( view_width/block_width );
	var slide_distance = (nbr_blocks-1)*block_width;
	//var right_stop = -slider_width + slide_distance;
	//10 to acccount for extra width of slider
	var right_stop = -slider_width + nbr_blocks*block_width+10;
	
	$('#gs-slider-right-arrow').click(function(){
		
		left_indent = Math.max( left_indent-slide_distance, right_stop );
		
		$('#schedule-slider').css( {'left' : left_indent } );
		
	});
	
	
	$('#gs-slider-left-arrow').click(function(){
	
		//alert( 'BEFORE: left_indent= ' + left_indent + ' /slide_distance= ' + slide_distance + ' /' );
	
		left_indent = Math.min( left_indent+slide_distance, left_stop );

		$('#schedule-slider').css( {'left' : left_indent } );
		
		//alert( 'AFTER: left_indent= ' + left_indent + ' /slide_distance= ' + slide_distance + ' /' );
			
	});
});