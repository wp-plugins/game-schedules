jQuery(function($) {
	var left_indent = parseInt($('#schedule-slider').css('left'));
	var block_width = $('.game-block').outerWidth();
	var slider_width = $('#schedule-slider').outerWidth();
	var left_stop = 0;
	var view_width = $('.gs-slider-area').outerWidth();
	var nbr_blocks = view_width/block_width;
	var right_stop = -slider_width + nbr_blocks*block_width;
	
	$('#gs-slider-right-arrow').click(function(){
		//get the width of the items ( i like making the jquery part dynamic, so if you change the width in the css you won't have o change it here too ) '
        
		//alert("block_width= " + block_width );
		//calculate the new left indent of the unordered list
        
		//alert("left_ident= " + left_indent );
		
		if (left_indent > right_stop ) {
			left_indent = left_indent - block_width;
			$('#schedule-slider').css( {'left' : left_indent } );
		}
		
		
		
		//$('#schedule-slider').animate({'left' : left_indent},{queue:false, duration:500},function(){
                //get the first list item and put it after the last list item (that's how the infinite effects is made) '
                //$('#carousel_ul li:last').after($('#carousel_ul li:first'));

                //and get the left indent to the default -210px
               //$('#schedule-slider').css({'left' : left_indent+block_width});
            //});
		
	});
	
	$('#gs-slider-left-arrow').click(function(){
		if (left_indent < left_stop ) {
			left_indent = left_indent + block_width;
			$('#schedule-slider').css( {'left' : left_indent } );
		}
		
		
	});
});


//jQuery(document).ready(function () {
//$(document).ready(function() {
        //move the last list item before the first item. The purpose of this is if the user clicks previous he will be able to see the last item.
       // $('#carousel_ul li:first').before($('#carousel_ul li:last'));

        //when user clicks the image for sliding right
       //$('#gs-slider-right-arrow').click(function(){
		//	alert("Right Arrow!!");
            //get the width of the items ( i like making the jquery part dynamic, so if you change the width in the css you won't have o change it here too ) '
            //var block_width = $('#carousel_ul li').outerWidth() + 10;

            //calculate the new left indent of the unordered list
           // var left_indent = parseInt($('#carousel_ul').css('left')) - block_width;

            //make the sliding effect using jquery's anumate function '
            //$('#carousel_ul').animate({'left' : left_indent},{queue:false, duration:500},function(){

                //get the first list item and put it after the last list item (that's how the infinite effects is made) '
                //$('#carousel_ul li:last').after($('#carousel_ul li:first'));

                //and get the left indent to the default -210px
               // $('#carousel_ul').css({'left' : '-210px'});
            //});
       // });

        //when user clicks the image for sliding left
        //$('#gs-slider-left-arrow').click(function(){
			//alert("Left Arrow!!");
			
            //var block_width = $('#carousel_ul li').outerWidth() + 10;

            /* same as for sliding right except that it's current left indent + the item width (for the sliding right it's - block_width) */
            //var left_indent = parseInt($('#carousel_ul').css('left')) + block_width;

           // $('#carousel_ul').animate({'left' : left_indent},{queue:false, duration:500},function(){

            /* when sliding to left we are moving the last item before the first item */
            //$('#carousel_ul li:first').before($('#carousel_ul li:last'));

            /* and again, when we make that change we are setting the left indent of our unordered list to the default -210px */
            //$('#carousel_ul').css({'left' : '-210px'});
            //});

        //});
 // });
  
  


//$(document).ready(function() {  
        //move the last list item before the first item. The purpose of this is if the user clicks previous he will be able to see the last item.  
        //$('#carousel_ul li:first').before($('#carousel_ul li:last'));  
  
        //when user clicks the image for sliding right  
        //$('#right_scroll img').click(function(){  
		
		//$('#gs-slider-right-arrow').click(function(){  
			//alert( "Handler for .click() called." );
            //get the width of the items
            /*var block_width = 187; //$('.game-block').outerWidth();  
  
            //calculate the new left indent of the unordered list  
            var left_indent = parseInt($('#schedule-slider').css('left')) - block_width; 

			var sched_width = $('#schedule-slider').outerWidth();
			
			if ( left_indent < sched_width - block_width ) {
				left_ident = left_indent + block_width;
				// turn off the right arrow
			}
			else {
				//make the sliding effect using jquery's anumate function '  
				$('#schedule-slider').animate({'left' : left_indent},{queue:false, duration:500},function(){  
	  
					//get the first list item and put it after the last list item (that's how the infinite effects is made) '  
					//$('#carousel_ul li:last').after($('#carousel_ul li:first'));  
	  
					//and set the left indent to the default -210px  
					$('#schedule-slider').css({'left' : '-900px'});  
				}); 
			}
			$('#schedule-slider').css({'background-color' : '#f00'});
			*/
        //});
		
  /*
        //when user clicks the image for sliding left  
        $('#gs-slider-left-arrow').click(function(){  
  
            var block_width = $('#carousel_ul li').outerWidth() + 10;  
  
            // same as for sliding right except that it's current left indent + the item width (for the sliding right it's - block_width) 
            var left_indent = parseInt($('#carousel_ul').css('left')) + block_width;  
  
            $('#carousel_ul').animate({'left' : left_indent},{queue:false, duration:500},function(){  
  
            // when sliding to left we are moving the last item before the first item   
            $('#carousel_ul li:first').before($('#carousel_ul li:last'));  
  
            // and again, when we make that change we are setting the left indent of our unordered list to the default -210px 
            $('#carousel_ul').css({'left' : '-210px'});  
            });  
  
        });  
		*/
  //});  