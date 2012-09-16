function loadSelectables(selectables,selectablesInfo,oldtypes){
	
	var root = document.getElementById("Container");
	var output = "";
	
	for(i=0;i<selectables.length;i++){
		output += "<div class='container'>";
		var category = "<div class='category pointer' onclick='toggle(this.parentNode)'>"+
			"<div class='left toggle closed'><p>&#9658;</p></div>"+
			"<div class='left name'><p>"+selectables[i].name+"</p></div>"+
			"<div class='right count'><p>?/#</p></div>"+
			"</div>";
		output+=category;
		output+="<div class='types invisible'>";
			
		for(u=0;u<selectables[i]["types"].length;u++){
			var type = "<div class='type'>"+
						"<div class='left input'><p><input type='checkbox' name='"+selectables[i]["types"][u].name+"' onchange='calculateCount(this)' value='"+selectables[i]["types"][u].id+"'><p></div>"+
						"<div class='left name'><div class='nameinner'><p>"+selectables[i]["types"][u].name+"</p></div></div>"+
						"<div class='right infoButton' onclick='toggleInfo(this.parentNode)'><p>i</p></div>"+
						"<div class='left infoText invisible'>"+selectablesInfo[""+selectables[i]["types"][u].id]+"</div>"+
					"</div>";
			output+=type;
		}
		output+="</div>";
		output+="</div>";
	}
	root.innerHTML += output;
	
	oldtypes = oldtypes.split(',');
	var inputs = document.getElementsByTagName("input");
	for(i=0;i<oldtypes.length;i++){
		inputs[i].checked="true";
	}
}

function toggle(category){
	var types = getDivByClassName(category,"types");
	if(types.className.indexOf("invisible")!=-1){
		types.className = types.className.replace("invisible","visible");
	} else {
		types.className = types.className.replace("visible","invisible");
	}
}
function toggleInfo(type){
	var info = getDivByClassName(type,"infoText");
	if(info.className.indexOf("invisible")!=-1){
		info.className = info.className.replace("invisible","visible");
	} else {
		info.className = info.className.replace("visible","invisible");
	}
}
function getDivByClassName(element,name){
	var divs = element.getElementsByTagName("div");
	for(i=0;i<divs.length;i++){
		if(divs[i].className.indexOf(name)!=-1)
			return divs[i];
	}
	return null;
}
function calculateCount(container){
	container = container.parentNode.parentNode.parentNode.parentNode.parentNode;
	var inputs = container.getElementsByTagName("input");
	var count = getDivByClassName(container,"count");
	var x = 0; // The number of checkboxes active
	for(i=0;i<inputs.length;i++){
		if(inputs[i].checked){
			x++;
		}
	}
	count.innerHTML = "<p>"+x+"/"+inputs.length+"</p>";
}

/*
	Sends the user back to the map with the new settings.
*/
function createQuery(latitude,longitude,zoom){
	var inputs = document.getElementsByTagName("input");
	types = "";
	for(i=0;i<inputs.length;i++){
		if(inputs[i].checked){
			if(types=="")
				types+=inputs[i].value;
			else
				types+=","+inputs[i].value;
		}
	}
	document.location.href = "http://recycleFinder.co.uk/map.php?types="+types+"&latitude="+latitude+"&longitude="+longitude+"&zoom="+zoom;
}