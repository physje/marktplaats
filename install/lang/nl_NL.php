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

$lang['Prev'] = 'Vorige';
$lang['Next'] = 'Volgende';
$lang['Yes'] = 'Ja';
$lang['No'] = 'Nee';

// Welcome
$lang['Welcome'] = 'Welkom';
$lang['WelcomeIntro'] = 'Setup verzameld eerst informatie om MarktplaatsChecker te installeren. Kies a.j.b. een taal en kies Volgende om te beginnen.';
$lang['WelcomeError'] = 'De config-file (<i>include/inc_config_general.php</i>) is beveiligd tegen schrijven.';
$lang['SetupLanguage'] = 'Taal';
$lang['SetupLanguage_ToolTip'] = 'Taal voor de installatie';

// Database
$lang['Database'] = 'Database';
$lang['dbHostname'] = 'Computernaam';
$lang['dbName'] = 'Database naam';
$lang['dbUsername'] = 'Gebruikersnaam';
$lang['dbPassword'] = 'Wachtwoord';
$lang['dbTablePrefix'] = 'Tabelnaam voorvoegsel';
$lang['dbHostname_ToolTip'] = 'Computernaam op welke de MySQL database draait';
$lang['dbName_ToolTip'] = 'Naam van de MySQL database';
$lang['dbUsername_ToolTip'] = 'Gebruikersnaam voor de MySQL database';
$lang['dbPassword_ToolTip'] = 'Wachtwoord voor de MySQL database';
$lang['dbTablePrefix_ToolTip'] = 'Voorvoegsel voor tabelnamen in de MySQL database';
$lang['dbPassword_Note'] = 'Wachtwoord opgeslagen. Voer opnieuw in om te wijzigen.';

// Email
$lang['Email'] = 'E-mail';
$lang['OntvangerNaam'] = 'Uw naam';
$lang['OntvangerMail'] = 'Uw e-mail adres';
$lang['ScriptMailAdress'] = 'E-mail adres voor script';
$lang['SubjectPrefix'] = 'Voorvoegsel e-mail onderwerp';
$lang['OntvangerNaam_ToolTip'] = 'Uw volledige naam (optioneel)';
$lang['OntvangerMail_ToolTip'] = 'Het adres waar MarktplaatsChecker e-mail naar toe moet sturen';
$lang['ScriptMailAdress_ToolTip'] = 'De afzender voor e-mail van MarktplaatsChecker (hoeft niet te bestaan)';
$lang['SubjectPrefix_ToolTip'] = 'Onderwerp van e-mails van MarktplaatsChecker beginnen met deze tekst';

// Settings
$lang['Settings'] = 'Instellingen';
$lang['Language'] = 'Taal';
$lang['OudeAdvTijd'] = 'Oude adv leeftijd';
$lang['CookieTime'] = 'Houdbaarheid cookies';
$lang['Checken'] = 'Actief';
$lang['ScriptRoot'] = 'Root url';
$lang['ZipCode'] = 'Uw postcode';
//$lang['AllowedIP'] = 'Toegestane IP adressen';
$lang['Language_ToolTip'] = 'MarktplaatsChecker taal';
$lang['OudeAdvTijd_ToolTip'] = 'Leeftijd wanneer een advertentie als oud wordt gezien (in seconden)';
$lang['CookieTime_ToolTip'] = 'Leeftijd wanneer een cookie als oud wordt gezien (in seconden)';
$lang['Checken_ToolTip'] = 'MarktplaatsChecker in- of uitschakelen';
$lang['ScriptRoot_ToolTip'] = 'Root url voor MarktplaatsChecker';
$lang['ZipCode_ToolTip'] = 'Uw postcode om afstanden te berekenen';
//$lang['AllowedIP_ToolTip'] = 'IP adressen die MarktplaatsChecker mogen beheren';

// Ready for installation
$lang['ReadyForInstall'] = 'Klaar voor installatie';
$lang['StartInstall'] = 'Setup heeft nu voldoende informatie om de installatie te starten. Klik op Volgende om de installatie te starten.';

// Installatie voortgang
$lang['WritingConfig'] = 'Configuratiebestand aanmaken...';
$lang['ConnectionToDb'] = 'Verbinding maken met database...';
$lang['CreatingTables'] = 'Tabellen aanmaken...';
$lang['InsertDemoContent'] = 'Voorbeelden toevoegen...';
$lang['Done'] = 'Klaar.';

// Finished
$lang['Finished'] = 'Klaar';
$lang['InstallDone'] = 'Setup is nu klaar. U kunt naar de <a href="../admin/">beheerpagina</a> gaan om zoekopdrachten toe te voegen.';
$lang['InstallWarning'] = 'Vergeet a.u.b niet om de installatiemap te verwijderen.';

?>