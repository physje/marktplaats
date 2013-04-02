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
include ("../../general_include/general_functions.php");
include ("../include/inc_config_general.php");
include ("../lng/language_$Language.php");
include ("../include/inc_functions.php");
$minAcces = 2;
include ("../include/inc_head.php");

echo '<table border=1>';
echo "<td>&nbsp;</td>\n";

for($d=0 ; $d < 7 ; $d++) {
	echo "<td>$d</td>\n";	
}

for($h=0 ; $h < 24 ; $h++) {
	$temp = "<tr>\n<td>". $h ."</td>\n";
	
	for($d=0 ; $d < 7 ; $d++) {
		$Termen		= getZoekTermen('', $d, $h, 1);
		$HTML			= '';
		
		foreach($Termen as $term) {
			$String		= getZoekString($term);
			//$HTML[] = "<a href='edit.php?id=$term'>$String</a>";
			$HTML[] = "<a href='edit.php?id=$term' title='$String'>$term</a>";			
		}
		
		if(is_array($HTML)) {
			$temp .= '<td>'. implode(', ', $HTML) ."</td>\n";
		} else {
			$temp .= "<td>&nbsp;</td>\n";
		}
	}
	$temp .= "</tr>\n";
	
	echo $temp;
}

echo '</table>';

echo "<p>";
echo "<a href='index.php'><img src='../images/1_home.png' border='0'></a>";

include ('../include/inc_footer.php');
?>