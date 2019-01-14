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

include ("../../general_include/general_config.php");
include ("../../general_include/shared_functions.php");
include ("../include/inc_config_general.php");
include ("../lng/language_$Language.php");
include ("../include/inc_functions.php");
//include ("../include/inc_head.php");
$db = connect_db();

if(isset($_REQUEST['term'])) {
	$term = $_REQUEST['term'];
} else {
	exit;
}

$TermData = getZoekData($term);
$ads = getAds($term, false);

foreach($ads as $advertentie) {
	$data = getPageDataByMarktplaatsID($advertentie);
	$rij[] = implode(';', $data);
}

$kop[] = 'ID';
$kop[] = 'marktplaatsID';
$kop[] = 'URL';
$kop[] = 'actief';
$kop[] = 'title';
$kop[] = 'beschrijving';
$kop[] = 'verkoper';
$kop[] = 'datum';
$kop[] = 'added';
$kop[] = 'changed';
$kop[] = 'picture';
$kop[] = 'afstand';
$kop[] = 'prijs';
                  
$file_name = $TermData['naam'].'.csv';
$output = implode(';', $kop) ."\n";
$output .= implode("\n", $rij);
                  
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.$file_name.'"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length:'.strlen($output));
echo $output;
exit;


?>
