<?php
// Script start time - so we can see how long it takes at various stages
$time_start = microtime(true);
// Set counters to 0
$xmlcounter = 0;
$rowcounter = 0;
// Include file to convert OS grid reference format (easting/northing) to latitude/longitude
require("os_to_latlong.php");
// Connect to database
mysql_connect(localhost,"recycle_finder","leifgivesyoulemons");
@mysql_select_db("recycle_finder") or die( "Unable to select database");
// These numeric IDs correspond to specific recyclable types in the recyclescotland database
$recycle_type_ids = array(1,2,3,4,5,6,7,8,9,11,12,13,14,16,17,18,19,20,21,22,23,24,25,26,27,29,30,31,32,33,34,37,38,40,41,42,43,44,45,46,49,51,52,53,56,57,80,81,82,83,84,130,145,146,148,149,150,151,152);
// Loop through all the known recyclable types
foreach ($recycle_type_ids as $type) {
    // Output some more feedback to the browser
    echo "[TIME: ".round(microtime(true)-$time_start)."] Type $type started.<br />";
    // Loop through all known (33) areas in the database to get all possible data sets
    for ($area=1;$area<33;$area++) {
        // Output some feedback to the browser
        echo "[TIME: ".round(microtime(true)-$time_start)."] &nbsp;&nbsp;Area $area started; ";
        // Build an HTTP POST query to request XML data for a specific area+type combination
        $query = http_build_query ( array('areaId' => $area,'itemId' => $type) );
        // Add request headers to the query
        $contextData = array('method' => 'POST','header' => "Connection: close\r\nContent-Length: ".strlen($query)."\r\n",'content'=> $query );						
        // Encapsulate HTTP query in context format for PHP
        $context = stream_context_create (array ( 'http' => $contextData ));
        // Actually send the HTTP request and get the data from the recyclescotland server, finally!        
        $xml = file_get_contents ('http://www.recycleforscotland.com/rssMaps/mapProxy.asp', false, $context);
        // Prevent injection or other screwups
        $xmlsafe = mysql_real_escape_string($xml);
        // Insert block of XML into new row in the database
        mysql_query("INSERT INTO outletXML VALUES ('$type','$area','$xmlsafe')") or die(mysql_error()); 
        $xmlcounter++;
        // Output some feedback to the browser
        echo "XML saved, ";
        // Check for empty dataset
        if(strpos($xml,'<myOutlets></myOutlets>')) { echo "Found no outlets of type $type in area $area, skipping.<br />"; continue; }
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
            $latlongarr = E_N_to_Lat_Long($easting, $northing);
            $latitude = $latlongarr[0];
            $longitude = $latlongarr[1];
            // Skip if no id, not sure where this is coming from, probably a newline somewhere
            if($id==0 && $outletType==0) continue;
            $sql = "REPLACE INTO outlets (`outlet_id`, `outlet_type`, `outlet_name`, `area`, `latitude`, `longitude`, `coords`) 
                    VALUES ('$id', '$outletType', '$name', '$area', '$latitude', '$longitude', GeomFromText('POINT($latitude $longitude)'))";
            mysql_query($sql) or die(mysql_error()); 
            
            $sql = "REPLACE INTO outlets_recycle_types (`outlet_id`, `recycle_type`) 
                    VALUES ('$id', '$type')";
            mysql_query($sql) or die(mysql_error()); 
            
            $arearowcount++;
            $rowcounter++;
        }
        // Output some feedback to the browser
        echo "Inserted $arearowcount outlet types. Area $area complete!<br />";
    }
    // Output some more feedback to the browser
    echo "[TIME: ".round(microtime(true)-$time_start)."] Type $type complete!<br /><br />";
}
// Output final feedback to the browser
echo "<br />Recycle data update complete! Wrote $xmlcounter XML files and $rowcounter total rows to DB in ".floor((microtime(true)-$time_start)/60)." minutes.";
?>
