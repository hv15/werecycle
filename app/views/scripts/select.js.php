$('.category').click(function(index) { 
	$(this).next().toggle("slow", function() {
		$(this).children().each(function(index) {
			$(this).toggle("slow");
		});
	}); 
});