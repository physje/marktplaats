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

include ("../general_include/general_config.php");
include ("../general_include/general_functions.php");
include ("../general_include/class.phpmailer.php");

include ("include/inc_config_general.php");
include ("lng/language_$Language.php");
include ("include/inc_functions.php");
$publicPage = true;
include ("include/inc_head.php");

setlocale(LC_ALL, 'nl_NL');

if(isset($_REQUEST['forcedID'])) {
	$Termen		= array($_REQUEST['forcedID']);
	$Checken	= true;
} else {
	$Termen		= getZoekTermen('', date("w"), date("H"), 1);
}

$debug = 0;

if($Checken) {	
	foreach($Termen as $term) {
		makeAdsInactive($term);
		
		$teller_n = $teller_c = 0;
		$PlainMessage = $PlainItem = $HTMLItem = $HTMLMessage = $Subjects = array();
		$ZoekData	= getZoekData($term);
		$UserData = getUserData($ZoekData['user']);
		$rss			= $UserData['RSS'];		
		$URL				= $ZoekData['URL'];
		if($URL == '') {
			$URL = getURL($term);
		}
		
		$URL = addPCtoURL($URL, $UserData['postcode']);
				
		$inhoud		= file_get_contents($URL);
		
		//echo "RSS : $rss|";
		
		if(strpos($inhoud, '<table class="search-results-table">')) {
			$beginString = '<table class="search-results-table">';
		} else {
			$beginString = '';
		}
		
		if(strpos($inhoud, '<td class="ec_list_header" colspan="6">')) {
			$eindString = '<td class="ec_list_header" colspan="6">';
		} else {
			$eindString = '';
		}
		
		if($eindString == '' AND strpos($inhoud, '<td colspan="5" id="bottom-listings-divider">Advertenties door Admarkt</td>')) {
			$eindString = '<td colspan="5" id="bottom-listings-divider">Advertenties door Admarkt</td>';
		}
		
		$string	= getString($beginString, $eindString, $inhoud, 0);
		
		$array		= explode('<tr class="search-result defaultSnippet group-', $string[0]);
		$aantal		= count($array);
			
		$teller_n		= 0;
		$teller_c		= 0;
		$SPAM				= false;
		
		if($rss == 0 OR $rss == 2) {
			$PlainHeader = "";		
			
			$HTMLHeader	 = "<!--     Deze pagina is onderdeel van $ScriptTitle $Version gemaakt door Matthijs Draijer     -->\n\n";
			$HTMLHeader	.= "<html>\n";
			$HTMLHeader	.= "<head>\n";
			$HTMLHeader	.= "	<link rel='stylesheet' type='text/css' href='$ScriptRoot/include/style_mail.css'>\n";
			$HTMLHeader	.= "	<title>$ScriptTitle $Version</title>\n";
			$HTMLHeader	.= "</head>\n";
			$HTMLHeader	.= "<body>\n";
			$HTMLHeader	.= "<center>\n";
			$HTMLHeader	.= "<table width='100%' align='center' border=0>\n";
		}
		
		//$reclame = "Door een bug in het script is marktplaats.nl enige tijd niet gecheckt.... probleem is nu verholpen.";
		//$reclame = "De layout van marktplaats.nl is gewijzigd, het script en de zoektermen moesten daarvoor op de schop.<br>Controleer daarom of de zoekopdracht nog de juiste resultaten geeft en pas hem zonodig aan.";
		$extraWitregel = false;
		
		if(($rss == 0 OR $rss == 2) AND $reclame != '') {
			$HTMLItem  = "<tr>\n";	
			$HTMLItem .= "	<td colspan='2' align='center'>". showBlock($reclame) ."</td>";
			$HTMLItem .= "</tr>\n";
			
			$HTMLHeader		.= $HTMLItem;
			$PlainHeader	.= $reclame;
			$extraWitregel = true;
		}
		
		if($aantal > 101) {
			if($rss == 0 OR $rss == 2) {
				$HTMLItem  = "<tr>\n";	
				$HTMLItem .= "	<td colspan='2' align='center'>". showBlock($strCheckOverflow) ."</td>";
				$HTMLItem .= "</tr>\n";
			
				$HTMLHeader		.= $HTMLItem;
				$PlainHeader	.= $strCheckOverflow;				
				$extraWitregel = true;
			}
			
			if($debug == 0) {
				writeToLog($term, $strLogOverflow);
			}
			$maximum = 100;
		} else {
			$maximum = $aantal;
		}
		
		if($extraWitregel) {
			$HTMLItem  = "<tr>\n";	
			$HTMLItem .= "	<td colspan='2' align='center'>&nbsp;</td>";
			$HTMLItem .= "</tr>\n";
			
			$HTMLHeader		.= $HTMLItem;
			$PlainHeader	.= "\n\n\n";
		}
		
		if($debug == 2) {
			echo "URL : $URL<br>\nAantal : $aantal<br>\nMaximum : $maximum<br>\n<br>\n";
			
			if($maximum > 15) {
				$maximum = 15;
			}
		}
		
		for($i=1 ; $i <= $maximum ; $i++) {			
			# Event. reclame buiten de deur houden
			if(!strpos($array[$i], '<span class="mp-listing-priority-product">Topadvertentie</span>') AND !$SPAM) {							
				# Deze is nog geen spam, dus daarom binnen de loop
				if(strpos($array[$i], 'Advertenties door Admarkt') OR strpos($array[$i], 'Advertenties uit andere rubrieken')) {
					$SPAM = true;					
				}
				
				if($array[$i] != "") {                        
					$data			= getMarktplaatsData_v3($array[$i]);
					
					if($debug == 2) {
						echo '<hr>';
						foreach($data as $key => $value) {
							echo "$key -> $value<br>\n\n";
						}
					}
								
					if(NewItem($data, $term) OR changedItem($data, $term)) {
						if($rss == 0 OR $rss == 2) {
							$pictures = explode('|', $data['picture']);
													
							$PlainItem = strtoupper($data['title']). "\n";
							$PlainItem .= makeTextBlock($data['descr_long'], 500). "\n";
							$PlainItem .= $data['price']. "\n";
							$PlainItem .= $data['plaats']. " (".  $data['afstand'] ." km)\n";
							$PlainItem .= $data['URL'];											  			
							
							$Item = "<table width='100%'>\n";
							$Item .= "<tr>\n";
							$Item .= "	<td><a href='". $data['URL'] ."'><b>". $data['title'] ."</b></a>". (changedItem($data, $term) ? ' (gewijzigd)' : '') ."</td>\n";
							$Item .= "</tr>\n";
							$Item .= "<tr>\n";
							$Item .= "	<td align='right'><b>". $data['price'] ."</b>". ($data['price_add'] != '' ? ' <small>('. $data['price_add'] .')</small>' : '') ." | <i><a href='http://verkopers.marktplaats.nl/". $data['verkoper_id'] ."'>". $data['verkoper'] ."</a></i> | <a href='http://maps.google.nl/maps?q=". $data['plaats'] ."%2C+Nederland&z=9'>". $data['plaats'] ."</a> (". $data['afstand'] ." km) | ". strftime("%a %e %b %H:%M", $data['date']) ."</td>\n";
							$Item .= "</tr>\n";
							$Item .= "<tr>\n";
							$Item .= "	<td>". makeTextBlock($data['descr_long'], 500) ."</td>\n";
							$Item .= "</tr>\n";
							$Item .= "<tr>\n";
							$Item .= "	<td>\n";
							$Item .= "	<table>\n";
							$Item .= "	<tr>\n";
							
							foreach($pictures as $key => $foto) {
								// 0 -> width | 1 -> height
								$size = getimagesize('http:'.$foto);
								
								if($size[0] > $size[1]) {
									$atr = 'width="100"';
								} else {
									$atr = 'height="100"';
								}
								
								$Item .= "		<td><img src='http:$foto' $atr></td>\n";
								
								if(fmod($key, 3) == 2) {
									$Item .= "	</tr>\n";
									$Item .= "	<tr>\n";
								}
							}
							
							$Item .= "	</tr>\n";
							$Item .= "	</table>\n";
							$Item .= "	</td>\n";
							$Item .= "</tr>\n";
							$Item .= "</table>\n";
            							
							$PlainMessage[]	= $PlainItem;
							$HTMLMessage[]	= showBlock($Item);
						}
						
						if($debug == 0) {
							if(changedItem($data, $term)) {
								$teller_c++;
							} else {
								$teller_n++;
							}
						}										
					}
					
					if($debug == 1) {
						if(NewItem($data, $term)) {
							echo $data['title'] .' is nieuw : '. strftime("%a %e %b %H:%M", $data['date']) .'<br>';
						} elseif(changedItem($data, $term)){
							echo $data['title'] .' is gewijzigd<br>';
						} else {
							echo $data['title'] .' bestaat al<br>';
						}				
					}
					
					AddUpdateData($data, $term);
				}				
			}
		}
				
		if($teller_n > 0 OR $teller_c > 0 OR $debug == 1) {
			if($rss == 1 OR $rss == 2) {
				makeRSSFeed($term, '');
				writeToLog($term, 'RSS-feed geupdate');
			}
			
			if($rss == 0 OR $rss == 2) {
				$PlainFooter = "\n";
				$PlainFooter .= "$strCheckCommand   : ". $ScriptRoot. "admin/edit.php?id=$term\n";
				$PlainFooter .= "$strCheckResults : ". $url;
				$PlainFooter .= "\n\n";
				$PlainFooter .= $ScriptTitle.' '.$Version ."\n";
				$PlainFooter .= "© ". (date("Y") != 2006 ? '2006-' : ''). date("Y") ." Matthijs Draijer";
			
				$FooterText = "	<table width=100%>\n";
				$FooterText .= "	<tr>\n";
				$FooterText .= "		<td align='left'><a href='". $ScriptRoot ."admin/edit.php?id=$term'>$strCheckCommand</a> | <a href='$URL'>$strCheckResults</a> | <a href='". $ScriptRoot ."RSS/". $ZoekData['key'] .".xml'>RSS-feed</a></td>\n";
				$FooterText .= "		<td align='right'>© ". (date("Y") != 2006 ? '2006-' : ''). date("Y") ." <a href='mailto:Matthijs Draijer <hotscripts@draijer.org>?Subject=Opmerking over $ScriptTitle $Version'>Matthijs Draijer</a>\n";
				$FooterText .= "	</tr>\n";
				$FooterText .= "	</table>\n";
				
				$HTMLFooter = "<tr>\n";
				$HTMLFooter .= "	<td colspan='2' align='center'>&nbsp;</td>\n";
				$HTMLFooter .= "</tr>\n";				
				$HTMLFooter .= "<tr>\n";
				$HTMLFooter .= "	<td colspan='2' align='center'>". showBlock($FooterText) ."</td>\n";
				$HTMLFooter .= "</tr>\n";
				$HTMLFooter .= "</table>\n";			
				$HTMLFooter .= "</body>\n";
				$HTMLFooter .= "</html>\n";
				$HTMLFooter .= "\n\n<!--     Deze pagina is onderdeel van $ScriptTitle $Version gemaakt door Matthijs Draijer     -->";
      	
      	$PlainMail  = $PlainHeader . implode("\n\n--- --- --- --- ---\n\n", $PlainMessage) . $PlainFooter;
      	
      	$omslag = round(count($HTMLMessage)/2);
      	$KolomEen = array_slice ($HTMLMessage, 0, $omslag);
      	$KolomTwee = array_slice ($HTMLMessage, $omslag, $omslag);
      	     					
				$HTMLMail = $HTMLHeader;
				$HTMLMail .= "<tr>\n";
				$HTMLMail .= "<td width='50%' valign='top' align='center'>\n";
				$HTMLMail .= implode("\n<p>\n", $KolomEen);
				$HTMLMail .= "</td><td width='50%' valign='top' align='center'>\n";
				if(count($KolomTwee) > 0) {
					$HTMLMail .= implode("\n<p>\n", $KolomTwee);	
				} else {
					$HTMLMail .= "&nbsp;";	
				}
				$HTMLMail .= "</td>\n";
				$HTMLMail .= "</tr>\n";
				$HTMLMail .= $HTMLFooter;
				
				if($debug == 0) {					
					if($teller_n != 0) {
						$Subjects[] = $teller_n . " ". ($teller_n == 1 ? $strCheckItemNew : $strCheckItemsNew);
					}
					if($teller_c != 0) {
						$Subjects[] = $teller_c . " ". ($teller_c == 1 ? $strCheckItemChanged : $strCheckItemsChanged);						
					}
					
					$mail = new PHPMailer;
					$mail->From     = $ScriptMailAdress;
					$mail->FromName = $ScriptTitle;
					$mail->WordWrap = 90;
					$mail->AddAddress($UserData['mailadres'], $UserData['naam']);
					$mail->Subject	= $SubjectPrefix . implode(' en ', $Subjects) ." $strCheckSubject '". getZoekString($term) ."'";
					
					if($ZoekData['CC'] != '') {
						$mail->AddCC($ZoekData['CC']);
					}					
					
					if($UserData['HTML'] == 1) {
						$mail->IsHTML(true);
						$mail->Body			= $HTMLMail;
						$mail->AltBody	= $PlainMail;			
					} else {
						$mail->Body	= $PlainMail;
					}
    
					if(!$mail->Send()) {
						echo date("H:i") . " : $strMailError '". getZoekString($term) ."' ($term).<br>";
						writeToLog($term, $strLogMailError . ' ('. $UserData['naam'] .')');
					} else {
						echo date("H:i") . " : $strMailOkay '". getZoekString($term) ."' ($term).<br>";
						writeToLog($term, $strLogMailOkay . ' ('. $UserData['naam'] .')');
					}
				} else {
					echo $HTMLMail;
				}
			}				
		} else {
			if($debug == 0) {
				echo date("H:i") . " : $strCheckOkay '". getZoekString($term) ."' ($term).<br>";
				writeToLog($term, $strLogChecked);
			}
		}
	}
} else {
	echo $strCheckNoCheck;
}

include ('include/inc_footer.php');
?>