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

if(isset($_REQUEST['opslaan'])) {
	saveURL($_REQUEST['id'], $_SESSION['UserID'], $_REQUEST['active'], $_REQUEST['URL'], $_REQUEST['CC'], $_REQUEST['naam'], $_POST['min_price'], $_POST['max_price'], $_POST['min_afstand'], $_POST['max_afstand'], $_REQUEST['dagen'], $_REQUEST['uren']);
				
	if($_REQUEST['id'] != "") {
		echo "'<b>". getZoekString($_REQUEST['id']) ."</b>' $strEditChanged.";
		writeToLog($_REQUEST['id'], $strLogChanged);
	} else {
		echo "'<b>". $_REQUEST['q'] ."</b>' $strEditAdded";
		writeToLog('', $strLogAdded);
	}
} else {
	if(isset($_REQUEST['test'])) {		
		if(isset($_POST['active']))				$active = $_POST['active'];
		if(isset($_POST['uren']))					$uur = $_POST['uren'];
		if(isset($_POST['dagen']))				$dag = $_POST['dagen'];		
		if(isset($_POST['naam']))					$naam = $_POST['naam'];
		if(isset($_POST['CC']))						$CC = $_POST['CC'];
		if(isset($_POST['URL']))					$URL = $_REQUEST['URL'];
		if(isset($_POST['min_price']))		$pmin = $_REQUEST['min_price'];
		if(isset($_POST['max_price']))		$pmax = $_REQUEST['max_price'];
		if(isset($_POST['min_afstand']))	$dmin = $_REQUEST['min_afstand'];
		if(isset($_POST['max_afstand']))	$dmax = $_REQUEST['max_afstand'];
				
		echo "<a href='$URL' target='_new'>$strPreview</a>";
		echo "<p>";
		echo "<hr>";
		echo "<p>";		
	} elseif(isset($_REQUEST['id'])) {
		$data = getZoekData($_REQUEST['id']);
		
		$active		= $data['active'];
		$user			= $data['user'];
		$uur			= $data['uur'];
		$dag			= $data['dag'];
		$naam			= $data['naam'];
		$CC				= $data['CC'];
		$URL			= $data['URL'];
		$pmin			= $data['pmin'];
		$pmax			= $data['pmax'];
		$dmin			= $data['dmin'];
		$dmax			= $data['dmax'];
				
		if($user != $_SESSION['UserID']) {
			echo "Helaas je hebt geen toegang tot deze zoekterm.";
			echo "<p>\n";
			echo "<a href='index.php'><img src='../images/1_home.png'></a>\n";
    
			include ('../include/inc_footer.php');
			exit;
		}
	}	
		
	echo "<form method='post' action='$_SERVER[PHP_SELF]'>\n";
	if(isset($_REQUEST['id'])) {
		echo "<input type='hidden' name='id' value='". $_REQUEST['id'] ."'>\n";
	}
	echo "<b>$strActive</b><br>\n";
	echo "<input type='checkbox' id='active' name='active' value='1'". ($active == 1 ? ' checked' : '') .">$strActive.<br>\n";
	echo "<br>\n";
	echo "<b>Op welke dagen en uren controleren</b> (aantal opdrachten wat al op dat uur wordt uitgevoerd)<br>\n";
	echo "<table border=0>\n";
	echo "	<tr>\n";
	echo "	<td><table border=0>";
	
	$Namen = array(0 => 'Zondag', 1 => 'Maandag', 2 => 'Dinsdag', 3 => 'Woensdag', 4 => 'Donderdag', 5 => 'Vrijdag', 6 => 'Zaterdag');
	
	for($d = 0; $d < 7 ; $d++) {
		echo "<tr><td><input type='checkbox' name='dagen[$d]' value='1'". ((isset($dag[$d]) AND $dag[$d] == 1) ? ' checked' : '') ."> ". $Namen[$d] ."</td></tr>\n";
	}
	
	echo "	</table>\n";
	echo "	</td>\n";
	echo "	<td>&nbsp;</td>\n";
	echo "	<td>op</td>\n";
	echo "	<td>&nbsp;</td>\n";
	echo "	<td>\n";
	echo "		<table border=0>\n";
	
	for($h = 0; $h < 24 ; $h++) {
		$Termen		= getZoekTermen('', '', $h, 1);
		$nrTermen	= count($Termen);
	
		echo "<td><input type='checkbox' name='uren[$h]' value='1'". ((isset($uur[$h]) AND $uur[$h] == 1) ? ' checked' : '') ."> $h uur ($nrTermen)</td>\n";
		echo "<td>&nbsp;</td>\n";
		
		if(fmod($h,4) == 3) {
			echo "</tr>\n<tr>\n";
		}
	}
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "	</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<br>\n";
	echo "<b>Stuur een kopie van de resultaten naar</b><br>\n";
	echo "<input type='text' name='CC' size='50' value='$CC' alt='CC van resultaat naar'><br>\n";	
	echo "<br>\n";
	echo "<b>Logische Naam</b><br>\n";
	echo "<input type='text' name='naam' size='50' value='$naam' alt='Logische naam'><br>\n";	
	echo "<br>\n";
	echo "<b>Zoek-URL</b><br>\n";
	echo "<input type='text' name='URL' size='100' value='$URL' alt='Wat is de zoekopdracht die bij marktplaats.nl in de adresregel staat ?'><br>\n";	
	echo "<br>\n";
	echo "<b>Prijsrange</b> (leeglaten voor geen)<br>\n";
	echo "&euro; <input type='text' name='min_price' size='5' value='$pmin' alt='Wat is de minimale prijs'> t/m <input type='text' name='max_price' size='5' value='$pmax' alt='Wat is de maximale prijs'><br>\n";	
	echo "<br>\n";
	echo "<b>Afstanden</b> (leeglaten voor geen)<br>\n";
	echo "<input type='text' name='min_afstand' size='5' value='$dmin' alt='Wat is de minimale afstand'> t/m <input type='text' name='max_afstand' size='5' value='$dmax' alt='Wat is de maximale afstand'> km<br>\n";	
	echo "<br>\n";
	
	echo "<input type='submit' name='test' value='$strPreview'>&nbsp;&nbsp;<input type='submit' name='opslaan' value='$strSave'><br>\n";
	echo "</form>\n";
}

echo "<p>\n";
echo "<a href='index.php'><img src='../images/1_home.png' border='0'></a>\n";

include ('../include/inc_footer.php');
?>