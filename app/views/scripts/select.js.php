function recalculateSelected() {
	$('.category').each(function(index) {
		var newval = $(this).next().children('.typeCheckbox').length;
		newval++;
		alert("newval = "+newval);
		$(this).children('.selectedCount').text(newval);
	});
}

$('.category').click(function(index) { 
	$(this).next().toggle(); 
	$(this).next().children().toggle();
});

$('.typeCheckbox').change(function() {
	recalculateSelected();
});