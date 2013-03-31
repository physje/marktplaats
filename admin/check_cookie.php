<?php
//*********************************************************************
// Marktplaats Checker (c) 2006-2008 Matthijs Draijer
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

if($_GET[uitloggen] == true)
{
	setcookie ("LoggedIn", false);
	$message = 'U bent succesvol uitgelogd';
	include("interface.php");
	exit;
}

if(!isset($_POST['interface']))
{
	include("interface.php");
	exit;
}


if($_POST[loginname] == "" AND $_POST['interface'] == 'Ja')
{
	$message = 'Geen username';		
	include("interface.php");
	exit;
}

if($_POST[wachtwoord] == "" AND $_POST['interface'] == 'Ja')
{
	$message = 'Geen wachtwoord';
	include("interface.php");
	exit;
}

connect_db();
$sql		= "SELECT * FROM $TableUsers WHERE $UsersNaam like '". $_POST[loginname] ."' AND $UsersWachtwoord like '". $_POST[wachtwoord] ."';";
$result	= mysql_query($sql);

if($row = mysql_fetch_array($result)) {
	setcookie ("LoggedIn", true, time()+$CookieTime);
	setcookie ("level", $row[$UsersLevel], time()+$CookieTime);
	setcookie ("UserID", $row[$UsersID], time()+$CookieTime);	
	setcookie ("PC", $row[$UsersPostcode], time()+$CookieTime);
} else {
	$message = 'Ongeldige inloggegevens';
	include("interface.php");
	exit;
}

?>