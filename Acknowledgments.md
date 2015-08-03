We have used several code snippets and APIs to get this app to work.

# Backend #

The backend of this app is exclusively written in PHP and uses a MySQL database for the data set.

## MVC ##

After are initial revision, which was just a jumble of PHP files and and JS scripts, we opted to move to using the MVC model design to make are code more scalable and organized. We went ahead and implemented [CodeIgniter](http://codeigniter.com/) as our framework for the app.

## Location ##

Our data set is comprised of the location of various recycling points across Scotland. The data includes the location of these points in [northing and easting](http://en.wikipedia.org/wiki/Easting_and_northing) format. For simplistic we want it in standard latitude and longitude.

To do this we used the code snippet from [www.sitepoint.com](http://www.sitepoint.com/forums/showthread.php?656315-Adding-Distance-To-GPS-Coordinates-To-Get-Bounding-Box&s=8cb0e6cc7aa94cda82d5ad1d106b920f&p=4519646&viewfull=1#post4519646). The code is based upon the maths and functions available from [this blog](http://sgowtham.net/blog/2009/08/04/php-calculating-distance-between-two-locations-given-their-gps-coordinates/).

# Frontend #

The frontend is comprised of standard HTML5 and Javascript.

## Boilerplate ##

After a long winded fiasco wit IE9, we decided to us [HTML5BoilerPlate](http://html5boilerplate.com/) which include modernizr.js and normalize.css to make our site multi-browser compatible.

## Mapping ##

The obvious choice for mapping the data set was to use Google's Maps API. At the moment the app uses [v3](https://developers.google.com/maps/documentation/javascript/) of the JS API. Furthermore we use the [geolocationmarker](http://code.google.com/p/google-maps-utility-library-v3/source/browse/trunk/geolocationmarker/?r=388) and the [markerclusterer](http://code.google.com/p/google-maps-utility-library-v3/source/browse/trunk/markerclusterer) for some of the nice UI features and for Geolocation.

## JQuery ##

We have begun using [JQuery](http://jquery.com/) for the AJAX calls to the PHP backend.