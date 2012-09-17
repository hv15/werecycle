$('.category').click(function(index) { 
	$(this).next().toggle(); 
	$(this).next().children().toggle();
});