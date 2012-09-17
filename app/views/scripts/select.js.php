$('.category').click(function(index) { 
	$(this).next().toggle("fast"); 
	$(this).next().children().each(function(index) {
	    $(this).toggle("fast");
	});
});