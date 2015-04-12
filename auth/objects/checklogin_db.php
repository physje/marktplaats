<?php
/**************************************************************/
/*              phpSecurePages version 0.42 beta               */
/*              Copyright 2013 Circlex.com, Inc.              */
/*       Versions .30 and earlier coded by Paul Kruyt         */
/*                http://www.phpSecurePages.com               */
/*                                                            */
/*              Free for non-commercial use only.             */
/*               If you are using commercially,               */
/*         or using to secure your clients' web sites,        */
/*   please purchase a license at http://phpsecurepages.com   */
/*                                                            */
/**************************************************************/
/*      There are no user-configurable items on this page     */
/**************************************************************/

// check login with Database
$db = connect_db();

$sql		= "SELECT * FROM $TableUsers WHERE $UsersNaam like '$login' AND $UsersWachtwoord like '$password'";
//$result	= mysql_query($sql);
$result	= mysqli_query($db, $sql);

// check user and password
if (mysqli_num_rows($result) != 0) {
	// user exist --> continue
	$row = mysqli_fetch_array($result);
	$userLevel	= $row[$UsersLevel];
	$UserID			= $row[$UsersID];
	$PC					= $row[$UsersPostcode];
	
	$_SESSION['level']		= $userLevel;
	$_SESSION['UserID']		= $UserID;
	$_SESSION['PC']				= $PC;
			
	//$sql = "UPDATE $TableUsers SET $UsersLastLogin = '". time() ."' WHERE $UsersID = ". $row[$UsersID];
	//mysql_query($sql);	
} else {
	// user not present in database
  $message = 'Ongeldige inloggegevens';
  include($cfgProgDir . "interface.php");
  exit;
}

if (( isset($requiredUserLevel) && !empty($requiredUserLevel[0])) || isset($minUserLevel) ) {
	if ( empty($UsersLevel) || ( !is_in_array($userLevel, @$requiredUserLevel) && ( !isset($minUserLevel) || empty($minUserLevel) || $userLevel < $minUserLevel ) ) ) {
		// this user does not have the required user level
		$message = $strUserNotAllowed;
		include($cfgProgDir . "interface.php");
    exit;
  }
}
?>
