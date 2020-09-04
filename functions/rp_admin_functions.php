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

function sanitizeSingle($string) {
		$string = stripslashes($string);

		$string = wp_check_invalid_utf8( $string, true ); 
//		$string = wp_filter_nohtml_kses( $string );	// to strip out all html
		$string = wp_kses($string, wp_kses_allowed_html( 'post' )); // allow same html as posts 
	return $string;
}
function sanitize($string) {
	if (is_array($string)){
		foreach($string as $k => $v) {
			if (is_array($v)) {
				$string[$k] = sanitize($v);
			} else {
				$string[$k] = sanitizeSingle($v);
			}
		}
	} else {
		$string = sanitizeSingle($string);
	}
	return $string;
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function ryuzine_options_validate($input) {
	$input = sanitize($input);
	return $input;
}

function ryuzine_uninstall( $which_ryu ) {
	// Check permissions
	if ( !current_user_can( 'administrator' ) ) {
		echo "<div class='error'><p>Sorry, you do not have the correct priveledges to remove the files.</p></div>";
		return ; 
	}
	if ( $which_ryu == 'press') { $whichfile = 'single';} else { $whichfile = 'archive';}
	$ryuFile = get_stylesheet_directory().'/'.$whichfile.'-ryuzine.php';
	if (file_exists($ryuFile)) {
		// If it exists it was already installed //
		if (is_writable(get_stylesheet_directory())) {
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
	$current_version = ryuzine_current_version($sub_path,$file_name);
// return;
	$source = 'https://github.com/ryumaru/'.$file_name.'/archive/'.$current_version.'.zip';
	$upload = WP_CONTENT_DIR.'/uploads/'.$file_name.'-'.$current_version.'.zip';
	if ($sub_path != '') { $sub_path = $sub_path."/"; }
	if (is_writable(WP_CONTENT_DIR.'/uploads')) {
		// Directory is writable, lets copy the zip file //
		copy($source,$upload);
		// Now make sure it is there before trying to unzip! //
		if (file_exists($upload)) {
			$zip = new ZipArchive;
			$res = $zip->open($upload);
			if ( $res === TRUE ) {
				$zip->extractTo(ryuzine_pluginfo('plugin_path').$sub_path);
				$zip->close();
				sleep(1);	// Windows servers may need this?
				// if ryuzine folder already exists we need to delete it before new one can be installed
				$target = ryuzine_pluginfo('plugin_path').$sub_path.$file_name;
				if ( file_exists($target) ) {
					foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($target, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
        				$path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname());
					}
					rmdir($target);
				}
				rename(ryuzine_pluginfo('plugin_path').$sub_path.$file_name.'-'.$current_version,$target);
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

function ryuzine_current_version($sub_path,$file_name) {
	// Get the version number of the latest release on Github //
	$checkfile="https://github.com/ryumaru/".$file_name."/releases/latest";
	
	if (extension_loaded('curl')) {
	// try cURL first
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $checkfile);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Must be set to true so that PHP follows any "Location:" header
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$a = curl_exec($ch); // $a will contain all headers
	$checkfile = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); // returns last URL of redirect
	// Uncomment to see all headers
/*
	echo "<pre>";
	print_r($a);echo"<br>";
	echo "</pre>";
*/
	} else if (function_exists('get_headers')) {
	// or try to get headers
	$header = get_headers($checkfile,1);
	// if there were multiple redirects Location will be an array
	if(array_key_exists('Location',$header)){
		if (is_array($header['Location'])){
			$check = end($header['Location']); // url of landing page
		} else {
			$check = $header['Location'];
		}
	}
//	echo 'get_headers() => '.$check."</br>";
/*	} else if (function_exists('fsockopen')) {
		// This doesn't necessarily get a valid release link, however
		$checkfile = "https://raw.githubusercontent.com/ryumaru/".$file_name."/master/CHANGELOG.txt"
			$fh = fopen($checkfile, "rb");
			$checkversion = fgets($fh);
			fclose($fh);
			$current_version = explode("-", $checkversion);
*/	} else {
		// use the version defined in pluginfo (which may not be the latest)
		$current_version = ryuzine_pluginfo('webapp_version');
	}
//	echo $checkfile; // redirect path
	$current_version = basename($checkfile);	// gets last part of url, which is version number
	
	return $current_version;
}


function ryuzine_installed_version() {
	if (file_exists(ryuzine_pluginfo('plugin_path').'ryuzine/js/ryuzine.js')) {
		if ( ini_get('allow_url_fopen') ) {
			$instfile = ryuzine_pluginfo('plugin_url').'ryuzine/CHANGELOG.txt';
			$fh = fopen($instfile, "rb");
			$instversion = fgets($fh);
			fclose($fh);
			$instversion = explode("-", $instversion);	// knock off alphabeta
			$installed_version = preg_replace("/[a-zA-Z ]+/", "", $instversion[0] ); // numbers only
			$installed_version = explode(".", $installed_version);	// split at dots
			array_splice($installed_version,1,0,".");				// restore first dot
			$installed_version = implode("",$installed_version);	// back to string
		} else {
			$installed_version = ryuzine_pluginfo('webapp_version'); // guess that it is
		}
	} else {
		$installed_version = "0";
	}
	update_option('ryuzine_app_installed',$installed_version); // because it is installed
	return $installed_version;
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
	ryu_download_unzip('','ryuzine');
	if (file_exists(ryuzine_pluginfo('plugin_path').'ryuzine/js/ryuzine.js')) {
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

function reset_rp_options(){
if (isset($_POST['ryuzine_reset'])) {
if ( ! wp_verify_nonce( $_POST['rp_reset_nonce'], basename(__FILE__) ) ) {
		add_defaults_fn( $reset = true );				
	} else {
		return false;
	}
}
}
add_action('init','reset_rp_options');
?>