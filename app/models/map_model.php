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
			$sql = "SELECT DISTINCT outlets.outlet_id, outlet_type, outlet_name, latitude, longitude 
				FROM outlets,`outlets_recycle_types` 
				WHERE outlets.outlet_id = outlets_recycle_types.outlet_id
				AND MBRContains( GeomFromText('Polygon(({$sw['lat']} {$sw['lon']}, {$ne['lat']} {$sw['lon']}, {$ne['lat']} {$ne['lon']}, {$sw['lat']} {$ne['lon']}, {$sw['lat']} {$sw['lon']}))'), outlets.coords )";		
		} else {
			$types = explode(',',$types);
			$count = count($types);
			if($count==1) {
				$sql = "SELECT DISTINCT outlets.outlet_id, outlet_type, outlet_name, latitude, longitude 
					FROM outlets,`outlets_recycle_types` 
					WHERE recycle_type = {$types[0]} AND outlets.outlet_id = outlets_recycle_types.outlet_id
					AND MBRContains( GeomFromText('Polygon(({$sw['lat']} {$sw['lon']}, {$ne['lat']} {$sw['lon']}, {$ne['lat']} {$ne['lon']}, {$sw['lat']} {$ne['lon']}, {$sw['lat']} {$sw['lon']}))'), outlets.coords )";
			} elseif($count==2) {
				$sql = "SELECT DISTINCT outlets.outlet_id, outlet_type, outlet_name, latitude, longitude FROM outlets,`outlets_recycle_types`, 
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
		}
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	public function get_info($id) {
		$output = '';
		
		// Build SQL query to get outlet information for all selected types
		$sql = "SELECT * FROM outlets_info,outlets WHERE outlets.outlet_id = $id AND outlets.outlet_id = outlets_info.outlet_id";
		$query = $this->db->query($sql);
		$row = $query->result_array();
		$row = $row[0];
		$html = $row["html_info"];
		//return print_r($row,1);
		    
		    // Output name of outlet
		    //$output .= "<span class='nametitle'>Name</span><br />\n<span class='name'>{$row["outlet_name"]}</span><br /><br />\n";
		    
		    // Check to see if there is a phone number for this outlet to determine the regex we use
		    if(strpos($html,'miniIconTelephoneRec')) {
				$phone = preg_replace('|.+<img class="pic20 picL" src="siImages/miniIconTelephoneRec.gif" />(.+?) <div.+|s', '\1', $html);
				$address = preg_replace('|.+<b>Information</b><div class="lineGreen"></div><div class="spacer5y"></div>(.+?)<img class="pic20.+|s', '\1', $html);
				$address = preg_replace('|<br />|s',', ',$address);
				$address = trim($address," \n\r\t,");
				//$addressenc = urlencode($address);
				//$mapsurl = "http://maps.google.com/maps?q=".$row['latitude'].','.$row['longitude'];
				//$output .= "<span class='phonetitle'>Phone</span><br />\n<span class='phone'>$phone</span><br /><br />\n\n";
				//$output .= "<span class='addresstitle'>Address</span><br />\n<div class='address'><a href='$mapsurl' target='_blank'>$address</a></div><br /><br />\n\n";
		    } else {
				$address = preg_replace('|.+<b>Information</b><div class="lineGreen"></div><div class="spacer5y"></div>(.+?)<div class="spacer1y">.+|s', '\1', $html);
				$address = preg_replace('|<br />|s',', ',$address);
				$address = trim($address," \n\r\t,");
				//$addressenc = urlencode($address);
				//$mapsurl = "http://maps.google.com/maps?q=".$row['latitude'].','.$row['longitude'];
				//$output .= "<span class='addresstitle'>Address</span><br />\n<div class='address'><a href='$mapsurl' target='_blank'>$address</a></div><br /><br />\n\n";
		    }
		    
		    // Output the block of text which shows the opening hours, nicely marked up for CSS
		    if(strpos($html,'openHours')) {
				$openhours = preg_replace('|.+<div class="openHours">(.+?)<div class="spacer5y.+|s', '\1', $html);
				$openhours = preg_replace('|<b class="textGreen">(.+?)</b>|s', "\n".'<span class="openhoursperiodtext">\1</span><br />'."\n", $openhours);
				$openhours = trim($openhours," \n\r\t,");
				// Wanted to group the text beneath opehoursperiod
				//$openhours = preg_replace('| </div>|',"<br /><br />\n\n", $openhours); 
				$openhours = preg_replace('|/>\n([^<].+?<br />.+?)<br />|s',"/>\n<span class='openhourstimetext'>".'\1'."</span><br />", $openhours);
				//$output .= "<span class='openhourstitle'>Opening Hours</span><br /><div class='openhours'>\n".$openhours;
		    }
		    
		    /*// Build SQL query to get outlet information for all selected types
		    $sql = "SELECT `recycle_types`.`recycle_type`,`recycle_types`.`name` FROM `outlets_recycle_types`,`recycle_types` WHERE `outlets_recycle_types`.`recycle_type`=`recycle_types`.`recycle_type` AND `outlet_id` = $id";
		    $query = $this->db->query($sql);
		    $output .= "<span class='outletypestitle'>What you can Recycle here:</span><br />\n";
		    // Output the block of text which shows the recycle types, nicely marked up for CSS
		    $i=0;
		    $count=$query->num_rows();
		    foreach($query->result_array() as $recycle_type_row) {
			$output .= "<span class='recycle_type_{$recycle_type_row['recycle_type']}'>{$recycle_type_row['name']}";
			$i++; if($i!=$count) {
				$output .= ", </span>\n";
			} else {
				$output .= "</span><br /><br />\n\n";
			}
		    }*/
			
			
		$output = array(
			'name' => $row["outlet_name"],
			'latitude' => $row['latitude'],
			'longitude' => $row['longitude'],
			'phone' => (isset($phone) ? $phone : FALSE),
			'address' => $address,
			'openhours' => (isset($openhours) ? $openhours : FALSE)
		);
		return $output;
	}
}
?>