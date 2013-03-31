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

if(isset($_REQUEST['toevoegen'])) { $toevoegen = $_REQUEST['toevoegen']; }


if(isset($_REQUEST['opslaan'])) {
	$db 		= connect_db();
	$sql = "INSERT INTO $TableNotepad ($NotepadUser, $NotepadTerm, $NotepadMID, $NotepadTijd, $NotepadBericht) VALUES (". $_COOKIE["UserID"] .", ". $_REQUEST['term'] .", ". $_REQUEST['id'] .", ". time() .", '". urlencode($_REQUEST['krabbel']) ."')";
	if(!mysql_query($sql)) {
		echo "Foutje [$sql]";
	}
}

echo "<table border=0>\n";
echo "<tr>\n";
echo "	<td colspan='6'><a href='?toevoegen=". ($toevoegen == 1 ? '0' : '1') ."'>". ($toevoegen == 1 ? 'Dichtklappen' : 'Advertentie toevoegen') ."</a></td>\n";		
echo "</tr>\n";
echo "<tr>\n";
echo "	<td colspan='6'>&nbsp;</td>\n";		
echo "</tr>\n";

$Termen				= getZoekTermen($_COOKIE["UserID"], '', '');

foreach($Termen as $term) {
	$Entry	= array();
	$Entrys = getNotepadEntry($term, 0);
	
	if(count($Entrys) > 0 OR $toevoegen == 1) {
		echo "<tr>\n";
		echo "	<td colspan='6'><h1><b>". getZoekString($term) ."</b></h1></td>\n";		
		echo "</tr>\n";
	}
	
	if(count($Entrys) > 0) {
		foreach($Entrys as $key => $value) {
			$PageData		= getPageDataByMarktplaatsID($value['id']);
			$Berichten	= getNotepadEntry(0, $value['id']);
			
			if($PageData['picture'] == '') {
				$src 	= "http://statisch.marktplaats.nl/images/no_image1.gif";
			} else {
    		$src	= "http://bigthumbs.marktplaats.com/kopen/thumbnail/". $PageData['picture'];
    	}
			
			$EntryText = "<tr>\n";
			$EntryText .= "	<td>&nbsp;</td>\n";
			$EntryText .= "	<td colspan='5'><a href='http://link.marktplaats.nl/". $value['id'] ."'>". getAdTitle($value['id']) ."</a></td>\n";		
			$EntryText .= "</tr>\n";
			$EntryText .= "<tr>\n";
			$EntryText .= "	<td>&nbsp;</td>\n";
			$EntryText .= "	<td valign='top'><img src='$src'></td>\n";
			$EntryText .= "	<td>&nbsp;</td>\n";
			$EntryText .= "	<td valign='top'>";
			
			foreach($Berichten as $key_2 => $value_2) {
				$EntryText .= "<b>". date("d-m H:i", $value_2['tijd']) ."</b><br>". urldecode($value_2['bericht']) ."<p>\n";
			}
			$EntryText .= "	</td>\n";
			$EntryText .= "	<td>&nbsp;</td>\n";
			$EntryText .= "	<td><form method='post'>\n<input type='hidden' name='term' value='$term'>\n<input type='hidden' name='id' value='". $value['id'] ."'>\n<textarea name='krabbel'></textarea><br><input type='submit' name='opslaan' value='Toevoegen'></form></td>\n";
			$EntryText .= "</tr>\n";
			
			$Entry[] = $EntryText;
		}
		
		echo implode("<tr><td colspan='6'>&nbsp;</td></tr>", $Entry);
	}
	
	if($toevoegen == 1) {
		$advertenties = getAds($term, false);
	
		echo "<tr>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td colspan='3'><form method='post'>\n<input type='hidden' name='term' value='$term'>\n<select name='id'>";
	
		foreach($advertenties as $value) {
			echo "<option value='$value'>". getAdTitle($value) ."</option>\n";
		}
		
		echo "</select></td>\n";		
		echo "	<td>&nbsp;</td>\n";
		echo "	<td><textarea name='krabbel'></textarea><br><input type='submit' name='opslaan' value='Toevoegen'></form></td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td colspan='5'>&nbsp;</td>\n";		
		echo "</tr>\n";
	}
}

echo "</table>\n";
echo "<p>\n";
echo "<a href='index.php'><img src='../images/1_home.png' border='0'></a>\n";

include ('../include/inc_footer.php');
?>