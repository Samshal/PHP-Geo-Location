<?php
	/*
		This package is distributed with a composer.json file.
		If you have composer installed, you have to install the contents of the file
		It creates an autoload class that contains the Unirest Class.
		For users without composer, you need to download the Unirest class provided by Mashape.
		You can find it on Github.

		After getting the necessary autoloads, you need to require the vendor/autoload.php file
		so that the Unirest class can be included in your script. Then include the geolocation.class.php
		class too.
	*/

	require_once("vendor/autoload.php");
	require_once("geolocation.class.php");
	
	use Unirest\Request; //We use the Request class thats created in the Unirest namespace to make life easier later on

	$geoLocation = new GeoLocation(new Request()); //Instantiate the GeoLocation() class, accepts a Unirest\Request Object (class) as parameter

	$locationToSearchFor = "Abuja"; //This can be any place in the world. It can be a city, village, country, state and so on.

	$location = $geoLocation->getLocation($locationToSearchFor); //queries the api server for the complete geo-data of the location you searched for

	/*
		You can var_dump or print_r the $location variable to get a full look at the values returned by the getLocation() method
	*/
	$matches = $geoLocation->getMatches(); //the getLocation method returns more than one result, you use this method to count the number of results returned
	
	/* We loop through all the results returned below */
	for ($count = 0; $count < $matches; $count++)
	{
		echo "Name: ".$geoLocation->getName($count) . "<br/>"; //The name of the current matched result
		echo "Type: ".$geoLocation->getType($count) . "<br/>"; //The type, could be city, country, state and so on
		echo "Country: ".$geoLocation->getCountry($count) ."<br/>"; //The country where the matched result is located
		echo "Zip Code: ".$geoLocation->getZipCode($count) . "<br/>"; //the zip code of the location that's searched for
		echo "Magic Code: ".$geoLocation->getMagicCode($count) . "<br/>"; //The magic code of the matched result
		echo "WMO Identifier: ".$geoLocation->getWMOID($count). "<br/>"; //The WMO Identifier
		echo "Time Zone: ".$geoLocation->getTimeZone($count)."<br/>"; //The current time zone of the matched result
		echo "Standard Time Zone Abbreviation: ".$geoLocation->getStandardTimeZone($count)."<br/>"; //returns the abbreviated standard time zone
		echo "Longitude: ".$geoLocation->getLongitude($count)."<br/>"; //returns the longitude of the location returned
		echo "Latitude: ".$geoLocation->getLatitude($count)."<br/>"; //returns the latitude of the returned location
		echo "<hr/>"; //an horizontal rule to separate our results
	}


	/* You can browse through the geolocation.class.php file to get further details about the methods and
		look at other ways you can achieve the same task. for instance you could also get the longitude of a
		place by doing this:
			echo $geoLocation->getLatLong($count)["Lon"];
	*/