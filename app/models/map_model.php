<?php
class map_model extends CI_Model {

	public function __construct()
	{
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

	public function get_outlets_new($types){
		//if(!isset($types) && !isset($latitude) && !isset($longitude) && !isset($zoom)) return FALSE;
		
		// Script start time - so we can see how long it takes at various stages
		$time_start = microtime(true);
		$output = '';
		
		$cachetime = 100000;
		foreach (glob("/home/recycle/public_html/tmp/*.outlets.json") as $filename) {
			$cachetime = explode('.',basename($filename));
			$cachetime = $cachetime[0];
		}
		if((time() - $cachetime) < 86400) {
			$outlets_json = file_get_contents("/home/recycle/public_html/tmp/$cachetime.outlets.json");
			$outlets = json_decode($outlets_json,1);
			//$output .= "Found cached json file with timestamp: $cachetime. Loading this instead of regenerating outlets array!\nHere's a sample dataset:\n";
			//$output .= print_r($outlets,1);
		} else {		
			// Load ALL OUTLETS and ALL OUTLET RECYCLE TYPES into PHP ARRAYS
			$sql = "SELECT outlets.outlet_id, outlets.latitude, outlets.longitude FROM outlets";
			$query = $this->db->query($sql);
			$outlets_table = $query->result_array();
			$sql = "SELECT * FROM outlets_recycle_types";
			$query = $this->db->query($sql);
			$outlets_recycle_types_table = $query->result_array();
			
			// Explode array of types we want to show
			$typesarray = explode(',',$types);
			// Clone outlets array to add more refined data to
			$outlets = Array();
			
			//
			// START AND BLOCK
			//
			// Loop through all outlets rows to create more useful multidimensional associative array
			foreach ($outlets_table as $outlet_row) {
				$outlets[$outlet_row['outlet_id']] = Array('lat' => $outlet_row['latitude'], 'lng' => $outlet_row['longitude'], 'types' => Array() );
			}
			// Loop through all recycle types rows to create more useful multidimensional associative array inside outlets
			foreach ($outlets_recycle_types_table as $outlets_recycle_types_table_row) {
				$outlets[$outlets_recycle_types_table_row['outlet_id']]['types'][] = $outlets_recycle_types_table_row['recycle_type'];
			}
			file_put_contents("/home/recycle/public_html/tmp/".time().".outlets.json", json_encode($outlets));
			
			//$output .= "No up to date outlets cache could be found, regenerating outlets array!\nHere's a sample dataset:\n";
			//$output .= print_r($outlets,1);
		}
		
		///"<pre>".print_r($outlets,1)."</pre> <br /> 
		$output .= "Took ". (microtime(true)-$time_start) . " seconds, i think";
		return "<pre>".$output."</pre>";
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