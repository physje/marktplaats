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

function getZoekTermen($id, $dag, $uur, $active) {
	global $TableZoeken, $TableLichting, $ZoekenUser, $ZoekenActive, $ZoekenID, $LichtingUur, $LichtingTerm, $LichtingDag;
	$db = connect_db();
	
	$ZoekTermen = array();
	
	//if($active != 0) {
	//	// alles wat 'active' is
	//	$sql		= "SELECT * FROM $TableZoeken WHERE $ZoekenActive = 1";	
	//} elseif($id != 0) {
	//	// alles van deze gebruiker
	//	$sql		= "SELECT * FROM $TableZoeken WHERE $ZoekenUser = $id";		
	//} else {
	//	// alles voor dit uur
	//	$sql		= "SELECT * FROM $TableLichting, $TableZoeken WHERE $TableLichting.$LichtingTerm = $TableZoeken.$ZoekenID AND $ZoekenActive = 1 AND $LichtingUur = $uur";
	//}
	
	$Tabel = array(0, 0);
	if(is_numeric($active)) {
		// alles wat 'active' is
		$WHERE[] = "$TableZoeken.$ZoekenActive = $active";
		$Tabel[0] = 1;
	}
	
	if(is_numeric($id)) {
		// alles van deze gebruiker
		$WHERE[] = "$TableZoeken.$ZoekenUser = $id";
		$Tabel[0] = 1;
	}
	
	if(is_numeric($uur)) {
		// alles voor dit uur
		$WHERE[] = "$TableLichting.$LichtingUur = $uur";
		$Tabel[0] = 1;
		$Tabel[1] = 1;
	}
	
	if(is_numeric($dag)) {
		// alles voor deze dag
		$WHERE[] = "$TableLichting.$LichtingDag = $dag";
		$Tabel[0] = 1;
		$Tabel[1] = 1;
	}
	
	if(is_numeric($dag) OR is_numeric($uur)) {
		$WHERE[] = "$TableLichting.$LichtingTerm = $TableZoeken.$ZoekenID";
	}
		
	if($Tabel[0] == 1) { $Tabellen[] = $TableZoeken; }
	if($Tabel[1] == 1) { $Tabellen[] = $TableLichting; }
	
	$sql		= "SELECT * FROM ". implode(', ', $Tabellen) ." WHERE ". implode(' AND ', $WHERE);
	
	//echo '['. $sql .']';

	$result = mysqli_query($db, $sql);	
	if($row = mysqli_fetch_array($result)) {			
		do {
			if($active == 0 AND $id == 0) {
				$ZoekTermen[] = $row[$LichtingTerm];
			} else {
				$ZoekTermen[] = $row[$ZoekenID];
			}
		}
		while($row = mysqli_fetch_array($result));
	}
	
	if(is_array($ZoekTermen)) {
		return $ZoekTermen;
	} else {
		return array();
	}
}

function getURL($id) {
	global $TableZoeken, $TableUsers, $ZoekenID, $ZoekenUser, $ZoekenTerm, $ZoekenOr, $ZoekenNot, $ZoekenTitel, $ZoekenGroep, $ZoekenSubGroep, $ZoekenPrijsMin, $ZoekenPrijsMax, $ZoekenGeenPrijs, $ZoekenLokatie, $ZoekenPostcode, $ZoekenAfstand, $ZoekenProvincie, $ZoekenFoto, $ZoekenPayPal, $UsersID, $UsersPostcode;
	
	$db			= $db = connect_db();
	$sql		= "SELECT * FROM $TableZoeken WHERE $ZoekenID = $id";	
	$result = mysqli_query($db, $sql);
	$row		= mysqli_fetch_array($result);
		
	if($row[$ZoekenPostcode] != 0) {
		$postcode = $row[$ZoekenPostcode];
	} else {
		$sql_user			= "SELECT * FROM $TableUsers WHERE $UsersID = ". $row[$ZoekenUser];
		$result_user	= mysqli_query($db,$sql_user);
		$row_user			= mysqli_fetch_array($result_user);
		$postcode			= $row_user[$UsersPostcode];
	}
	
	$URL = makeURL($row[$ZoekenTerm], $row[$ZoekenTitel], $row[$ZoekenGroep], $row[$ZoekenSubGroep], $row[$ZoekenPrijsMin], $row[$ZoekenPrijsMax], $row[$ZoekenGeenPrijs], $row[$ZoekenLokatie], $postcode, $row[$ZoekenAfstand], $row[$ZoekenProvincie], $row[$ZoekenPayPal], $row[$ZoekenFoto], $row[$ZoekenOr], $row[$ZoekenNot]);
	
	return $URL;
}


function addPCtoURL($URL, $postcode) {
	if(strpos($URL, '#')) {
		$URL = $URL.'|postcode:'.$postcode;
	} else {
		$URL = $URL.'/#postcode:'.$postcode;
	}
	
	return $URL;
}

function addLimit2URL($URL, $limit) {
	if(strpos($URL, '#')) {
		$URL = $URL.'|limit:'.$limit;
	} else {
		$URL = $URL.'/#limit:'.$limit;
	}
	
	return $URL;
}

function addPage2URL($URL, $p) {
	$URLelementen = explode('/', $URL);
	$key = (array_search('q', $URLelementen)+2);
	
	$URL = array_merge(array_slice($URLelementen, 0, $key), array('p', $p), array_slice($URLelementen, $key));
		
	return implode('/', $URL);
}

function getBasicMarktplaatsData($string) {	
	$statusArray = array('Nieuw', 'Gebruikt', 'Zo goed als nieuw');
	$transportArray = array('Ophalen', 'Ophalen of Verzenden', 'Verzenden');
	$listing_1 = $listing_2 = array('','');
	$Output['status'] = $Output['transport'] = '';
	
	$url					= getString('<span data-url="', '?', $string, 0);	 
	$id_gok				= getString('data-item-id="', '">', $string, 0);
	$title				= getString('" title="', '"', $url[1], 0);
	$beschrijving	= getString('<span class="mp-listing-description">', '</span>', $title[1], 0); 
	$prijs				= getString('<span class="price-new">', '</span>', $beschrijving[1], 0); 
	$datum				= getString('<div class="date">', '</div>', $prijs[1], 0);
	//$plaats				= getString(' <div class="location-name">', ',', $datum[1], 0);
	//$provincie		= getString(',', '</div>', $plaats[1], 0); 
	$plaats				= getString(' <div class="location-name">', '</div>', $datum[1], 0);
	$afstand			= getString('<div class="distance">', 'km', $plaats[1], 0);
	
	if(strpos($string, '<span class="mp-listing-attributes first">'))	$listing_1 = getString('<span class="mp-listing-attributes first">', '</span>', $string, 0);
	if(strpos($string, '<span class="mp-listing-attributes">'))				$listing_2 = getString('<span class="mp-listing-attributes">', '</span>', $string, 0); 
	
	$Output['key']					=	formatString(substr($id_gok[0], 1)); 
	$Output['URL']					=	formatString($url[0]);   
	$Output['title']				=	$title[0];
	$Output['descr_short']	=	formatString($beschrijving[0]); 
	$Output['price']				=	formatString($prijs[0]); 
	$Output['plaats']				=	formatString($plaats[0]); 
	$Output['provincie']		=	''; 
	$Output['afstand']			=	formatString($afstand[0]);
	//$Output['afstand']			=	$afstand[0];
	
	if(in_array($listing_1[0], $statusArray))	$Output['status'] =	formatString($listing_1[0]); 
	if(in_array($listing_2[0], $statusArray))	$Output['status'] =	formatString($listing_2[0]);
	
	if(in_array($listing_1[0], $transportArray))	$Output['transport'] =	formatString($listing_1[0]); 
	if(in_array($listing_2[0], $transportArray))	$Output['transport'] =	formatString($listing_2[0]);
		
	return $Output;	
}


function getAdvancedMarktplaatsData($string) {
	$latitude = $longitude = array('');
	
	$data				= file_get_contents($string);
	$omschrijving	= getString('<div id="vip-ad-description" class="wrapped">', '</div>', $data, 0); 
	$gallery		= getString('data-images-l="', '"', $data, 0); 
	$thumbs			= explode("&", $gallery[0]); 
	
	if(strpos($data, 'lat="'))		$latitude		= getString('lat="', '"', $data, 0); 
	if(strpos($data, 'long="'))		$longitude	= getString('long="', '"', $data, 0); 
		
	if($thumbs[0] != '') {
		foreach($thumbs as $thumb) {			
			$picture[] = str_replace ('_84.JPG', '_82.JPG', str_replace('http://', '//', $thumb));
		}
	} else {
		$picture[] = '//s.marktplaats.com/aurora/res/images/no_photo.jpg'; 
	}
	
	$Output['picture']			=	implode("|", $picture); 
	$Output['descr_long']		=	formatString($omschrijving[0]); 
	$Output['lat']					=	$latitude[0];
	$Output['long']					=	$longitude[0];

	return $Output;	
}
                                                                                                                                                

function NewItem($id, $term) {
	global $DataMarktplaatsID, $TableData, $DataZoekterm;

	$db = connect_db();
	$sql = "SELECT * FROM $TableData WHERE $DataMarktplaatsID = $id AND $DataZoekterm = $term";
		
	$result = mysqli_query($db, $sql);
	
	if($row = mysqli_fetch_array($result))	{
		return false;
	} else {
		//echo '{'. $data['title'] .'}';
		return true;
	}	
}

function makeAdsInactive($term) {
	global $TableData, $DataZoekterm, $DataActive, $DataNotSeen;
	
	$db			= $db = connect_db();
	$sql = "UPDATE $TableData SET $DataNotSeen = $DataNotSeen + 1 WHERE $DataZoekterm = $term";
	$result = mysqli_query($db, $sql);
	
	$sql = "UPDATE $TableData SET $DataActive = '0' WHERE $DataNotSeen > 6";
	$result = mysqli_query($db, $sql);
}


function AddUpdateData($data, $term, $status) {
	global $TableData, $DataMarktplaatsID, $DataPlaats;
	
	$newItem = $status['new'];
	$changedTitle = $status['title'];
	$changedPrice = $status['prijs'];
	$changedStatus = $status['status'];
	$changedTransport = $status['transport'];
	
	if(!$newItem AND ($changedPrice OR $changedTitle OR $changedStatus OR $changedTransport)) {
		# UPDATE titel / prijs		
		changeData($data, $term);
	} elseif ($newItem) {
		# INVOEREN
		AddData($data, $term);
	} else {
		# UPDATE tijd
		UpdateData($data['key'], $term);
	}
}


function AddData($data, $term) {
	global $TableData, $DataMarktplaatsID, $DataActive, $DataURL, $DataTitle, $DataTitleOorsprong, $DataBeschrijving, $DataDatum, $DataZoekterm, $DataAdded, $DataChanged, $DataVerkoper, $DataPlaatje, $DataPrice, $DataPriceOorsprong, $DataPlaats, $DataAfstand, $DataStatus, $DataTransport;
	
	$tijd	= time();
	
	$db 	= connect_db();
	$sql	= "INSERT INTO $TableData ($DataMarktplaatsID, $DataActive, $DataURL, $DataTitle, $DataTitleOorsprong, $DataBeschrijving, $DataVerkoper, $DataDatum, $DataPlaatje, $DataPrice, $DataPriceOorsprong, $DataPlaats, $DataAfstand, $DataZoekterm, $DataAdded, $DataChanged, $DataStatus, $DataTransport) VALUES (". $data['key'] .", '1', '". urlencode($data['URL']) ."', '". urlencode($data['title']) ."', '". urlencode($data['title']) ."', '". urlencode($data['descr_long']) ."','". urlencode($data['verkoper']) ."', ". $data['date'] .", '". $data['picture'] ."', '". $data['price'] ."', '". $data['price'] ."', '". urlencode($data['plaats']) ."', '". $data['afstand'] ."', $term, $tijd, $tijd, '". $data['status'] ."', '". $data['transport'] ."')";
	
	if(mysqli_query($db,$sql)) {
		writeToLog($term, "Toegevoegd", $data['key']);
	} else {
		writeToLog($term, "Fout met toevoegen", $data['key']);
		echo $sql ."<br>\n";
	}
}


function UpdateData($id, $term) {
	global $TableData, $DataMarktplaatsID, $DataChanged, $DataActive, $DataNotSeen;
	
	$tijd	= time();
	
	$db 	= $db = connect_db();
	$sql	= "UPDATE $TableData SET $DataChanged = $tijd, $DataActive = '1', $DataNotSeen = '0' WHERE $DataMarktplaatsID = '$id'";
	
	if(mysqli_query($db,$sql)) {
		writeToLog($term, "Geupdate", $id);
	} else {
		writeToLog($term, "Fout met updaten", $id);
	}
}


function changeData($data, $term) {
	global $TableData, $DataActive, $DataMarktplaatsID, $DataTitle, $DataZoekterm, $DataChanged, $DataPrice, $DataPlaats, $DataNotSeen, $DataStatus, $DataTransport;

	$tijd	= time();
	
	$db = connect_db();	
	$sql	= "UPDATE $TableData SET $DataActive = '1', $DataTitle = '". urlencode($data['title']) ."', $DataPlaats = '". urlencode($data['plaats']) ."', $DataPrice = '". $data['price'] ."', $DataStatus = '". $data['status'] ."', $DataTransport = '". $data['transport'] ."', $DataChanged = $tijd, $DataNotSeen = '0'	WHERE $DataMarktplaatsID = ". $data['key'];
		
	if(mysqli_query($db,$sql)) {
		writeToLog($term, "Gewijzigd", $data['key']);
	} else {
		writeToLog($term, "Fout met wijzigen", $data['key']);
		echo $sql ."<br>\n";
	}
}



function deleteURL($term) {
	global $TableZoeken, $TableLichting, $ZoekenID, $LichtingTerm;
	$db	= $db = connect_db();	
	$sql = "DELETE FROM $TableZoeken WHERE $ZoekenID = '$term'";
	$sql_lichting = "DELETE FROM $TableLichting WHERE $LichtingTerm = $term";
				
	if(mysqli_query($db,$sql) AND mysqli_query($db,$sql_lichting)) {
		writeToLog($term, "URL verwijderd", '');
	} else {
		writeToLog($term, "Fout met verwijderen URL", '');
	}	
}

function deleteData($term) {
	global $TableData, $DataZoekterm;
	$db	= $db = connect_db();	
	$sql = "DELETE FROM $TableData WHERE $DataZoekterm = '$term'";
		
	if(mysqli_query($db,$sql)) {
		writeToLog($term, "Data verwijderd", '');
	} else {
		writeToLog($term, "Fout met verwijderen data", '');
	}	
}

function formatString($string) {
	$output	= $string;
	$output	= strip_tags($output, '<br>');
	$output = str_replace ("\n", " ", $output);	
	$output = str_replace ("&nbsp;", " ", $output);
	$output = str_replace ("  ", " ", $output);
	$output = trim($output);
	//$output = nl2br($output);
	
	return $output;	
}

function formatPrice($price, $euro = true) {
	$input = $price/100;
	
	if($euro) {
		return "&euro; ". number_format($input, 2,',','.');
	} else {
		return number_format($input, 2,',','.');
	}
}

function getMaand($maand) {
	$maand = strtolower($maand);
	
	if($maand == 'jan') {
		$output = '01';
	} elseif ($maand == 'feb') {
		$output = '02';
	} elseif ($maand == 'mrt') {
		$output = '03';
	} elseif ($maand == 'apr') {
		$output = '04';
	} elseif ($maand == 'mei') {
		$output = '05';
	} elseif ($maand == 'jun') {
		$output = '06';
	} elseif ($maand == 'jul') {
		$output = '07';
	} elseif ($maand == 'aug') {
		$output = '08';
	} elseif ($maand == 'sep') {
		$output = '09';
	} elseif ($maand == 'okt') {
		$output = '10';
	} elseif ($maand == 'nov') {
		$output = '11';
	} else {
		$output = '12';
	}
	
	return $output;
}

function getZoekString($id) {
	global $TableZoeken, $ZoekenID, $ZoekenNaam, $ZoekenTerm;
	
	$db			= $db = connect_db();
	$sql		= "SELECT * FROM $TableZoeken WHERE $ZoekenID = $id";	
	$result = mysqli_query($db, $sql);
	$row		= mysqli_fetch_array($result);
	
	if($row[$ZoekenNaam] != '') {
		return $row[$ZoekenNaam];
	} elseif($row[$ZoekenTerm] != '') {
		return $row[$ZoekenTerm];	
	} else {
		return '?';
	}
}

function getAdTitle($id) {
	global $TableData, $DataMarktplaatsID, $DataTitle;
	
	$db			= $db = connect_db();
	$sql		= "SELECT * FROM $TableData WHERE $DataMarktplaatsID = $id";	
	$result = mysqli_query($db, $sql);
	$row		= mysqli_fetch_array($result);
	
	$output	= urldecode($row[$DataTitle]);
	
	if($output != '') {
		return $output;
	} else {
		return '?';
	}
}

function getZoekData($id) {
	global $TableZoeken, $ZoekenID, $ZoekenActive, $ZoekenUser, $ZoekenPrijsMin, $ZoekenPrijsMax, $ZoekenAfstandMin, $ZoekenAfstandMax, $ZoekenKey, $ZoekenCC, $ZoekenNaam, $ZoekenURL;
	global $TableLichting, $LichtingTerm, $LichtingUur, $LichtingDag;
	
	$db = connect_db();
	$sql	= "SELECT * FROM $TableZoeken WHERE $ZoekenID = $id";	
	$result = mysqli_query($db, $sql);
	$row = mysqli_fetch_array($result);
	
	# ALTER TABLE `marktplaats_zoeken` ADD `pmin` INT(5) NULL AFTER `url`, ADD `pmax` INT(5) NULL AFTER `pmin`;
	# ALTER TABLE `marktplaats_zoeken` ADD `dmin` INT(5) NULL AFTER `pmax`, ADD `dmax` INT(5) NULL AFTER `dmin`;
			
	$data['active']		= $row[$ZoekenActive];
	$data['user']			= $row[$ZoekenUser];
	$data['pmin']			= $row[$ZoekenPrijsMin];
	$data['pmax']			= $row[$ZoekenPrijsMax];
	$data['dmin']			= $row[$ZoekenAfstandMin];
	$data['dmax']			= $row[$ZoekenAfstandMax];
	$data['key']			= $row[$ZoekenKey];	
	$data['naam']			= $row[$ZoekenNaam];
	$data['CC']				= $row[$ZoekenCC];
	$data['URL']			= $row[$ZoekenURL];
		
	$sql		= "SELECT * FROM $TableLichting WHERE $LichtingTerm = $id";
	$result = mysqli_query($db, $sql);
	if($row = mysqli_fetch_array($result)) {
		do {
			$uur = $row[$LichtingUur];
			$dag = $row[$LichtingDag];
			$uren[$uur] = 1;
			$dagen[$dag] = 1;			
		} while($row = mysqli_fetch_array($result));
	}
	
	$data['uur'] = $uren;
	$data['dag'] = $dagen;
	
	return $data;
}

function saveURL($id, $user, $active, $url, $CC, $naam, $pmin, $pmax, $dmin, $dmax, $dag, $uur) {
	global $TableZoeken, $ZoekenActive, $ZoekenUser, $ZoekenID, $ZoekenKey, $ZoekenCC, $ZoekenNaam, $ZoekenURL, $ZoekenPrijsMin, $ZoekenPrijsMax, $ZoekenAfstandMin, $ZoekenAfstandMax;
	global $TableLichting, $LichtingTerm, $LichtingUur, $LichtingDag;
	$db	= connect_db();
	
	if($id == '') {
		$key = generateKey(8);
		$sql = "INSERT INTO $TableZoeken ($ZoekenActive, $ZoekenKey, $ZoekenUser, $ZoekenCC, $ZoekenURL, $ZoekenNaam, $ZoekenPrijsMin, $ZoekenPrijsMax, $ZoekenAfstandMin, $ZoekenAfstandMax) VALUES ('$active', '$key', '$user', '$CC', '$url', '$naam', $pmin, $pmax, $dmin, $dmax)";
	} else {
		$sql = "UPDATE $TableZoeken SET $ZoekenActive = '$active', $ZoekenUser = '$user', $ZoekenCC = '$CC', `$ZoekenURL` = '$url', $ZoekenNaam = '$naam', $ZoekenPrijsMin = ". ($pmin == '' ? 'NULL' : $pmin) .", $ZoekenPrijsMax = ". ($pmax == '' ? 'NULL' : $pmax) .", $ZoekenAfstandMin = ". ($dmin == '' ? 'NULL' : $dmin) .", $ZoekenAfstandMax = ". ($dmax == '' ? 'NULL' : $dmax) ." WHERE $ZoekenID = '$id'";
	}
			
	if(!mysqli_query($db,$sql)) {
		echo "[$sql]";
	} elseif($id == '') {
		$id = mysql_insert_id();
	}
		
	$sql_lichting = "DELETE FROM $TableLichting WHERE $LichtingTerm = $id";
	mysqli_query($db,$sql_lichting);
		
	foreach($dag as $d_key => $d_waarde) {
		foreach($uur as $u_key => $u_waarde) {
			if($d_waarde == 1 AND $u_waarde == 1) {
				$sql_lichting = "INSERT INTO $TableLichting ($LichtingDag, $LichtingUur, $LichtingTerm) VALUES ($d_key, $u_key, $id)";
				mysqli_query($db,$sql_lichting);
			}
		}
	}	
}

  
function isActive($id) {
	global $TableZoeken, $ZoekenID, $ZoekenActive;
	
	$db			= $db = connect_db();
	$sql		= "SELECT * FROM $TableZoeken WHERE $ZoekenID = $id";	
	$result = mysqli_query($db, $sql);
	$row		= mysqli_fetch_array($result);
			
	if($row[$ZoekenActive] == 1) {
		return true;
	} else {
		return false;
	}
}

function getPages($term) {
	global $TableData, $DataID, $DataURL, $DataZoekterm, $DataAdded;
	
	$db 		= $db = connect_db();
	$sql		= "SELECT $DataID, $DataURL FROM $TableData WHERE $DataZoekterm = '$term' ORDER BY $DataAdded DESC";
	$result = mysqli_query($db, $sql);
	
	$row		= mysqli_fetch_array($result);
	
	do
	{
		$key					= $row[$DataID];
		$Pages[$key]	= $row[$DataURL];
	}
	while ($row = mysqli_fetch_array($result));
	
	return $Pages;
}

function deletePage($term, $id) {
	global $TableData, $TableNotepad, $DataMarktplaatsID, $NotepadTerm, $NotepadMID, $NotepadTijd, $NotepadBericht;
	
	$db 		= $db = connect_db();
	$sql		= "DELETE FROM $TableData WHERE $DataMarktplaatsID = '$id'";
	
	if(mysqli_query($db,$sql)) {
		writeToLog($term, "Verwijderd", $id);
	} else {
		writeToLog($term, "Fout met verwijderen", $id);
	}
	
	$sql		= "SELECT * FROM $TableNotepad WHERE $NotepadMID = $id";
	$result = mysqli_query($db, $sql);
		
	if(mysqli_num_rows($result) > 0) {		
		$sql = "INSERT INTO $TableNotepad ($NotepadTerm, $NotepadMID, $NotepadTijd, $NotepadBericht) VALUES ($term, $id, ". time() .", '". urlencode('Advertentie bestaat niet meer') ."')";
			
		if(mysqli_query($db,$sql)) {
			writeToLog($term, "Kladblok geupdate", $id);
		} else {
			writeToLog($term, "Fout met updaten van kladblok", $id);
		}
	}
}

function writeToLog($term, $string, $id = '') {
	global $TableLog, $LogTijd, $LogIP, $LogTerm, $LogMarktplaatsID, $LogLog;
	
	$tijd		= time();
	$db 		= $db = connect_db();
	$sql		= "INSERT INTO $TableLog ($LogTijd, $LogIP, $LogTerm, $LogMarktplaatsID, $LogLog) VALUES ('$tijd', '$_SERVER[REMOTE_ADDR]', '$term', '$id', '$string');";
		
	if(!mysqli_query($db,$sql)) {
		echo "LOG-ERROR met [ $sql ]";
	}
}

function getLogData($begin, $eind, $id, $term, $aantal) {
	global $TableLog, $LogTijd, $LogIP, $LogTerm, $LogMarktplaatsID, $LogLog;
	$db 		= $db = connect_db();
		
	if($begin != '' AND $eind != '')	{	$where[]	= "$LogTijd BETWEEN $begin AND $eind"; }
	if($id != '') 										{	$where[]	= "$LogMarktplaatsID = '$id'"; }
	if($term != '')										{	$where[]	= "$LogTerm = '$term'"; }
	
	$sql		= "SELECT * FROM $TableLog WHERE " . implode(' AND ', $where) ." ORDER BY $LogTijd DESC LIMIT 0, $aantal;";
	$result	= mysqli_query($db,$sql);

	if($row = mysqli_fetch_array($result))
	{
		do
		{
			$data[$i]['tijd'] = $row[$LogTijd];
			$data[$i]['ip'] = $row[$LogIP];
			$data[$i]['id'] = $row[$LogMarktplaatsID];
			$data[$i]['term'] = $row[$LogTerm];
			$data[$i]['log'] = $row[$LogLog];
			$i++;
			
		}
		while($row = mysqli_fetch_array($result));
	}
	
	return $data;	
}


function getAds($term, $old) {
	global $TableData, $DataMarktplaatsID, $DataZoekterm, $DataChanged, $DataNotSeen;
	
	$Pages	= array();
		
	$db = connect_db();	
	
	if($old) {
		$sql	 	= "SELECT * FROM $TableData WHERE $DataZoekterm like '$term' AND $DataNotSeen > 5";
		writeToLog($term, 'Advertenties die 5 keer niet gezien zijn');
	} else {
		$sql	 	= "SELECT * FROM $TableData WHERE $DataZoekterm like '$term' ORDER BY $DataChanged ASC";
	}
			
	$result	= mysqli_query($db,$sql);
	
	if($row = mysqli_fetch_array($result)) {
		do {
			$Pages[] = $row[$DataMarktplaatsID];			
		} while($row = mysqli_fetch_array($result));
	}

	return $Pages;
}


function getGroup($url, $begin, $eind) {
	$content= file_get_contents($url);
	$string = getString($begin, $eind, $content, 0);
	$output	= explode('<option label', $string[0]);
	$aantal = count($output);
	
	for($i = 1; $i<$aantal ; $i++)
	{
		$naam	= getString('"', '"', $output[$i], 0);
		$value	= getString('="', '">', $naam[1], 0);
		
		if(substr($value[0], 1, 1) == '"') {$value[0] = substr($value[0], 0, 1); }
		$key	= $value[0];
		$groep[$key] = $naam[0];
	}
	
	return $groep;
}

function cleanupLog() {
	global $TableLog, $LogTijd, $OudeLogsTijd;
	
	$tijd		= time() - $OudeLogsTijd;
	$db 		= $db = connect_db();
	$sql		= "DELETE FROM $TableLog WHERE $LogTijd < $tijd";
		
	if(mysqli_query($db,$sql)) {
		writeToLog('', "Oude logs verwijderd", '');
	} else {
		writeToLog('', "Fout met verwijderen oude logs", '');
	}	
}

function getUserData($id) {
	global $TableUsers, $UsersID, $UsersWachtwoord, $UsersNaam, $UsersMail, $UsersHTML, $UsersRSS, $UsersPostcode, $UsersLatitude, $UsersLongitude;
	
	$db 		= $db = connect_db();
	$sql		= "SELECT * FROM $TableUsers WHERE $UsersID = ". $id;
	$result = mysqli_query($db, $sql);
	$row		= mysqli_fetch_array($result);
	
	$data['naam']				= $row[$UsersNaam];
	$data['wachtwoord']	= $row[$UsersWachtwoord];
	$data['mailadres']	= $row[$UsersMail]; 
	$data['HTML']				= $row[$UsersHTML];
	$data['RSS']				= $row[$UsersRSS];
	$data['postcode']		= $row[$UsersPostcode];
	$data['lat']				= $row[$UsersLatitude];
	$data['long']				= $row[$UsersLongitude];
	$data['coord']			= array($data['long'], $data['lat']);
		
	return $data;	
}


function makeRSSFeed($term, $prefix) {
	global $ScriptRoot, $ScriptTitle, $ScriptMailAdress, $ScriptDescr;
	
	$TermData = getZoekData($term);
	$Pages		= getPages($term);
	
	// http://cyber.law.harvard.edu/rss/rss.html
	
	$feedHeader  ="<?xml version=\"1.0\"?>\n";
	$feedHeader .="<rss version=\"2.0\">\n";
	$feedHeader .="   <channel>\n";
	$feedHeader .="      <title>$ScriptTitle - '". $TermData['q'] ."'</title>\n";
	$feedHeader .="      <link>$ScriptRoot</link>\n";
	$feedHeader .="      <description>$ScriptDescr</description>\n";
	$feedHeader .="      <language>nl-nl</language>\n";
	$feedHeader .="      <pubDate>". gmdate ("D, j M Y H:i:s") ." GMT</pubDate>\n";
	$feedHeader .="      <lastBuildDate>". gmdate ("D, j M Y H:i:s") ." GMT</lastBuildDate>\n";
	//$feedHeader .="      <docs>http://blogs.law.harvard.edu/tech/rss</docs>\n";
	//$feedHeader .="      <generator>Weblog Editor 2.0</generator>\n";
	$feedHeader .="      <managingEditor>$ScriptMailAdress</managingEditor>\n";
	$feedHeader .="      <webMaster>$ScriptMailAdress</webMaster>\n";
	
	foreach($Pages as $id => $url) {
		$PageData = getPageData($id);		
		$src	= "http://bigthumbs.marktplaats.com/kopen/thumbnail/". $PageData['picture'];
		
		$feedItem  = "      <item>\n";
		$feedItem .= "         <title><![CDATA[". str_replace("\n", ' ', strip_tags(urldecode($PageData['title']))) ."]]></title>\n";
		$feedItem .= "         <link>". urldecode($PageData['URL']) ."</link>\n";
		$feedItem .= "         <description><![CDATA[". urldecode($PageData['beschrijving']) ."<br>\n<img src='$src'>]]></description>\n";
		$feedItem .= "         <pubDate>". gmdate ("D, j M Y H:i:s", $PageData['datum']) ." GMT</pubDate>\n";
		$feedItem .= "         <author>". urldecode($PageData['verkoper']) ." | ". $PageData['afstand'] ." km | ". str_replace('�', 'EUR', $PageData['prijs']) ."</author>\n";
		$feedItem .= "      </item>";
	
		$feedItems[] = $feedItem;
	}
	
	$feedFooter	= "   </channel>\n";
	$feedFooter	= "</rss>\n";
	
	$filename = $TermData['key'] . '.xml';
	$handle = fopen($prefix.'RSS/'.$filename, 'w+');
	fwrite($handle, $feedHeader . implode("\n", $feedItems) . $feedFooter);
	fclose($handle);	
}

function getPageData($id) {
	global $TableData, $DataID, $DataMarktplaatsID;
	
	$db 		= $db = connect_db();	
	$sql		= "SELECT * FROM $TableData WHERE $DataID = $id";
	$result	= mysqli_query($db,$sql);
	$row = mysqli_fetch_array($result);
	
	return getPageDataByMarktplaatsID($row[$DataMarktplaatsID]);
}

function getPageDataByMarktplaatsID($id) {
	global $TableData, $DataID, $DataMarktplaatsID, $DataActive, $DataURL, $DataTitle, $DataBeschrijving, $DataDatum, $DataAdded, $DataChanged, $DataVerkoper, $DataPlaatje, $DataAfstand, $DataPrice, $DataPlaats, $DataNotSeen, $DataTitleOorsprong, $DataPriceOorsprong, $DataStatus, $DataTransport;
	
	$db 		= $db = connect_db();	
	$sql		= "SELECT * FROM $TableData WHERE $DataMarktplaatsID = $id";	
	$result	= mysqli_query($db,$sql);
		
	if($row = mysqli_fetch_array($result)) {
 		$PageData['ID'] = $row[$DataID];
 		$PageData['mID'] = $row[$DataMarktplaatsID];
 		$PageData['URL'] = $row[$DataURL];
 		$PageData['active'] = $row[$DataActive];
 		$PageData['title'] = $row[$DataTitle];
 		$PageData['beschrijving'] = $row[$DataBeschrijving];
 		$PageData['verkoper'] = $row[$DataVerkoper];
 		$PageData['datum'] = $row[$DataDatum];
 		$PageData['added'] = $row[$DataAdded];
 		$PageData['changed'] = $row[$DataChanged]; 		
 		$PageData['picture'] = $row[$DataPlaatje];
 		$PageData['afstand'] = $row[$DataAfstand];
 		$PageData['prijs'] = $row[$DataPrice];
 		$PageData['plaats'] = $row[$DataPlaats];
 		$PageData['niet_gezien'] = $row[$DataNotSeen];
 		$PageData['title_o'] = $row[$DataTitleOorsprong];
 		$PageData['prijs_o'] = $row[$DataPriceOorsprong];
 		$PageData['status'] = $row[$DataStatus];
 		$PageData['transport'] = $row[$DataTransport]; 		
	}
	
	return $PageData;
}

function generateKey($size) {
	$options = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '0', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
	
	for($i=0 ; $i<$size ; $i++) {
		$id = rand(0, count($options));
		$key .= $options[$id];
	}
	
	return $key;
}

function deleteRSS($term) {
	global $ScriptRoot;
	
	$ZoekData = getZoekData($term);
	$RSSkey		= $ZoekData['key'];
		
	unlink($ScriptRoot ."RSS/". $RSSkey .".xml");
		
	//echo $ScriptRoot ."RSS/". $RSSkey .".xml";
}

function makeTextBlock($string, $length) {
	if(strlen($string) > $length) {
		$titel = substr($string, 0, $length-5) . "<br>\n.....";
	} else {
		$titel = $string;
	}
	
	return $titel;
}

function showBlock($String) {
	$Text = "<table width='95%' cellpadding='8' cellspacing='1' bgcolor='#636367'>\n";
	$Text .= "<tr>\n";
	$Text .= "	<td bgcolor='#EAEAEA'>$String</td>\n";
	$Text .= "</tr>\n";
	$Text .= "</table>\n";
	
	return $Text;
}

function getNotepadEntry($term, $marktplaats_id) {
	global $TableNotepad, $NotepadUser, $NotepadTerm, $NotepadMID, $NotepadTijd, $NotepadBericht;
	
	$db 		= $db = connect_db();
	
	if($marktplaats_id == 0) {
		$sql 		= "SELECT * FROM $TableNotepad WHERE $NotepadTerm = $term GROUP BY $NotepadMID";
	} else {
		$sql 		= "SELECT * FROM $TableNotepad WHERE $NotepadMID = $marktplaats_id";
	}
	$result	= mysqli_query($db,$sql);
	
	if($row = mysqli_fetch_array($result)) {
		$i = 0;
		do {
			$Output[$i]['id'] = $row[$NotepadMID];
			$Output[$i]['tijd'] = $row[$NotepadTijd];
			$Output[$i]['bericht'] = $row[$NotepadBericht];
			$i++;			
		} while($row = mysqli_fetch_array($result));
	}
	
	return $Output;
}


function send2Pushover($dataArray) {
	global $PushoverKey, $PushoverToken;
	
	if($PushoverKey != '' AND $PushoverToken != '') {
		$push = new Pushover();
		$push->setUser($PushoverKey);
		$push->setToken($PushoverToken);			
		$push->setTitle($dataArray['title']);
		$push->setMessage($dataArray['message']);
		if($dataArray['url'] != '')				$push->setUrl($dataArray['url']);
		if($dataArray['urlTitle'] != '')	$push->setUrlTitle($dataArray['urlTitle']);
		$push->setHtml(1);
		$push->setDebug(true);
		$push->setTimestamp(time());
		$push->send();		
	}
}

function showArray($keys, $array) {
	foreach($array as $key => $value) {
		$newKey = $keys.'['.$key .']';
		
		if(is_array($value)) {
			showArray($newKey, $value);
		} else {
			echo $newKey .' -> '. $value .'<br>';
		}
	}
}
?>
