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
$publicPage = true;
include ("../include/inc_head.php");

/*
$Termen				= getZoekTermen(0, 0);

foreach($Termen as $term) {
	$Pages	= getAds($term, true);
		
	foreach($Pages as $id) {		
		deletePage($term, $id);
	}
	
	$String		= getZoekString($term);
	echo "$String $strCleanCleaned<br>\n";
	
	writeToLog($term, 'Advertenties opgeschoond');
}
*/

$Termen = getZoekTermen('', '', '', 1);

foreach($Termen as $term) {
	// Oude advertenties verwijderen
	$OldPages	= getAds($term, true);
	
	foreach($OldPages as $id) {		
		//$data = getPageDataByMarktplaatsID($id);
		deletePage($term, $id);
	}
	
	$String		= getZoekString($term);
	echo "$String $strCleanCleaned<br>\n";
	
	//writeToLog($term, 'Advertenties opgeschoond');
}

cleanupLog();

echo "<br>\nlogs $strCleanCleaned<br>\n";

writeToLog('', 'Logs opgeschoond');

echo "<p>\n";
echo "<a href='index.php'><img src='../images/1_home.png' border='0'></a>\n";

include ('../include/inc_footer.php');
?>