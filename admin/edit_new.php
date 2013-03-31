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

include ("../../general_include/general_config.php");
include ("../../general_include/general_functions.php");
include ("../include/inc_config_general.php");
include ("../lng/language_$Language.php");
include ("../include/inc_functions.php");
include ("../include/inc_head.php");
?>

<form method="GET" name="form1">
<table>
<tr>
	<td><b>Trefwoorden</b><br/>
	<input type="text" name="q" size="50" value="">&nbsp;&nbsp;&nbsp;
	<select name="or_query_words">
		<option value=0>Alle woorden</option>
		<option value=1>Een van de woorden</option>
	</select><br/>
	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top"><input type="checkbox" name="ts" value="1">Zoek in titel <b>en</b> beschrijving</td>
		<td><input type="checkbox" name="f" value="1">Alleen met foto</td>
	</tr>
	</table><br />
	<b>Zonder de trefwoorden</b><br/>
	<input type="text" name="not_query_words" size="50" value=""><br />
	<br />
	<b>Groep</b><br/>
	<select size="1" name="g">
		<option value="">Alle groepen&hellip;</option>
		<option label="Antiek, Kunst, Sieraden" value="1">Antiek, Kunst, Sieraden</option>
		<option label="Audio, Tv en Video" value="31">Audio, Tv en Video</option>
		<option label="Auto's" value="91">Auto's</option>
		<option label="Auto diversen" value="48">Auto diversen</option>
		<option label="Banen" value="167">Banen</option>
		<option label="Boeken en Tijdschriften" value="201">Boeken en Tijdschriften</option>
		<option label="Bouw en Tuin" value="239">Bouw en Tuin</option>
		<option label="Caravans en Kamperen" value="289">Caravans en Kamperen</option>
		<option label="Computer Hardware" value="322">Computer Hardware</option>
		<option label="Computer Software" value="356">Computer Software</option>
		<option label="Contacten en Berichten" value="378">Contacten en Berichten</option>
		<option label="Diensten" value="1098">Diensten</option>
		<option label="Dieren en Toebehoren" value="395">Dieren en Toebehoren</option>
		<option label="Elektronica en Witgoed" value="537">Elektronica en Witgoed</option>
		<option label="Fietsen en Accessoires" value="445">Fietsen en Accessoires</option>
		<option label="Fotografie" value="469">Fotografie</option>
		<option label="Hobby en Vrije tijd" value="1099">Hobby en Vrije tijd</option>
		<option label="Huis en Inrichting" value="504">Huis en Inrichting</option>
		<option label="Kinderen en Baby's" value="565">Kinderen en Baby's</option>
		<option label="Kleding en Schoenen" value="621">Kleding en Schoenen</option>
		<option label="Motoren en Brommers" value="678">Motoren en Brommers</option>
		<option label="Muziek en Instrumenten" value="728">Muziek en Instrumenten</option>
		<option label="Sport en Fitness" value="784">Sport en Fitness</option>
		<option label="Telecommunicatie" value="820">Telecommunicatie</option>
		<option label="Vakantie en Toerisme" value="856">Vakantie en Toerisme</option>
		<option label="Verzamelen" value="895">Verzamelen</option>
		<option label="Watersport en Boten" value="976">Watersport en Boten</option>
		<option label="Woningen | Huur" value="999">Woningen | Huur</option>
		<option label="Woningen | Koop" value="1032">Woningen | Koop</option>
		<option label="Zakelijke goederen" value="1085">Zakelijke goederen</option>
		<option label="Diversen" value="428">Diversen</option>
	</select><br/>
	<br/>
	<b>Rubriek</b><br/>
	<select size="1" name="u">
		<option value="">Alle rubrieken&hellip;</option>
	</select><br/>
	<br/>
	<br /></td>
</tr>
</table>
<table class="main_table">
<tr>
	<td><b>Prijs</b><br />
	€&nbsp;<input type="text" name="pmin" maxlength=20" size="10"> tot <input type="text" name="pmax" maxlength="20" size="10"><input type="checkbox" name="np" value="1">Ook zonder prijs<br />
	<select name="price_type" size="1">
		<option value="0">Kies een prijsstelling... &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
		<option label="Bieden" value="1">Bieden</option>
		<option label="N.o.t.k" value="2">N.o.t.k</option>
		<option label="N.v.t." value="3">N.v.t.</option>
		<option label="T.e.a.b." value="4">T.e.a.b.</option>
		<option label="Ruilen" value="5">Ruilen</option>
		<option label="Gratis" value="6">Gratis</option>
		<option label="Zie omschrijving" value="8">Zie omschrijving</option>
		<option label="Op aanvraag" value="9">Op aanvraag</option>
		<option label="Gereserveerd" value="7">Gereserveerd</option>
	</select>&nbsp;&nbsp;<br />
  </td>
</tr>
<tr>
	<td><b>Zoekgebied</b><br/>
	<input type="radio" name="loc_type" checked value="zip">Binnen <input type="text" size="3" maxlength="3" name="distance_temp"> km van postcode <input type="text" size="11" maxlength="6" name="postcode" value="7545"><br />
	<input type="radio" name="loc_type" value="province">
	<select size="1" name="pv">
		<option value="">Kies een provincie&hellip;</option>
    <option label="Drenthe" value="2">Drenthe</option>
		<option label="Flevoland" value="3">Flevoland</option>
		<option label="Friesland" value="4">Friesland</option>
		<option label="Gelderland" value="5">Gelderland</option>
		<option label="Groningen" value="6">Groningen</option>
		<option label="Limburg" value="7">Limburg</option>
		<option label="Noord-Brabant" value="8">Noord-Brabant</option>
		<option label="Noord-Holland" value="9">Noord-Holland</option>
		<option label="Overijssel" value="10">Overijssel</option>
		<option label="Utrecht" value="11">Utrecht</option>
		<option label="Zeeland" value="12">Zeeland</option>
		<option label="Zuid-Holland" value="13">Zuid-Holland</option>
		<option label="Buitenland" value="15">Buitenland</option>
	</select><br />
	</td>
</tr>
</table>
<table class="main_table">
<tr>
	 <td><input type="submit" value="Zoek" name="submit1" /></td>
</tr>
</table>
</form>
 
<?
include ('../include/inc_footer.php');
?>