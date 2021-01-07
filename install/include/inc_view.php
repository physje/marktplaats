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

$view = array();
$errors = array();

// Constanten

$pageWelcome = 1;
$pageDatabase = 2;
$pageEmail = 3;
$pageSettings = 4;
$pageReadyForInstall = 5;
$pageFinished = 6;

// Paging

$currentPage = 0;
$comeFromPage = 0;

// Wrapper voor language array

function lang($name) {
	global $lang;
	
	if (isset($lang[$name])) {
		return $lang[$name];
	} else {
		return '[language string "'.$name.'" not set]';
	}
}

// Foutrapportage

function setError($id, $msg) {
	global $errors, $currentPage, $comeFromPage;

	// Registreer error en zorg dat we op de huidge pagina blijven
	$errors[$id] = $msg;
	$currentPage = $comeFromPage;
}

function getError($id) {
	global $errors;

	if (isset($errors[$id])) {
		return $errors[$id];
	} else {
		return "";
	}
}

function showError($id) {
	global $errors;

	if (isset($errors[$id])) {
		return '<td class="Error">'.$errors[$id].'</td>';
	} else {
		return '<td></td>';
	}
}

function showNote($msg) {
	if (isset($msg)) {
		return '<td class="Note">'.$msg.'</td>';
	} else {
		return '<td></td>';
	}
}

// Viewstate

function getInstallerViewState() {
	global $view, $currentPage, $comeFromPage, $pageWelcome;
	
	$view['SetupLanguage'] = getParam('SetupLanguage', 'en_US');
	
	// Viewstate - Database
	$view['dbHostname'] = getParam('dbHostname', 'localhost');
	$view['dbName'] = getParam('dbName');
	$view['dbUsername'] = getParam('dbUsername');
	$view['dbPassword'] = getParam('dbPassword');
	if ($view['dbPassword'] == '') $view['dbPassword'] = getParam('dbPasswordHidden');
	$view['dbTablePrefix'] = getParam('dbTablePrefix', 'marktplaats_');
	
	// Viewstate - Email
	$view['OntvangerNaam'] = getParam('OntvangerNaam');
	$view['OntvangerMail'] = getParam('OntvangerMail');
	$view['ScriptMailAdress'] = getParam('ScriptMailAdress', 'marktplaats@domain.com');
	$view['SubjectPrefix'] = getParam('SubjectPrefix', '[Marktplaats] ');
	
	// Viewstate - Settings
	$currentUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];

	$view['Language'] = getParam('Language', 'nl');
	$view['OudeAdvTijd'] = getParam('OudeAdvTijd', '10800');
	$view['CookieTime'] = getParam('CookieTime', '604800');
	$view['Checken'] = getParam('Checken', 'true');
	$view['ScriptRoot'] = getParam('ScriptRoot', substr($currentUrl, 0, strpos($currentUrl, "install/install.php")));
	$view['ZipCode'] = getParam('ZipCode');
	//$view['AllowedIP'] = getParam('AllowedIP', $_SERVER['REMOTE_ADDR']);

	// Viewstate - Paging
	$view['next'] = getParam('next') != '';
	$view['prev'] = getParam('prev') != '';

	$comeFromPage = getParam('page', $pageWelcome);
	$currentPage = $comeFromPage;
}

// Validatie

function validateDatabase() {
	global $view;

	if ($view['dbHostname'] == '') setError("dbHostname", "Please specify a hostname. Usually localhost");
	if ($view['dbUsername'] == '') setError("dbUsername", "Please specify a username");
	if ($view['dbTablePrefix'] == '') setError("dbTablePrefix", "Please specify prefix for tablenames");
	
	if (($view['dbHostname'] != '') && ($view['dbUsername'] != '')) {
		// Test de database account
		$dbcheck = mysqli_connect($view['dbHostname'], $view['dbUsername'], $view['dbPassword']);
		if (!$dbcheck) {
			setError("dbUsername", "Bad username or password for this host");
			setError("dbPassword", "Bad username or password for this host");
	        } else {
	        	$dbcheck = mysqli_selectdb($view['dbName']);
			if (!$dbcheck) setError("dbName", "Database not found or no acces for this database");
	        }
	}
}

function validateEmail() {
	global $view;
	
	if ($view['OntvangerMail'] == "") setError("OntvangerMail", "Please specify your e-mail address");
	if ($view['ScriptMailAdress'] == "") setError("ScriptMailAdress", "Please specify an e-mail address for the script");
}

function validateSettings() {
	global $view;

	if ($view['Language'] == "") setError("Language", "Please specify a languag");
	if ($view['OudeAdvTijd'] == "") setError("OudeAdvTijd", "Please specify a time for old ads");
	if ($view['ScriptRoot'] == "") setError("ScriptRoot", "Please specify the root url for MarktplaatsChecker");
	//if ($view['AllowedIP'] == "") setError("AllowedIP", "Please specify allowed IP addresses (comma separated)");
}

?>