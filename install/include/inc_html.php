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

require_once('inc_view.php');

function htmlStartPage($title, $currentPage, $pageNumber) {
	$class = ($currentPage == $pageNumber) ? 'TabActive' : 'Tab';
	
	return "\n<div class=\"".$class."\">\n<h2>".$title."</h2>\n";
}

function htmlEndPage() {
	return "\n</div>\n";
}

function htmlSelect($name, $selectedKey, $keys, $values) {
	$result = '<select name="'.$name.'">';
	for ($i = 0; $i < count($keys); $i++) {
		$sel = ($keys[$i] == $selectedKey) ? 'selected="true"' : '';
		$result .= '<option value="'.$keys[$i].'" '.$sel.'>'.$values[$i].'</option>';
	}
	$result .= "</select>\n";
	
	return $result;
}

function htmlNameValueRow($name) {
	global $view;
	
	$viewData = isset($view[$name]) ? $view[$name] : '';
	
	$result = '<tr>';
	$result .= '<td class="Key">'.lang($name).':</td>';
	$result .= '<td class="Value"><input type="text" name="'.$name.'" value="'.$viewData.'" title="'.lang($name.'_ToolTip').'"></td>';
	$result .= showError($name);
	$result .= "</tr>\n";
	
	return $result;
}

function htmlNameValueSelectRow($name, $keys, $values) {
	global $view;
	
	$viewData = isset($view[$name]) ? $view[$name] : '';
	
	$result = '<tr>';
	$result .= '<td class="Key">'.lang($name).':</td>';
	$result .= '<td class="Value"><select name="'.$name.'" title="'.lang($name.'_ToolTip').'">';
	for ($i = 0; $i < count($keys); $i++) {
		$sel = ($keys[$i] == $view[$name]) ? 'selected="true"' : '';
		$result .= '<option value="'.$keys[$i].'" '.$sel.'>'.$values[$i].'</option>';
	}
	$result .= "</select></td>\n";
	$result .= showError($name);
	$result .= "</tr>\n";
	
	return $result;
}

function htmlNameValuePasswordRow($name) {
	global $view;
	
	$viewData = isset($view[$name]) ? $view[$name] : '';
	
	$result = '<tr>';
	$result .= '<td class="Key">'.lang($name).':</td>';
	$result .= '<td class="Value"><input type="password" name="'.$name.'" title="'.lang($name.'_ToolTip').'"></td>';
	if (getError($name) == '') {
		if ($viewData != '') {
			$result .= showNote(lang($name.'_Note'));
			$result .= '<input type="hidden" name="'.$name.'Hidden" value="'.$viewData.'" />';
		}
	} else {
		$result .= showError($name);
	}
	$result .= "</tr>\n";
	
	return $result;
}

function htmlNavButtons($prev, $next) {
	$result = '';
	if ($prev) {
		$result .= '<input type="submit" name="prev" value="'.lang('Prev').'" />';
	}
	if ($next) {
		$result .= '<input type="submit" name="next" value="'.lang('Next').'" />';
	}
	
	return $result;
}

?>