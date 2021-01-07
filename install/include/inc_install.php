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

require_once('inc_view.php');

// Variabelen

$configFile = dirname(__FILE__)."/../../include/inc_config_general.php";
$writeAccess = true;//file_exists($configFile) && chmod($configFile, 0666);

// Installatie voortgang

$installLog = "";

function addInstallLog($msg) {
	global $installLog;
	
	$installLog .= "$msg<br/>\n";
}

// Installatie

function createConfigPhp() {
	global $configFile;
	global $view;

	// view rechtstreeks vertalen naar een config.php
	$fh = fopen($configFile, "w") or die("Can't create ".$configFile);
	fwrite($fh, "<?php\n");
	fwrite($fh, "\n");
	fwrite($fh, '$dbHostname = "'.$view['dbHostname']."\";\n");
	fwrite($fh, '$dbName = "'.$view['dbName']."\";\n");
	fwrite($fh, '$dbUsername = "'.$view['dbUsername']."\";\n");
	fwrite($fh, '$dbPassword = "'.$view['dbPassword']."\";\n");
	fwrite($fh, '$dbTablePrefix = "'.$view['dbTablePrefix']."\";\n");
	fwrite($fh, "\n");
	fwrite($fh, '$ScriptMailAdress = "'.$view['ScriptMailAdress']."\";\n");
	fwrite($fh, '$SubjectPrefix = "'.$view['SubjectPrefix']."\";\n");
	fwrite($fh, "\n");
	fwrite($fh, '$Language = "'.$view['Language']."\";\n");
	fwrite($fh, '$OudeAdvTijd = '.$view['OudeAdvTijd'].";\n");
	fwrite($fh, '$CookieTime = '.$view['CookieTime'].";\n");
	fwrite($fh, '$Checken = '.$view['Checken'].";\n");
	fwrite($fh, '$ScriptRoot = "'.$view['ScriptRoot']."\";\n");
	fwrite($fh, "\n");
	fwrite($fh, '$dirname = dirname(__FILE__);'."\n");
	fwrite($fh, 'include_once($dirname.\'/inc_config_tables.php\');'."\n");	
	fwrite($fh, "\n");
	fwrite($fh, "?>");
	fclose($fh);
}

function createTables($db) {
	global $dbTablePrefix;
	
	if (!isset($dbTablePrefix)) die("config.php should be created and included before calling createTables()");
	
	// Table 1: data
	addInstallLog('&nbsp;&nbsp;&nbsp;&nbsp;'.$dbTablePrefix.'data');

	$sql  = "DROP TABLE IF EXISTS ".$dbTablePrefix."data";
	$result = mysqli_query($db, $sql);
	
	$sql  = "CREATE TABLE ".$dbTablePrefix."data (\n";
	$sql .= "  id int(4) NOT NULL auto_increment,\n";
	$sql .= "  marktplaats_id int(9) NOT NULL default '0',\n";
	$sql .= "  URL text NOT NULL,\n";
	$sql .= "  title text NOT NULL,\n";
	$sql .= "  beschrijving text NOT NULL,\n";
	$sql .= "  datum datetime NOT NULL default '0000-00-00 00:00:00',\n";
	$sql .= "  term int(3) NOT NULL default '0',\n";
	$sql .= "  time_add int(11) NOT NULL default '0',\n";
	$sql .= "  time_change int(11) NOT NULL default '0',\n";
	$sql .= "  KEY id (id)\n";
	$sql .= ") TYPE=MyISAM;\n";
	$result = mysqli_query($db, $sql);
	
	// Table 2: groep
	addInstallLog('&nbsp;&nbsp;&nbsp;&nbsp;'.$dbTablePrefix.'groep');

	$sql  = "DROP TABLE IF EXISTS ".$dbTablePrefix."groep";
	$result = mysqli_query($db, $sql);

	$sql  = "CREATE TABLE ".$dbTablePrefix."groep (\n";
	$sql .= "  id int(4) NOT NULL auto_increment,\n";
	$sql .= "  groep text NOT NULL,\n";
	$sql .= "  naam text NOT NULL,\n";
	$sql .= "  KEY id (id)\n";
	$sql .= ") TYPE=MyISAM;\n";
	$result = mysqli_query($db, $sql);

	// Table 3: log
	addInstallLog('&nbsp;&nbsp;&nbsp;&nbsp;'.$dbTablePrefix.'log');

	$sql  = "DROP TABLE IF EXISTS ".$dbTablePrefix."log";
	$result = mysqli_query($db, $sql);

	$sql  = "CREATE TABLE ".$dbTablePrefix."log (\n";
	$sql .= "  id int(6) NOT NULL auto_increment,\n";
	$sql .= "  tijd int(10) NOT NULL default '0',\n";
	$sql .= "  ip varchar(15) NOT NULL default '',\n";
	$sql .= "  term int(3) NOT NULL default '0',\n";
	$sql .= "  marktplaats_id int(9) NOT NULL default '0',\n";
	$sql .= "  log text NOT NULL,\n";
	$sql .= "  UNIQUE KEY id (id)\n";
	$sql .= ") TYPE=MyISAM;\n";
	$result = mysqli_query($db, $sql);

	// Table 4: subgroep
	addInstallLog('&nbsp;&nbsp;&nbsp;&nbsp;'.$dbTablePrefix.'subgroep');

	$sql  = "DROP TABLE IF EXISTS ".$dbTablePrefix."subgroep";
	$result = mysqli_query($db, $sql);

	$sql  = "CREATE TABLE ".$dbTablePrefix."subgroep (\n";
	$sql .= "  id int(4) NOT NULL auto_increment,\n";
	$sql .= "  groep text NOT NULL,\n";
	$sql .= "  subgroep text NOT NULL,\n";
	$sql .= "  naam text NOT NULL,\n";
	$sql .= "  KEY id (id)\n";
	$sql .= ") TYPE=MyISAM;\n";
	$result = mysqli_query($db, $sql);

	// Table 5: zoeken
	addInstallLog('&nbsp;&nbsp;&nbsp;&nbsp;'.$dbTablePrefix.'zoeken');

	$sql  = "DROP TABLE IF EXISTS ".$dbTablePrefix."zoeken";
	$result = mysqli_query($db, $sql);
               
	$sql  = "CREATE TABLE ".$dbTablePrefix."zoeken (\n";
	$sql .= "  id int(4) NOT NULL auto_increment,\n";	
	$sql .= "  active set('1','0') NOT NULL default '1',\n";
	$sql .= "  user int(4) NOT NULL default '1',\n";	
	$sql .= "  zoekterm text NOT NULL,\n";
	$sql .= "  ts set('1','0') NOT NULL default '1',\n";
	$sql .= "  groep int(4) NOT NULL default '0',\n";
	$sql .= "  subgroep int(4) NOT NULL default '0',\n";
	$sql .= "  prijs_min int(5) NOT NULL default '0',\n";
	$sql .= "  prijs_max int(5) NOT NULL default '0',\n";
	$sql .= "  geen_prijs set('0','1') NOT NULL default '1',\n";
	$sql .= "  lok_type enum('zip','province') NOT NULL default 'zip',\n";
	$sql .= "  postcode int(4) NOT NULL default '0',\n";
	$sql .= "  distance enum('','5000','10000','25000','50000','100000','150000','200000') NOT NULL default '',\n";
	$sql .= "  provincie int(2) NOT NULL default '0',\n";
	$sql .= "  paypal set('0','1') NOT NULL default '',\n";
	$sql .= "  f set('0','1') NOT NULL default '0',\n";
	$sql .= "  KEY id (id)\n";
	$sql .= ") TYPE=MyISAM;\n";
	$result = mysqli_query($db, $sql);

	// Table 6: users
	addInstallLog('&nbsp;&nbsp;&nbsp;&nbsp;'.$dbTablePrefix.'users');

	$sql  = "DROP TABLE IF EXISTS ".$dbTablePrefix."users";
	$result = mysqli_query($db, $sql);
               
	$sql  = "CREATE TABLE ".$dbTablePrefix."users (\n";
	$sql .= "  id int(4) NOT NULL auto_increment,\n";	
	$sql .= "  naam text NOT NULL,\n";
	$sql .= "  wachtwoord text NOT NULL,\n";
	$sql .= "  mail text NOT NULL,\n";
	$sql .= "  html set('1','0') NOT NULL default '1',\n";	
	$sql .= "  postcode int(4) NOT NULL default '0',\n";
	$sql .= "  level int(1) NOT NULL default '1',\n";
	$sql .= "  KEY id (id)\n";
	$sql .= ") TYPE=MyISAM;\n";
	$result = mysqli_query($db, $sql);	
}

function createDemoData($db) {
	global $dbTablePrefix;
	global $view;
	
	addInstallLog('&nbsp;&nbsp;&nbsp;&nbsp;vuurkorf');

	$sql  = "INSERT INTO ".$dbTablePrefix."zoeken VALUES (1, '1', '1', 'vuurkorf', '1', 0, 0, 0, 0, '', '', 1234, '25000', 0, '', '');";
	$result = mysqli_query($db, $sql);
	
	addInstallLog('&nbsp;&nbsp;&nbsp;&nbsp;gebruiker');
	
	$sql  = "INSERT INTO ".$dbTablePrefix."users VALUES (1, '". $view['OntvangerNaam'] ."', 'appelboom', '". $view['OntvangerMail'] ."', '1', ". $view['ZipCode'] .", 3);";
	$result = mysqli_query($db, $sql);	
}

?>