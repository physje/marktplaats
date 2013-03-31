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

$landingPage = isset($landingPage) && $landingPage;
$publicPage = isset($publicPage) && $publicPage;
if(!isset($minAcces)) { $minAcces = 1; }

if(!$publicPage) {
	if($_COOKIE["LoggedIn"] == false OR $_GET[uitloggen] == true) {
		include('check_cookie.php');	
	} elseif ($_COOKIE["LoggedIn"] == true) {
		setcookie ("LoggedIn", true, time()+$CookieTime);
		setcookie ("level", $_COOKIE["level"], time()+$CookieTime);
		setcookie ("UserID", $_COOKIE["UserID"], time()+$CookieTime);	
	}
}

echo "<!--     Deze pagina is onderdeel van $ScriptTitle $Version gemaakt door Matthijs Draijer     -->\n\n";
?>

<html>
<head>
<title><?php echo $ScriptTitle ." ". $Version ?></title>
<link rel='stylesheet' type='text/css' href='<?php echo $ScriptRoot ?>include/style.css'>
<meta http-equiv="pragma" content="no-cache">
<?php if($landingPage) {
	echo "<meta http-equiv=refresh content='5;url=$ScriptDownload'>\n";
} ?>
</head>
<body>
<table width=100% height=95%>
<tr align='center'>
	<td valign='center'>
	<table>
	<tr>
		<td>
<?
if(!$publicPage && is_dir ('../install')) {
	echo "<font class='error'>Helaas, de install-directory bestaat nog.</font>";
	include ('inc_footer.php');
	exit;
}

if(!$publicPage && $_COOKIE["level"] < $minAcces) {
	echo "<font class='error'>Helaas, je hebt geen toegang tot deze pagina.</font>";
	include ('inc_footer.php');
	exit;
}
?>