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
include ("../include/inc_head.php");

if($_REQUEST['loc_type'] == 'zip') {
	$pv = '';
} else {
	$postcode = $distance = '';		
}

if(isset($_REQUEST['opslaan'])) {
	saveURL($_REQUEST['id'], $_COOKIE["UserID"], $_REQUEST['active'], $_REQUEST['q'], $_REQUEST['ts'], $_REQUEST['g'], $_REQUEST['u'], $_REQUEST['pmin'], $_REQUEST['pmax'], $_REQUEST['np'], $_REQUEST['loc_type'], $_REQUEST['postcode'], $_REQUEST['distance'], $_REQUEST['pv'], $_REQUEST['f'], $_REQUEST['or'], $_REQUEST['not'], $_REQUEST['CC'], $_REQUEST['naam'], $_REQUEST['dagen'], $_REQUEST['uren']);
			
	if($_REQUEST['id'] != "") {
		echo "'<b>". getZoekString($_REQUEST['id']) ."</b>' $strEditChanged.";
		writeToLog($_REQUEST['id'], $strLogChanged);
	} else {
		echo "'<b>". $_REQUEST['q'] ."</b>' $strEditAdded";
		writeToLog('', $strLogAdded);
	}
} else {
	if(isset($_REQUEST['test'])) {
		$URL = makeURL($_REQUEST['q'], $_REQUEST['ts'], $_REQUEST['g'], $_REQUEST['u'], $_REQUEST['pmin'], $_REQUEST['pmax'], $_REQUEST['np'], $_REQUEST['loc_type'], $_REQUEST['postcode'], $_REQUEST['distance'], $_REQUEST['pv'], $_REQUEST['pp'], $_REQUEST['f'], $_REQUEST['or'], $_REQUEST['not']);
		
		if(isset($_POST['active']))		{ $active = $_POST['active']; }
		if(isset($_POST['q']))			{ $q = $_POST['q']; }
		if(isset($_POST['ts']))			{ $ts = $_POST['ts']; }
		if(isset($_POST['g']))			{ $g = $_POST['g']; }
		if(isset($_POST['u']))			{ $u = $_POST['u']; }
		if(isset($_POST['pmin']))		{ $pmin = $_POST['pmin']; }
		if(isset($_POST['pmax']))		{ $pmax = $_POST['pmax']; }
		if(isset($_POST['np']))			{ $np = $_POST['np']; }
		if(isset($_POST['loc_type']))	{ $loc_type = $_POST['loc_type']; }
		if(isset($_POST['postcode']))	{ $postcode = $_POST['postcode']; }
		if(isset($_POST['distance']))	{ $distance = $_POST['distance']; }
		if(isset($_POST['pv']))			{ $pv = $_POST['pv']; }
		if(isset($_POST['pp']))			{ $pp = $_POST['pp']; }
		if(isset($_POST['f']))			{ $f = $_POST['f']; }
		if(isset($_POST['or']))			{ $or = $_POST['or']; }
		if(isset($_POST['not'])) 		{ $not = $_POST['not']; }
		//if(isset($_POST['lichting']))	{ $lichting = $_POST['lichting']; }
		if(isset($_POST['uren']))		{ $uur = $_POST['uren']; }
		if(isset($_POST['dagen']))	{ $dag = $_POST['dagen']; }		
		if(isset($_POST['naam']))		{ $naam = $_POST['naam']; }
		if(isset($_POST['CC']))			{ $CC = $_POST['CC']; }
		
		
		echo "<a href='$URL' target='_new'>$strPreview</a>";
		echo "<p>";
		echo "<hr>";
		echo "<p>";		
	} elseif(isset($_REQUEST['id'])) {
		$data = getZoekData($_REQUEST['id']);
		
		$active		= $data['active'];
		$user			= $data['user'];
		$q				= $data['q'];
		$ts				= $data['ts'];
		$g				= $data['g'];
		$u				= $data['u'];
		$pmin			= $data['pmin'];
		$pmax			= $data['pmax'];
		$np				= $data['np'];
		$loc_type	= $data['loc_type'];
		$postcode	= $data['postcode'];
		$distance	= $data['distance'];
		$pv				= $data['pv'];
		$pp				= $data['pp'];
		$f				= $data['f'];		
		$or				= $data['or'];
		$not			= $data['not'];
		$uur			= $data['uur'];
		$dag			= $data['dag'];
		$naam			= $data['naam'];
		$CC				= $data['CC'];
		
		if($user != $_COOKIE["UserID"]) {
			echo "Helaas je hebt geen toegang tot deze zoekterm.";
			echo "<p>\n";
			echo "<a href='index.php'><img src='../images/1_home.png'></a>\n";

			include ('../include/inc_footer.php');
			exit;
		}	
	}	
		
	echo "<form method='post' action='$_SERVER[PHP_SELF]'>\n";
	if(isset($_REQUEST['id'])) {
		echo "<input type='hidden' name='id' value='". $_REQUEST['id'] ."'>\n";
	}
	echo "<b>$strActive</b><br>\n";
	echo "<input type='checkbox' id='active' name='active' value='1'". ($active == 1 ? ' checked' : '') .">$strActive.<br>\n";
	echo "<br>\n";
	//echo "<b>Hoe vaak controleren</b><br>\n";
	//echo "<select size='1' name='lichting'>\n";
	//echo "	<option value='1'". ($lichting == 1 ? ' selected' : '') .">Elke uur</option>\n";
	//echo "	<option value='2'". ($lichting == 2 ? ' selected' : '') .">De even uren</option>\n";
	//echo "	<option value='3'". ($lichting == 3 ? ' selected' : '') .">De oneven uren</option>\n";
	//echo "	<option value='4'". ($lichting == 4 ? ' selected' : '') .">Om de 4 uur</option>\n";
	//echo "	<option value='5'". ($lichting == 5 ? ' selected' : '') .">Om de 6 uur</option>\n";
	//echo "	<option value='6'". ($lichting == 6 ? ' selected' : '') .">2x per dag</option>\n";
	//echo "	<option value='7'". ($lichting == 7 ? ' selected' : '') .">1x per dag</option>\n";
	//echo "	<option value='8'". ($lichting == 8 ? ' selected' : '') .">1x per week</option>\n";
	//echo "</select><br>\n";	
	
	
		
	echo "<b>Op welke dagen en uren controleren</b> (aantal opdrachten wat al op dat uur wordt uitgevoerd)<br>\n";
	echo "<table border=0>\n";
	echo "	<tr>\n";
	echo "	<td><table border=0>";
	
	$Namen = array(0 => 'Zondag', 1 => 'Maandag', 2 => 'Dinsdag', 3 => 'Woensdag', 4 => 'Donderdag', 5 => 'Vrijdag', 6 => 'Zaterdag');
	
	for($d = 0; $d < 7 ; $d++) {
		echo "<tr><td><input type='checkbox' name='dagen[$d]' value='1'". ($dag[$d] == 1 ? ' checked' : '') ."> ". $Namen[$d] ."</td></tr>\n";
	}
	
	echo "	</table>\n";
	echo "	</td>\n";
	echo "	<td>&nbsp;</td>\n";
	echo "	<td>op</td>\n";
	echo "	<td>&nbsp;</td>\n";
	echo "	<td>\n";
	echo "		<table border=0>\n";
	
	for($h = 0; $h < 24 ; $h++) {
		$Termen		= getZoekTermen('', '', $h, 1);
		$nrTermen	= count($Termen);
	
		echo "<td><input type='checkbox' name='uren[$h]' value='1'". ($uur[$h] == 1 ? ' checked' : '') ."> $h uur ($nrTermen)</td>\n";
		echo "<td>&nbsp;</td>\n";
		
		if(fmod($h,4) == 3) {
			echo "</tr>\n<tr>\n";
		}
	}
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "	</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<br>\n";
	echo "<b>Stuur een kopie van de resultaten naar</b><br>\n";
	echo "<input type='text' name='CC' size='50' value='$CC' alt='CC van resultaat naar'><br>\n";	
	echo "<br>\n";
	echo "<b>Logische Naam</b><br>\n";
	echo "<input type='text' name='naam' size='50' value='$naam' alt='Logische naam'><br>\n";	
	echo "<br>\n";
	echo "<b>$strEditCommands</b><br>\n";
	echo "<input type='text' name='q' size='50' value='$q'>&nbsp;&nbsp;\n";
	echo "<select size='1' name='or'>\n";
	echo "	<option value='0'". ($or == 0 ? ' selected' : '') .">$strSearchAllWords</option>\n";
	echo "	<option value='1'". ($or == 1 ? ' selected' : '') .">$strSearchOneWord</option>\n";
	echo "</select><br>\n";
	echo "<input type='checkbox' id='ts' name='ts' value='1'". ($ts == 1 ? ' checked' : '') .">$strEditTitleText<br>\n";
	echo "<br>\n";
	echo "<b>$strSearchNot</b><br>\n";
	echo "<input type='text' name='not' size='50' value='$not' alt='welke woorden mogen niet voorkomen'><br>\n";	
	echo "<br>\n";
	echo "<b>$strEditGroups<br>\n";
	echo "<table>\n";
	echo "<tr>\n";
	echo "	<td>\n";
	echo "	<b>$strGroup</b><br>\n";
	echo "	<select size='1' name='g'>\n";
	echo "		<option value='' style='color: #BBBBBB'>$strAll&hellip;</option>\n";
	
	$groepen = getGroepen('');
	
	foreach($groepen as $groep => $naam) { 
		echo "		<option label='". urldecode($naam) ."' value='$groep'". ($g == $groep ? ' selected' : '') .">". urldecode($naam) ."</option>\n";
	}
	
	/*
	echo "</select><br>\n";
	echo "<br>\n";
	echo "<b>$strSubGroup</b><br>\n";
	*/
	
	echo "	</select></td>\n";
	echo "	<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
	echo "	<td><b>$strSubGroup</b><br>\n";
	echo "	<select size='1' name='u'>\n";
	echo "		<option value='' style='color: #BBBBBB'>$strAll&hellip;</option>\n";
	
	if($g != '') {
		$groepen = getGroepen($g);
	
		foreach($groepen as $groep => $naam) { 
			echo "		<option label='". urldecode($naam) ."' value='$groep'". ($u == $groep ? ' selected' : '') .">". urldecode($naam) ."</option>\n";
		}
	}
	
	echo "	</select></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<br>\n";
	echo "$strOptions<br>\n";
	echo "<b>$strPrice</b><br>\n";
	echo "€&nbsp;<input type='text' name='pmin' maxlength='7' size='7' value='$pmin'> $strTill <input type='text' name='pmax' maxlength='7' size='7' value='$pmax'><br>\n&nbsp;&nbsp;&nbsp;<input type='checkbox' id='np' name='np' value='1'". ($np == 1 ? ' checked' : '') ."> $strEditNoPrice<br>\n";
	echo "<br>\n";
	echo "<b>$strArea</b><br>\n";
	echo "<input type='radio' name='loc_type' value='zip' ".($loc_type == 'zip' ? ' checked' : '').">\n";
	echo "<input type='text' size='11' maxlength='6' name='postcode' value='".($postcode == 0 ? $_COOKIE["PC"] : $postcode)."'>\n";	
	echo "<select name='distance' width='10' size='1'>\n";
	
	$afstand_array[''] = "$strDistance&hellip;";
	$afstand_array['5000'] = '&lt; 5 km';
	$afstand_array['10000'] = '&lt; 10 km';
	$afstand_array['25000'] = '&lt; 25 km';
	$afstand_array['50000'] = '&lt; 50 km';
	$afstand_array['100000'] = '&lt; 100 km';
	$afstand_array['150000'] = '&lt; 150 km';
	$afstand_array['200000'] = '&lt; 200 km';
	
	foreach ($afstand_array as $key => $value) {
		echo "	<option value='$key'". ($key == $distance ? ' selected' : '') .">$value</option>\n";
	}
	
	echo "</select><br>\n";
	echo "<input type='radio' name='loc_type' value='province' ".($loc_type == 'province' ? ' checked' : '')."> \n";
	echo "<select size='1' name='pv'>\n";
	echo "	<option value=''>$strProvincie&hellip;</option>\n";
	
	$prov_array[2] = 'Drenthe';
	$prov_array[3] = 'Flevoland';
	$prov_array[4] = 'Friesland';
	$prov_array[5] = 'Gelderland';
	$prov_array[6] = 'Groningen';
	$prov_array[7] = 'Limburg';
	$prov_array[8] = 'Noord-Brabant';
	$prov_array[9] = 'Noord-Holland';
	$prov_array[10] = 'Overijssel';
	$prov_array[11] = 'Utrecht';
	$prov_array[12] = 'Zeeland';
	$prov_array[13] = 'Zuid-Holland';
	$prov_array[15] = 'Buitenland';
	
	foreach ($prov_array as $key => $value) {
		echo "	<option label='$value' value='$key'". ($key == $pv ? ' selected' : '') .">$value</option>\n";
	}
	
	echo "</select><br>\n";
	echo "<br>\n";
	echo "<b>$strPhoto</b><br>\n";
	echo "<input type='checkbox' name='f' value='1'". ($f == 1 ? ' checked' : '')."> $strEditOnlyPhoto.<br>\n";
	echo "<br>\n";
	//echo "<b>$strPayment</b><br>\n";
	//echo "<input type='checkbox' name='pp' value='1'". ($pp == 1 ? ' checked' : '')."> $strEditPayPal<br>\n";
	//echo "<br>\n";	
	echo "<input type='submit' name='test' value='$strPreview'>&nbsp;&nbsp;<input type='submit' name='opslaan' value='$strSave'><br>\n";
	echo "</form>\n";
}

echo "<p>\n";
echo "<a href='index.php'><img src='../images/1_home.png' border='0'></a>\n";

include ('../include/inc_footer.php');
?>