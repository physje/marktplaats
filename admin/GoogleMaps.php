<?php
//*********************************************************************
// Marktplaats Checker (c) 2006-2013 Matthijs Draijer
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; version 2 of the License.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
//*********************************************************************

include ("../../general_include/general_config.php");
include ("../../general_include/shared_functions.php");
include ("../include/inc_config_general.php");
include ("../lng/language_$Language.php");
include ("../include/inc_functions.php");
//include ("../include/inc_head.php");
$db = connect_db();

if(isset($_REQUEST['term'])) {
	$term = $_REQUEST['term'];
} else {
	exit;
}

$TermData = getZoekData($term);

$icons[0] = 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png';
$icons[1] = 'http://maps.google.com/mapfiles/ms/icons/green-dot.png';
$icons[2] = 'http://maps.google.com/mapfiles/ms/icons/red-dot.png';
$icons[3] = 'http://maps.google.com/mapfiles/ms/icons/orange-dot.png';
$icons[4] = 'http://maps.google.com/mapfiles/ms/icons/yellow-dot.png';
$icons[5] = 'http://maps.google.com/mapfiles/ms/icons/purple-dot.png';

echo "<!DOCTYPE html>\n";
echo "<html>\n";
echo "<title>Marktplaats | ". $TermData['naam'] ."</title>\n";
echo "<body>\n";
echo "<h1>". $TermData['naam'] ."</h1>\n";
echo "<div id='map' style='width:90%;height:650px'></div>\n";
echo "<script>\n";
echo "function myMap() {\n";
echo "  var deventer = new google.maps.LatLng(". str_replace(',', '.', $HomeLong) .", ". str_replace(',', '.', $HomeLat) .");\n";
echo "\n";
echo "  var mapCanvas = document.getElementById('map');\n";
echo "  var mapOptions = {center: deventer, zoom: 8};\n";
echo "  var map = new google.maps.Map(mapCanvas, mapOptions);\n";

$sql = "SELECT * FROM $TableData WHERE $DataZoekterm = $term AND $DataNotSeen < 5 AND $DataPlaats NOT LIKE '' AND $DataPlaats NOT LIKE 'Bezorgt%' GROUP BY $DataPlaats";
$result	= mysqli_query($db, $sql);
$row	= mysqli_fetch_array($result);

do {
	$plaats = $row[$DataPlaats];
	$code = $plaats;
	//$code = urldecode($code);
	$code = str_replace('+', '', $code);
	$code = str_replace('.', '', $code);
	$code = str_replace('-', '', $code);
	//$code = str_replace("'", '', $code);
	$code = str_replace('%26%23039%3B', '', $code);	
	$code = str_replace('%C3%A2', 'a', $code);
	$icon = $icons[1];
	
	$infowindow	= array();
	$infowindow[]	= "<h1>$plaats</h1>";
			
	$sql			= "SELECT * FROM $TableData WHERE $DataZoekterm = $term AND $DataNotSeen < 5 AND $DataPlaats like '$plaats'";
	$result_2	= mysqli_query($db, $sql);
	$row_2		= mysqli_fetch_array($result_2);
	
	do {
		$pictures = explode('|', $row[$DataPlaatje]);
		$infowindow[] = "<a href='http://link.marktplaats.nl/m". $row_2[$DataMarktplaatsID] ."'>". urldecode($row_2[$DataTitle]) ."</a><br>";
		foreach($pictures as $plaatje) {
			$infowindow[] = "<img src='http:". $plaatje ."'>&nbsp;";
		}
	} while($row_2 = mysqli_fetch_array($result_2));

		
	$sql_coord		= "SELECT * FROM $TableCoord WHERE $CoordPlaats like '$plaats'";
	$result_coord	= mysqli_query($db, $sql_coord);
	$row_coord		= mysqli_fetch_array($result_coord);

	echo "\n";
	echo "  var $code = new google.maps.Marker({icon:'$icon', position:new google.maps.LatLng(". $row_coord[$CoordLongitude] .", ". $row_coord[$CoordLatitude] .")});\n";
	echo "  $code.setMap(map);\n";
	echo "  google.maps.event.addListener($code,'click',function() {var infowindow = new google.maps.InfoWindow({content:\"". implode("", $infowindow) ."\"}); infowindow.open(map,$code);});\n";
} while($row = mysqli_fetch_array($result));

$API	= "AIzaSyDPTo6hWttkOe_jo3Aq2j3iRhSkbfPmNb4";

echo "}\n";
echo "</script>\n";
echo "\n";
echo "<script src='https://maps.googleapis.com/maps/api/js?key=$API&callback=myMap'></script>\n";
echo "\n";
//echo "<table width='90%'>\n";
//echo "<tr><td valign='top'>\n";
//echo "</td></tr>\n";
//echo "</table>\n";
echo "</body>\n";
echo "</html>\n";

?>
