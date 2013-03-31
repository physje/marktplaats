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

include ("include/inc_config_general.php");
include ("lng/language_$Language.php");
include ("include/inc_functions.php");
$publicPage = true;
include ("include/inc_head.php");
require ('include/class.phpmailer.php');

$Termen = getZoekTermen('', '', 1);

$tijd 	= time() - $OudeAdvTijd;

echo 'Het is nu '. date("d-m H:i") .' | '. date("d-m H:i", $tijd) .'<br>';

foreach($Termen as $term) {
	if($debug == 0) {
		// Oude advertenties verwijderen
		$OldPages	= getAds($term, true);
				
		foreach($OldPages as $id) {		
			$data = getPageDataByMarktplaatsID($id);
			
			echo $id .' -> '. $data['title'] .' | '. date("d-m H:i", $data['changed']) .'<br>';
			//deletePage($term, $id);
		}
	}
}

include ('include/inc_footer.php');
?>