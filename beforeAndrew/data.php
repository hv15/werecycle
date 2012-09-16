<?php
    require('latlong_box.php');
    $output = '';
    // Connect to database
    mysql_connect(localhost,"recycle_finder","leifgivesyoulemons");
    @mysql_select_db("recycle_finder") or die( "Unable to select database");

    // Setup defaults in case no parameters are passed
    //$types = empty($_GET['types']) ? '1,2,3,4,5,6,7,8,9,11,12,13,14,16,17,18,19,20,21,22,23,24,25,26,27,29,30,31,32,33,34,37,38,40,41,42,43,44,45,46,49,51,52,53,56,57,80,81,82,83,84,130,145,146,148,149,150,151,152' : $_GET['types'];
    $types = isset($_GET['types']) ? $_GET['types'] : '6';
    // Specify whether we are looking up only outlets which allow for ALL the specified types to be recycled
    $union = isset($_GET['union']) ? $_GET['union'] : 1;
    //$areas = empty($_GET['areas']) ? 12 : $_GET['areas'];
    $latitude = !empty($_GET['latitude']) ? $_GET['latitude'] : 55.9099;
    $longitude = !empty($_GET['longitude']) ? $_GET['longitude'] : -3.3220;
    $distance = isset($_GET['distance']) ? $_GET['distance'] : 10;
    $ne = bpot_getDueCoords($latitude, $longitude, 45, $distance, 'm', 1);
    $sw = bpot_getDueCoords($latitude, $longitude, 225, $distance, 'm', 1);
    
    if($union==0) { 
	// Build SQL query to get outlet information for all selected types
	$sql = "SELECT DISTINCT outlets.outlet_id, outlet_type, outlet_name, latitude, longitude 
	    FROM outlets, 
	    (SELECT outlet_id FROM outlets_recycle_types WHERE recycle_type IN ($types) ) AS ort
	    WHERE outlets.outlet_id = ort.outlet_id
	    AND MBRContains( GeomFromText('Polygon(({$sw[lat]} {$sw[lon]}, {$ne[lat]} {$sw[lon]}, {$ne[lat]} {$ne[lon]}, {$sw[lat]} {$ne[lon]}, {$sw[lat]} {$sw[lon]}))'), coords )
	    ORDER BY outlet_id ASC";
    } else {
	$types = explode(',',$types);
	$count = count($types);
	if($count>50) {$types = array_slice($types, 0, 50); $count = 50;}
	if($count==1) {
		$sql = "SELECT DISTINCT outlets.outlet_id, outlet_type, outlet_name, latitude, longitude FROM outlets,`outlets_recycle_types` WHERE recycle_type = {$types[0]} AND outlets.outlet_id = outlets_recycle_types.outlet_id AND MBRContains( GeomFromText('Polygon(({$sw[lat]} {$sw[lon]}, {$ne[lat]} {$sw[lon]}, {$ne[lat]} {$ne[lon]}, {$sw[lat]} {$ne[lon]}, {$sw[lat]} {$sw[lon]}))'), outlets.coords )";
	} elseif($count==2) {
		$sql = "SELECT DISTINCT outlets.outlet_id, outlet_type, outlet_name, latitude, longitude FROM outlets,`outlets_recycle_types`, 
			(SELECT * FROM `outlets_recycle_types` WHERE outlets_recycle_types.recycle_type = {$types[0]}) AS ort2
			WHERE ort2.outlet_id = outlets_recycle_types.outlet_id
			AND outlets_recycle_types.recycle_type = {$types[1]}
			AND outlets.outlet_id = outlets_recycle_types.outlet_id
			AND MBRContains( GeomFromText('Polygon(({$sw[lat]} {$sw[lon]}, {$ne[lat]} {$sw[lon]}, {$ne[lat]} {$ne[lon]}, {$sw[lat]} {$ne[lon]}, {$sw[lat]} {$sw[lon]}))'), outlets.coords )";
	} elseif($count==3) {
		$sql = "SELECT DISTINCT outlets.outlet_id, outlet_type, outlet_name, latitude, longitude FROM outlets,`outlets_recycle_types`, 
			(SELECT * FROM `outlets_recycle_types` WHERE outlets_recycle_types.recycle_type = {$types[2]}) AS ort2,
			(SELECT * FROM `outlets_recycle_types` WHERE outlets_recycle_types.recycle_type = {$types[1]}) AS ort3
			WHERE ort2.outlet_id = outlets_recycle_types.outlet_id
			AND ort3.outlet_id = outlets_recycle_types.outlet_id
			AND outlets_recycle_types.recycle_type = {$types[0]}
			AND outlets.outlet_id = outlets_recycle_types.outlet_id
			AND MBRContains( GeomFromText('Polygon(({$sw[lat]} {$sw[lon]}, {$ne[lat]} {$sw[lon]}, {$ne[lat]} {$ne[lon]}, {$sw[lat]} {$ne[lon]}, {$sw[lat]} {$sw[lon]}))'), outlets.coords )";
	} elseif($count>3) {
		$sql = "SELECT DISTINCT outlets.outlet_id, outlet_type, outlet_name, latitude, longitude 
			FROM outlets,`outlets_recycle_types`,\n";
		for($i=3;$i<$count;$i++) {
			$sql .= " (SELECT * FROM `outlets_recycle_types` WHERE outlets_recycle_types.recycle_type = {$types[$i]}) AS ort$i, \n";
		}
		$sql .= " (SELECT * FROM `outlets_recycle_types` WHERE outlets_recycle_types.recycle_type = {$types[2]}) AS ort2,
			(SELECT * FROM `outlets_recycle_types` WHERE outlets_recycle_types.recycle_type = {$types[1]}) AS ort1
			WHERE ort2.outlet_id = outlets_recycle_types.outlet_id ";
		for($i=3;$i<$count;$i++) {
			$sql .= " AND ort$i.outlet_id = outlets_recycle_types.outlet_id  \n";
		}
		$sql .= " AND ort1.outlet_id = outlets_recycle_types.outlet_id
			AND outlets_recycle_types.recycle_type = {$types[0]} AND outlets.outlet_id = outlets_recycle_types.outlet_id
			AND MBRContains( GeomFromText('Polygon(({$sw[lat]} {$sw[lon]}, {$ne[lat]} {$sw[lon]}, {$ne[lat]} {$ne[lon]}, {$sw[lat]} {$ne[lon]}, {$sw[lat]} {$sw[lon]}))'), outlets.coords )";
	}
    }
    
    
    //echo $sql; 
    $result = mysql_query($sql) or die(mysql_error()); 
    while($row = mysql_fetch_assoc($result)) {
        $output .= '{"id":'.$row[outlet_id].',"type":'.$row[outlet_type].',"lat":'.$row[latitude].',"lon":'.$row[longitude].',"name":"'.$row[outlet_name].'"},';
    }
    
    // Output javascript variable and square brackets to comma-separated JSON data to allow for easy javascript eval
    echo 'data = {"outlets": ['.$output.']}';
    //$output = preg_replace('|(.+),|s','\1',$output);
    //echo $output;
    
    
?>