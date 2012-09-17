recalculateSelected();

function recalculateSelected() {
	$('.category').each(function(index) {
		var newval = $(this).next().find('.typeCheckbox:checked').length;
		$(this).find('.selectedCount').text(newval);
	});
}

function createQuery() {
	var types = '';
	$('.typeCheckbox:checked').each(function(){
		types = $(this).val() + '/' + types;
	});
	$.get('/check/'+types, function(data) {
		if(data>0) {
			/*alert("There are "+data+" recycle points which fit this selection. Redirecting you to the map...");*/
			window.location.href = '/map/'+types;
		} else {
			alert("There are no recycle points available which allow that combination of types, please de-select some and try again");
		}
	});
}

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

$('.typeCheckbox').change(function() {
	recalculateSelected();
});
