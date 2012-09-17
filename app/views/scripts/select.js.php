$('.category').click(function(index) { 
	$(this).next().toggle("fast"); 
	$(this).next().children().toggle();
});