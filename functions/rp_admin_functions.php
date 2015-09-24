<?php
/*
	Ryuzine Press Plugin
	This file has all the functions that only work
	on the Admin back-end
*/

// WP 3.5 Media Manger Modal
function ryu_new_media_box() {
	wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'ryu_new_media_box' );

function ryu_admin_scripts() {
	wp_register_script('ryu-upload', WP_PLUGIN_URL.'/ryuzine-press/js/media_uploader.js', array('jquery'));
	wp_enqueue_script('ryu-upload');
}
function ryu_admin_styles() {
	// to-do list: migrate rest of styles
	wp_register_style('ryu-media', WP_PLUGIN_URL.'/ryuzine-press/plugin_css/media.css');
	wp_enqueue_style('ryu-media');
}
if ( isset($_GET['page']) && ($_GET['page'] == 'ryuzine-settings' || $_GET['page'] == 'ryuzine-tools') ) {
add_action('admin_print_scripts','ryu_admin_scripts');
add_action('admin_print_styles' ,'ryu_admin_styles');
}




// Sanitize and validate input. Accepts an array, return a sanitized array.
function ryuzine_options_validate($input) {

	// TO-DO: Sanitize the input, empty pass-through right now
		
	return $input;
}

function ryuzine_uninstall( $which_ryu ) {
	// Check permissions
	if ( !current_user_can( 'administrator' ) ) {
		echo "<div class='error'><p>Sorry, you do not have the correct priveledges to remove the files.</p></div>";
		return ; 
	}
	if ( $which_ryu == 'press') { $whichfile = 'single';} else { $whichfile = 'archive';}
	$ryuFile = STYLESHEETPATH.'/'.$whichfile.'-ryuzine.php';
	if (file_exists($ryuFile)) {
		// If it exists it was already installed //
		if (is_writable(STYLESHEETPATH)) {
			// Theme folder IS writable, so let's try to remove the file //
			unlink($ryuFile);
		}
	}
}

// Generic Function to Install - only works in PHP 5 and later //
function ryu_download_unzip($sub_path,$file_name) {
	// Check permissions
	if ( !current_user_can( 'administrator' ) ) {
		echo "<div class='error'><p>Sorry, you do not have the correct priveledges to install the files.</p></div>";
		return ; 
	}
	// Get the version number so we download the latest release //
	$checkfile = "http://192.168.1.108/ryumaru/downloads/ryuzinepress/version.txt";
//	$checkfile = "http://www.ryumaru.com/downloads/ryuzinepress/version.txt";
			$fh = fopen($checkfile, "rb");
			$checkversion = fgets($fh);
			fclose($fh);
			$current_version = explode("-", $checkversion);
	echo '<br/>DL: current version: '.$current_version[0].'<br/>';
	$source = 'http://www.ryumaru.com/downloads/ryuzine/'.$current_version[0].'/'.$file_name;
	$upload = WP_CONTENT_DIR.'/uploads/'.$file_name;
	if ($sub_path != '') { $sub_path = 'ryuzine/'.$sub_path.'/'; }
	if (is_writable(WP_CONTENT_DIR.'/uploads')) {
		// Directory is writable, lets copy the zip file //
		copy($source,$upload);
		// Now make sure it is there before trying to unzip! //
		if (file_exists($upload)) {
			$zip = new ZipArchive;
			$res = $zip->open($upload);
			if ( $res === TRUE ) {
				$zip->extractTo(WP_PLUGIN_DIR.'/ryuzine-press/'.$sub_path);
				$zip->close();
			} else {
				echo "<div class='error'><p>File could not be unzipped.  Install manually.</p></div>";			
			}
		}
		else {
		echo "<div class='error'><p>No file found to unzip!</p></div>";
		}
	} else {
		echo "<div class='error'><p>Uploads folder is not writable.  Install file manually.</p></div>";
	}
}



function install_ryuzine_app() {
	// verify this came from the our screen and with proper authorization.
	if ( !wp_verify_nonce( $_POST['ryu_app_noncename'], 'ryuzine-app_install' ) ) {
		return ;
	}
	// Check permissions
	if ( !current_user_can( 'administrator' ) ) {
		echo "<div class='error'><p>Sorry, you do not have the correct priveledges to install the files.</p></div>";
		return ; 
	}
	// OK, we're authenticated let's do it!	
	ryu_download_unzip('','ryuzine.zip');
	if (file_exists(WP_PLUGIN_DIR.'/ryuzine-press/ryuzine/js/ryuzine.js')) {
		update_option('ryuzine_app_installed',1);
		echo "<div class='updated'><p>Ryuzine WebApp installation complete.</p></div>";	
	} else {
		echo "<div class='error'><p>Ryuzine WebApp installation had problems.</p></div>";	
	}
	return ;
}

function delete_catalog_item() {
	if ( isset( $_POST['del_items']) && !empty($_POST['check_list']) ) {
		if ( !wp_verify_nonce( $_POST['ryu_delitem_noncename'], 'ryuzine-del-item' ) ) {
		echo "<div class='error'><p>Authentication failed.</p></div>";
		return ;
		}
		$ryu_catalog = get_option('ryuzine_rack_cat');
		$count = count($_POST['check_list']);
		for ($d=0;$d<$count;$d++) {
			$dex = $d+1;
			array_splice($ryu_catalog,$dex,1);
			update_option('ryuzine_rack_cat',$ryu_catalog);
			echo '<div class="error"><p>'.$dex.'</p></div>';
		}
	}
}
?>