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

include ("../../general_include/general_config.php");
include ("../../general_include/general_functions.php");
include ("../include/inc_config_general.php");
include ("../lng/language_$Language.php");
include ("../include/inc_functions.php");
$minAcces = 2;
include ("../include/inc_head.php");	
setlocale(LC_ALL, 'nl_NL');

$Termen				= getZoekTermen('', '', '', 1);
$AantalArray	= array(5, 10, 25, 50, 100, 250, 500, 1000);

if(!isset($_REQUEST['bDag']) OR !isset($_REQUEST['bMaand']) OR !isset($_REQUEST['bJaar'])) {
	$logShift = 24*60*60;
	$bDag = date('d', time() - $logShift);
	$bMaand = date('m', time() - $logShift);
	$bJaar = date('Y', time() - $logShift);
} else {
	$bDag = $_REQUEST['bDag'];
	$bMaand = $_REQUEST['bMaand'];
	$bJaar = $_REQUEST['bJaar'];
}

if(!isset($_REQUEST['eDag']) OR !isset($_REQUEST['eMaand']) OR !isset($_REQUEST['eJaar'])) {
	$eDag = date('d');
	$eMaand = date('m');
	$eJaar = date('Y');	
} else {
	$eDag = $_REQUEST['eDag'];
	$eMaand = $_REQUEST['eMaand'];
	$eJaar = $_REQUEST['eJaar'];
}

//echo "term : ". $_REQUEST['term'] ."<br>id : ". $_REQUEST['id'] ."<br>";

if(isset($_REQUEST['term']) AND $_REQUEST['term'] != '') {
	$term = $_REQUEST['term'];
}

if(isset($_REQUEST['id']) AND $_REQUEST['id'] != '') {
	$id = $_REQUEST['id'];
}

if($term == '') {
	$id = '0';
}

/*
$id = $_REQUEST['id'];

if(!isset($_REQUEST['term']) OR $_REQUEST['term'] == '') {
	if(!isset($_REQUEST['id'])) {
		$id = $_REQUEST['id'];
	} else {
		$id = '0';
	}
} else {
	$term = $_REQUEST['term'];	
}
*/

if(!isset($_REQUEST['aantal'])) {
	$aantal = $AantalArray[4];
} else {
	$aantal = $_REQUEST['aantal'];
}

echo "<form method='post'>\n";
echo "<table>\n";
echo "<tr>\n";
echo "	<td><b>$strLogStart</b></td>\n";
echo "	<td>&nbsp;</td>\n";
echo "	<td><b>$strLogEnd</b></td>\n";
echo "	<td>&nbsp;</td>\n";
echo "	<td><b>$strLogTerm</b></td>\n";
echo "	<td>&nbsp;</td>\n";
echo "	<td><b>$strLogNumber</b></td>\n";
echo "	<td>&nbsp;</td>\n";
echo "	<td rowspan='4'><input type='submit' value='$strSearch' name='submit'></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "	<td><select name='bDag'>\n";
for($d=1 ; $d<=31 ; $d++)	{	echo "<option value='$d'". ($d == $bDag ? ' selected' : '') .">$d</option>\n";	}
echo "	</select><select name='bMaand'>\n";
for($m=1 ; $m<=12 ; $m++)	{	echo "<option value='$m'". ($m == $bMaand ? ' selected' : '') .">". strftime("%B", mktime(0,0,0,$m,1,2006)) ."</option>\n";	}
echo "	</select><select name='bJaar'>\n";
for($j=(date('Y') - 1) ; $j<=(date('Y') + 1) ; $j++)	{	echo "<option value='$j'". ($j == $bJaar ? ' selected' : '') .">$j</option>\n";	}
echo "	</select></td>\n";
echo "	<td>&nbsp;</td>\n";
echo "	<td><select name='eDag'>\n";
for($d=1 ; $d<=31 ; $d++)	{	echo "<option value='$d'". ($d == $eDag ? ' selected' : '') .">$d</option>\n";	}
echo "	</select><select name='eMaand'>\n";
for($m=1 ; $m<=12 ; $m++)	{	echo "<option value='$m'". ($m == $eMaand ? ' selected' : '') .">". strftime("%B", mktime(0,0,0,$m,1,2006)) ."</option>\n";	}
echo "	</select><select name='eJaar'>\n";
for($j=(date('Y') - 1) ; $j<=(date('Y') + 1) ; $j++)	{	echo "<option value='$j'". ($j == $eJaar ? ' selected' : '') .">$j</option>\n";	}
echo "	</select></td>\n";
echo "	<td>&nbsp;</td>\n";
echo "	<td><select name='term'>\n";
echo "	<option value=''>$strAll</option>\n";
foreach($Termen as $t) {	echo "<option value='$t'". ($term == $t ? ' selected' : '') .">". getZoekString($t) ."</option>\n";	}
echo "	</select></td>\n";
echo "	<td>&nbsp;</td>\n";
echo "	<td><select name='aantal'>\n";
for($a=0 ; $a<count($AantalArray) ; $a++)	{	$teller = $AantalArray[$a]; echo "<option value='$teller'". ($aantal == $teller ? ' selected' : '') .">$teller</option>\n";	}
echo "	</select></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "	<td colspan='5'><b>$strLogAds</b>&nbsp:&nbsp\n";

if(isset($term) AND $term !='') {
	$Pages	= getAds($term, false);
	
	echo "	<select name='id'>\n";
	echo "	<option value=''>$strAll</option>\n";
	foreach($Pages as $p) {	echo "<option value='$p'". ($id == $p ? ' selected' : '') .">". getAdTitle($p) ."</option>\n";	}
	echo "	</select>\n";
} else {
	echo "<i></i>";
}

echo "</td>\n</tr>\n";
echo "</table>\n";
echo "</form>\n";
echo "<p>\n";

if($id != '' AND $id != 0) {
	echo "[ <a href='http://link.marktplaats.nl/$id'>Marktplaats advertentie</a> ]\n";
	echo "<p>\n";	
}

$begin	= mktime(0, 0, 0, $bMaand, $bDag, $bJaar);
$eind		= mktime(23, 59, 59, $eMaand, $eDag, $eJaar);
$data		= getLogData($begin, $eind, $id, $term, $aantal);

echo "<table>\n";

foreach($data as $record)
{
	echo "<tr>\n";
	echo "	<td>". date("d-m H:i:s", $record['tijd'])."</td>\n";
	echo "	<td>&nbsp;</td>\n";
	echo "	<td>". $record['ip'] ."</td>\n";
	echo "	<td>&nbsp;</td>\n";
	echo "	<td><a href='?bDag=$bDag&bMaand=$bMaand&bJaar=$bJaar&eDag=$eDag&eMaand=$eMaand&eJaar=$eJaar&term=". $record['term'] ."'>". getZoekString($record['term']) ."</a></td>\n";
	
	if(isset($term) AND $term != '') {
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>". ($record['id'] == 0 ? '-' : "<a href='?bDag=$bDag&bMaand=$bMaand&bJaar=$bJaar&eDag=$eDag&eMaand=$eMaand&eJaar=$eJaar&term=". $record['term'] ."&id=". $record['id'] ."'>". getAdTitle($record['id']) ."</a>") ."</td>\n";
	}
	
	echo "	<td>&nbsp;</td>\n";
	echo "	<td>". $record['log'] ."</td>\n";
	echo "<tr>\n";	
}

echo "</table>\n";

echo "<p>\n";
echo "<a href='index.php'><img src='../images/1_home.png' border='0'></a>\n";

include ('../include/inc_footer.php');
?>