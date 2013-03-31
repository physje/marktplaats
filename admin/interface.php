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

$publicPage = true;
include ("../include/inc_head.php");

echo "<form name='loginform' action='$_SERVER[PHP_SELF]' METHOD='post'>\n";
echo "<input type='hidden' name=\"interface\" value=\"Ja\">\n";
echo "<table align='center'>\n";
echo "<tr>\n";
echo "	<td colspan='2'>&nbsp;</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "	<td colspan='2' align='center'><h2>$strLogonScreen</h2></td>\n";
echo "</tr>\n";

if ($message) {
	echo "<tr>\n<td colspan=\"2\" align=\"center\"><b><i><font color=red>". $message ."</font></b></i></td>\n</tr>\n";
} else {
	echo "<tr>\n<td colspan=\"2\">&nbsp;</td>\n</tr>\n";
}

echo "<tr>\n";
echo "	<td colspan=\"2\">$strAccountName:<br>\n";
echo "  <center><input type=\"text\" name=\"loginname\" TABINDEX=\"1\" value='$quickName'></center>\n";
echo "  $strAccountPW<br>\n";
echo "  <center><input type=\"password\" name=\"wachtwoord\" TABINDEX=\"1\" value='$quickPassword'>\n";
echo "  </td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "	<td height='30' width='50%' valign='bottom' align='center'><INPUT TYPE=image src='../images/enter.gif' TABINDEX='1' border='0' alt='Login'></td>\n";
echo "	<td height='30' width='50%' valign='bottom' align='center'><A HREF='index.php' TABINDEX='2'><IMG SRC='../images/cancel.gif' BORDER='0' alt='Cancel'></A></td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</form>\n";

include ('../include/inc_footer.php');
?>