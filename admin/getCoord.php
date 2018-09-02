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

$sql = "SELECT $DataPlaats FROM $TableData WHERE $DataPlaats NOT IN (SELECT $CoordPlaats FROM $TableCoord) LIMIT 0,1";
$result = mysqli_query($db, $sql);

if($row = mysqli_fetch_array($result)) {
	do {
		$plaats = $row[$DataPlaats];
		
		$coord = getCoordinates('', '', $plaats);
		//array($latitude[0], $latitude[1], $longitude[0], $longitude[1], $location_type[0]);
		
		echo $plaats .' -> '. $coord[4];
		
	} while($row = mysqli_fetch_array($result));
}

include ('../include/inc_footer.php');
?>