function recalculateSelected() {
	alert("recalculating");
	$('.category').each(function(index) {
		var newval = $(this).next().children(':checked').length;
		alert("newval = "+newval);
		$(this).children('.selectedCount').text(newval);
	});
}

$('.category').click(function(index) { 
	$(this).next().toggle(); 
	$(this).next().children().toggle();
});

$('.typeCheckbox').change(function() {
	alert("change noticed");
	recalculateSelected();
});