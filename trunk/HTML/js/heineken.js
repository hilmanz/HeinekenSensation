$(document).ready(function() { 
	// Drop Down Menu
	$('ul#main-menu').superfish({ 
        delay:       600,
        animation:   {opacity:'show',height:'show'},
        speed:       'fast',
        autoArrows:  true,
        dropShadows: false
    });
	// Mobile Menu
	// Create the dropdown base
	$("<select />").appendTo("#nav");
      
	// Create default option "Go to..."
	$("<option />", {
		"selected": "selected",
		"value"   : "",
		"text"    : "Go to..."
	}).appendTo("#nav select");
	
      
	// Populate dropdown with menu items
	$("#main-menu a").each(function() {
		var el = $(this);
		$("<option />", {
			"value"   : el.attr("href"),
			"text"    : el.text()
		}).appendTo("#nav select");
	});
	
	// To make dropdown actually work
	$("#nav select").change(function() {
		window.location = $(this).find("option:selected").val();
	});
	
    $('.title').gradienttext({
        colors: [
            '#FF0000',
            '#FF6600',
            '#CCFF00',
            '#00FF00',
            '#0066FF',
            '#FF00FF'
        ],
        style: 'horizontal'
    });
	/*------------HEADING SCROLL------------*/	
	$(window).scroll(function(){
		console.log($(window).scrollTop());
		if($(window).scrollTop()<=720){
			$('#theading').removeClass('fixed');
		}else{
			$('#theading').addClass('fixed');
		}
	});
	if( $('#contact').length > 0){
		$('#contact').validate({
			invalidHandler: function(form, validator) {
				var errors = validator.numberOfInvalids();
				if (errors) {
					$( "#formContact" ).effect('shake', {times: 2, distance: 7}, 50);
				}
			}
		});
	}
	$('.fancybox').fancybox();
	$('.showPopup').fancybox();
	
});
$(window).load(function(){
  $('.flexslider').flexslider({
	animation: "slide",
	controlNav: false,
	start: function(slider){
	  $('body').removeClass('loading');
	}
  });
});
//scroll
