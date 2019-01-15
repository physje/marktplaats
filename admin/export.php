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

    if(isset($_POST['gaan'])) {
        $TermData = getZoekData($term);
        $ads = getAds($term, false);
        
        foreach($ads as $advertentie) {
        	$data = getPageDataByMarktplaatsID($advertentie);
        	$veld = array();
        	
        	if($_POST['koppen']['db-id'] == 1)      $veld[] = $data['ID'];
        	if($_POST['koppen']['m-id'] == 1)       $veld[] = $data['mID'];
        	if($_POST['koppen']['url'] == 1)        $veld[] = 'https://link.marktplaats.nl/m'.$data['mID'];
        	if($_POST['koppen']['active'] == 1)     $veld[] = $data['active'];
        	if($_POST['koppen']['title'] == 1)      $veld[] = urldecode($data['title']);
        	if($_POST['koppen']['descr'] == 1)      $veld[] = urldecode($data['beschrijving']);
        	if($_POST['koppen']['sale'] == 1)       $veld[] = $data['verkoper'];
        	if($_POST['koppen']['online'] == 1)     $veld[] = date('d-m-Y H:i:s', $data['datum']);
        	if($_POST['koppen']['add'] == 1)        $veld[] = date('d-m-Y H:i:s', $data['added']);
        	if($_POST['koppen']['change'] == 1)     $veld[] = date('d-m-Y H:i:s', $data['changed']);
        	if($_POST['koppen']['picture'] == 1)    $veld[] = $data['picture'];
        	if($_POST['koppen']['distance'] == 1)   $veld[] = $data['afstand'];
        	if($_POST['koppen']['plaats'] == 1)     $veld[] = $data['plaats'];
        	if($_POST['koppen']['not_seen'] == 1)   $veld[] = $data['niet_gezien'];
        	if($_POST['koppen']['price'] == 1)      $veld[] = trim(str_replace('&euro;', '', $data['prijs']));
        	if($_POST['koppen']['title_o'] == 1)    $veld[] = $data['title_o'];
        	if($_POST['koppen']['price_o'] == 1)    $veld[] = trim(str_replace('&euro;', '', $data['prijs_o']));
        	
        	$rij[] = implode(";", $veld);
        }

        if($_POST['koppen']['db-id'] == 1)      $kop[] = 'ID';
        if($_POST['koppen']['m-id'] == 1)       $kop[] = 'MarktplaatsID';
        if($_POST['koppen']['url'] == 1)        $kop[] = 'URL';
        if($_POST['koppen']['active'] == 1)     $kop[] = 'Actief';
        if($_POST['koppen']['title'] == 1)      $kop[] = 'Titel';
        if($_POST['koppen']['descr'] == 1)      $kop[] = 'Beschrijving';
        if($_POST['koppen']['sale'] == 1)       $kop[] = 'Verkoper';
        if($_POST['koppen']['online'] == 1)     $kop[] = 'Datum';
        if($_POST['koppen']['add'] == 1)        $kop[] = 'Added';
        if($_POST['koppen']['change'] == 1)     $kop[] = 'Changed';
        if($_POST['koppen']['picture'] == 1)    $kop[] = 'Picture';
        if($_POST['koppen']['distance'] == 1)   $kop[] = 'Afstand';
        if($_POST['koppen']['plaats'] == 1)     $kop[] = 'Plaats';
        if($_POST['koppen']['not_seen'] == 1)   $kop[] = '#Niet gezien';
        if($_POST['koppen']['price'] == 1)      $kop[] = 'Prijs';
        if($_POST['koppen']['title_o'] == 1)    $kop[] = 'Title (oorspronkelijk)';
        if($_POST['koppen']['price_o'] == 1)    $kop[] = 'Prijs (oorspronkelijk)';
                  
        $file_name = $TermData['naam'].'.csv';
        $output = implode(";", $kop) ."\n";
        $output .= implode("\n", $rij);
              
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$file_name.'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length:'.strlen($output));
        echo $output;
    } else {
        echo "<html>\n";
        echo "<body>\n";
        echo "<form method='post'>\n";
        echo "<input type='hidden' name='term' value='$term'>\n";
        echo "<input type='checkbox' name='koppen[db-id]' value='1'> Database-id<br>\n";
        echo "<input type='checkbox' name='koppen[m-id]' value='1'> Marktplaats-id<br>\n";
        echo "<input type='checkbox' name='koppen[url]' value='1'> URL<br>\n";
        echo "<input type='checkbox' name='koppen[active]' value='1'> Actief/inactief<br>\n";
        echo "<input type='checkbox' name='koppen[title]' value='1'> Titel<br>\n";
        echo "<input type='checkbox' name='koppen[title_o]' value='1'> Titel (oorspronkelijk)<br>\n";
        echo "<input type='checkbox' name='koppen[descr]' value='1'> Beschrijving<br>\n";
        echo "<input type='checkbox' name='koppen[sale]' value='1'> Verkoper<br>\n";
        echo "<input type='checkbox' name='koppen[online]' value='1'> Tijdstip online<br>\n";
        echo "<input type='checkbox' name='koppen[add]' value='1'> Tijdstip toegevoegd<br>\n";
        echo "<input type='checkbox' name='koppen[change]' value='1'> Tijdstip gewijzigd<br>\n";
        echo "<input type='checkbox' name='koppen[picture]' value='1'> Foto<br>\n";
        echo "<input type='checkbox' name='koppen[distance]' value='1'> Afstand<br>\n";
        echo "<input type='checkbox' name='koppen[plaats]' value='1'> Plaats<br>\n";
        echo "<input type='checkbox' name='koppen[not_seen]' value='1'> #_niet gezien<br>\n";
        echo "<input type='checkbox' name='koppen[price]' value='1'> Prijs<br>\n";
        echo "<input type='checkbox' name='koppen[price_o]' value='1'> Prijs (oorspronkelijk)<br>\n";
        
        echo "<input type='submit' name='gaan' value='Exporteren'>\n";
        echo "</form>\n";
        echo "</body>\n";
        echo "</html>\n";
    }
} else {
	exit;
}


?>
