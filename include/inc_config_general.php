<?php

$dbTablePrefix	= "marktplaats_";

$ScriptMailAdress = "";
$SubjectPrefix = "[marktplaats] ";

$Language = "nl";
$OudeAdvTijd = 8*24*60*60;
$OudeLogsTijd = 31*24*60*60;
$CookieTime = 604800;
$Checken = true;
$ScriptDir = "";
$ScriptRoot = "". $ScriptDir;

$dirname = dirname(__FILE__);
include_once($dirname.'/inc_config_tables.php');

# Thuis locatie
$HomeLong = 52;
$HomeLat = 6;

# API GoogleMaps
$API	= "";

?>