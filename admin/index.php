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

if($_SESSION['level'] > 1) {
	echo "<a href='../check.php'>$strAdminCheck</a>\n";
	echo "<p>\n";
}

echo "<a href='termen.php'>$strAdminView</a>\n";
echo "<p>\n";
echo "<a href='edit.php'>$strAdminAdd</a>\n";
echo "<p>\n";

if($_SESSION['level'] > 1) {
	//echo "<a href='groep.php'>$strAdminSync</a>\n";
	//echo "<p>\n";
	echo "<a href='cleanup.php'>$strAdminClean</a>\n";
	echo "<p>\n";
	echo "<a href='log.php'>$strAdminLog</a>\n";
	echo "<p>\n";
	echo "<a href='account.php?new=true'>$strMakeAccount</a>\n";
	echo "<p>\n";
	echo "<a href='verdeling.php'>[Bekijk de verdeling]</a>\n";
	echo "<p>\n";
}

echo "<a href='notepad.php'>Kladblok</a>\n";
echo "<p>\n";
echo "<a href='makeOPML.php'>[Download al je RSS-feeds als OPML-file]</a>\n";
echo "<p>\n";
echo "<a href='account.php'>$strChangeAccount</a>\n";
echo "<p>\n";
echo "<a href='http://www.marktplaats.nl/' target='_blank'>$strAdminSite</a>\n";
echo "<p>\n";
//echo "<a href='?uitloggen=true'>$strLogOff</a>\n";
echo "<a href='". $cfgProgDir ."objects/logout.php'>$strLogOff</a>\n";

include ('../include/inc_footer.php');
?>