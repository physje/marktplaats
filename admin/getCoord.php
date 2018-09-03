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
include ("../include/inc_head.php");

$db = connect_db();

$sql = "SELECT * FROM $TableData WHERE $DataPlaats NOT LIKE '' AND $DataPlaats NOT IN (SELECT $CoordPlaats FROM $TableCoord) LIMIT 0,1";
$result = mysqli_query($db, $sql);

if($row = mysqli_fetch_array($result)) {
	do {
		$plaats = urldecode($row[$DataPlaats]);
		$coord = getCoordinates('', '', $plaats);
		
		echo $plaats .' -> ';
		
		if($coord[0] > 0) {
		    $sql_INSERT = "INSERT INTO $TableCoord ($CoordPlaats, $CoordLongitude, $CoordLatitude) VALUES ('". urlencode($plaats) ."', '". $coord[0].'.'.$coord[1] ."', '". $coord[2].'.'.$coord[3] ."')";
		    mysqli_query($db, $sql_INSERT);
		    
		    echo 'toegevoegd';
		} else {
		  echo 'foutieve coordinaten';  
		}
		
		//. $coord[0].'.'.$coord[1] .'|'. $coord[2].'.'.$coord[3];
		
	} while($row = mysqli_fetch_array($result));
}

include ('../include/inc_footer.php');
?>