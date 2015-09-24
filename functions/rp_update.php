<?php
/*
	Ryuzine Press Plugin
	This file checks for automatic updates from 
	the www.ryumaru.com servers because this plug-in
	is not in the official WordPress repo.
*/

// Load the auto-update class
add_action('init', 'ryuzine_activate_au');
function ryuzine_activate_au()
{
    require_once ('wp_autoupdate.php');
    $ryuzine_plugin_current_version = ryuzine_pluginfo('version');
	$ryuzine_plugin_remote_path = 'http://www.ryumaru.com/downloads/testing/ryuzinepress/update_ryuzine-press.php';
    $ryuzine_plugin_slug = ryuzine_pluginfo('plugin_basename');
    new wp_auto_update ($ryuzine_plugin_current_version, $ryuzine_plugin_remote_path, $ryuzine_plugin_slug);
}

?>