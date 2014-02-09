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

if(isset($_REQUEST['forcedID'])) {
	$Termen		= array($_REQUEST['forcedID']);
} else {
	$Termen		= getZoekTermen($_SESSION['UserID'], 0);
}

foreach($Termen as $term) {
	makeRSSFeed($term, '../');
	echo "RSS-feed voor <i>". getZoekString($term) ."</i> gemaakt<br>\n";
}

include ('../include/inc_footer.php');
?>