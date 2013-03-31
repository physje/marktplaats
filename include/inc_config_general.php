<?php

$dbTablePrefix	= "marktplaats_";

$ScriptMailAdress = "marktplaats@draijer.org";
$SubjectPrefix = "[marktplaats] ";

$Language = "nl";
$OudeAdvTijd = 8*24*60*60;
$OudeLogsTijd = 31*24*60*60;
$CookieTime = 604800;
$Checken = true;
$ScriptDir = "/extern/marktplaats/";
$ScriptRoot = "http://www.draijer.org". $ScriptDir;

$dirname = dirname(__FILE__);
include_once($dirname.'/inc_config_tables.php');

?>