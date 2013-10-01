$(document).ready(function() {  
        //move the last list item before the first item. The purpose of this is if the user clicks previous he will be able to see the last item.  
        //$('#carousel_ul li:first').before($('#carousel_ul li:last'));  
  
        //when user clicks the image for sliding right  
        //$('#right_scroll img').click(function(){  
		$('.gs-slider-right-arrow').click(function(){  
  
            //get the width of the items
            var item_width = $('gs-slider-area .game-block').outerWidth();  
  
            //calculate the new left indent of the unordered list  
            var left_indent = parseInt($('.schedule-slider').css('left')) - item_width; 

			var sched_width = $(.schedule-slider).outerWidth();
			
			if ( left_indent < sched_width - item_width ) {
				left_ident = left_indent + item_width;
				// turn off the right arrow
			}
			else {
				//make the sliding effect using jquery's anumate function '  
				$('.schedule-slider').animate({'left' : left_indent},{queue:false, duration:500},function(){  
	  
					//get the first list item and put it after the last list item (that's how the infinite effects is made) '  
					//$('#carousel_ul li:last').after($('#carousel_ul li:first'));  
	  
					//and get the left indent to the default -210px  
					//$('#carousel_ul').css({'left' : '-210px'});  
				}); 
			}
        });  
  
        //when user clicks the image for sliding left  
        $('#left_scroll img').click(function(){  
  
            var item_width = $('#carousel_ul li').outerWidth() + 10;  
  
            /* same as for sliding right except that it's current left indent + the item width (for the sliding right it's - item_width) */  
            var left_indent = parseInt($('#carousel_ul').css('left')) + item_width;  
  
            $('#carousel_ul').animate({'left' : left_indent},{queue:false, duration:500},function(){  
  
            /* when sliding to left we are moving the last item before the first item */  
            $('#carousel_ul li:first').before($('#carousel_ul li:last'));  
  
            /* and again, when we make that change we are setting the left indent of our unordered list to the default -210px */  
            $('#carousel_ul').css({'left' : '-210px'});  
            });  
  
        });  
  });  