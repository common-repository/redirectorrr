<?php
/*
Plugin Name: Redirectorrr
Plugin URI: http://dev.wp-plugins.org/browser/redirectorrr/
Description: 
Version: 0.1
Author: Choan C. Gálvez <choan.galvez@gmail.com>
Author URI: http://dizque.lacalabaza.net/
*/

/*  
    Copyright 2006  Choan C. Gálvez  (email: choan.galvez@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


/**
 * 2006-11-19: Al subir el plugin a Dreamhost he descubierto un problema con la redirección. Modifico los hooks para que la realice _antes_ que ninguna otra cosa.
 */


function redirectorrr() {
	//if (!is_404()) return;
	//if (is_404()) {
		$url = redirectorrr_getURL();
	//}
	if ($url) {
		// Permanent redirection
		// (wp_redirect doesn't send 301 headers)
		header("HTTP/1.1 301 Moved Permanently", true);
		header("Location: $url");
		//echo "Moved to <a href='$url'>$url</a>";
		exit();
	} else {
		//redirectorrr_logFailedURL();
	}
}

function redirectorrr_logFailedURL($in) {
	if (!is_404()) return $in;
	$opt = get_option("redirectorrr");
	$uri = $_SERVER['REQUEST_URI'];
	$opt[$uri] = array("", "");
	update_option("redirectorrr", $opt);
	return $in;
}

function redirectorrr_getURL() {
	$index = $_SERVER['REQUEST_URI'];
	$table = get_option("redirectorrr");
	$val = null;
	if (substr($index, -1) == "/") {
		$index = substr($index, 0, -1);
	}
	if (isset($table[$index])) {
		$val = $table[$index];
	}
	
	if (!$val)
		return false;
	
	if (!isset($val[1])) {
		// default is path
		$val[1] = "path";
	}
	$url = null;
	switch ($val[1]) {
		case "post":
			$url = get_permalink($val[0]);
			break;
		case "category":
			$url = get_category_link($val[0]);
			break;
		case "path":
			$url = get_bloginfo("home") . $val[0];
			break;
		case "url":
		default:
			$url = $val[0];
			break;
	}
	
	return $url;
}


function redirectorrr_subpanel() {
//	var_dump($options);
	if (isset($_POST['info_update'])) {
		$updated = redirectorrr_saveForm($_POST);
		if ($updated) {
			echo '<div class="updated"><p><strong>' . __('Process completed fields in this if-block, and then print warnings, errors or success information.', 'redirectorrr') .'</strong></p></div>';
		} else {
			echo '<div class="error"><p><strong>' . __('Error malo malísimo, móntatelo mejor', 'redirectorrr') .'</strong></p></div>';			
		}
	}
	echo '<div class="wrap"><form method="post">';
	echo '<h2>Redirectorrr options</h2>';
	$options = get_option("redirectorrr");
	echo redirectorrr_table($options);
	echo '<div class="submit"><input type="submit" name="info_update" value="' . __('Update options', 'redirectorrr') . '" /></div></form></div>';
}

function redirectorrr_table($options) {
	$t = '<input type="text" name="%s%s" value="%s" />';
	$c = 0;
	// TODO: tipo como select
	$h = "<table id='redirectorrr'>";
	$h .= "<caption>" . __('Permanent redirections', 'redirectorrr') . "</caption>";
	$h .= "<thead><tr><th>From</th><th>To</th><th>Type</th></thead>";
	$h .= "<tbody>";
	$c = "[]";
	foreach ($options as $key => $value) {
		$h .= "<tr><td>";
		$h .= sprintf($t, "from", $c, $key);
		$h .= "</td><td>";
		$h .= sprintf($t, "to", $c, $value[0]);
		$h .= "</td><td>";
		$h .= sprintf($t, "type", $c, $value[1]);
		$h .= "</td></tr>";
	}

	$h .= "<tr><td>";
	$h .= sprintf($t, "from", $c, '');
	$h .= "</td><td>";
	$h .= sprintf($t, "to", $c, '');
	$h .= "</td><td>";
	$h .= sprintf($t, "type", $c, '');
	$h .= "</td></tr>";

	$h .= "</tbody></table>";
	return $h;
}

function redirectorrr_saveForm($form) {
	//var_dump($form);
	$l = count($form["from"]);
	$opts = array();
	for ($i = 0; $i < $l; $i++) {
		if ($form["from"][$i]) {
			$opts[$form["from"][$i]] = array($form["to"][$i], $form["type"][$i]);
		}
	}
	//var_dump($opts);
	update_option("redirectorrr", $opts);
	return true;
} 

function redirectorrr_menu() {
	if (function_exists('add_options_page')) {
		add_options_page('Redirectorrr', 'Redirectorrr', 9, basename(__FILE__), 'redirectorrr_subpanel');
	}
}

if (true /* debería hacerse solo al activar el plugin */) {
	add_option("redirectorrr", array(
			"/redirectorrr" => array("/", "path")
		),
		"URL Redirections",
		"no"
	);
}

function redirectorrr_script() {
	if (!isset($_GET['page']) || $_GET['page'] != "redirectorrr.php") return;
	echo "<script type='text/javascript'>\n";
	readfile(dirname(__FILE__) . "/redirectorrr.js");
	echo "\n</script>";
}


add_action('admin_menu', 'redirectorrr_menu');
add_action("template_redirect", "redirectorrr_logFailedURL");
add_action("init", "redirectorrr");
add_action("admin_head", "redirectorrr_script");
?>