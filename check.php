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
include ("../general_include/shared_functions.php");
include ("../general_include/class.phpmailer.php");

include ("include/inc_config_general.php");
include ("lng/language_$Language.php");
include ("include/inc_functions.php");
$publicPage = true;
include ("include/inc_head.php");

setlocale(LC_ALL, 'nl_NL');

if(isset($_REQUEST['forcedID'])) {
 $Termen  = array($_REQUEST['forcedID']);
 $Checken = true;
} else {
 $Termen  = getZoekTermen('', date("w"), date("H"), 1);
}

$debug = 0;

if($Checken) { 
 foreach($Termen as $term) {
  # Alle advertenties inactief zetten.
  # Advertenties die in deze run gevonden worden, worden weer actief gezet
  makeAdsInactive($term);
  
  # Initialiseren van verschillende variabelen
  $teller_n = $teller_c = $teller_b = 0;
  $PlainMessage = $PlainItem = $HTMLItem = $HTMLMessage = $Subjects = array();
  $p       = 1;
  $reclame    = '';
  $extraWitregel = false;
  $nextPage    = true;
  
  # Zoek data van de huidige zoekopdracht op
  $ZoekData = getZoekData($term);
  $UserData = getUserData($ZoekData['user']);
  $rss   = $UserData['RSS'];  
  $URL    = $ZoekData['URL'];
  if($URL == '') $URL = getURL($term);  
  $URL = addPCtoURL($URL, $UserData['postcode']);
  //$URL .= $URL."&sortBy=SortIndex";
  
  # Als er een mail verstuurd moet worden, mail initialiseren
  if($rss == 0 OR $rss == 2) {
   $PlainHeader = "";  
   
   $HTMLHeader  = "<!--     Deze pagina is onderdeel van $ScriptTitle $Version gemaakt door Matthijs Draijer     -->\n\n";
   $HTMLHeader .= "<html>\n";
   $HTMLHeader .= "<head>\n";
   $HTMLHeader .= " <link rel='stylesheet' type='text/css' href='$ScriptRoot/include/style_mail.css'>\n";
   $HTMLHeader .= " <title>$ScriptTitle $Version</title>\n";
   $HTMLHeader .= "</head>\n";
   $HTMLHeader .= "<body>\n";
   $HTMLHeader .= "<center>\n";
   $HTMLHeader .= "<table width='100%' align='center' border=0>\n";
  }
  
  # Als we reclame hebben, moet dat bovenaan de mail getoond worden
  if(($rss == 0 OR $rss == 2) AND $reclame != '') {
   $HTMLItem  = "<tr>\n"; 
   $HTMLItem .= " <td colspan='2' align='center'>". showBlock($reclame) ."</td>";
   $HTMLItem .= "</tr>\n";
   
   $HTMLHeader  .= $HTMLItem;
   $PlainHeader .= $reclame;
   $extraWitregel = true;
  }
  
  # Na reclame moet er wel witregel komen
  if($extraWitregel) {
   $HTMLItem  = "<tr>\n"; 
   $HTMLItem .= " <td colspan='2' align='center'>&nbsp;</td>";
   $HTMLItem .= "</tr>\n";
   
   $HTMLHeader  .= $HTMLItem;
   $PlainHeader .= "\n\n\n";
  }
  
  # Zolang er nog volgende pagina is moet er door gegaan worden met checken.    
  while($nextPage) {    
   # Initialiseren van de hele bende
   set_time_limit(30);
   $SPAM    = false;
   $inhoud  = file_get_contents(addPage2URL($URL,$p));
     
   $eindString = $beginString = '';
   $teller_p = 0;
   
   # Alle reclame en ander gepushed meuk buitensluiten
   if(strpos($inhoud, '<table class="search-results-table">'))   $beginString = '<table class="search-results-table">';  
   if(strpos($inhoud, '<td class="ec_list_header" colspan="6">')) $eindString = '<td class="ec_list_header" colspan="6">';
   if($eindString == '' AND strpos($inhoud, '<td colspan="5" id="bottom-listings-divider">Advertenties door Admarkt</td>'))  $eindString = '<td colspan="5" id="bottom-listings-divider">Advertenties door Admarkt</td>';
   
   # Alleen de relevante advertenties inlezen
   $string = getString($beginString, $eindString, $inhoud, 0);
   
   # Array maken met losse advertenties
   $array  = explode('<article class="row search-result defaultSnippet group-', $string[0]);
   $maximum = (count($array)-1);
   
   # Bij debuggen even laten zien welke pagina wordt bekeken
   if($debug != 0) echo "<h2><a href='". addPage2URL($URL,$p) ."'>pagina $p</a></h2>\n";
   
   # Bij ontwikkelen hoeven niet alle advertenties doorlopen te worden.
   # 15 zijn voldoende
   if($debug == 2) {
    echo "URL : $URL<br>\nMaximum : $maximum<br>\n<br>\n";
    if($maximum > 15) $maximum = 15;
   }
   
   # Doorloop alle advertenties op de pagina
   for($i=1 ; $i <= $maximum ; $i++) {
    # Bij ontwikkelen de ruwe tekst laten zien
    if($debug == 2) {
     echo '<hr>';
     echo '[$i = '. $i .']<br>';
     echo htmlspecialchars($array[$i]).'<br>';
    } 
     
    # Event. reclame buiten de deur houden
    if(!strpos($array[$i], '<span class="mp-listing-priority-product">Topadvertentie</span>') AND !$SPAM) {    
     # Deze is nog geen spam, dus daarom binnen de loop
     if(strpos($array[$i], 'Advertenties door Admarkt') OR strpos($array[$i], 'Advertenties uit andere rubrieken')) {
      if($debug == 2) { echo '[SPAM gedetecteerd]<br>'; }
      $SPAM = true;     
     }
     
     # Als de ruwe tekst niet leeg is moet de tekst geprocesed worden
     if($array[$i] != "") {                        
      $changedTitle = $changedPrijs = $changedData = $newItem = false;
      
      # Haal de relevantie data uit de ruwe tekst
      $basicData = getBasicMarktplaatsData($array[$i]);
      
      # Als hij nog niet bekend is, moet er verder gezocht worden      
      if(!NewItem($basicData['key'], $term)) {
       $oldData = getPageDataByMarktplaatsID($basicData['key']);
       
       # Wijziging in titel
       if(urldecode($oldData['title']) != $basicData['title']) {
        $changedTitle = true;
        $changedData = true;
       }
       
       # Wijziging in prijs
       if($oldData['prijs'] != $basicData['price']) {
        $changedPrijs = true;
        $changedData = true;
       }
      } else {
       $newItem = true;
      }
      
      # Bij een nieuwe of gewijzigde advertenties moet de pagina van de advertentie worden ingelezen
      if($newItem OR $changedData) {
       $detailData = getAdvancedMarktplaatsData($basicData['URL']);       
       $data = array_merge($basicData, $detailData);
      } else {
       $data = $basicData;
       $data['id'] = $data['key'];
      }
      
      # Bij ontwikkelen de losse data laten zien
      if($debug == 2) {
       echo '<hr>';
       foreach($data as $key => $value) {
        echo "$key -> $value<br>\n\n";
       }
       
       if($newItem) echo "Nieuw<br>\n";
       if($changedData) echo "Gewijzigd<br>\n";
      }
      
      # Bij een nieuwe of gewijzigde advertentie tekst voor de mail opstellen
      if($newItem OR $changedData) {
       if($rss == 0 OR $rss == 2) { 
        $pictures = explode('|', $data['picture']);
        
        $adInfo = array();        
        $adInfo[] = "<b>". $data['price'] .'</b>'. ($changedPrijs ? ' <s>'. $oldData['prijs'] .'</s>' : '').($data['price_add'] != '' ? ' <small>('. $data['price_add'] .')</small>' : '');
        $adInfo[] = "<i><a href='http://verkopers.marktplaats.nl/". $data['verkoper_id'] ."'>". $data['verkoper'] ."</a></i>";
        $adInfo[] = "<a href='http://maps.google.nl/maps?q=". $data['plaats'] ."%2C+Nederland&z=9'>". $data['plaats'] ."</a> (". $data['afstand'] ." km)";
        $adInfo[] = strftime("%a %e %b %H:%M", $data['date']);        
        if(isset($data['status']))  $adInfo[] = $data['status'];
        if(isset($data['transport'])) $adInfo[] = $data['transport'];
              
        $PlainItem = strtoupper($data['title']). "\n";
        $PlainItem .= makeTextBlock($data['descr_long'], 500). "\n";
        $PlainItem .= $data['price']. "\n";
        $PlainItem .= $data['plaats']. " (".  $data['afstand'] ." km)\n";
        $PlainItem .= $data['URL_short'];                
        
        $Item = "\n";
        $Item .= '<!-- '. $data['id'] ." -->\n";
        $Item .= "<table width='100%'>\n";
        $Item .= "<tr>\n";
        $Item .= " <td align='left'><a href='". $data['URL_short'] ."'><b>". $data['title'] .'</b></a>'. ($changedTitle ? ' <s>'. urldecode($oldData['title']) .'</s>' : '') . ($changedData ? ' (gewijzigd)' : '') ."</td>\n";       
        $Item .= "</tr>\n";
        $Item .= "<tr>\n";
        $Item .= " <td align='right'>". implode(' | ', $adInfo) ."</td>\n";
        $Item .= "</tr>\n";        
        $Item .= "<tr>\n";
        $Item .= " <td>". makeTextBlock($data['descr_long'], 500) ."</td>\n";
        $Item .= "</tr>\n";
        $Item .= "<tr>\n";
        $Item .= " <td>\n";
        $Item .= " <table>\n";
        $Item .= " <tr>\n";
        
        foreach($pictures as $key => $foto) {
         // 0 -> width | 1 -> height
         $size = getimagesize('http:'.$foto);
         
         if($size[0] > $size[1]) {
          $atr = 'width="100"';
         } else {
          $atr = 'height="100"';
         }
         
         $Item .= "  <td><a href='". str_replace('_82.', '_85.', $foto) ."'><img src='http:$foto' $atr></a></td>\n";
         
         if(fmod($key, 3) == 2) {
          $Item .= " </tr>\n";
          $Item .= " <tr>\n";
         }
        }
        
        $Item .= " </tr>\n";
        $Item .= " </table>\n";
        $Item .= " </td>\n";
        $Item .= "</tr>\n";
        $Item .= "</table>\n";
                    
        $PlainMessage[] = $PlainItem;
        $HTMLMessage[] = showBlock($Item);
       }
       
       if($debug == 0) {
        if($changedPrijs OR $changedTitle) {
         $teller_c++;
        } else {
         $teller_n++;
        }
       }          
      } else {
       $teller_b++;
      }
      $teller_p++;
      
      if($debug == 1) {
       if($newItem) {
        echo "'". $data['title'] ."' is nieuw : ". strftime("%a %e %b %H:%M", $data['date']) .'<br>';
       } elseif($changedTitle) {
        echo "'". $data['title'] ."' heeft gewijzigde titel<br>";
       } elseif($changedPrijs) {
        echo "'". $data['title'] ."' heeft gewijzigde prijs<br>";
       } else {
        echo "'". $data['title'] ."' bestaat al<br>";
       }    
      }
      
      $status = array(
       'new' => $newItem,
       'title' => $changedTitle,
       'prijs' => $changedPrijs
      );
          
      AddUpdateData($data, $term, $status);
     } elseif($debug != 0) {
      echo 'Array leeg<br>';
     }   
    } elseif($debug == 2) { echo '[topadvertentie]<br>'; }
   }
   
   writeToLog($term, 'Einde pagina '. $p .' ('. $teller_p .' resultaten)');
   
   # Zolang er een link is naar een volgende pagina doorgaan
   # Om te voorkomen dat hij mogelijk eindeloos doorgaat een maximum ingebouwd van 15 pagina
   if(strpos($inhoud, 'link rel="next"') AND $p < 16) {
    $nextPage  = true;
    $p++;
   } else {
    $nextPage  = false;
   }
  }
  
  writeToLog($term, 'Gevonden resulaten : '. ($teller_c+$teller_n+$teller_b));  
     
  if($teller_n > 0 OR $teller_c > 0 OR $debug == 1) {
   if($rss == 1 OR $rss == 2) {
    makeRSSFeed($term, '');
    writeToLog($term, 'RSS-feed geupdate');
   }
   
   if($rss == 0 OR $rss == 2) {
    $PlainFooter = "\n";
    $PlainFooter .= "$strCheckCommand   : ". $ScriptRoot. "admin/edit.php?id=$term\n";
    $PlainFooter .= "$strCheckResults : ". $URL;
    $PlainFooter .= "\n\n";
    $PlainFooter .= $ScriptTitle.' '.$Version ."\n";
    $PlainFooter .= "© ". (date("Y") != 2006 ? '2006-' : ''). date("Y") ." Matthijs Draijer";
   
    $FooterText = " <table width=100%>\n";
    $FooterText .= " <tr>\n";
    $FooterText .= "  <td align='left'><a href='". $ScriptRoot ."admin/edit.php?id=$term'>$strCheckCommand</a> | <a href='$URL'>$strCheckResults</a> | <a href='". $ScriptRoot ."RSS/". $ZoekData['key'] .".xml'>RSS-feed</a></td>\n";
    $FooterText .= "  <td align='right'>© ". (date("Y") != 2006 ? '2006-' : ''). date("Y") ." <a href='mailto:Matthijs Draijer <hotscripts@draijer.org>?Subject=Opmerking over $ScriptTitle $Version'>Matthijs Draijer</a>\n";
    $FooterText .= " </tr>\n";
    $FooterText .= " </table>\n";
    
    $HTMLFooter = "<tr>\n";
    $HTMLFooter .= " <td colspan='2' align='center'>&nbsp;</td>\n";
    $HTMLFooter .= "</tr>\n";    
    $HTMLFooter .= "<tr>\n";
    $HTMLFooter .= " <td colspan='2' align='center'>". showBlock($FooterText) ."</td>\n";
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
     $mail->Subject = $SubjectPrefix . implode(' en ', $Subjects) ." $strCheckSubject '". getZoekString($term) ."'";
     
     if($ZoekData['CC'] != '') {
      $mail->AddCC($ZoekData['CC']);
     }     
     
     if($UserData['HTML'] == 1) {
      $mail->IsHTML(true);
      $mail->Body   = $HTMLMail;
      $mail->AltBody = $PlainMail;   
     } else {
      $mail->Body = $PlainMail;
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
