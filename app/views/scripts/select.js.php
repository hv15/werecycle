recalculateSelected();

function recalculateSelected() {
	$('.category').each(function(index) {
		var newval = $(this).next().find('.typeCheckbox:checked').length;
		$(this).find('.selectedCount').text(newval);
	});
}

function setTypes() {
	if ($('#Confirm').is(":hidden")&&$('#Alert').is(":hidden")){
		spin("visible");
		var types = '';
		$('.typeCheckbox:checked').each(function(){
			types = $(this).val() + ',' + types;
		});
		types = types.slice(0, - 1);
		
		if(types.length == 0) {
			// alert("No types selected - The map will now showing all the recycling centers and points.");
			types = 'all';
		}
		
		var newSessionData = encodeURIComponent('{"types_selected":"'+types+'"}');
		$.get('/setsession/'+newSessionData, function(setSessionResponse){
			var urlRand = Math.random();
			$.get('/check/'+urlRand, function(checkResponse){
				eval(checkResponse);
				switch(check['code']) {
					case 1:
						window.location.href = '/map';
					break;
					case 5:
						//alert(check['message']);
						$("#ConfirmText").html(check['message']);
						$("#Confirm").toggle();
						//window.location.href = '/map';
					break;
					case 10:
						$("#ConfirmText").html(check['message']);
						$("#Confirm").toggle();
						//window.location.href = '/map';
					break;
					case 30:
						$("#ConfirmText").html(check['message']);
						$("#Confirm").toggle();
						//window.location.href = '/map';
					break;
					case 50:
						$("#ConfirmText").html(check['message']);
						$("#Confirm").toggle();
						//window.location.href = '/map';
					break;
					case 500:
						$("#ConfirmText").html(check['message']);
						$("#Confirm").toggle();
						//window.location.href = '/map';
					break;
					case 0:
						$("#AlertText").html(check['message']);
						$("#Alert").toggle();
						//alert(check['message']);/*+' [code: '+check['code']+']');*/
					break;
					default:
						$("#AlertText").html('Error, please try again from the start');
						$("#Alert").toggle();
						//alert('Error, please try again from the start');
				}
				spin("invisible");
			});
		});
	}
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

$('.typeCheckboxItem').change(function() {
	recalculateSelected();
});



$('#Cancel').click(function(index) { 
	$(this).parent().toggle();
});
$('#Ok').click(function(index) { 
	$(this).parent().toggle();
});
$('#Continue').click(function(index) { 
	window.location.href = '/map'
});

function spin(state){type="text/javascript" 
	var spin = document.getElementById("SearchSpin");
	spin.className = state;
}