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

if($_POST['opslaan']) {
	$db = connect_db();
	
	if($_POST['new'] AND $_SESSION['level'] > 1) {
		$sql		= "INSERT INTO $TableUsers ($UsersNaam, $UsersWachtwoord, $UsersMail, $UsersHTML, $UsersRSS, $UsersPostcode) VALUES ('". $_POST[naam] ."', '". md5($_POST[wachtwoord]) ."', '". $_POST[mail] ."', '". $_POST[type] ."', '". $_POST[rss] ."', '". $_POST[pc] ."')";
		if(mysql_query($sql)) {	echo $_POST[naam] . ' '. $strEditAdded;}
	} else {		
		$sql		= "UPDATE $TableUsers SET ";
		$sql		.= "$UsersNaam = '". $_POST[naam] ."', ";		
		if($_POST[wachtwoord] != '') {
			$sql		.= "$UsersWachtwoord = '". md5($_POST[wachtwoord]) ."', ";
		}
		$sql		.= "$UsersMail = '". $_POST[mail] ."', ";
		$sql		.= "$UsersHTML = '". $_POST[type] ."', ";
		$sql		.= "$UsersRSS = '". $_POST[rss] ."', ";
		$sql		.= "$UsersPostcode = '". $_POST[pc] ."' ";
		$sql		.= "WHERE $UsersID = ". $_SESSION['UserID'] .";";
		
		if(mysql_query($sql)) {	echo $_POST[naam] . ' '. $strEditChanged; }
	}	
} else {	
	echo "<form method='post' action='$_SERVER[PHP_SELF]'>\n";
	
	if(isset($_REQUEST['new']) AND $_SESSION['level'] > 1) {
		echo "<input type='hidden' name='new' value='true'>\n";
	} else {
		$data = getUserData($_SESSION['UserID']);
	}	
	
	echo "<b>$strAccountName</b><br>\n";
	echo "<input type='text' name='naam' size='50' value='". $data['naam'] ."'><br>\n";
	echo "<br>\n";
	echo "<b>$strAccountPW</b><br>\n";
	echo "<input type='password' name='wachtwoord' size='50' value=''><br>\n";
	echo "<br>\n";
	echo "<b>$strAccountMail</b><br>\n";
	echo "<input type='text' name='mail' size='50' value='". $data['mailadres'] ."'><br>\n";
	echo "<br>\n";
	echo "<b>$strAccountZipcode</b><br>\n";
	echo "<input type='text' name='pc' size='50' value='". $data['postcode'] ."'><br>\n";
	echo "<br>\n";
	echo "<b>Notificatie</b><br>\n";
	echo "<select size='1' name='rss'>\n";
	echo "	<option value='1'". ($data['RSS'] == 1 ? ' selected' : '') .">RSS</option>\n";
	echo "	<option value='0'". ($data['RSS'] == 0 ? ' selected' : '') .">mail</option>\n";
	echo "	<option value='2'". ($data['RSS'] == 2 ? ' selected' : '') .">beide</option>\n";
	echo "</select><br>\n";
	echo "<br>\n";
	echo "<b>Indien mail, type e-mail</b><br>\n";
	echo "<select size='1' name='type'>\n";
	echo "	<option value='1'". ($data['HTML'] == 1 ? ' selected' : '') .">HTML</option>\n";
	echo "	<option value='0'". ($data['HTML'] == 0 ? ' selected' : '') .">Plain</option>\n";
	echo "</select><br>\n";
	echo "<br>\n";
	echo "<input type='submit' name='opslaan' value='$strSave'><br>\n";
	echo "</form>\n";
}

echo "<p>\n";
echo "<a href='index.php'><img src='../images/1_home.png' border='0'></a>\n";

include ('../include/inc_footer.php');
?>