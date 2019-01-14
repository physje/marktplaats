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

$TableZoeken     = $dbTablePrefix."zoeken";
$ZoekenID        = "id";
$ZoekenUser      = "user";
$ZoekenActive    = "active";
$ZoekenNaam			 = "logische_naam";
$ZoekenCC				 = "CC_mail";
$ZoekenKey				= "sleutel"; // nog toevoegen in inc_install
$ZoekenURL				= "url"; // nog toevoegen in inc_install

$TableData					= $dbTablePrefix."data";
$DataID							= "id";
$DataActive					= "active"; // nog toevoegen in inc_install
$DataMarktplaatsID	= "marktplaats_id";
$DataURL						= "URL";
$DataTitle					= "title";
$DataBeschrijving		= "beschrijving";
$DataVerkoper				= "verkoper"; // nog toevoegen in inc_install
$DataDatum					= "datum";
$DataPlaatje				= "picture"; // nog toevoegen in inc_install
$DataPrice					= "price"; // nog toevoegen in inc_install
$DataPlaats					= "plaats"; // nog toevoegen in inc_install
$DataAfstand				= "distance"; // nog toevoegen in inc_install
$DataZoekterm				= "term";
$DataAdded					= "time_add";
$DataChanged				= "time_change";
$DataNotSeen				= "times_not_seen"; // nog toevoegen in inc_install

$TableLog         = $dbTablePrefix.'log';
$LogID            = 'id';
$LogTijd          = 'tijd';
$LogIP            = 'ip';
$LogTerm          = 'term';
$LogMarktplaatsID = 'marktplaats_id';
$LogLog           = 'log';

$TableUsers				= $dbTablePrefix.'users';
$UsersID					= 'id';
$UsersActive			= 'active';   // nog toevoegen in inc_install
$UsersNaam				= 'naam';
$UsersWachtwoord	= 'wachtwoord';
$UsersMail				= 'mail';
$UsersHTML				= 'html';
$UsersRSS					= 'rss';   // nog toevoegen in inc_install
$UsersPostcode		= 'postcode';
$UsersLevel				= 'level';

$TableLichting		= $dbTablePrefix.'lichting';
$LichtingTerm			= 'term';
$LichtingDag			= 'dag';
$LichtingUur			= 'uur';

$TableNotepad			= $dbTablePrefix.'notepad';
$NotepadID 				= 'id';
$NotepadUser 			= 'user';
$NotepadTerm 			= 'term';
$NotepadMID 			= 'marktplaats_id';
$NotepadTijd 			= 'tijd';
$NotepadBericht		= 'bericht';

$TableCoord				= $dbTablePrefix.'coord';
$CoordID 					= 'id';
$CoordPlaats			= 'plaats';
$CoordLongitude		= 'longitude';
$CoordLatitude		= 'latitude';

$ScriptDownload   = "https://github.com/physje/marktplaats";
$ScriptTitle      = "Marktplaats Checker";
$ScriptDescr	  	= "Marktplaats Checker is a script which checks the site marktplaats.nl. Every time the script is running it checks marktplaats.nl for new items you are looking for. If there are, you will get a mail notification.";    // nog toevoegen in inc_install
$Version          = "v3.0";

?>