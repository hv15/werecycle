
$('.category').click(function(index) { 
	if(!$(this).hasClass("open"))
		$(this).addClass("open");
	else
		$(this).removeClass("open");
	$(this).next().slideToggle('slow');
});

$('.type').click(function(index) { 
	$(this).toggleClass('open');
	$(this).find('.typeInfoText').slideToggle('slow');
});