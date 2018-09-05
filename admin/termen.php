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
$minUserLevel = 1;
$cfgProgDir = '../auth/';
include($cfgProgDir. "secure.php");

include ("../include/inc_head.php");

$Termen				= getZoekTermen($_SESSION['UserID'], '', '', '');

echo "<table>";

foreach($Termen as $term) {
	$ZoekData	= getZoekData($term);
	$UserData = getUserData($ZoekData['user']);
	$String		= getZoekString($term);	
	//$String		= $ZoekData['q'];
	$RSSkey		= $ZoekData['key'];
	$aan			= isActive($term);
	$results	= getNumberOfAds($term, false);
	//$old			= getNumberOfAds($term, true);
			
	echo "<tr>\n";
	echo "	<td><a href='edit.php?id=$term'><img src='../images/1_edit.png' border='0'></a></td>\n";
	echo "	<td>&nbsp;</td>\n";
	echo "	<td><a href='delete.php?id=$term'><img src='../images/1_drop.png' border='0'></a></td>\n";
	echo "	<td>&nbsp;</td>\n";
	echo "	<td><font class='". ($aan ? 'StringActive' : 'StringInActive') ."'>$String</font></td>\n";
	echo "	<td>&nbsp;</td>\n";
	echo "	<td>$results</td>\n";
	//echo "	<td>&nbsp;</td>\n";
	//echo "	<td>$old</td>\n";
	echo "	<td>&nbsp;</td>\n";
	//echo "	<td><a href='$url' target='new'>$strCheckResults</a></td>\n";
	echo "	<td><a href='". addPCtoURL($ZoekData['URL'], $UserData['postcode']) ."' target='new'><img src='../images/magnifying.jpg' border='0' height=16></a></td>\n";	
	echo "	<td>&nbsp;</td>\n";
	echo "	<td><a href='../check.php?forcedID=$term'><img src='../images/refresh.png' border='0' height=16></a></td>\n";
	echo "	<td>&nbsp;</td>\n";
	
	if(file_exists("../RSS/". $RSSkey .".xml")) {
		echo "	<td><a href='". $ScriptRoot ."RSS/". $RSSkey .".xml' target='new'><img src='../images/ico_rss.gif' border='0'></a></td>\n";
	} else {
		echo "	<td>&nbsp;</td>\n";
	}
	
	echo "	<td>&nbsp;</td>\n";
	echo "	<td><a href='makeRSS.php?forcedID=$term'><img src='../images/rss_refresh.gif' border='0' height=16></a></td>\n";
	echo "	<td>&nbsp;</td>\n";
	echo "	<td><a href='GoogleMaps.php?term=$term'><img src='../images/maps.png' border='0' height=16></a></td>\n";
	echo "	<td>&nbsp;</td>\n";
	
	echo "</tr>\n";
}

echo "</table>";
echo "<p>";
echo "<a href='index.php'><img src='../images/1_home.png' border='0'></a> | <a href='edit.php'>$strAdminAdd</a>";

include ('../include/inc_footer.php');
?>