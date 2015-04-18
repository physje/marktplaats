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

include ("../general_include/general_config.php");
include ("../general_include/shared_functions.php");
include ("include/inc_config_general.php");
include ("lng/language_$Language.php");
include ("include/inc_functions.php");
$landingPage = true;
$publicPage = true;
include ("include/inc_head.php");

echo "$strIndexMain <b>$ScriptTitle</b>\n";
echo "<p>\n";
echo "$strIndexDownload <a href='$ScriptDownload'>$ScriptTitle $Version</a>\n";

include ('include/inc_footer.php');
?>
