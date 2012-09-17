<?php
class map_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}
	
	public function get_recycle_types()
	{
		$query = $this->db->get('recycle_types');
		return $query->result_array();
	}
	
	public function get_outlets()
	{
		include(APPPATH.'libraries/latlong_box.php');
		$types = '6,13,3';
		$latitude = 55.9099;
		$longitude = -3.3220;
		$distance = 10;
		$ne = bpot_getDueCoords($latitude, $longitude, 45, $distance, 'm', 1);
		$sw = bpot_getDueCoords($latitude, $longitude, 225, $distance, 'm', 1);
		$types = explode(',',$types);
		$count = count($types);
		if($count==1) {
			$sql = "SELECT DISTINCT outlets.outlet_id, outlet_type, outlet_name, latitude, longitude 
				FROM outlets,`outlets_recycle_types` 
				WHERE recycle_type = {$types[0]} AND outlets.outlet_id = outlets_recycle_types.outlet_id
				AND MBRContains( GeomFromText('Polygon(({$sw['lat']} {$sw['lon']}, {$ne['lat']} {$sw['lon']}, {$ne['lat']} {$ne['lon']}, {$sw['lat']} {$ne['lon']}, {$sw['lat']} {$sw['lon']}))'), outlets.coords )";
		} elseif($count==2) {
			$sql = "SELECT DISTINCT outlets.outlet_id, outlet_type, outlet_nam$paramse, latitude, longitude FROM outlets,`outlets_recycle_types`, 
				(SELECT * FROM `outlets_recycle_types` WHERE outlets_recycle_types.recycle_type = {$types[0]}) AS ort2
				WHERE ort2.outlet_id = outlets_recycle_types.outlet_id
				AND outlets_recycle_types.recycle_type = {$types[1]}
				AND outlets.outlet_id = outlets_recycle_types.outlet_id
				AND MBRContains( GeomFromText('Polygon(({$sw['lat']} {$sw['lon']}, {$ne['lat']} {$sw['lon']}, {$ne['lat']} {$ne['lon']}, {$sw['lat']} {$ne['lon']}, {$sw['lat']} {$sw['lon']}))'), outlets.coords )";
		} elseif($count>=3) {
			$sql = "SELECT DISTINCT outlets.outlet_id, outlet_type, outlet_name, latitude, longitude 
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
		
		$query = $this->db->query($sql);
		
		foreach ($query->result_array() as $row) {
		    $output .= '{"id":'.$row['outlet_id'].',"type":'.$row['outlet_type'].',"lat":'.$row['latitude'].',"lon":'.$row['longitude'].',"name":"'.$row['outlet_name'].'"},';
		}
		$output = preg_replace('|(.+),|s','\1',$output);
		$output = 'var data = {"outlets": ['.$output.']}';

		return $output;
	}
}
?>