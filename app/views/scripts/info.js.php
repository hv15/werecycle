
$('.category').click(function(index) { 
	if(!$(this).hasClass("open"))
		$(this).addClass("open")
	else
		$(this).removeClass("open")
	$(this).next().toggle(); 
	$(this).next().children().toggle();
});

$('.type').click(function(index) { 
	/*$(this).toggleClass('open');*/
	$(this).find('.typeInfoText').toggle('slow');
});