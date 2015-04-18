<?php
include ("../../general_include/general_config.php");
include ("../../general_include/shared_functions.php");
include ("../../general_include/class.phpmailer.php");
include ("../../general_include/class.html2text.php");

include ("../include/inc_config_general.php");
include ("../lng/language_$Language.php");
include ("../include/inc_functions.php");
include ("../include/inc_head.php");

if(isset($_POST['opvragen'])) {
	$db = connect_db();
	$invoer	= $_POST['invoer'];
	$sql		= "SELECT * FROM $TableUsers WHERE $UsersNaam like '$invoer' OR $UsersMail like '$invoer'";
	$result = mysqli_query($db, $sql);
	
	if(mysqli_num_rows($result) == 0) {
		$text[] = "Er is helaas niks gevonden met '$invoer'";
	} else {
		$row	= mysqli_fetch_array($result);
		$id		= $row[$UsersID];
		$nieuwPassword = generatePassword(12);
		
		$sql		= "UPDATE $TableUsers SET $UsersWachtwoord = '". md5($nieuwPassword) ."' WHERE $UsersID = $id";
		mysqli_query($db,$sql);
		
		$data = getUserData($id);
		
		$Mail[] = "Beste ". $data['naam'] .",<br>";
		$Mail[] = "<br>";
		$Mail[] = "je hebt een nieuw wachtwoord aangevraagd voor $ScriptTitle $Version.<br>";
		$Mail[] = "Je kan inloggen met :<br>";
		$Mail[] = "<br>";
		$Mail[] = "Loginnaam : ". $data['naam'] ."<br>";
		$Mail[] = "Wachtwoord : ". $nieuwPassword ."<br>";
		$Mail[] = "<br>";
		$Mail[] = "Met deze gegevens kan je via <a href='". $ScriptRoot ."admin/account.php'>". $ScriptRoot ."admin/account.php</a> je eigen wachtwoord instellen<br>";	
		$HTMLMail = implode("\n", $Mail);
		
		$html =& new html2text($HTMLMail);
		$html->set_base_url($ScriptURL);
		$PlainText = $html->get_text();
		
		$mail = new PHPMailer;
		$mail->AddAddress($data['mailadres'], $data['naam']);
		$mail->From     = $ScriptMailAdress;
		$mail->FromName = $ScriptTitle;
		$mail->Subject	= $SubjectPrefix ."Nieuw wachtwoord voor $ScriptTitle";
		$mail->IsHTML(true);
		$mail->Body			= $HTMLMail;
		$mail->AltBody	= $PlainText;
		
		if(!$mail->Send()) {
			$text[] = "Inloggegevens konden helaas niet verstuurd worden";
			
			echo $HTMLMail;
			
		} else {			
			$text[] = "Inloggegevens zijn verstuurd";
		}		
	}	
} else {
	$text[] = "<form action='$_SERVER[PHP_SELF]' method='post'>\n";
	$text[] = "<table>";
	$text[] = "<tr>";
	$text[] = "	<td>Voer uw loginnaam of email-adres in. Het systeem zal dan een nieuw wachtwoord mailen.</td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td><input type='text' name='invoer' size='75'></td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td>&nbsp;</td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td align='center'><input type='submit' name='opvragen' value='Opvragen'></td>";
	$text[] = "</tr>";
	$text[] = "</table>";
	$text[] = "</form>";
}

echo $HTMLHeader;
echo "<tr>\n";
echo "<td width='25%'>&nbsp;</td>\n";
echo "<td width='50%' valign='top' align='center'>\n";
echo showBlock(implode("\n", $text));
echo "</td>\n";
echo "<td width='25%' valign='top' align='center'>\n";
echo "</tr>\n";
echo $HTMLFooter;


?>