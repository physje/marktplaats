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

/*
function getZoekTermen($id, $lichting) {
	global $ZoekenID, $TableZoeken, $ZoekenActive, $ZoekenLichting, $ZoekenUser;
	
	$db = connect_db();
	
	if(is_array($lichting)) {		
		foreach($lichting as $groep) {
			$aLicht[] = "$ZoekenLichting = $groep";
		}
		
		$postFix = " AND (". implode(" OR ", $aLicht) .")";
	}
	
	
	if($id == 0) {
		$sql = "SELECT $ZoekenID FROM $TableZoeken WHERE $ZoekenActive = 1". $postFix;
	} else {
		$sql = "SELECT $ZoekenID FROM $TableZoeken WHERE $ZoekenUser = $id". $postFix;
	}
		
	$result = mysqli_query($db, $sql);
	
	if($row = mysqli_fetch_array($result)) {
		do {
			$ZoekTermen[] = $row[$ZoekenID];
		}
		while($row = mysqli_fetch_array($result));
	}
	
	if(is_array($ZoekTermen)) {
		return $ZoekTermen;
	} else {
		return array();
	}
}
*/

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
		$result_user	= mysql_query($sql_user);
		$row_user			= mysqli_fetch_array($result_user);
		$postcode			= $row_user[$UsersPostcode];
	}
	
	$URL = makeURL($row[$ZoekenTerm], $row[$ZoekenTitel], $row[$ZoekenGroep], $row[$ZoekenSubGroep], $row[$ZoekenPrijsMin], $row[$ZoekenPrijsMax], $row[$ZoekenGeenPrijs], $row[$ZoekenLokatie], $postcode, $row[$ZoekenAfstand], $row[$ZoekenProvincie], $row[$ZoekenPayPal], $row[$ZoekenFoto], $row[$ZoekenOr], $row[$ZoekenNot]);
	
	return $URL;
}

function addPCtoURL($URL, $postcode) {
	$URLelementen = parse_url($URL);
	$ZoekElementen = proper_parse_str($URLelementen['query']);
	
	if(!array_key_exists('postcode', $ZoekElementen)) {
		$URL = $URL.'&postcode='.$postcode;
	}
	
	return $URL;
}

function makeURL($q, $ts, $g, $u, $pmin, $pmax, $np, $loc_type, $postcode, $distance, $pv, $pp, $f, $or, $not) {
	$URL = "http://www.marktplaats.nl/z.html?";
	//query=HTC+Desire+-Z&categoryId=1685&postcode=2012&distance=15000
	
	if($q != '')										$URL .= "query=". urlencode($q).($not != '' ? "+-". urlencode($not) : '')."&";
	if($or != 0)										$URL .= "or_query_words=$or&";	
	if($ts != 0)										$URL .= "ts=$ts&";
	if($g != 0)											$URL .= "g=$g&";
	if($u != 0)											$URL .= "categoryId=$u&";
	if($pmin != 0 AND $pmin != '')	$URL .= "pmin=$pmin&";
	if($pmax != 0 AND $pmax != '')	$URL .= "pmax=$pmax&";
	if($np == 1)										$URL .= "np=$np&";
	if($loc_type != '')							$URL .= "loc_type=$loc_type&";
	if($postcode != 0)							$URL .= "postcode=$postcode&";
	if($loc_type == 'zip' AND $distance != 0)	$URL .= "distance=$distance&";
	if($loc_type == 'province' AND $pv != 0)	$URL .= "pv=$pv&";
	if($pp != 0 AND $pp != '')			$URL .= "pp=$pp&";
	if($f != 0 AND $f != 0)					$URL .= "f=$f&";
		
	return $URL;
}


//function makeURL($q, $ts, $g, $u, $pmin, $pmax, $np, $loc_type, $postcode, $distance, $pv, $pp, $f, $or, $not) {
//	
//	$URL = "http://kopen.marktplaats.nl/search.php?";
//	
//	if($not != '')									$URL .= "not_query_words=". urlencode($not) ."&";
//	if($q != '')										$URL .= "q=". urlencode($q) ."&";	
//	if($or != 0)										$URL .= "or_query_words=$or&";	
//	if($ts != 0)										$URL .= "ts=$ts&";
//	if($g != 0)											$URL .= "g=$g&";
//	if($u != 0)											$URL .= "u=$u&";
//	if($pmin != 0 AND $pmin != '')	$URL .= "pmin=$pmin&";
//	if($pmax != 0 AND $pmax != '')	$URL .= "pmax=$pmax&";
//	if($np == 1)										$URL .= "np=$np&";
//	if($loc_type != '')							$URL .= "loc_type=$loc_type&";
//	if($postcode != 0)							$URL .= "postcode=$postcode&";
//	if($loc_type == 'zip' AND $distance != 0)	$URL .= "distance=$distance&";
//	if($loc_type == 'province' AND $pv != 0)	$URL .= "pv=$pv&";
//	if($pp != 0 AND $pp != '')			$URL .= "pp=$pp&";
//	if($f != 0 AND $f != 0)					$URL .= "f=$f&";
//		
//	//return $URL.'s=500&tb=of';
//	return $URL.'s=500&av[-1][0]=0&srt=lu';
//}

//function makeURL($q, $ts, $g, $u, $pmin, $pmax, $np, $loc_type, $postcode, $distance, $pv, $pp, $f, $or, $not)
//{
//	
//	$URL = "http://kopen.marktplaats.nl/search.php?";
//	$URL .= "xl=1&ds=";
//	
//	if($q != '')										$URL .= "kw:". urlencode($q) .";";
//	//if($ts != '')										$URL .= "to:". urlencode($ts) .";";	
//	if($g != 0)											$URL .= "l1:$g;";
//	if($u != 0)											$URL .= "l2:$u;";
//	if($pmin != 0 AND $pmin != '')	$URL .= "pm:$pmin;";
//	if($pmax != 0 AND $pmax != '')	$URL .= "pa:$pmax;";
//	if($loc_type != '')							$URL .= "lt:$loc_type;";	
//	if($loc_type == 'zip' AND $distance != 0)	$URL .= "di:$distance;";
//	
//	if($ts != 0)										$URL .= "ts=$ts&";
//	if($postcode != 0)							$URL .= "postcode=$postcode&";
//	
//	$URL .= "osi:2&ppu=0&av[-1][0]=0";
//	
//	return $URL;
//}

//function getMarktplaatsData($string)
//{
//	$id						= getString('', '" class="row', $string, 0);
//	$thumb				= getString('"><img class="thumbnail" src="', '" align="top">', $id[1], 0);
//	//$picture		= str_replace ("http://thumbs.marktplaats.nl/kopen/thumbs/", "", $thumb[0]);
//	$picture		= str_replace ("http://bigthumbs.marktplaats.com/kopen/thumbnail/", "", $thumb[0]);
//	$URL					= getString('<a href="', '" userdata="return', $thumb[1], 0);
//	$title				= getString('<span>', '</span>', $URL[1], 0);
//	
//	if(strpos($URL[1], '</script><b>')) {
//		$title				= getString('</script><b>', '</b>', $URL[1], 0);
//	}
//	
//	//$title				= getString('">', '</h3>', $URL[1], 0);	
//	$beschrijving	= getString('<small>', '</small>', $title[1], 0);
//	$prijs				= getString('align="right">', 'div>', $beschrijving[1], 0);		
//	$geplaatst		= getString('align="center">', 'div>', $prijs[1], 0);	
//	$bezoeken			= getString('align="center">', '</td>', $geplaatst[1], 0);	
//	$plaats				= getString('<div class="text_margin">', ',', $bezoeken[1], 0);
//	$provincie		= getString(',', '<br>', $plaats[1], 0);
//	$afstand			= getString('<br>', 'km</div>', $provincie[1], 0);
//		
//	if(substr_count ($thumb[0], 'http://statisch.marktplaats.nl/images/no_image') == 1) {
//		$picture = '';
//	}
//	
//	$temp			= getString('', '" align="top"', $picture, 0);
//	$fotos[]		= $temp[0];
//		
//	$data					= file_get_contents(formatString($URL[0]));
//	$ad_nr 				= getString('Advertentienr.:', '</span>', $data, 0);
//	//$datum				= getString('align="center">', '</td>', $prijs[1], 0);
//	$verkoper			= getString('<dt>Naam:</dt>', '</dd>', $ad_nr[1], 0);
//	$datum				= getString('<dt>Datum:</dt>', '</dd>', $ad_nr[1], 0);
//		
//	if(strpos($ad_nr[1], 'Telefoon:')) {	$telefoon			= getString('<dt>Telefoon:</dt>', '</dd>', $ad_nr[1], 0);	}
//	$descr_long		= getString('<div class="vip_descr_txt">', '</div>', $ad_nr[1], 0);
//	
//	if(strpos($descr_long[1], 'http://thumbs.marktplaats.nl/kopen/thumbs/')) {
//		$photos = explode ('http://thumbs.marktplaats.nl/kopen', $descr_long[1]);
//		
//		$aantal = count($photos);
//		
//		for($p=2 ; $p < $aantal ; $p++) {
//			$photo	= $photos[$p];
//			$thumb		= getString('thumbs/', '" alt', $photo, 0);								
//			$fotos[]	= formatString($thumb[0]);
//		}
//	}
//		
//	if(is_array($fotos)) {
//		$picture = implode('|', $fotos);
//	} else {
//		$picture = '';
//	}
//	
//	$Output['id']						=	formatString($id[0]);
//	$Output['URL']					=	"http://link.marktplaats.nl/". formatString($id[0]);//formatString($URL[0]);
//	$Output['picture']			=	$picture;
//	$Output['title']				=	$title[0];
//	$Output['descr_short']	=	formatString($beschrijving[0]);
//	$Output['price']				=	formatString($prijs[0]);
//	$Output['date']					=	convertDate(formatString($datum[0]));
//	$Output['visits']				=	formatString($bezoeken[0]);
//	$Output['plaats']				=	formatString($plaats[0]);
//	$Output['provincie']		=	formatString($provincie[0]);
//	$Output['afstand']			=	formatString($afstand[0]);	
//	$Output['nummer']				=	formatString($ad_nr[0]);
//	$Output['verkoper']			=	formatString($verkoper[0]);
//	$Output['telefoon']			=	formatString($telefoon[0]);
//	$Output['descr_long']		=	$descr_long[0];
//	
//	//foreach($Output as $key => $value) {
//	//	echo "$key -> $value<br>\n\n";
//	//}
//	
//	return $Output;
//}

//function getMarktplaatsData_v2($string) {
//	$id						= getString('', '" class="row', $string, 0);
//	$thumb				= getString('src="', '"', $id[1], 0);
//			
//	//if(strpos ($thumb[0], 'geenfoto')) {
//	//	$picture = '';
//	//} else {
//	//	$picture			= str_replace ("http://thumbs.marktplaats.com/kopen/thumbs/", "", $thumb[0]);
//	//}
//	
//	$title				= getString('<span>', '</span>', $thumb[1], 0);
//	$beschrijving	= getString('<small>', '</small>', $title[1], 0);
//	$prijs				= getString('align="right">', '</div>', $beschrijving[1], 0);
//	$datum				= getString('align="center">', '</div>', $prijs[1], 0);
//	$bezoeken			= getString('align="center">', '</td>', $datum[1], 0);
//	$plaats				= getString('<div class="text_margin">', ',', $bezoeken[1], 0);
//	$provincie		= getString(',', '<br>', $plaats[1], 0);
//	$afstand			= getString('<br>', 'km', $provincie[1], 0);
//	
//	$data					= file_get_contents("http://link.marktplaats.nl/". formatString($id[0]));	
//	$DatumAll			= getString('<nobr>sinds ', '</nobr>', $data, 0);
//	$verkoper_id	= getString("'seller_id']='", "';", $data, 0);
//	$postcode			= getString("['ad.zipcode']='", "';", $data, 0);
//	$verkoper			= getString('name="recipient_nickname" value="', '" />', $DatumAll[1], 0);
//	$omschrijving	= getString('<div class="l" style="margin-top: 5px;*margin-top:3px;">', '</div>', $DatumAll[1], 0);
//	$temp					= getString('<p class="lh20">', "<", $data, 0);	
//	
//	$plaats				= getString('', ',', $temp[0], 0);
//	$provincie		= getString(',', '', $temp[0], 0);
//	
//	if(strpos($data, '<nobr><small>')) {
//		$prijs_add		= getString("<nobr><small>(", ")</small></nobr>", $data, 0);
//	} else {
//		$prijs_add[0] = '';
//	}	
//			
//	if(strpos($data, 'photos = []')) {
//		$gallery			= getString("var photos = [];", "var gallery", $data, 0);
//		$thumbs				= explode("photos.push", $gallery[0]);
//	
//		foreach($thumbs as $thumb) {
//			if(strpos($thumb, 'thumbs.push(')) {
//				$picture_url = getString("thumbs.push('", "');", $thumb, 0);
//			
//				if(strpos($picture_url[0], 'thumbs.marktplaats.com/kopen/thumbs')) {
//					$picture[] = str_replace ("http://thumbs.marktplaats.com/kopen/thumbs/", "http://bigthumbs.marktplaats.com/kopen/thumbnail/", $picture_url[0]);
//				} elseif(strpos($picture_url[0], 'cps.marktplaats.com/cps/auroraprod')) {
//					$picture[] = str_replace ("_32.jpeg", "_33.jpeg", $picture_url[0]);
//				}
//			}
//		}
//	} else {
//		$picture[] = 'http://statisch.marktplaats.com/images/vip/vip_no_photo.gif';
//	}
//	
//				
//	$Output['id']						=	formatString($id[0]);
//	$Output['URL']					=	"http://link.marktplaats.nl/". formatString($id[0]);
//	$Output['picture']			=	implode("|", $picture);
//	$Output['title']				=	$title[0];
//	//$Output['descr_short']	=	formatString($beschrijving[0]);
//	$Output['descr_long']		=	formatString($omschrijving[0]);
//	$Output['price']				=	formatString($prijs[0]);
//	$Output['price_add']		=	formatString($prijs_add[0]);
//	$Output['date']					=	convertDate(formatString($DatumAll[0]));
//	//$Output['date_2']					=	$DatumAll[0] . ' ['. date("d-m H:i", $Output['date']) .']' ;
//	//$Output['visits']				=	formatString($bezoeken[0]);
//	$Output['plaats']				=	formatString($plaats[0]);
//	//$Output['provincie']		=	formatString($provincie[0]);
//	$Output['afstand']			=	formatString($afstand[0]);
//	$Output['verkoper']			=	formatString($verkoper[0]);
//	$Output['verkoper_id']	=	formatString($verkoper_id[0]);
//	$Output['postcode']			=	formatString($postcode[0]);
//		
//	
//	//foreach($Output as $key => $value) {
//	//	echo "$key -> $value<br>\n\n";
//	//}	
//	
//	return $Output;
//}

function getMarktplaatsData_v3($string) { 
	$url					= getString('<span data-url="', '?', $string, 0);
		 
	//$title				= getString('"thumb-placeholder-centered juiceless-link" title="', '"', $url[1], 0); 
	$title				= getString('" title="', '"', $url[1], 0);
	$beschrijving	= getString('<span class="mp-listing-description wrapped">', '</span>', $title[1], 0); 
	//$prijs				= getString('<span class="price">', '</span>', $beschrijving[1], 0); 
	$prijs				= getString('<div class="price-new ellipsis">', '</div>', $beschrijving[1], 0); 
	//$datum				= getString('<td class="column-date">', '</td>', $prijs[1], 0);
	$datum				= getString('<div class="date">', '</div>', $prijs[1], 0);
	$plaats				= getString(' <div class="location-name">', ',', $datum[1], 0);
	$provincie		= getString(',', '</div>', $plaats[1], 0); 
	$afstand			= getString('<div class="distance">', 'km', $provincie[1], 0); 
	
	$data					= file_get_contents($url[0]);
	$bezoeken			= getString('<div id="vip-ad-count">', 'x gezien', $data, 0);	
	$DatumAll			= getString('sinds ', '</div>', $bezoeken[1], 0); 
	$id						= getString('data-advertisement-id="', '"', $DatumAll[1], 0);
	$verkoper_id	= getString('<a href="http://www.marktplaats.nl/verkopers/', '.html?', $data, 0); 
	//$postcode			= getString("['ad.zipcode']='", "';", $data, 0); 
	$verkoper			= getString('<h2 class="name" title="', '">', $verkoper_id[1], 0); 
	$omschrijving	= getString('<div id="vip-ad-description" class="wrapped">', '</div>', $id[1], 0); 
		 
	if(strpos($data, '<nobr><small>')) { 
		$prijs_add		= getString("<nobr><small>(", ")</small></nobr>", $data, 0); 
	} else { 
		$prijs_add[0] = ''; 
	}	 
			 
	$gallery	= getString('data-images-l="', '"', $data, 0); 
	$thumbs		= explode("&", $gallery[0]); 
	
	if($thumbs[0] != '') {
		foreach($thumbs as $thumb) {
			//$picture[] = str_replace ("_84.JPG", "_82.JPG", $thumb); 
			$picture[] = str_replace ('_84.JPG', '_82.JPG', str_replace('http://', '//', $thumb));
		}
	} else {
		$picture[] = '//s.marktplaats.com/aurora/res/images/no_photo.jpg'; 
	}
					 
	$Output['id']						=	formatString($id[0]); 
	$Output['URL']					=	formatString($url[0]); 
	$Output['picture']			=	implode("|", $picture); 
	$Output['title']				=	$title[0];
	$Output['descr_long']		=	formatString($omschrijving[0]); 
	$Output['price']				=	formatString($prijs[0]); 
	$Output['price_add']		=	formatString($prijs_add[0]); 
	$Output['date']					=	convertDate(formatString($DatumAll[0])); 
	//$Output['date_2']					=	$DatumAll[0] . ' ['. date("d-m H:i", $Output['date']) .']' ; 
	$Output['visits']				=	formatString($bezoeken[0]); 
	$Output['plaats']				=	formatString($plaats[0]); 
	$Output['provincie']		=	formatString($provincie[0]); 
	$Output['afstand']			=	formatString($afstand[0]); 
	$Output['verkoper']			=	formatString($verkoper[0]); 
	$Output['verkoper_id']	=	formatString($verkoper_id[0]); 
	//$Output['postcode']			=	formatString($postcode[0]); 
		 
	 
	//foreach($Output as $key => $value) { 
	//	echo "$key -> $value<br>\n\n"; 
	//}	 
	 
	return $Output; 
}                                                                                                                                                   

function NewItem($data, $term) {
	global $DataMarktplaatsID, $TableData, $DataZoekterm;
	
	$id			= $data['id'];
	
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


function changedItem($data, $term) {
	global $DataMarktplaatsID, $TableData, $DataZoekterm, $DataTitle;
	
	if(NewItem($data, $term)) {
		return false;
	} else {
		$id			= $data['id'];
		$titel	= $data['title'];
	
		$db			= $db = connect_db();
		$sql		= "SELECT * FROM $TableData WHERE $DataMarktplaatsID = $id AND $DataZoekterm = $term";
		$result = mysqli_query($db, $sql);	
		$row 		= mysqli_fetch_array($result);
		
		if($row[$DataTitle] == urlencode($titel)) {			
			return false;
		} else {
			//echo '['. $row[$DataTitle] .'|'. urlencode($titel) .'] -> ';
			return true;
		}
	}	
}

function makeAdsInactive($term) {
	global $TableData, $DataZoekterm, $DataActive;
	
	$db			= $db = connect_db();
	$sql = "UPDATE $TableData SET $DataActive = 0 WHERE $DataZoekterm = $term";
	$result = mysqli_query($db, $sql);	
}


function AddUpdateData($data, $term) {
	global $TableData, $DataMarktplaatsID, $DataZoekterm;
	
	$changed = changedItem($data, $term);	
	$NewItem = NewItem($data, $term);
	
	//echo $data['title'];
	
	if(!$NewItem AND $changed) {
		# UPDATE titel		
		changeData($data, $term);
		//echo ' -> update titel';
	} elseif ($NewItem) {
		# INVOEREN
		AddData($data, $term);
		//echo ' -> toevoegen !<br>';
	} else {
		# UPDATE tijd
		UpdateData($data['id'], $term);
		//echo ' -> update tijd<br>';
	}
	//echo "<br>\n";
}


function AddData($data, $term) {
	global $TableData, $DataMarktplaatsID, $DataActive, $DataURL, $DataTitle, $DataBeschrijving, $DataDatum, $DataZoekterm, $DataAdded, $DataChanged, $DataVerkoper, $DataPlaatje, $DataPrice, $DataAfstand;
	
	$tijd	= time();
	
	$db 	= $db = connect_db();
	$sql	= "INSERT INTO $TableData ($DataMarktplaatsID, $DataActive, $DataURL, $DataTitle, $DataBeschrijving, $DataVerkoper, $DataDatum, $DataPlaatje, $DataPrice, $DataAfstand, $DataZoekterm, $DataAdded, $DataChanged) VALUES (". $data['id'] .", '1', '". urlencode($data['URL']) ."', '". urlencode($data['title']) ."' ,'". urlencode($data['descr_long']) ."','". urlencode($data['verkoper']) ."', ". $data['date'] .", '". $data['picture'] ."', '". $data['price'] ."', '". $data['afstand'] ."',	$term, $tijd, $tijd)";
	
	if(mysql_query($sql)) {
		writeToLog($term, "Toegevoegd", $data['id']);
	} else {
		writeToLog($term, "Fout met toevoegen", $data['id']);
		echo $sql ."<br>\n";
	}
}


function UpdateData($id, $term) {
	global $TableData, $DataMarktplaatsID, $DataChanged, $DataActive;
	
	$tijd	= time();
	
	$db 	= $db = connect_db();
	$sql	= "UPDATE $TableData SET $DataChanged = $tijd, $DataActive = '1' WHERE $DataMarktplaatsID = '$id'";
	
	if(mysql_query($sql)) {
		writeToLog($term, "Geupdate", $id);
	} else {
		writeToLog($term, "Fout met updaten", $id);
	}
}


function changeData($data, $term) {
	global $TableData, $DataActive, $DataMarktplaatsID, $DataURL, $DataTitle, $DataBeschrijving, $DataDatum, $DataZoekterm, $DataAdded, $DataChanged, $DataVerkoper, $DataPlaatje, $DataPrice, $DataAfstand;

	$id = $data['id'];	
	$tijd	= time();
	
	$db = connect_db();	
	
	$sql	= "DELETE FROM $TableData WHERE $DataMarktplaatsID = '$id'";	
	if(mysql_query($sql)) {
		$sql	= "INSERT INTO $TableData ($DataMarktplaatsID, $DataActive, $DataURL, $DataTitle, $DataBeschrijving, $DataVerkoper, $DataDatum, $DataPlaatje, $DataPrice, $DataAfstand, $DataZoekterm, $DataAdded, $DataChanged) VALUES (". $data['id'] .", '1', '". urlencode($data['URL']) ."', '". urlencode($data['title']) ."' ,'". urlencode($data['descr_long']) ."','". urlencode($data['verkoper']) ."', ". $data['date'] .", '". $data['picture'] ."', '". $data['price'] ."', '". $data['afstand'] ."',	$term, $tijd, $tijd)";
	
		if(mysql_query($sql)) {
			writeToLog($term, "Gewijzigd", $data['id']);
		} else {
			writeToLog($term, "Fout met wijzigen", $data['id']);
			echo $sql ."<br>\n";
		}
	}
	
}



function deleteURL($term) {
	global $TableZoeken, $TableLichting, $ZoekenID, $LichtingTerm;
	$db	= $db = connect_db();	
	$sql = "DELETE FROM $TableZoeken WHERE $ZoekenID = '$term'";
	$sql_lichting = "DELETE FROM $TableLichting WHERE $LichtingTerm = $term";
				
	if(mysql_query($sql) AND mysql_query($sql_lichting)) {
		writeToLog($term, "URL verwijderd", '');
	} else {
		writeToLog($term, "Fout met verwijderen URL", '');
	}	
}

function deleteData($term) {
	global $TableData, $DataZoekterm;
	$db	= $db = connect_db();	
	$sql = "DELETE FROM $TableData WHERE $DataZoekterm = '$term'";
		
	if(mysql_query($sql)) {
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

function convertDate($date) {
	//echo "(". substr($date, 10, 2) .", ". substr($date, 13, 2) .", 0, ". substr($date, 3, 2) .", ". substr($date, 0, 2) .", 20". substr($date, 6, 2) .")";

	$delen = explode(',', $date);
	
	$datumDelen = explode(' ', $delen[0]);
	$tijdDelen = explode(':', $delen[1]);
	
	switch ($datumDelen[1]) {
		case 'jan.':
			$maand = 1;
			break;
		case 'feb.':
			$maand = 2;
			break;
		case 'mrt.':
			$maand = 3;
			break;
		case 'apr.':
			$maand = 4;
			break;
		case 'mei':
			$maand = 5;
			break;
		case 'jun.':
			$maand = 6;
			break;
		case 'jul.':
			$maand = 7;
			break;
		case 'aug.':
			$maand = 8;
			break;
		case 'sep.':
			$maand = 9;
			break;
		case 'okt.':
			$maand = 10;
			break;
		case 'nov.':
			$maand = 11;
			break;
		case 'dec.':
			$maand = 12;
			break;
	}
		
	return mktime($tijdDelen[0], $tijdDelen[1], 0, $maand, $datumDelen[0], '20'. substr($datumDelen[2], -2));
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

function getGroepen($id) {
	global $TableGroep, $TableSubGroep, $SubGroepGroep, $GroepGroep, $GroepNaam, $SubGroepSubGroep, $SubGroepNaam;
	
	$db = connect_db();
	
	if($id == '') {
		$sql = "SELECT * FROM $TableGroep";
	} else {
		$sql = "SELECT * FROM $TableSubGroep WHERE $SubGroepGroep = $id";
	}
	
	$result = mysqli_query($db, $sql);
	
	if($row = mysqli_fetch_array($result)) {
		do {
			if($id == '') {
				$key		= $row[$GroepGroep];
				$Groepen[$key]	= $row[$GroepNaam];
			} else {
				$key		= $row[$SubGroepSubGroep];
				$Groepen[$key]	= $row[$SubGroepNaam];
			}
		}
		while($row = mysqli_fetch_array($result));
	}
	
	return $Groepen;
}

function getZoekData($id) {
	global $TableZoeken, $TableLichting, $ZoekenID, $ZoekenActive, $ZoekenUser, $ZoekenTerm, $ZoekenOr, $ZoekenNot, $ZoekenTitel, $ZoekenGroep, $ZoekenSubGroep, $ZoekenPrijsMin, $ZoekenPrijsMax, $ZoekenGeenPrijs, $ZoekenLokatie, $ZoekenPostcode, $ZoekenAfstand, $ZoekenProvincie, $ZoekenFoto, $ZoekenPayPal, $ZoekenKey, $LichtingTerm, $LichtingUur, $LichtingDag, $ZoekenCC, $ZoekenNaam, $ZoekenURL;
	
	$db = connect_db();
	$sql	= "SELECT * FROM $TableZoeken WHERE $ZoekenID = $id";	
	$result = mysqli_query($db, $sql);
	$row = mysqli_fetch_array($result);
	
	$data['active']		= $row[$ZoekenActive];
	$data['user']			= $row[$ZoekenUser];
	$data['q']				= $row[$ZoekenTerm];
	$data['ts']				= $row[$ZoekenTitel];
	$data['g']				= $row[$ZoekenGroep];
	$data['u']				= $row[$ZoekenSubGroep];
	$data['pmin']			= $row[$ZoekenPrijsMin];
	$data['pmax']			= $row[$ZoekenPrijsMax];
	$data['np']				= $row[$ZoekenGeenPrijs];
	$data['loc_type']	= $row[$ZoekenLokatie];
	$data['postcode']	= $row[$ZoekenPostcode];
	$data['distance']	= $row[$ZoekenAfstand];
	$data['pv']				= $row[$ZoekenProvincie];
	$data['pp']				= $row[$ZoekenPayPal]; 
	$data['f']				= $row[$ZoekenFoto];
	$data['or']				= $row[$ZoekenOr];
	$data['not']			= $row[$ZoekenNot];
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

//function saveURL($id, $user, $active, $q, $ts, $g, $u, $pmin, $pmax, $np, $loc_type, $postcode, $distance, $pv, $f, $or, $not, $CC, $naam, $dag, $uur) {
//	global $TableZoeken, $TableLichting, $ZoekenActive, $ZoekenLichting, $ZoekenOr, $ZoekenNot, $ZoekenUser, $ZoekenTerm, $ZoekenTitel, $ZoekenGroep, $ZoekenSubGroep, $ZoekenPrijsMin, $ZoekenPrijsMax, $ZoekenGeenPrijs, $ZoekenLokatie, $ZoekenPostcode, $ZoekenAfstand, $ZoekenProvincie, $ZoekenFoto, $ZoekenID, $ZoekenKey, $ZoekenCC, $ZoekenNaam, $LichtingTerm, $LichtingUur, $LichtingDag;
//	$db	= $db = connect_db();
//	
//	if($id == '') {
//		$key = generateKey(8);
//		$sql = "INSERT INTO $TableZoeken ($ZoekenActive, $ZoekenUser, $ZoekenKey, $ZoekenTerm, $ZoekenNot, $ZoekenOr, $ZoekenTitel, $ZoekenGroep, $ZoekenSubGroep, $ZoekenPrijsMin, $ZoekenPrijsMax, $ZoekenGeenPrijs, $ZoekenLokatie, $ZoekenPostcode, $ZoekenAfstand, $ZoekenProvincie, $ZoekenFoto,	$ZoekenCC, $ZoekenNaam) VALUES ('$active', '$user', '$key', '$q', '$not', '$or', '$ts', '$g', '$u', '$pmin', '$pmax', '$np', '$loc_type', '$postcode', '$distance', '$pv', '$f',	'$CC', '$naam')";
//	} else {
//		$sql = "UPDATE $TableZoeken SET $ZoekenActive = '$active', $ZoekenUser = '$user', $ZoekenTerm = '$q', $ZoekenOr = '$or' , $ZoekenNot = '$not', $ZoekenTitel = '$ts', $ZoekenGroep = '$g', $ZoekenSubGroep = '$u', $ZoekenPrijsMin = '$pmin', $ZoekenPrijsMax = '$pmax', $ZoekenGeenPrijs = '$np', $ZoekenLokatie = '$loc_type', $ZoekenPostcode = '$postcode', $ZoekenAfstand = '$distance', $ZoekenProvincie = '$pv', $ZoekenFoto = '$f', $ZoekenCC = '$CC', $ZoekenNaam = '$naam'	WHERE $ZoekenID = '$id'";
//	}
//	
//	if(!mysql_query($sql)) {
//		echo "[$sql]";
//	} elseif($id == '') {
//		$id = mysql_insert_id();
//	}
//		
//	$sql_lichting = "DELETE FROM $TableLichting WHERE $LichtingTerm = $id";
//	mysql_query($sql_lichting);
//		
//	foreach($dag as $d_key => $d_waarde) {
//		foreach($uur as $u_key => $u_waarde) {
//			if($d_waarde == 1 AND $u_waarde == 1) {
//				$sql_lichting = "INSERT INTO $TableLichting ($LichtingDag, $LichtingUur, $LichtingTerm) VALUES ($d_key, $u_key, $id)";
//				mysql_query($sql_lichting);
//			}
//		}
//	}	
//}

function saveURL($id, $user, $active, $url, $CC, $naam, $dag, $uur) {
	global $TableZoeken, $TableLichting, $ZoekenActive, $ZoekenUser, $ZoekenID, $ZoekenKey, $ZoekenCC, $ZoekenNaam, $ZoekenURL, $LichtingTerm, $LichtingUur, $LichtingDag;
	$db	= $db = connect_db();
	
	if($id == '') {
		$key = generateKey(8);
		$sql = "INSERT INTO $TableZoeken ($ZoekenActive, $ZoekenKey, $ZoekenUser, $ZoekenCC, $ZoekenURL, $ZoekenNaam) VALUES ('$active', '$key', '$user', '$CC', '$url', '$naam')";
	} else {
		$sql = "UPDATE $TableZoeken SET $ZoekenActive = '$active', $ZoekenUser = '$user', $ZoekenCC = '$CC', $ZoekenURL = '$url', $ZoekenNaam = '$naam' WHERE $ZoekenID = '$id'";
	}	
	
	if(!mysql_query($sql)) {
		echo "[$sql]";
	} elseif($id == '') {
		$id = mysql_insert_id();
	}
		
	$sql_lichting = "DELETE FROM $TableLichting WHERE $LichtingTerm = $id";
	mysql_query($sql_lichting);
		
	foreach($dag as $d_key => $d_waarde) {
		foreach($uur as $u_key => $u_waarde) {
			if($d_waarde == 1 AND $u_waarde == 1) {
				$sql_lichting = "INSERT INTO $TableLichting ($LichtingDag, $LichtingUur, $LichtingTerm) VALUES ($d_key, $u_key, $id)";
				mysql_query($sql_lichting);
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
	
	if(mysql_query($sql)) {
		writeToLog($term, "Verwijderd", $id);
	} else {
		writeToLog($term, "Fout met verwijderen", $id);
	}
	
	$sql		= "SELECT * FROM $TableNotepad WHERE $NotepadMID = $id";
	$result = mysqli_query($db, $sql);
		
	if(mysqli_num_rows($result) > 0) {		
		$sql = "INSERT INTO $TableNotepad ($NotepadTerm, $NotepadMID, $NotepadTijd, $NotepadBericht) VALUES ($term, $id, ". time() .", '". urlencode('Advertentie bestaat niet meer') ."')";
			
		if(mysql_query($sql)) {
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
		
	if(!mysql_query($sql)) {
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
	$result	= mysql_query($sql);

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

function getNumberOfAds($term, $old) {
	global $TableData, $DataMarktplaatsID, $DataZoekterm, $DataChanged, $OudeAdvTijd;
		
	$db 	= $db = connect_db();
	
	$sql	 = "SELECT * FROM $TableData WHERE $DataZoekterm = '$term' ";
	
	if($old) {
		$tijd = time() - $OudeAdvTijd;
		$sql	.= "AND $DataChanged < $tijd";
	}
	
	$result = mysqli_query($db, $sql);	
	return mysqli_num_rows($result);
}


function getAds($term, $old) {
	global $TableData, $DataMarktplaatsID, $DataZoekterm, $DataChanged, $OudeAdvTijd;
	
	$Pages	= array();
		
	$db 		= $db = connect_db();	
	
	if($old) {
		$sql_tijd	= "SELECT max($DataChanged) FROM $TableData WHERE $DataZoekterm like '$term'";
		$result		= mysql_query($sql_tijd);
		$row			= mysqli_fetch_array($result);
		
		// Ik zoek dus uit wanneer er voor het laatst iets gewijzigd is.
		// 2 maal dat verschil ten opzichte van nu is 'oud'
		$tijd 	= time() - (2*(time() - $row[0]));
		$sql	 	= "SELECT * FROM $TableData WHERE $DataZoekterm like '$term' AND $DataChanged < $tijd";
		writeToLog($term, 'Advertenties van voor '. date("d-m H:i", $tijd));
	} else {
		$sql	 	= "SELECT * FROM $TableData WHERE $DataZoekterm like '$term'";
	}
		
	$result	= mysql_query($sql);
	
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
		
	if(mysql_query($sql)) {
		writeToLog('', "Oude logs verwijderd", '');
	} else {
		writeToLog('', "Fout met verwijderen oude logs", '');
	}	
}

function getUserData($id) {
	global $TableUsers, $UsersID, $UsersWachtwoord, $UsersNaam, $UsersMail, $UsersHTML, $UsersRSS, $UsersPostcode;
	
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
		
	return $data;	
}

/*
function generateLichtingArray($hour, $day) {
	
	$uren[1] = array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23);
	$uren[2] = array(0,2,4,6,8,10,12,14,16,18,20,22);
	$uren[3] = array(1,3,5,7,9,11,13,15,17,19,21,23);
	$uren[4] = array(2,6,10,14,18,22);
	$uren[5] = array(3,9,15,23);
	$uren[6] = array(4,16);
	$uren[7] = array(5);
	$uren[8] = array(7);
	
	if($day == 5) { $last = 9; } else { $last = 8; }
	
	for($l = 1 ; $l < $last ; $l++) {
		if(in_array($hour, $uren[$l])) {
			$lichting[] = $l;
		}
	}
		
	return $lichting;
}
*/

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
	global $TableData, $DataID, $DataMarktplaatsID, $DataURL, $DataTitle, $DataBeschrijving, $DataDatum, $DataAdded, $DataChanged, $DataVerkoper, $DataPlaatje, $DataAfstand, $DataPrice;
	
	$db 		= $db = connect_db();	
	$sql		= "SELECT * FROM $TableData WHERE $DataID = $id";
	$result	= mysql_query($sql);
	
	if($row = mysqli_fetch_array($result)) {
		$PageData['ID'] = $row[$DataMarktplaatsID];
 		$PageData['URL'] = $row[$DataURL];
 		$PageData['title'] = $row[$DataTitle];
 		$PageData['beschrijving'] = $row[$DataBeschrijving];
 		$PageData['verkoper'] = $row[$DataVerkoper];
 		$PageData['datum'] = $row[$DataDatum];
 		$PageData['added'] = $row[$DataAdded];
 		$PageData['changed'] = $row[$DataChanged]; 		
 		$PageData['picture'] = $row[$DataPlaatje];
 		$PageData['afstand'] = $row[$DataAfstand];
 		$PageData['prijs'] = $row[$DataPrice]; 		
	}
	
	return $PageData;
}

function getPageDataByMarktplaatsID($id) {
	global $TableData, $DataID, $DataMarktplaatsID, $DataURL, $DataTitle, $DataBeschrijving, $DataDatum, $DataAdded, $DataChanged, $DataVerkoper, $DataPlaatje, $DataAfstand, $DataPrice;
	
	$db 		= $db = connect_db();	
	$sql		= "SELECT * FROM $TableData WHERE $DataMarktplaatsID = $id";	
	$result	= mysql_query($sql);
	
	if($row = mysqli_fetch_array($result)) {
 		$PageData['URL'] = $row[$DataURL];
 		$PageData['title'] = $row[$DataTitle];
 		$PageData['beschrijving'] = $row[$DataBeschrijving];
 		$PageData['verkoper'] = $row[$DataVerkoper];
 		$PageData['datum'] = $row[$DataDatum];
 		$PageData['added'] = $row[$DataAdded];
 		$PageData['changed'] = $row[$DataChanged]; 		
 		$PageData['picture'] = $row[$DataPlaatje];
 		$PageData['afstand'] = $row[$DataAfstand];
 		$PageData['prijs'] = $row[$DataPrice]; 		
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
	$result	= mysql_query($sql);
	
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

function proper_parse_str($str) {
  # result array
  $arr = array();

  # split on outer delimiter
  $pairs = explode('&', $str);

  # loop through each pair
  foreach ($pairs as $i) {
    # split into name and value
    list($name,$value) = explode('=', $i, 2);
   
    # if name already exists
    if( isset($arr[$name]) ) {
      # stick multiple values into an array
      if( is_array($arr[$name]) ) {
        $arr[$name][] = $value;
      }
      else {
        $arr[$name] = array($arr[$name], $value);
      }
    }
    # otherwise, simply stick it in a scalar
    else {
      $arr[$name] = $value;
    }
  }

  # return result array
  return $arr;
}

?>
