<?php
// Modified from:
// http://www.sitepoint.com/forums/showthread.php?656315-adding-distance-gps-coordinates-get-bounding-box
/**
 * bearing is 0 = north, 180 = south, 90 = east, 270 = west
 *
 */
  function bpot_getDueCoords($latitude, $longitude, $bearing, $distance, $distance_unit = "m", $return_as_array = FALSE) {
  
    if ($distance_unit == "m") {
      // Distance is in miles.
		  $radius = 3963.1676;
    }
    else {
      // distance is in km.
      $radius = 6378.1;
    }
  
    //	New latitude in degrees.
    $new_latitude = rad2deg(asin(sin(deg2rad($latitude)) * cos($distance / $radius) + cos(deg2rad($latitude)) * sin($distance / $radius) * cos(deg2rad($bearing))));
    		
    //	New longitude in degrees.
    $new_longitude = rad2deg(deg2rad($longitude) + atan2(sin(deg2rad($bearing)) * sin($distance / $radius) * cos(deg2rad($latitude)), cos($distance / $radius) - sin(deg2rad($latitude)) * sin(deg2rad($new_latitude))));
    
    if ($return_as_array) {
      //  Assign new latitude and longitude to an array to be returned to the caller.
      $coord = array();
      $coord['lat'] = $new_latitude;
      $coord['lon'] = $new_longitude;
    }
    else {
      $coord = $new_latitude . "," . $new_longitude;
    }
    
    return $coord;
  
  }	
  ?>