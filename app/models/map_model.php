<?php
class map_model extends CI_Model {

	public function __construct()
	{
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
	
	public function get_outlets($types='6,13',$latitude=55.9099,$longitude=-3.3220,$distance=10)
	{
		include(APPPATH.'libraries/latlong_box.php');
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
		
		$query = $this->db->query($sql);
		
		$output = '';
		foreach ($query->result_array() as $row) {
		    $output .= '{"id":'.$row['outlet_id'].',"type":'.$row['outlet_type'].',"lat":'.$row['latitude'].',"lon":'.$row['longitude'].',"name":"'.$row['outlet_name'].'"},';
		}
		$output = preg_replace('|(.+),|s','\1',$output);
		$output = 'var data = {"outlets": ['.$output.']}';

		return $output;
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
		    $output .= "<span class='nametitle'>Name</span><br />\n<span class='name'>{$row["outlet_name"]}</span><br /><br />\n";
		    
		    // Check to see if there is a phone number for this outlet to determine the regex we use
		    if(strpos($html,'miniIconTelephoneRec')) {
			$phone = preg_replace('|.+<img class="pic20 picL" src="siImages/miniIconTelephoneRec.gif" />([0-9 ]+) <div.+|s', '\1', $html);
			$address = preg_replace('|.+<b>Information</b><div class="lineGreen"></div><div class="spacer5y"></div>(.+?)<img class="pic20.+|s', '\1', $html);
			$address = preg_replace('|<br />|s',', ',$address);
			$address = trim($address," \n\r\t,");
			$addressenc = urlencode($address);
			$mapsurl = "http://maps.google.com/maps?q=".$row['latitude'].','.$row['longitude'];
			$output .= "<span class='phonetitle'>Phone</span><br />\n<span class='phone'>$phone</span><br /><br />\n\n";
			$output .= "<span class='addresstitle'>Address</span><br />\n<span class='address'><a href='$mapsurl' target='_blank'>$address</a></span><br /><br />\n\n";
		    } else {
			$address = preg_replace('|.+<b>Information</b><div class="lineGreen"></div><div class="spacer5y"></div>(.+?)<div class="spacer1y">.+|s', '\1', $html);
			$address = preg_replace('|<br />|s',', ',$address);
			$address = trim($address," \n\r\t,");
			$addressenc = urlencode($address);
			$mapsurl = "http://maps.google.com/maps?q=".$row['latitude'].','.$row['longitude'];
			$output .= "<span class='addresstitle'>Address</span><br />\n<span class='address'><a href='$mapsurl' target='_blank'>$address</a></span><br /><br />\n\n";
		    }
		    
		    // Output the block of text which shows the opening hours, nicely marked up for CSS
		    if(strpos($html,'openHours')) {
			$openhours = preg_replace('|.+<div class="openHours">(.+?)<div class="spacer5y.+|s', '\1', $html);
			$openhours = preg_replace('|<b class="textGreen">(.+?)</b>|s', "\n".'<span class="openhoursperiodtext">\1</span><br />'."\n", $openhours);
			$openhours = trim($openhours," \n\r\t,");
			$openhours = preg_replace('| </div>|',"<br /><br />\n\n", $openhours);
			$openhours = preg_replace('|/>\n([^<].+?<br />.+?)<br />|s',"/>\n<span class='openhourstimetext'>".'\1'."</span><br />", $openhours);
			$output .= "<span class='openhourstitle'>Opening Hours</span><br />\n".$openhours;
		    }
		    
		    // Build SQL query to get outlet information for all selected types
		    $sql = "SELECT `recycle_types`.`recycle_type`,`recycle_types`.`name` FROM `outlets_recycle_types`,`recycle_types` WHERE `outlets_recycle_types`.`recycle_type`=`recycle_types`.`recycle_type` AND `outlet_id` = $id";
		    $query = $this->db->query($sql);
		    $output .= "<span class='outletypestitle'>What you can Recycle here:</span><br />\n";
		    // Output the block of text which shows the recycle types, nicely marked up for CSS
		    $i=0;
		    $count=$query->num_rows();
		    while( $row = $query->result_array() ) {
			$output = print_r($row,1);
			$output .= "<span class='recycle_type_{$row['recycle_type']}'>{$row['name']}";
			$i++; if($i!=$count) {
				$output .= ", </span>\n";
			} else {
				$output .= "</span><br /><br />\n\n";
			}
		    }
		return $output;
	}
}
?>