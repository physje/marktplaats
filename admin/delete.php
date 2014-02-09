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
$minUserLevel = 1;
$cfgProgDir = '../auth/';
include($cfgProgDir. "secure.php");
include ("../include/inc_head.php");		

if(isset($_REQUEST['id'])) {
	$String		= getZoekString($_REQUEST['id']);
	
	if(isset($_REQUEST['GoDelete'])) {		
		deleteURL($_REQUEST['id']);
		deleteData($_REQUEST['id']);
		deleteRSS($_REQUEST['id']);
		
		echo "'<b>$String</b>' $strDeleteOkay";
		
	} else {
		echo "$strDeleteSure '<b>$String</b>'?";
		echo "<p>";
		echo "<a href='?id=". $_REQUEST['id'] ."&GoDelete=true'>$strYes</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='?edit.php'>$strNo</a>";
	}	
} else {
	echo $strDeleteError;
}	

echo "<p>\n";
echo "<a href='index.php'><img src='../images/1_home.png' border='0'></a>\n";

include ('../include/inc_footer.php');
?>