recalculateSelected();

function recalculateSelected() {
	$('.category').each(function(index) {
		var newval = $(this).next().find('.typeCheckbox:checked').length;
		$(this).find('.selectedCount').text(newval);
	});
}

function setTypes() {
	var types = '';
	var typescommas = '';
	$('.typeCheckbox:checked').each(function(){
		types = $(this).val() + '/' + types;
		typescommas = $(this).val() + ',' + typescommas;
	});
	$.get('/check/'+types, function(data) {
		if(data>0) {
			/*alert("There are "+data+" recycle points which fit this selection. Redirecting you to the map...");*/		
			var newSessionData = encodeURIComponent('{"types_selected":'+typescommas.slice(0, - 1);+'}');
			$.get('/setsession/'+newSessionData, function(data){
				/*window.location.href = '/map';*/
				alert(data);
			});
		} else {
			alert("There are no recycle points available which allow that combination of types, please de-select some and try again");
		}
	});
}

$('.category').click(function(index) { 
	if(!$(this).hasClass("open"))
		$(this).addClass("open");
	else
		$(this).removeClass("open");
	$(this).next().slideToggle('slow');
});

$('.typeInfoButton').click(function(index) { 
	$(this).next().slideToggle('slow'); 
});

$('.typeCheckbox').change(function() {
	recalculateSelected();
});
