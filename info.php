<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0,user-scalable=no,width=device-width" />
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=9"/>
		<title>RecycleFinder</title>
		<link type="text/css" rel="stylesheet" href="styles/main.css"/>
		<link type="text/css" rel="stylesheet" href="styles/index.css"/>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
		<script type="text/javascript" src="scripts/index.js"></script>
	</head>
	<body>
		<div id="Info">
<?php      
    // Connect to database
    mysql_connect(localhost,"recycle_finder","leifgivesyoulemons");
    @mysql_select_db("recycle_finder") or die( "Unable to select database");
    // Return nothing if no id specified
    if(empty($_GET['id'])) die;
    $id = trim($_GET['id']);
    
    // Build SQL query to get outlet information for all selected types
    $sql = "SELECT * FROM outlets_info,outlets WHERE outlets.outlet_id = $id AND outlets.outlet_id = outlets_info.outlet_id";
    $result = mysql_query($sql) or die(mysql_error()); 
    $row = mysql_fetch_assoc($result);
    $html = $row["html_info"];
    
    // Output name of outlet
    echo "<span class='nametitle'>Name</span><br />\n<span class='name'>{$row["name"]}</span><br /><br />\n";
    
    // Check to see if there is a phone number for this outlet to determine the regex we use
    if(strpos($html,'miniIconTelephoneRec')) {
        $phone = preg_replace('|.+<img class="pic20 picL" src="siImages/miniIconTelephoneRec.gif" />([0-9 ]+) <div.+|s', '\1', $html);
        $address = preg_replace('|.+<b>Information</b><div class="lineGreen"></div><div class="spacer5y"></div>(.+?)<img class="pic20.+|s', '\1', $html);
        $address = preg_replace('|<br />|s',', ',$address);
	$address = trim($address," \n\r\t,");
	$addressenc = urlencode($address);
	$mapsurl = "http://maps.google.com/maps?q=".$row['latitude'].','.$row['longitude'];
	echo "<span class='phonetitle'>Phone</span><br />\n<span class='phone'>$phone</span><br /><br />\n\n";
	echo "<span class='addresstitle'>Address</span><br />\n<span class='address'><a href='$mapsurl' target='_blank'>$address</a></span><br /><br />\n\n";
    } else {
        $address = preg_replace('|.+<b>Information</b><div class="lineGreen"></div><div class="spacer5y"></div>(.+?)<div class="spacer1y">.+|s', '\1', $html);
        $address = preg_replace('|<br />|s',', ',$address);
	$address = trim($address," \n\r\t,");
	$addressenc = urlencode($address);
	$mapsurl = "http://maps.google.com/maps?q=".$row['latitude'].','.$row['longitude'];
	echo "<span class='addresstitle'>Address</span><br />\n<span class='address'><a href='$mapsurl' target='_blank'>$address</a></span><br /><br />\n\n";
    }
    
    // Output the block of text which shows the opening hours, nicely marked up for CSS
    if(strpos($html,'openHours')) {
        $openhours = preg_replace('|.+<div class="openHours">(.+?)<div class="spacer5y.+|s', '\1', $html);
        $openhours = preg_replace('|<b class="textGreen">(.+?)</b>|s', "\n".'<span class="openhoursperiodtext">\1</span><br />'."\n", $openhours);
        $openhours = trim($openhours," \n\r\t,");
        $openhours = preg_replace('| </div>|',"<br /><br />\n\n", $openhours);
        $openhours = preg_replace('|/>\n([^<].+?<br />.+?)<br />|s',"/>\n<span class='openhourstimetext'>".'\1'."</span><br />", $openhours);
        echo "<span class='openhourstitle'>Opening Hours</span><br />\n".$openhours;
    }
    
    
    // Build SQL query to get outlet information for all selected types
    $sql = "SELECT `recycle_types`.`recycle_type`,`recycle_types`.`name` FROM `outlets_recycle_types`,`recycle_types` WHERE `outlets_recycle_types`.`recycle_type`=`recycle_types`.`recycle_type` AND `outlet_id` = $id";
    $result = mysql_query($sql) or die(mysql_error()); 
    echo "<span class='outletypestitle'>What you can Recycle here:</span><br />\n";
    // Output the block of text which shows the recycle types, nicely marked up for CSS
    $i=0;
    $count=mysql_num_rows($result);
    while( $row = mysql_fetch_assoc($result) ) {
        echo "<span class='recycle_type_{$row['recycle_type']}'>{$row['name']}";
        $i++; if($i!=$count) echo ',';
        echo "</span>\n";
    }
?>

		</div>
	</body>
</html>