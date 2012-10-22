<?php
class map_model extends CI_Model {

	public function __construct()
	{
		require_once(APPPATH.'libraries/phpcoord-2.3.php');
		require_once(APPPATH.'libraries/latlong_box.php');
	}
	
	public function get_categories()
	{
		$output = array();
		$categoriesquery = $this->db->query('SELECT * FROM recycle_categories');
		foreach ($categoriesquery->result_array() as $category) {
			$catid = $category['recycle_category'];
			$output[$catid] = array( 'name' => $category['name'], 'types' => array() );
			
			$typesquery = $this->db->query('SELECT * FROM recycle_types WHERE recycle_category = '.$catid);
			foreach ($typesquery->result_array() as $type) {
				$output[$catid]['types'][$type['recycle_type']] = array( 
					'name' => $type['name'],
					'description' => $type['description']
				);
			}
		}
		return $output;
	}
	
	public function get_outlet_categories($id)
	{
		$sqlcats = "SELECT DISTINCT recycle_categories.recycle_category,recycle_categories.name FROM outlets_recycle_types,recycle_types,recycle_categories WHERE `outlets_recycle_types`.`recycle_type`=`recycle_types`.`recycle_type` AND `outlet_id` = $id AND recycle_types.recycle_category = recycle_categories.recycle_category" ;
		$catsquery = $this->db->query($sqlcats);
				
		$output = array();
		foreach ($catsquery->result_array() as $category) {
			$catid = $category['recycle_category'];
			$output[$catid] = array( 'name' => $category['name'], 'types' => array() );

			$sqltypes = "SELECT `recycle_types`.`recycle_type`,`recycle_types`.`name`,`recycle_types`.`description` FROM `outlets_recycle_types`,`recycle_types` WHERE `outlets_recycle_types`.`recycle_type`=`recycle_types`.`recycle_type` AND `outlet_id` = $id AND recycle_types.recycle_category = $catid";
			$typesquery = $this->db->query($sqltypes);
			foreach ($typesquery->result_array() as $type) {
				$output[$catid]['types'][$type['recycle_type']] = array( 
					'name' => $type['name'],
					'description' => $type['description']
				);
			}
		}
		return $output;
	}
	
	public function get_outlets($types,$latitude,$longitude,$distance)
	{
		if(!isset($types) && !isset($latitude) && !isset($longitude) && !isset($distance)) return FALSE;
		$ne = bpot_getDueCoords($latitude, $longitude, 45, $distance, 'm', 1);
		$sw = bpot_getDueCoords($latitude, $longitude, 225, $distance, 'm', 1);
		if($types=='all') {
			$sql = "SELECT DISTINCT outlets.outlet_id
				FROM outlets,`outlets_recycle_types` 
				WHERE outlets.outlet_id = outlets_recycle_types.outlet_id
				AND MBRContains( GeomFromText('Polygon(({$sw['lat']} {$sw['lon']}, {$ne['lat']} {$sw['lon']}, {$ne['lat']} {$ne['lon']}, {$sw['lat']} {$ne['lon']}, {$sw['lat']} {$sw['lon']}))'), outlets.coords )";		
		} else {
			$types = explode(',',$types);
			$count = count($types);
			if($count==1) {
				$sql = "SELECT DISTINCT outlets.outlet_id
					FROM outlets,`outlets_recycle_types` 
					WHERE recycle_type = {$types[0]} AND outlets.outlet_id = outlets_recycle_types.outlet_id
					AND MBRContains( GeomFromText('Polygon(({$sw['lat']} {$sw['lon']}, {$ne['lat']} {$sw['lon']}, {$ne['lat']} {$ne['lon']}, {$sw['lat']} {$ne['lon']}, {$sw['lat']} {$sw['lon']}))'), outlets.coords )";
			} elseif($count==2) {
				$sql = "SELECT DISTINCT outlets.outlet_id FROM outlets,`outlets_recycle_types`, 
					(SELECT * FROM `outlets_recycle_types` WHERE outlets_recycle_types.recycle_type = {$types[0]}) AS ort2
					WHERE ort2.outlet_id = outlets_recycle_types.outlet_id
					AND outlets_recycle_types.recycle_type = {$types[1]}
					AND outlets.outlet_id = outlets_recycle_types.outlet_id
					AND MBRContains( GeomFromText('Polygon(({$sw['lat']} {$sw['lon']}, {$ne['lat']} {$sw['lon']}, {$ne['lat']} {$ne['lon']}, {$sw['lat']} {$ne['lon']}, {$sw['lat']} {$sw['lon']}))'), outlets.coords )";
			} elseif($count>=3) {
				$sql = "SELECT DISTINCT outlets.outlet_id
					FROM outlets,`outlets_recycle_types`,";
					
				for($i=3;$i<$count;$i++) {
					$sql .= " (SELECT * FROM `outlets_recycle_types` 
						   WHERE outlets_recycle_types.recycle_type = {$types[$i]}) AS ort$i,";
				}
				
				$sql .= " (SELECT * FROM `outlets_recycle_types` 
					   WHERE outlets_recycle_types.recycle_type = {$types[2]}) AS ort2,
					   
					  (SELECT * FROM `outlets_recycle_types` WHERE outlets_recycle_types.recycle_type = {$types[1]}) AS ort1
					   WHERE ort2.outlet_id = outlets_recycle_types.outlet_id ";
					   
				for($i=3;$i<$count;$i++) {
					$sql .= " AND ort$i.outlet_id = outlets_recycle_types.outlet_id";
				}
				
				$sql .= " AND ort1.outlet_id = outlets_recycle_types.outlet_id
					AND outlets_recycle_types.recycle_type = {$types[0]} 
					AND outlets.outlet_id = outlets_recycle_types.outlet_id
					AND MBRContains( GeomFromText('Polygon(({$sw['lat']} {$sw['lon']}, {$ne['lat']} {$sw['lon']}, {$ne['lat']} {$ne['lon']}, {$sw['lat']} {$ne['lon']}, {$sw['lat']} {$sw['lon']}))'), outlets.coords )";
			}
		}
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function get_outlets_new($types,$lat,$lng,$zoom){
		//if(!isset($types) && !isset($latitude) && !isset($longitude) && !isset($zoom)) return FALSE;
		if($types=='all') $types = "1,2,3,4,5,6,7,8,9,11,12,13,14,16,17,18,19,20,21,22,23,24,25,26,27,29,30,31,32,33,34,37,38,40,41,42,43,44,45,46,49,51,52,53,56,57,80,81,82,83,84,130,145,146,148,149,150,151,152";
		
		// Script start time - so we can see how long it takes at various stages
		$time_start = microtime(true);
		$output = '';
		
		$cachetime = 0;
		foreach (glob("/home/recycle/public_html/tmp/*.outlets.json") as $filename) {
			$cachetime = explode('.',basename($filename));
			$cachetime = $cachetime[0];
			//$output .= "Found cached json file with timestamp: $cachetime.\n";
		}
		if((time() - $cachetime) < 86400) {
			//$output .= "Found cached json file less than a day old, timestamp: $cachetime. Loading this instead of regenerating outlets array!\n\n";
			$outlets_json = file_get_contents("/home/recycle/public_html/tmp/$cachetime.outlets.json");
			$outlets = json_decode($outlets_json,1);
			//$output .= print_r($outlets,1);
		} else {		
			//$output .= "No up to date outlets cache could be found, regenerating outlets array!\n\n";
			
			// Load ALL OUTLETS and ALL OUTLET RECYCLE TYPES into PHP ARRAYS
			$sql = "SELECT outlets.outlet_id, outlets.latitude, outlets.longitude FROM outlets";
			$query = $this->db->query($sql);
			$outlets_table = $query->result_array();
			$sql = "SELECT * FROM outlets_recycle_types";
			$query = $this->db->query($sql);
			$outlets_recycle_types_table = $query->result_array();
			
			// Clone outlets array to add more refined data to
			$outlets = Array();
			
			// Loop through all outlets rows to create more useful multidimensional associative array
			foreach ($outlets_table as $outlet_row) {
				$outlets[$outlet_row['outlet_id']] = Array('lat' => $outlet_row['latitude'], 'lng' => $outlet_row['longitude'], 'types' => Array() );
			}
			// Loop through all recycle types rows to create more useful multidimensional associative array inside outlets
			foreach ($outlets_recycle_types_table as $outlets_recycle_types_table_row) {
				$outlets[$outlets_recycle_types_table_row['outlet_id']]['types'][] = $outlets_recycle_types_table_row['recycle_type'];
			}
			
			foreach (glob("/home/recycle/public_html/tmp/*.outlets.json") as $filename) {
				unlink($filename);
			}
			file_put_contents("/home/recycle/public_html/tmp/".time().".outlets.json", json_encode($outlets));
			
			//$output .= print_r($outlets,1);
		}
				
		// Explode array of specified recycle types
		$typesarray = explode(',',$types);
		//$output .= "Types to check for:\n\n".print_r($typesarray,1);
		
		// Get a fuzzy max distance from center of viewport to points to eliminate points which are off the screen
		switch ($zoom) {
			case 19: $maxDistance = 0.13; break;
			case 18: $maxDistance = 0.2; break;
			case 17: $maxDistance = 0.4; break;
			case 16: $maxDistance = 0.9; break;
			case 15: $maxDistance = 1.8; break;
			case 14: $maxDistance = 3.5; break;
			case 13: $maxDistance = 8; break;
			case 12: $maxDistance = 15; break;
			case 11: $maxDistance = 27; break;
			case 10: $maxDistance = 60; break;
			case 9: $maxDistance = 120; break;
			case 8: $maxDistance = 250; break;
			case 7: $maxDistance = 500; break;
			case 6: $maxDistance = 1000; break;
			case 5: $maxDistance = 2000; break;
			case 4: $maxDistance = 4000; break;
			case 3: $maxDistance = 10000; break;
			default: $maxDistance = 10000;
		}
		
		// Get a fuzzy max distance from center of viewport to points to eliminate points which are off the screen
		switch ($zoom) {
			case 19: $clusterRadius = 0; break;
			case 18: $clusterRadius = 0; break;
			case 17: $clusterRadius = 0.006; break;
			case 16: $clusterRadius = 0.012; break;
			case 15: $clusterRadius = 0.024; break;
			case 14: $clusterRadius = 0.05; break;
			case 13: $clusterRadius = 0.1; break;
			case 12: $clusterRadius = 0.2; break;
			case 11: $clusterRadius = 0.4; break;
			case 10: $clusterRadius = 0.8; break;
			case 9: $clusterRadius = 1.6; break;
			case 8: $clusterRadius = 3.2; break;
			case 7: $clusterRadius = 6.5; break;
			case 6: $clusterRadius = 13; break;
			case 5: $clusterRadius = 26; break;
			case 4: $clusterRadius = 52; break;
			case 3: $clusterRadius = 104; break;
			default: $clusterRadius = 200;
		}
		
		// Create array to store clusters of outlets in
		$clusters = Array();
		// And array to store outlets which aren't in a cluster
		$singleOutlets = Array();
		
		foreach ($outlets as $id => $outlet) {
			//$output .= "Comparing types:\n\n".print_r($typesarray,1);
			//$output .= "With outlet types:\n\n".print_r($outlet['types'],1);
			$foundtypes = 0;
			foreach($typesarray as $type) {
				if(in_array($type,$outlet['types'])) {
					$foundtypes++;
				}
			}
			// We only want to process outlets which have at least one of the requested recycle types
			if( $foundtypes > 0 ) {
				// This outlet supports at least one of the recycle types we want, give it a new parameter to tell us what percentage of the ones we want it supports
				$outlet['typesratio'] = $foundtypes/count($typesarray);
				
				// Get the distance from this outlet to the centre of the viewport
				$lld1 = new LatLng($lat, $lng); // LatLng of viewport center
				$lld2 = new LatLng($outlet['lat'], $outlet['lng']);  // LatLng of outlet
				$distance = $lld1->distance($lld2); // in km
				// Skip this outlet, it's off the screen
				if($distance > $maxDistance) {
					//$output .= "$distance > $maxDistance so skipping outlet\n";
					continue;
				}
				
				// Create first cluster
				if(empty($clusters)) {
					$clusters[] = Array('id' => $id, 'lat' => $outlet['lat'], 'lng' => $outlet['lng'], 'count' => 1 );
					// Add this outlet to the singleOutlets array, even though we're making a cluster from it, because we don't know if this cluster will get any other outlets yet
					$singleOutlets[$id] = $outlet;
				} else {
					// Loop through all clusters which exist to see if we should add this one to a cluster
					$outletAddedToCluster = false;
					foreach ($clusters as $clusterKey => $cluster) {
						// Calculate distance between this outlet and the centre of whichever cluster we are testing
						$lld1 = new LatLng($cluster['lat'], $cluster['lng']); // LatLng of cluster center
						$lld2 = new LatLng($outlet['lat'], $outlet['lng']);  // LatLng of outlet
						$clusterOutletDistance = $lld1->distance($lld2); // in km
						// This outlet is within the radius of a cluster, add it to the cluster
						if($clusterOutletDistance < ($clusterRadius*7)) {
							// Increment the count variable of the cluster to show we've added an outlet to it
							$clusters[$clusterKey]['count']++;
							// Remove the outlet (which originally created the cluster we are now adding to) from the singleOutlets array
							// This prevents cluster-starter outlets from appearing as singleOutlets
							// But doesn't stop clusters with no other outlets from appearing as clusters, this needs to be done outside the outlet loop
							unset($singleOutlets[$clusters[$clusterKey]['id']]);
							// This allows us to create a new cluster outside of this loop if we don't find a cluster to add this outlet to
							$outletAddedToCluster = true;
							// We've added this outlet to a cluster so there's no need to keep looking for a cluster to add it to
							break;
						}
					}
					// We didn't find a cluster to add the marker to, let's create a new cluster
					if($outletAddedToCluster === false) {
						$clusters[] = Array('id' => $id, 'lat' => $outlet['lat'], 'lng' => $outlet['lng'], 'count' => 1 );
						// Add this outlet to the singleOutlets array, even though we're making a cluster from it, because we don't know if this cluster will get any other outlets yet
						$singleOutlets[$id] = $outlet;
					}
				}
				
				//$output .= "Found outlet with $foundtypes types! ID: $id\n";
			} else {
				//$output .= "Intersect isn't the same as typesarray!\n Intersect:\n".print_r($intersect,1)." ID: $id\n\n";
			}
		}
		// So we've just finished looping through all the outlets
		// We now have two arrays, clusters and singleOutlets. singleOutlets should be fine but clusters still contains clusters which have no other outlets
		// So let's loop through and delete them
		foreach($clusters as $key => $cluster) {
			if($cluster['count']==1) unset($clusters[$key]);
		}
		
		//$output .= "\ntotal outlets after filters: ".count($outlets_filtered);
		//$output .= "\ntotal clusters: ".count($clusters);
		//$output .= "\n\n clusters: ".print_r($clusters,1);
			
			
		//$output .= "OR-filtered outlets:\n\n".print_r($outlets_filtered_or,1);
		//$output .= "AND-filtered outlets:\n\n".print_r($outlets_filtered_and,1);
		//$output .= "\n\nFiltered outlets:\n\n".print_r($outlets_filtered,1);
		//$output .= "\n\nAll outlets:\n\n".print_r($outlets,1);
			
		//"<pre>".print_r($outlets,1)."</pre> <br /> 
		//$output .= "\n\nTook ". (microtime(true)-$time_start) . " seconds, i think";
		//return "<pre>".$output."</pre>";
		return array('clusters'=>$clusters,'singleOutlets'=>$singleOutlets);
	}
	
	public function get_info($id) {
		// Build SQL query to get outlet information for specified id
		$sql = "SELECT * FROM outlets WHERE outlets.outlet_id = $id";
		$query = $this->db->query($sql);
		$row = $query->result_array();
		$latitude = $row[0]['latitude'];
		$longitude = $row[0]['longitude'];
		
		// Build SQL queries and get outlet data for specified id
		$row = $this->db->query("SELECT * FROM outlets_data WHERE outlet_id = $id AND `key` = 'name'")->result_array();
		$name = $row[0]['value'];
		$row = $this->db->query("SELECT * FROM outlets_data WHERE outlet_id = $id AND `key` = 'type'")->result_array();
		$type = $row[0]['value'];
		$row = $this->db->query("SELECT * FROM outlets_data WHERE outlet_id = $id AND `key` = 'area'")->result_array();
		$area = $row[0]['value'];
		$row = $this->db->query("SELECT * FROM outlets_data WHERE outlet_id = $id AND `key` = 'phone'")->result_array();
		$phone = $row[0]['value'];
		$row = $this->db->query("SELECT * FROM outlets_data WHERE outlet_id = $id AND `key` = 'address'")->result_array();
		$address = $row[0]['value'];
		$row = $this->db->query("SELECT * FROM outlets_data WHERE outlet_id = $id AND `key` = 'openhours'")->result_array();
		$openhours = $row[0]['value'];
		
		$output = array(
			'name' => $name,
			'latitude' => $latitude,
			'longitude' => $longitude,
			'type' => $type,
			'area' => $area,
			'address' => $address,
			'phone' => (empty($phone) ? FALSE : $phone),
			'openhours' => (empty($openhours) ? FALSE : $openhours)
		);
		return $output;
	}
}
?>