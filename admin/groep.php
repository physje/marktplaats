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
include ("../../general_include/general_functions.php");
include ("../include/inc_config_general.php");
include ("../lng/language_$Language.php");
include ("../include/inc_functions.php");
$minAcces = 2;
include ("../include/inc_head.php");		

if(isset($_REQUEST['GoEmpty']))
{
	$base_url = "http://kopen.marktplaats.nl/searchadvanced.php";
	$groep		= getGroup($base_url, 'name="g"', '/select>');
	
	$db		 		= connect_db();	
	$sql_1		= "DELETE FROM $TableGroep;";
	$sql_2		= "DELETE FROM $TableSubGroep;";
	
	if(mysql_query($sql_1)) {
		echo "$strDeleteGroups<br>";
	}
	
	if(mysql_query($sql_2)) {
		echo "$strDeleteSubgroups<br>";
	}
	
	foreach ($groep as $key => $value) {
		$sql		= "INSERT INTO $TableGroep ($GroepGroep, $GroepNaam) VALUES ('$key', '". urlencode($value)."');";
		
		if(mysql_query($sql)) {
			echo "'$value' $strAdded<br>\n";
		} else {
			echo "'$value' $strNotAdded<br>\n";
		}		
	
		$subgroep = getGroup($base_url."?g=$key", 'name="u"', '/select>');
		
		foreach ($subgroep as $subkey => $subvalue) {
			$sql	="INSERT INTO $TableSubGroep ($SubGroepGroep, $SubGroepSubGroep, $SubGroepNaam) VALUES ('$key', '$subkey', '". urlencode($subvalue)."');";
			
			if(mysql_query($sql)) {
				echo "'$subvalue' $strOf '$value' $strAdded<br>\n";
			} else {
				echo "'$subvalue' $strOf '$value' $strNotAdded<br>\n";
			}		
		}
		echo "<p>";
	}
	echo "<p>";
	echo "<a href='index.php'><img src='../images/1_home.png'></a></a>";	
} else {
	echo "$strDeleteAllGroups ?";
	echo "<p>";
	echo "<a href='?GoEmpty=true'>$strYes</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='?admin.php'>$strNo</a>";
}

include ('../include/inc_footer.php');
?>