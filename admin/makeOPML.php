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

$User		= getUserData($_SESSION['UserID']);
$TermenInactive	= getZoekTermen($_SESSION['UserID'], '', '', 0);
$TermenActive		= getZoekTermen($_SESSION['UserID'], '', '', 1);

$OPML = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
$OPML .= "<opml version=\"1.0\">\n";
$OPML .= "	<head>$ScriptTitle | ". $User['naam'] ."</head>\n";
$OPML .= "	<body>\n";

for($i = 0; $i < 2 ; $i++) {
	if($i == 0) {
		$Termen = $TermenInactive;
		$naam = 'Uitgeschakeld';
	} else {
		$Termen = $TermenActive;
		$naam = 'Ingeschakeld';
	}

	$OPML .= "		<outline title=\"$ScriptTitle | $naam\" text=\"$ScriptTitle | $naam\">\n";

	foreach($Termen as $term) {
		$ZoekData	= getZoekData($term);
		$String		= $ZoekData['naam'];
		$RSSkey		= $ZoekData['key'];
		
		$OPML .= "			<outline text=\"". $String ."\" xmlUrl=\"". $ScriptRoot ."RSS/". $RSSkey .".xml\" type=\"rss\" version=\"RSS\"></outline>\n";
	}
	$OPML .= "		</outline>\n";
}

$OPML .= "	</body>\n";
$OPML .= "</opml>\n";

header("Expires: Mon, 26 Jul 2001 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache");
header("Cache-control: private");
header('Content-type: application/ov2');
header('Content-Disposition: attachment; filename="'. $ScriptTitle .'_'. $User['naam'] .'.opml"');

print $OPML;
?>