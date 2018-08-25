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

if(isset($_REQUEST['term'])) {
	$advertenties = getAds($_REQUEST['term'], false);
	
	echo "<table border=0>\n";
	echo "<tr>\n";
	echo "	<td><b>ID</b></td>\n";
	echo "	<td>&nbsp;</td>\n";
	echo "	<td><b>Advertentie</b></td>\n";
	echo "	<td>&nbsp;</td>\n";
	echo "	<td><b>Online</b></td>\n";
	echo "	<td>&nbsp;</td>\n";
	echo "	<td><b>Toegevoegd</b></td>\n";
	echo "	<td>&nbsp;</td>\n";
	echo "	<td><b>Laatst gezien</b></td>\n";
	echo "<tr>\n";
	
	foreach($advertenties as $value) {
		$pageData = getPageDataByMarktplaatsID($value);
		$active = $pageData['active'];
		
		echo "<tr>\n";
		echo "	<td>$value</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td><a href='". urldecode($pageData['URL']) ."' target='_blank' class='". ($active == '1' ? 'StringActive' : 'StringInActive') ."'>". urldecode($pageData['title']) ."</a></td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>". date("d-m H:i", $pageData['datum']) ."</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>". date("d-m H:i", $pageData['added']) ."</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>". date("d-m H:i", $pageData['changed']) ."</td>\n";
		echo "<tr>\n";
	}
	echo "</table>\n";
} else {
	$Termen				= getZoekTermen($_SESSION['UserID'], '', '', 1);

	foreach($Termen as $term) {
		$data = getZoekData($term);
		echo "<a href='?term=$term'>". $data['naam'] ."</a><br>";
	}
}

?>