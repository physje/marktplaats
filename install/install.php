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

$publicPage = true;

include('include/inc_view.php');
include('include/inc_html.php');
include('include/inc_install.php');

include('../include/inc_config_general.php');
include('../include/inc_functions.php');

include ("../../general_include/shared_functions.php");

getInstallerViewState();

include('lang/'.$view['SetupLanguage'].'.php');

// Bepaal volgende pagina. Bepaal ook in welke richting wordt gelopen
$forward = false;
if ($writeAccess) {
	if ($view['next']) {
		$currentPage = $comeFromPage + 1;
		$forward = true;
	} else {
		if ($view['prev']) {
			$currentPage = $comeFromPage - 1;
		}
	}
}

// Validatie wordt alleen uitgevoerd wanneer voor Volgende wordt gekozen (gebruiker mag altijd terug)
if ($forward) {	
	switch ($comeFromPage) {
		case $pageDatabase:
			validateDatabase();			
			break;
			
		case $pageEmail:
			validateEmail();
			break;
			
		case $pageSettings:
			validateSettings();
			break;
			
		case $pageReadyForInstall:
			addInstallLog($lang['WritingConfig']);
			createConfigPhp();
					
			addInstallLog($lang['ConnectionToDb']);
			include($configFile); // Include de zojuist aangemaakte config. Hiermee kan de db geopend worden
			$db = connect_db();
			
			addInstallLog($lang['CreatingTables']);
			createTables($db);
	
			addInstallLog($lang['InsertDemoContent']);
			createDemoData($db);
	
			addInstallLog($lang['Done']);
			break;
	}
}

include("../include/inc_head.php");

?>

<h1>MarktplaatsChecker Setup</h1>

<form method="post" action="./install.php">
<input type="hidden" name="page" value="<?php echo $currentPage ?>" />

<?php
// Welcome
echo htmlStartPage(lang('Welcome'), $currentPage, $pageWelcome);

if ($writeAccess) {
	echo lang('WelcomeIntro');
	
	echo '<table cellspacing="0" cellpadding="0">';
	echo htmlNameValueSelectRow('SetupLanguage', array('en_US','nl_NL'), array('English', 'Nederlands'));
	echo '</table>';
	
	echo htmlNavButtons(false, true);
} else {
	echo lang('WelcomeError');
}

echo htmlEndPage();


// Database
echo htmlStartPage(lang('Database'), $currentPage, $pageDatabase);

echo '<table cellspacing="0" cellpadding="0">';
echo htmlNameValueRow('dbHostname');
echo htmlNameValueRow('dbName');
echo htmlNameValueRow('dbUsername');
echo htmlNameValuePasswordRow('dbPassword');
echo htmlNameValueRow('dbTablePrefix');
echo '</table>';

echo htmlNavButtons(true, true);
echo htmlEndPage();


// Email
echo htmlStartPage(lang('Email'), $currentPage, $pageEmail);

echo '<table cellspacing="0" cellpadding="0">';
echo htmlNameValueRow('OntvangerNaam');
echo htmlNameValueRow('OntvangerMail');
echo htmlNameValueRow('ScriptMailAdress');
echo htmlNameValueRow('SubjectPrefix');
echo '</table>';

echo htmlNavButtons(true, true);
echo htmlEndPage();


// Settings
echo htmlStartPage(lang('Settings'), $currentPage, $pageSettings);

echo '<table cellspacing="0" cellpadding="0">';
echo htmlNameValueSelectRow('Language', array('uk','nl'), array('English', 'Nederlands'));
echo htmlNameValueRow('OudeAdvTijd');
echo htmlNameValueRow('CookieTime');
echo htmlNameValueSelectRow('Checken', array('true','false'), array($lang['Yes'], $lang['No']));
echo htmlNameValueRow('ScriptRoot');
echo htmlNameValueRow('ZipCode');
//echo htmlNameValueRow('AllowedIP');
echo '</table>';

echo htmlNavButtons(true, true);
echo htmlEndPage();


// Ready for installation
echo htmlStartPage(lang('ReadyForInstall'), $currentPage, $pageReadyForInstall);

echo '<p>'.lang('StartInstall').'</p>';

echo htmlNavButtons(true, true);
echo htmlEndPage();


// Ready for installation
echo htmlStartPage(lang('Finished'), $currentPage, $pageFinished);

echo $installLog;
echo '<p>'.lang('InstallDone').'</p>';
echo '<p>'.lang('InstallWarning').'</p>';

echo htmlEndPage();

?>

</form>

<?php

include ('../include/inc_footer.php');

?>
