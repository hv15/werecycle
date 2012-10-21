<?php
class Update extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		require_once(APPPATH.'libraries/phpcoord-2.3.php');
	}

	public function output($string) 
	{
		file_put_contents("/home/recycle/public_html/updatelog.html", $string, FILE_APPEND);
	}
	
	public function recycleForScotland()
	{		
		// Script start time - so we can see how long it takes at various stages
		$time_start = microtime(true);
		// Set counters to 0
		$xmlcounter = 0;
		$rowcounter = 0;
		
		// These numeric IDs correspond to specific recyclable types in the recyclescotland database
		$recycle_type_ids = array(1,2,3,4,5,6,7,8,9,11,12,13,14,16,17,18,19,20,21,22,23,24,25,26,27,29,30,31,32,33,34,37,38,40,41,42,43,44,45,46,49,51,52,53,56,57,80,81,82,83,84,130,145,146,148,149,150,151,152);
		// Loop through all the known recyclable types
		foreach ($recycle_type_ids as $type) {
		    // Output some more feedback to the browser
		    $this->output( "[TIME: ".round(microtime(true)-$time_start)."] Type $type started.<br />");
		    // Loop through all known (33) areas in the database to get all possible data sets
		    for ($area=12;$area<13;$area++) {
			// Output some feedback to the browser
			$this->output( "[TIME: ".round(microtime(true)-$time_start)."] &nbsp;&nbsp;Area $area started; ");
			// Build an HTTP POST query to request XML data for a specific area+type combination
			$query = http_build_query ( array('areaId' => $area,'itemId' => $type) );
			// Add request headers to the query
			$contextData = array('method' => 'POST','header' => "Connection: close\r\nContent-Length: ".strlen($query)."\r\n",'content'=> $query );						
			// Encapsulate HTTP query in context format for PHP
			$context = stream_context_create (array ( 'http' => $contextData ));
			// Actually send the HTTP request and get the data from the recyclescotland server, finally!        
			$xml = file_get_contents ('http://www.recycleforscotland.com/rssMaps/mapProxy.asp', false, $context);
			
			$xmlcounter++;
			// Output some feedback to the browser
			$this->output( "XML loaded, ");
			// Check for empty dataset
			if(strpos($xml,'<myOutlets></myOutlets>')) { $this->output("Found no outlets of type $type in area $area, skipping.<br />"); continue; }
			// Remove header and footer XML from string
			$xml = preg_replace('|.+myOutlets>(.+)</myOutlets></mapresponse>|', '\1', $xml);
			// Add newline after each recycle point / outlet for easier separation
			$xml = preg_replace('|</outlet>|', "</outlet>\n", $xml);
			// Replace XML formatted data with delimited syntax, remove unneccessary distanct/markup
			$pipedelimited = preg_replace('|<outlet id="(.+)" outlettypeid="(.+)" easting="(.+)" northing="(.+)"><outletname>(.+)</outletname><distance>.+</distance></outlet>|', '\1|\2|\3|\4|\5', $xml);
			// Split string into array of outlets, then each outlet into array of data
			$areaarray = explode("\n",$pipedelimited);
			
			// Loop through every outlet, convert lat/long and insert into database
			$arearowcount=0;
			foreach($areaarray as $outlet) {
			    $outlet = explode('|',$outlet);
			    $id = $outlet[0];
			    $outletType = $outlet[1];
			    $easting = $outlet[2];
			    $northing = $outlet[3];
			    $name = mysql_real_escape_string($outlet[4]);
				$phone = null;
				$openhours = null;
				$address = '';
			    
			    $os1 = new OSRef($easting, $northing);
			    $ll1 = $os1->toLatLng();
				$ll1->OSGB36ToWGS84();

			    $latitude = $ll1->lat;
			    $longitude = $ll1->lng;
			    
				// Build an HTTP POST query to request XML data for a specific area+type combination
				$query = http_build_query ( array('theID' => "$id|Recycle") );
				// Add request headers to the query
				$contextData = array('method' => 'POST','header' => "Connection: close\r\nContent-Length: ".strlen($query)."\r\n",'content'=> $query );						
				// Encapsulate HTTP query in context format for PHP
				$context = stream_context_create (array ( 'http' => $contextData ));
				// Actually send the HTTP request and get the data from the recyclescotland server, finally!        
				$html = file_get_contents ('http://www.recycleforscotland.com/tools/singleOutletScript.asp', false, $context);
			    
				/* DEBUG HTML REGEX
			    $sql = "REPLACE INTO outlets_info_html VALUES ('$id', '".mysql_real_escape_string($html)."')";
			    $this->db->query($sql);*/
		
				// Check to see if there is a phone number for this outlet to determine the regex we use
				if(strpos($html,'miniIconTelephoneRec')) {
					$phone = preg_replace('|.+<img class="pic20 picL" src="siImages/miniIconTelephoneRec.gif" />(.+?) <div.+|s', '\1', $html);
					$address = preg_replace('|.+<b>Information</b><div class="lineGreen"></div><div class="spacer5y"></div>(.+?)<img class="pic20.+|s', '\1', $html);
					$address = preg_replace('|<br />|s',', ',$address);
					$address = trim($address," \n\r\t,");
				} else {
					$address = preg_replace('|.+<b>Information</b><div class="lineGreen"></div><div class="spacer5y"></div>(.+?)<div class="spacer1y">.+|s', '\1', $html);
					$address = preg_replace('|<br />|s',', ',$address);
					$address = trim($address," \n\r\t,");
				}
		
				// Output the block of text which shows the opening hours, nicely marked up for CSS
				if(strpos($html,'openHours')) {
					$openhours = preg_replace('|.+<div class="openHours">(.+?)</div.+|s', '\1', $html);
					if (!preg_match("/[0-9]/", $openhours)) $openhours = null;
				}
							    
			    // Skip if no id, not sure where this is coming from, probably a newline somewhere
			    if($id==0 && $outletType==0) continue;
				
				// Sanitize
				$phone = mysql_real_escape_string($phone);
				$address = mysql_real_escape_string($address);
				$openhours = mysql_real_escape_string($openhours);
				
			    $sql = "REPLACE INTO outlets (`outlet_id`, `latitude`, `longitude`, `coords`) VALUES ('$id', '$latitude', '$longitude', GeomFromText('POINT($latitude $longitude)'))";
			    $this->db->query($sql);
				
			    $sql = "REPLACE INTO outlets_data (`outlet_id`, `key`, `value`) VALUES ('$id', 'name', '$name')";
			    $this->db->query($sql);
			    $sql = "REPLACE INTO outlets_data (`outlet_id`, `key`, `value`) VALUES ('$id', 'type', '$outletType')";
			    $this->db->query($sql);
			    $sql = "REPLACE INTO outlets_data (`outlet_id`, `key`, `value`) VALUES ('$id', 'area', '$area')";
			    $this->db->query($sql);
			    $sql = "REPLACE INTO outlets_data (`outlet_id`, `key`, `value`) VALUES ('$id', 'address', '$address')";
			    $this->db->query($sql);
			    $sql = "REPLACE INTO outlets_data (`outlet_id`, `key`, `value`) VALUES ('$id', 'phone', '$phone')";
			    $this->db->query($sql);
			    $sql = "REPLACE INTO outlets_data (`outlet_id`, `key`, `value`) VALUES ('$id', 'openhours', '$openhours')";
			    $this->db->query($sql);
			    
			    $sql = "REPLACE INTO outlets_recycle_types (`outlet_id`, `recycle_type`) VALUES ('$id', '$type')";
			    $this->db->query($sql);
			    
			    $arearowcount++;
			    $rowcounter++;
			}
			// Output some feedback to the browser
			$this->output( "Inserted $arearowcount outlet types. Area $area complete!<br />");
		    }
		    // Output some more feedback to the browser
		    $this->output( "[TIME: ".round(microtime(true)-$time_start)."] Type $type complete!<br /><br />");
		}
		// Output final feedback to the browser
		$this->output( "<br />Recycle data update complete! Read $xmlcounter XML files and wrote $rowcounter total rows to DB in ".floor((microtime(true)-$time_start)/60)." minutes." );
		
		echo "Done. <a href='/updatelog.html'>Log</a>";
	}
	
}
?>
