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

$lang['Prev'] = 'Previous';
$lang['Next'] = 'Next';
$lang['Yes'] = 'Yes';
$lang['No'] = 'No';

// Welcome
$lang['Welcome'] = 'Welcome';
$lang['WelcomeIntro'] = 'Setup will now collect information to install MarktplaatsChecker. Please choose your language and click Next to start.';
$lang['WelcomeError'] = 'The config-file (<i>include/inc_config_general.php</i>) is write-protected.';
$lang['SetupLanguage'] = 'Language';
$lang['SetupLanguage_ToolTip'] = 'Language for the installation only';

// Database
$lang['Database'] = 'Database';
$lang['dbHostname'] = 'Host';
$lang['dbName'] = 'Database name';
$lang['dbUsername'] = 'Username';
$lang['dbPassword'] = 'Password';
$lang['dbTablePrefix'] = 'Tablename prefix';
$lang['dbHostname_ToolTip'] = 'Hostname on which the MySQL database runs';
$lang['dbName_ToolTip'] = 'Name of the MySQL database';
$lang['dbUsername_ToolTip'] = 'Username for the MySQL database';
$lang['dbPassword_ToolTip'] = 'Password for the MySQL database';
$lang['dbTablePrefix_ToolTip'] = 'Prefix for tables in the MySQL database';
$lang['dbPassword_Note'] = 'Password saved. Enter again to change.';

// Email
$lang['Email'] = 'E-mail';
$lang['OntvangerNaam'] = 'Your name';
$lang['OntvangerMail'] = 'Your e-mail address';
$lang['ScriptMailAdress'] = 'Script e-mail address';
$lang['SubjectPrefix'] = 'Prefix for e-mail subject';
$lang['OntvangerNaam_ToolTip'] = 'Your full name (optional)';
$lang['OntvangerMail_ToolTip'] = 'The address where MarktplaatsChecker should send e-mail to';
$lang['ScriptMailAdress_ToolTip'] = 'The sender for e-mail from MarktplaatsChecker (does not have to exist)';
$lang['SubjectPrefix_ToolTip'] = 'Prefix the subject from MarktplaatsChecker e-mails with this text';

// Settings
$lang['Settings'] = 'Settings';
$lang['Language'] = 'Language';
$lang['OudeAdvTijd'] = 'Old ad age';
$lang['CookieTime'] = 'Lifetime cookies';
$lang['Checken'] = 'Active';
$lang['ScriptRoot'] = 'Root url';
$lang['ZipCode'] = 'Your ZIP code';
$lang['Language_ToolTip'] = 'MarktplaatsChecker language';
$lang['OudeAdvTijd_ToolTip'] = 'Age when an ad is consideren old (in seconds)';
$lang['CookieTime_ToolTip'] = 'Lifetime of cookies (in seconds)';
$lang['Checken_ToolTip'] = 'Enable or disable MarktplaatsChecker';
$lang['ScriptRoot_ToolTip'] = 'Root url for MarktplaatsChecker';
$lang['ZipCode_ToolTip'] = 'Your ZIP code to compute distances';

// Ready for installation
$lang['ReadyForInstall'] = 'Ready for installation';
$lang['StartInstall'] = 'Setup has now enough information to start installation. Please click Next to start installation.';

// Installatie voortgang
$lang['WritingConfig'] = 'Writing configuration file...';
$lang['ConnectionToDb'] = 'Connecting to database...';
$lang['CreatingTables'] = 'Creating tables...';
$lang['InsertDemoContent'] = 'Insert demo content...';
$lang['Done'] = 'Done.';

// Finished
$lang['Finished'] = 'Finished';
$lang['InstallDone'] = 'Setup is now finished. You can visit the <a href="../admin/">admin page</a> to add searches.';
$lang['InstallWarning'] = 'Please do not forget to remove the installation folder';

?>