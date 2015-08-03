# Introduction #
We use several PHP scripts to retrieve data from our MySQL database and from the data source to update our database.

The scripts are:
```
data.php
info.php
updatedata.php
updateinfo.php
```

## data.php ##

_data.php_ responds to HTTP GET requests. Once called, _data.php_ will connect to our MySQL server and retrieve all of the data related to the session variables specified. If any one session variable is not given, a default value is used instead.

These are the supported session variables and their defaults values:
```
/***********************************************************************
 * session variables for data.php
 *
 * types:
 *       A string of numbers (recyclables id) divided by commas ','
 *       possible options:
 *         '1,2,3,4,5,6,7,8,9,11,12,13,14,16,17,18,19,20,21,22,23,24,25,
 *          26,27,29,30,31,32,33,34,37,38,40,41,42,43,44,45,46,49,51,52,
 *          53,56,57,80,81,82,83,84,130,145,146,148,149,150,151,152'
 *
 * latitude:
 *       Latitude of origin
 *
 * longitude:
 *       Longitude of origin
 *
 * homeLatitude:
 *       homeLatitude of origin
 *
 * homeLongitude:
 *       homeLongitude of origin 
 *
 *       These home variables are perceived from the home interface of
 *       the website.
 *
 * distance:
 *       The interval from the origin (given by latitude and longitude)
 *       to search within. This interval defines a square that is 2n
 *       long on each side
 *
 * union:
 *       Is a special variable that defines whether or not the assigned
 *       values of $type are regarded as intersections of each other,
 *       or exclusive of each other when it comes to retrieving the 
 *       data from the database
 *
 */
types => 6,7,2,16,1 (stored as an array)
latitude => 55.9099
longitude => -3.3220
distance => 5
union => 1
```

### Example ###

Here is an example of using _data.php_:
```
# if data.php is called by itself, the default values are take, as displayed above
http://www.recyclefinder.co.uk/data // 190 points are given

To define the variables one has to define it via session variables.

So, if latitude is equal to 55.952994 and longitude to -3.189715 the same URL would return 203 points in the location of Balmoral hotel

If the distance variable is set to 100, it will return 1787 points.

If the type array is specified to be [1], the function outputs 2124 points.If the type array is specified to be [1,2,3], then it will result in 81 points, instead. This is because the types are connected via union method, thus reducing the number of result only to recycling points or centres that process all of specified recyclebles.
```

## info.php ##

_info.php_ responds to HTTP GET requests. Once called, _info.php_ will connect to our MySQL server and retrieve a data set relating to the session variable specified. If the session variable is not given, _**the script ends without error**_.

The **required** session variable is:
```
/***********************************************************************
 * session variable for info.php
 *
 * id:
 *       A single number referring to the unique id of a 
 *
 */
id
```

### Example ###

Here is an example of _info.php_ when id is 1:
```
http://www.recyclefinder.co.uk/info
```
Returns:
```
<span class='addresstitle'>Address</span><br />
<span class='address'>Next to shore, Shore Street, Lossiemouth, IV31 6PB</span><br /><br />
<span class='openhourstitle'>Opening Hours</span><br />Monday - Saturday: 8am - 4pm<br /><br />
<span class='outletypestitle'>What you can Recycle here:</span><br />
<span class='recycle_type_1'>Household metal packaging,</span>
<span class='recycle_type_2'>Cardboard,</span>
<span class='recycle_type_7'>Paper,</span>
<span class='recycle_type_11'>Clothing,</span>
<span class='recycle_type_13'>Yellow pages,</span>
<span class='recycle_type_18'>Books,</span>
<span class='recycle_type_25'>DVDs, CDs, videos and tapes,</span>
<span class='recycle_type_29'>Aerosol cans</span>
```

## updatedata.php ##
Loops through every single recycle type in recycleforscotland.com database and requests a list of both recycle points and centres with their coordinates and names from various sources. Once completed the script connects to the local database and stores all received data in outlets\_recycle\_types table.

## updateinfo.php ##
Loops through every single recycle outlet in recycleforscotland.com database and requests a list of both both recycle points and centres with their opening times, recyclable items and address. Once complete script connects to the local database and stores all receives data in outlet\_info table.

# API #

This app supports a few API calls via a HTTP GET request to a PHP script.

More details to follow...