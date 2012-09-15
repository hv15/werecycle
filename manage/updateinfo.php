<?php
// Script start time - so we can see how long it takes at various stages
$time_start = microtime(true);
// Set total count of info written to 0
$counter = 0;

// Connect to database
mysql_connect(localhost,"recycle_finder","leifgivesyoulemons");
@mysql_select_db("recycle_finder") or die( "Unable to select database");
// Select all the outlet IDs in the database
$result = mysql_query("SELECT outlet_id FROM outlets");
// Loop through the IDs
while($row = mysql_fetch_array($result)) {
    // Build an HTTP POST query to request XML data for a specific area+type combination
    $query = http_build_query ( array('theID' => "{$row['outlet_id']}|Recycle") );
    // Add request headers to the query
    $contextData = array('method' => 'POST','header' => "Connection: close\r\nContent-Length: ".strlen($query)."\r\n",'content'=> $query );						
    // Encapsulate HTTP query in context format for PHP
    $context = stream_context_create (array ( 'http' => $contextData ));
    // Actually send the HTTP request and get the data from the recyclescotland server, finally!        
    $html = file_get_contents ('http://www.recycleforscotland.com/tools/singleOutletScript.asp', false, $context);
    
    // APPLY REGEXES TO HTML TO MAKE IT NICE!


    // Put safe HTML in the info table in the database
    $htmlsafe = mysql_real_escape_string($html);
    $sql = "REPLACE INTO outlets_info (`outlet_id`, `html_info`) 
            VALUES ('{$row['outlet_id']}', '$htmlsafe')";
    mysql_query($sql) or die(mysql_error()); 
    echo "Inserted HTML info for outlet ID: ".$row['outlet_id']."<br />";
    
    // Output some feedback to the browser
    // echo (microtime(true)-$time_start)."s: Wrote 'data/{$type}/{$area}.xml'.<br />";
    $counter++;
 }
// Output final feedback to the browser
echo "<br />Recycle outlet info update complete! Wrote $counter HTML snippets to DB in ".floor((microtime(true)-$time_start)/60)." minutes.";
?>
