
$('.category').click(function(index) { 
	if(!$(this).hasClass("open"))
		$(this).addClass("open")
	else
		$(this).removeClass("open")
	$(this).next().toggle(); 
	$(this).next().children().toggle();
});

$('.typeInfoButton').click(function(index) { 
	$(this).next().toggle(); 
});