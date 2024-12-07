<?php
/*
	Ryuzine Press Plugin
	This file creates the tabbed pages under 
	Ryuzine Press > Tools on the Admin back-end.
*/

function ryuzine_get_tools_tabs() {
     $tabs = array(
     	  'resources' => 'Resources',
          'rackbuild' => 'Rack Builder',
          'update' => 'Update Ryuzine'
     );
     return $tabs;
}
function ryuzine_tools_tabs( $current = 'resources' ) {
     if ( isset ( $_GET['tab'] ) ) :
          $current = $_GET['tab'];
     else:
          $current = 'resources';
     endif;
     $tabs = ryuzine_get_tools_tabs();
     $links = array();
     foreach( $tabs as $tab => $name ) :
          if ( $tab == $current ) :
               $links[] = '<a class="nav-tab nav-tab-active" href="?post_type=ryuzine&page=ryuzine-tools&tab='.$tab.'">'.$name.'</a>';
          else :
               $links[] = '<a class="nav-tab" href="?post_type=ryuzine&page=ryuzine-tools&tab='.$tab.'">'.$name.'</a>';
          endif;
     endforeach;
     echo '<h2 class="nav-tab-wrapper">';
     foreach ( $links as $link )
          echo $link;
     echo '</h2>';
}



// Add About Page
function ryuzine_tools_add_page() {
add_submenu_page('edit.php?post_type=ryuzine', 'Tools', 'Tools','manage_options','ryuzine-tools','ryuzine_tools_page');
}

// Set Up Tools Page //
add_action('admin_init', 'ryuzine_tools_init');
add_action('admin_menu', 'ryuzine_tools_add_page');

// Init plugin options to white list our options
function ryuzine_tools_init(){
	register_setting( 'ryuzine_opt_rack', 'ryuzine_opt_rack', 'ryuzine_options_validate' );
	register_setting( 'ryuzine_rack_cat', 'ryuzine_rack_cat', 'ryuzine_options_validate' );
}


function ryuzine_tools_page() { ?>
	<div class="wrap">
<?php 
	if ( isset( $_POST['add_cat']) ) {
		if ( !wp_verify_nonce( $_POST['ryu_addcat_noncename'], 'ryuzine-add-cat' ) ) {
		echo "<div class='error'><p>Authentication failed.</p></div>";
		return ;
		}
		$ryu_catalog = get_option('ryuzine_rack_cat');
		$count = count($ryu_catalog)+1;
		$new_item = array(array ('Catalog '.$count.'',0,''),array ('ID','Date','Title','Description','Category','URL','Type','Thumbnail','Promotion'),array ( 0,'','','','','','','','' ));
			array_push($ryu_catalog,$new_item);
		update_option('ryuzine_rack_cat',$ryu_catalog);
	}

	if ( isset($_POST['submit']) ) {
		$ryu_catalog = get_option('ryuzine_rack_cat');
		$tables = count($ryu_catalog);
		for ($t=0;$t<$tables;$t++) {
			if (isset( $_POST['submit'][$t]) ) {
				$rows = count($ryu_catalog[$t]);
				for ($r=0;$r<$rows;$r++) {
					$items = count($ryu_catalog[$t][$r]);
					for ($i=0;$i<$items;$i++) {
						if (isset( $_POST['ryuzine_rack_cat'][$t][$r][$i])) {
							$ryu_catalog[$t][$r][$i] = $_POST['ryuzine_rack_cat'][$t][$r][$i];
						}
					}
				}	
			}
			update_option('ryuzine_rack_cat',$ryu_catalog);
		}
	
	
	}

	if ( isset( $_POST['add_item']) ) {
		if ( !wp_verify_nonce( $_POST['ryu_additem_noncename'], 'ryuzine-add-item' ) ) {
		echo "<div class='error'><p>Authentication failed.</p></div>";
		return ;
		}
		$ryu_catalog = get_option('ryuzine_rack_cat');
		$tables = count($ryu_catalog);
		for ($t=0;$t<$tables;$t++) {
			if ( isset( $_POST['add_item'][$t]) ) {
			$count = count($ryu_catalog[$t]);
			$new_item = array ( $count,'','','','','','','','' );
				array_push($ryu_catalog[$t],$new_item);
			update_option('ryuzine_rack_cat',$ryu_catalog);
			}
		}
	}
	if ( isset( $_POST['del_items']) && !empty($_POST['check_list']) ) {
		if ( !wp_verify_nonce( $_POST['ryu_delitem_noncename'], 'ryuzine-del-item' ) ) {
		echo "<div class='error'><p>Authentication failed.</p></div>";
		return ;
		}
		$ryu_catalog = get_option('ryuzine_rack_cat');
		$tables = count($ryu_catalog);
		$num_del = 0;
		for ($t=0;$t<$tables;$t++) {
			$count = count($_POST['check_list'][$t]);
			for ($d=0;$d<$count;$d++) {
				$dex = $_POST['check_list'][$t][$d];
				array_splice($ryu_catalog[$t],$dex,1);
				update_option('ryuzine_rack_cat',$ryu_catalog);
				$num_del++;
			}
		}
			echo '<div class="updated"><p>'.$num_del.' Items Deleted from Catalog.  Sorry this cannot be undone.</p></div>';
	}

	if ( isset( $_POST['del_cats']) && !empty($_POST['cat_list']) ) {
		if ( !wp_verify_nonce( $_POST['ryu_delcats_noncename'], 'ryuzine-del-cats' ) ) {
		echo "<div class='error'><p>Authentication failed.</p></div>";
		return ;
		}
		$ryu_catalog = get_option('ryuzine_rack_cat');
		$count = count($_POST['cat_list']);
		$num_del = 0;
		for ($d=0;$d<$count;$d++) {
			$dex = $_POST['cat_list'][$d];
			array_splice($ryu_catalog,$dex,1);
			update_option('ryuzine_rack_cat',$ryu_catalog);
			$num_del++;
		}
			echo '<div class="updated"><p>'.$num_del.' Catalogs Deleted from database.  Sorry this cannot be undone.</p></div>';
	}

	if ( isset( $_POST['ryu_admin_hide_text']) ) {
		if (get_option('ryu_admin_hide_text')==0) {
			update_option('ryu_admin_hide_text',1);
		} else {
			update_option('ryu_admin_hide_text',0);
		}		
	}

	if ( isset( $_POST['install_ryuzine']) ) {
		if ( !ini_get('allow_url_fopen') ) {
			echo "<div class='error'><p>Server will not allow transfer via HTTP. You will need to download and install the add-on manually.</p></div>";
		} else {
			install_ryuzine_app();
		}
	}
	if ( isset( $_POST['update_check']) ) {
		if ( !ini_get('allow_url_fopen') ) {
		echo "<div class='error'><p>Server is not configured to check remote files.  Go to <a href='https://github.com/ryumaru/ryuzine/releases/latest' target='_blank'>GitHub.com</a> and manually check/download any updates to the Ryuzine WebApp</p></div>";
		} else {
			$installed_version = get_option('ryuzine_app_installed');
			$current_version = ryuzine_current_version('','ryuzine','ryuzine');
			if ( round($installed_version,4) < round($current_version,4) ){	// compare values
			// If there is an update switch button to installer //
				update_option('ryuzine_app_installed',0); 
				echo "<div class='error'><p>There is an update available to version ".$current_version."</p></div>";
			} else {
				echo "<div class='updated' style='background:#BCE954;border:1px solid #A0C544;'><p>You are using the current version of Ryuzine :-)</p></div>";
			}
		}
	}
	
	if ( isset( $_POST['generate_stylesheets']) ) {
		if ( !is_writable(ryuzine_pluginfo('plugin_path').'css') ) {
			echo "<div class='error'><p>Files cannot be generated due to folder permissions.  Either use the in in-page styles or create external stylesheets manually.</p></div>";
		} else {			
			generate_ryuzine_stylesheets();
			$status = get_option('ryu_css_admin');
				if ($status=="1"){
				echo "<div class='updated'><p>Issue-specific Stylesheets successfully regenerated</p></div>";
				} else if ($status=="2") {
				echo "<div class='error'><p>Issue-specific stylesheets cannot be regenerated due to folder permissions at <em>".plugins_url('/css/',__FILE__)."</em>.</p></div>";
				} else {}
			update_option('ryu_css_admin',0);
		}
	}
	if ( isset( $_POST['uninstall_from_theme']) ) {
		if(!is_writable(STYLESHEETPATH)) {
			echo "<div class='error'><p>Folder permissions will not allow uninstallation.  You will need to deleted the <em>'single-ryuzine.php'</em> file via FTP.</p></div>";
		} else {
			ryuzine_uninstall('press');
			echo "<div class='updated'><p>&quot;single-ryuzine.php&quot; successfully removed from current theme.</p></div>";
		}
	}
	if ( isset( $_POST['uninstall_rack_from_theme']) ) {
		if(!is_writable(STYLESHEETPATH)) {
			echo "<div class='error'><p>Folder permissions will not allow uninstallation.  You will need to deleted the <em>'archive-ryuzine.php'</em> file via FTP.</p></div>";
		} else {
			ryuzine_uninstall('rack');
			echo "<div class='updated'><p>&quot;archive-ryuzine.php&quot; successfully removed from current theme.</p></div>";
		}
	}
	

?>	
<style type="text/css">
.nav-tab-active {
	background: #F9F9F9;
	border-bottom: 1px solid #F9F9F9;
}
div .tabbox {
	padding: 10px;
	margin: 0;
background: #f9f9f9;
background: -moz-linear-gradient(top,  #f9f9f9 0%, #fefefe 100%);
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f9f9f9), color-stop(100%,#fefefe));
background: -webkit-linear-gradient(top,  #f9f9f9 0%,#fefefe 100%);
background: -o-linear-gradient(top,  #f9f9f9 0%,#fefefe 100%);
background: -ms-linear-gradient(top,  #f9f9f9 0%,#fefefe 100%);
background: linear-gradient(top,  #f9f9f9 0%,#fefefe 100%);
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f9f9f9', endColorstr='#fefefe',GradientType=0 );

	border: 1px solid #DFDFDF;
	border-top: none;
	-webkit-border-bottom-right-radius: 10px;
	-webkit-border-bottom-left-radius: 10px;
	-moz-border-radius-bottomright: 10px;
	-moz-border-radius-bottomleft: 10px;
	border-bottom-right-radius: 10px;
	border-bottom-left-radius: 10px;
}
div .standalone {
	margin-top: 20px;
	border-top: 1px solid #DFDFDF;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	border-radius: 10px;
}
div .standalone img {
	margin-right: 10px;
}
div .standalone a {
	text-decoration: none;
}
div .standalone a:hover {
	text-decoration: underline;
}
#uploadnotice {
	background:#BCE954;
	border:1px solid #A0C544;
}
a .button-download {
min-width: 150px;
background: #29bb30;
background: -moz-linear-gradient(top,  #29bb30 0%, #1d8524 100%);
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#29bb30), color-stop(100%,#1d8524));
background: -webkit-linear-gradient(top,  #29bb30 0%,#1d8524 100%);
background: -o-linear-gradient(top,  #29bb30 0%,#1d8524 100%);
background: -ms-linear-gradient(top,  #29bb30 0%,#1d8524 100%);
background: linear-gradient(top,  #29bb30 0%,#1d8524 100%);
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#29bb30', endColorstr='#1d8524',GradientType=0 );
border-color: #1d8524;
}

.button_as_link {
	border: none !important;
	background: none !important;
	text-decoration: underline;
}

.rackdata, .rackdata tbody, #addmasthead {
	display: block;
	width: 100%;
	padding: 0;
	margin: 0 auto;
}
	#addmasthead {
		margin: 5px auto;
	}
	.rackdata tr {
		display: block;
		width: 100%;
		height: 20px;
		clear: both;
	}
	.rackdata thead {
		display: block;
	}
	.rackdata td, .rackdata th {
		display: block;
		width: 10.8%;
		height: 100%;
		float: left;
		border: 1px solid #ccc;
		overflow: hidden;
		padding: 0;
		margin: 0;
	}
	.rackdata th {
		background: #eee;
		text-align: left;
	}
	.rackdata td {
		background: #fff;
	}
	.rackdata input[type="text"], .rackdata textarea {
		position: relative;
		background: transparent;
		margin: 0; padding: 0;
		border: none;
		-webkit-appearance: none;
	}
	.rackdata input[type="text"]:focus {
		position: absolute;
		border: 1px solid blue;
		background: white;
		border-radius: 0;
		z-index: 1000;
	}
	.rackdata textarea:focus {
		position: absolute;
		border: 1px solid blue;
		background: white;
		border-radius: 0;
		z-index: 1000;	
	}
	.rackdata th input[type="text"] {
		font-weight: bold;
		font-size: 14px
	}
	.rackdata td input[type="text"], .rackdata td p,
	.rackdata td select, .rackdata td textarea {
		font-weight: normal;
		font-size: 12px;
	}
	.rackdata td p {
		margin: 0;
		text-align: center;
		line-height: 20px;
	}
	.rackdata .noedit {
		color: red;
	}
	.rackdata .nosort input[type="text"] {
		color: darkBlue;
	}
	.rackdata select {
		-webkit-appearance: none;
		background: transparent;
		border: none;
		width: 100%;
		height: 100%;
	}

	.rackdata .image_upload {

	}
		.rackdata .image_upload .button {
			position: relative;
			top: -15px;
			left: 0;
			height: 100%;
			width: 20px;
			margin: 0;
			border-radius: 0;
			background: #eee;
			z-index: 1;
		}
		.rackdata .image_upload input[type="text"] {
			margin-left: 20px;
		}
		.rackdata .image_upload input[type="text"]:focus {
			margin: 0;
		}

	#del_items {
		margin-left: -80px;
	}
	#del_col td {
		height: 20px;
	}
	#del_list {
		position: absolute;
		right: 0;
		background: #eee;
		border: 1px solid #333;
		padding: 10px;
		z-index: 1000;
		-webkit-box-shadow: 4px 4px 5px #666;
		-moz-box-shadow: 4px 4px 5px #666;
		-ms-box-shadow: 4px 4px 5px #666;
		-o-box-shadow: 4px 4px 5px #666;
		box-shadow: 4px 4px 5px #666;
	}



</style>

<h2 class="subtitle"><img src="<?php echo plugins_url('../images/ryuzine-press-icon-03.png',__FILE__); ?>" style="vertical-align:middle;height:32px;width:32px;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;margin-top:-7px;"> Ryuzine<sup style="font-size:xx-small;">&trade;</sup> Press Tools</h2>
<div id="uploadnotice" class="updated" style="display:none"><p id="noticemsg"></p></div>

<?php ryuzine_tools_tabs(); ?>
<?php global $pagenow;
if ($pagenow=="edit.php" && $_GET['post_type'] == "ryuzine" && isset( $_GET['page'] ) && 'ryuzine-tools' == $_GET['page'] ) :
     if ( isset ( $_GET['tab'] ) ) :
          $tab = $_GET['tab'];
     else:
          $tab = 'resources';
     endif;
     switch ( $tab ) :
          case 'resources':
?>
<div class="tabbox">
		<img src="<?php echo plugins_url('../images/ryuzine_logo.png',__FILE__); ?>" style="display:block;margin:20px auto;" alt="Ryuzine Logo" />
		<div class="tabbox standalone" style="display:inline;float:right;margin: 10px;max-width:250px;">
		<?php
			$details = array();
			array_push( $details, ryuzine_pluginfo('version') );
			if (!file_exists(ryuzine_pluginfo('plugin_path').'ryuzine/js/ryuzine.js')) {
				$webapp = '<span style="color:red;">False</span>'; } else { $webapp = 'True';}
			array_push($details, $webapp);
			if ($webapp != 'True') {
			array_push($details, '---');
			} else {
			$webapp_installed_version = ryuzine_installed_version();
			array_push($details, $webapp_installed_version);
			};
			if (file_exists(STYLESHEETPATH.'/single-ryuzine.php')) {
				array_push($details, '<a href="'.get_admin_url().'edit.php?post_type=ryuzine&page=ryuzine-tools&tab=update" style="color:red;">Remove!</a>'); 
			} else { 
				array_push($details, 'Good');
			}
			if (file_exists(STYLESHEETPATH.'/archive-ryuzine.php')) {
				array_push($details, '<a href="'.get_admin_url().'edit.php?post_type=ryuzine&page=ryuzine-tools&tab=update" style="color:red;">Remove!</a>'); 
			} else { 
				array_push($details, 'Good');
			}
			$rack_status = get_option('ryuzine_opt_rack');
			if ($rack_status['install']=='1') {
				array_push($details, 'Enabled');
			} else {
				array_push($details, '<a href="'.get_admin_url().'edit.php?post_type=ryuzine&page=ryuzine-settings&tab=rack" style="color:red;">Disabled</a>');
			}
			$chk_opt = get_option('ryuzine_opt_rack');
			array_push($details, $chk_opt['autocat']);
			
		?>
		<h3>Plugin Details</h3>
			<ul>
			<li><strong>Ryuzine Press Version:</strong> <?php echo $details[0]; ?></li>
			<li><strong>Ryuzine Webapp Installed:</strong> <?php echo $details[1]; ?></li>
			<li><strong>Ryuzine Webapp Version:</strong> <?php echo $details[2]; ?></li>
			<li><strong>Ryuzine Reader in Theme:</strong> <?php echo $details[3]; ?></li>
			<li><strong>Ryuzine Rack in Theme:</strong> <?php echo $details[4]; ?></li>
			<li><strong>Ryuzine Rack Status:</strong> <?php echo $details[5]; ?></li>
			<li><strong>Default Rack Category:</strong> <?php if ($details[6] == '') { echo 'none'; } else {echo $details[6];}; ?> (slug)</li>
			</ul>
			<small>Note: Ryuzine Press and Ryuzine Webapp versions may be different.</small>
		</div>
		<h3>About Ryuzine Press Plugin</h3> 
		<p>"Ryuzine" is a digital publishing webapp ("web application") targeting both desktop and mobile users.  It features a responsive HTML5+CSS3+Javascript interface. 
		The original stand-alone version was released in October 2011 along with an authoring web app.  Neither require additional frameworks, libraries, or server-side processing.</p>
		<p>"Ryuzine Press" is a plugin which <em>bridges</em> content from your WordPress blog to a Ryuzine WebApp installation.  The plugin allows you to easily invoke the Ryuzine WebApp and assign new or existing WordPress posts and other media to the webapp's pages to create unlimited, curated "editions" of your blog content as Ryuzine publications.  It is <em>not</em> a theme itself, and can be used in conjunction with any WordPress theme without altering the front-end layout of your blog.  Your Ryuzine Press Editions will exist alongside your blog, not in place of it.</p>
		
		<h3>Quick Start Guide</h3>
		<p>Ryuzine Press enables you to publish curated digital magazine versions of your blog as easy as creating a regular blog post.</p>
		<ol>
		<li>Go to Ryuzine > Add New to create a new Ryuzine Press Edition.</li>
		<li>Give the Edition any title you want (for example "Issue 1")</li>
		<li>You don't have to enter anything in the text boxes, but if you do the first one should have a summary of the Edition content and/or 
		shortcodes for the cover image and any lightbox content.</li>
		<li>Select (or create) one or more entries from the "Ryuzine Issues" meta-box to assign this Edition to one or more Issues</li>
		<li>Now go to your regular "All Posts" menu item and either create new posts or edit existing posts by assigning them to the same 
		"Ryuzine Issue" as the Edition you created.  A single Post can be assigned to multiple Ryuzine Issues.
		<li>Go to Ryuzine > All Editions and click the "View" link under the Edition you created, or go to its Post Edit screen an press 
		the "View Ryuzine" button.</li>
		<li>Either add the Ryuzine Press edition to a custom menu, provide a direct link to it on your site (remember it exists parallel to your blog, it isn't really part of it so the Ryuzine Press Edition itself 
		does not normally show up in archives on your blog).</li>
		</ol>
		<h3>License</h3>
		<p><strong>The Ryuzine Press plugin</strong> is released under the GPLv3, in accordance with the WordPress license, because the plugin contains code derived WordPress or other GPL projects. The license file can be found in the plugin's folder.</p>
		<p><strong>The Ryuzine webapps</strong> are released under the MPLv2 (Mozilla Public License) and only supply some scripts, images, and stylesheets to the Ryuzine Press plugin templates.  You may have received the Ryuzine webapp bundled with the plugin (allowable under MPLv2 Section 3.3) and the combined "Larger Work" is released under a GPL/MPL dual-license.</p>
		<p>Source code for both the Ryuzine Press plugin and Ryuzine webapps is available on <a href="https://github.com/ryumaru" target="_blank">GitHub</a></p>		
</div>		
<?php	break;
		case 'rackbuild':
?>

<div class="tabbox">
			<form method="post" action="">
<?php wp_nonce_field( 'ryuzine-add-cat', 'ryu_addcat_noncename');?>
						<p style="float:left;"><input name="add_cat" type="submit" value="Add Catalog" /></p>
			</form>
			<div style="float:right;position:relative;">
				<p><input type="button" value="Delete Catalogs" onclick="if(document.getElementById('del_list').style.display=='none'){document.getElementById('del_list').style.display='block'}else{document.getElementById('del_list').style.display='none';}" /></p>
				<div id="del_list" style="display:none;">
					<form method="post" action="">
						<?php wp_nonce_field( 'ryuzine-del-cats', 'ryu_delcats_noncename');
						  $catalogs = get_option('ryuzine_rack_cat');
						  $count = count($catalogs);
						  for ($c=1;$c<$count;$c++) {
							echo '<p><input name="cat_list[]" type="checkbox" value="'.$c.'" /> ['.$c.'] "'.$catalogs[$c][0][0].'"</p>';			
					 } ?>
					 <p style="float:right;"><input name="del_cats" type="submit" value="Delete Checked" /></p>
					 <p style="text-align:center"><small><b>This cannot be undone!</b></small></p>
					</form>
				</div>
			</div>

<hr style="clear:both;"/>
		<div style="float:right;">
		<form method="post" action="">
			<input class="button_as_link" name="ryu_admin_hide_text" type="submit" value="<?php if(get_option('ryu_admin_hide_text')==1){ echo 'Show';}else{ echo 'Hide';} ?> Intro Text" />
		</form>
		</div>
		<h2 class="subtitle">RyuzineRack Data Catalogs</h2>

<div    style="<?php if (get_option('ryu_admin_hide_text') == 1) { echo 'display:none;'; } else { echo 'display:block;'; }; ?>">
		<p>The RyuzineRack newsstand is an optional webapp which replaces the standard Ryuzine archive page in WordPress.  It will automatically 
		include all your published Ryuzine Press Editions.  However, you can also include additional content by appending the main catalog and/or 
		creating additional catalogs.</p>
		<p>A catalog might contain links to store pages for print publications, or downloadable PDF files, and you can divide up the content into 
		individual catalogs focused on a single year, title, media type, etc.  Each catalog can also have an optional "masthead" image, which is 
		displayed above the first page of newsstand items.</p>
		<p>Additional items might include links to other websites, Ryuzine publications on other websites, links to store pages for print publications, 
		downloads, etc.</p>
		<p>Below you can see the Data Builder for the default catalog (which is always appended to the Ryuzine Press Edition archive data).  The 
		Catalog Names appear in the reader's "Sort List" panel, as do the sortable column names (in <b>bold black</b> text in the table headers).</p>
		<p>The drop-down lists for "Category" and "Type" are defined in the Options panel, as they are part of the Configuration File. </p>
		<p><div style="display:block;height:20px;width:20px;margin:3px;background:#eee;border:1px solid #999;text-align:center;line-height:20px;float:left;">&#9656;</div> 
		This arrow button in the table item rows below will open the "Media Library" to select an image for the Cover Thumbnail or a Promotion Banner.  You can also simply enter the word <em>"auto"</em> and 
		RyuzineRack will automatically build a promotional layout for that item.  However, to prevent too many items from being shown as promos it will 
		<em>always</em> show the newest item as a promo, and then up to five additional items with promos, selected newest to oldest.</p>
		<p>Cover Images will be resized to 150x230 pixels. Promo banners should be 600x300 pixels, however they are not scaled to fit so if they are larger they will simply be cropped.</p>
</div>

<?php	
		$library = get_option('ryuzine_rack_cat');
		$libcount = count($library);
		for ($c=0;$c<$libcount;$c++) { ?>
	<table><tr><td>	
		<form form method="post" action="">
		<?php   $tab = $_GET['tab']; ?>
		<?php settings_fields('ryuzine_rack_cat'); ?>
		<?php do_settings_sections('ryuzine-tools&tab=rackbuild');  
			 	$catalog = get_option('ryuzine_rack_cat');
			 	$rows = count($catalog[$c]);	
	?>
	
		<table class="form-table">
		<tr valign="top">
			<th scope="row">Catalog Name:</th>
			<td>
				<input name="ryuzine_rack_cat[<?php echo $c; ?>][0][0]" type="text" value="<?php echo $catalog[$c][0][0]; ?>" />
				<?php if ( $c == 0 ) { ?>
				<input name="ryuzine_rack_cat[<?php echo $c; ?>[0][1]" type="hidden" value="0" />
				<?php } else { ?>
				<input name="ryuzine_rack_cat[<?php echo $c; ?>][0][1]" type="radio" value="0" <?php checked( '0', $catalog[$c][0][1] );  ?> /> Append to Main Catalog
				<input name="ryuzine_rack_cat[<?php echo $c; ?>][0][1]" type="radio" value="1" <?php checked( '1', $catalog[$c][0][1] );  ?> /> Separate Catalog
				<?php } ?>
		<tr valign="top">
			<th scope="row">Catalog Masthead <i>(optional)</i><p class="submit"><input name="submit[<?php echo $c; ?>]" type="submit" class="button-primary" value="<?php _e('Update Catalog') ?>" /></p></th>
			<td>
			<label for="upload_image" class="uploader">
			<input id="catalog_<?php echo $c; ?>_masthead" name="ryuzine_rack_cat[<?php echo $c; ?>][0][2]" type="text" size="36" value="<?php echo $catalog[$c][0][2]; ?>"  />
			<input id="catalog_<?php echo $c; ?>_masthead_button" name="catalog_<?php echo $c; ?>_masthead_button" type="button" class="button" value="Media Library" data-choose="<?php esc_attr_e( 'Choose a Default Image' ); ?>" data-update="<?php esc_attr_e( 'Set as default image' ); ?>" />
			<br />Select an image from your Media Library, Upload a new image, or enter an image URL.<br />
				  This is ignored if left empty.<br />			
			<?php if ( $catalog[$c][0][2] != "" ) {
						echo '<div style="border:1px solid #ccc;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;padding: 0px 10px 10px;overflow:auto;max-height:250px;background:#F8F8F8 ;"><h4>Catalog Masthead Preview:</h4>';
						echo '<img id="cat_<?php echo $c; ?>_mastheadpreview" src="'.$catalog[$c][0][2].'" />';
						echo '</div>';
					}
			?>
			</label>
			</td></tr>
		</table>

	<?php		 		 	
					$rack_html = '<table class="rackdata">';
					$rack_html .='<thead><tr>'.
					'<th class="nosort end"><input name="ryuzine_rack_cat['.$c.'][1][0]" type="text" value="'.$catalog[$c][1][0].'" /></th>'.
					'<th><input name="ryuzine_rack_cat['.$c.'][1][1]" type="text" value="'.$catalog[$c][1][1].'" /></th>'.
					'<th><input name="ryuzine_rack_cat['.$c.'][1][2]" type="text" value="'.$catalog[$c][1][2].'" /></th>'.
					'<th class="nosort"><input name="ryuzine_rack_cat['.$c.'][1][3]" type="text" value="'.$catalog[$c][1][3].'" /></th>'.
					'<th><input name="ryuzine_rack_cat['.$c.'][1][4]" type="text" value="'.$catalog[$c][1][4].'"/></th>'.
					'<th class="nosort"><input name="ryuzine_rack_cat['.$c.'][1][5]" type="text" value="'.$catalog[$c][1][5].'" /></th>'.
					'<th><input name="ryuzine_rack_cat['.$c.'][1][6]" type="text" value="'.$catalog[$c][1][6].'" /></th>'.
					'<th class="nosort"><input name="ryuzine_rack_cat['.$c.'][1][7]" type="text" value="'.$catalog[$c][1][7].'" /></th>'.
					'<th class="nosort"><input name="ryuzine_rack_cat['.$c.'][1][8]" type="text" value="'.$catalog[$c][1][8].'" /></th>'.
					'</tr></thead>'.
					'<tbody>';
					for ($r=2; $r < $rows; $r++) {
							$rack_html .= '<tr>'.
							'<td class="end"><p class="noedit">'.($r-2).'<input name="ryuzine_rack_cat['.$c.']['.$r.'][0]" type="hidden" value="'.($r-2).'" /></p></td>'.
							'<td><input name="ryuzine_rack_cat['.$c.']['.$r.'][1]" type="text" value="'.$catalog[$c][$r][1].'" /></td>'.
							'<td><input name="ryuzine_rack_cat['.$c.']['.$r.'][2]" type="text" value="'.$catalog[$c][$r][2].'" /></td>'.
							'<td><textarea name="ryuzine_rack_cat['.$c.']['.$r.'][3]" >'.$catalog[$c][$r][3].'</textarea></td>'.
							'<td><select name="ryuzine_rack_cat['.$c.']['.$r.'][4]">';

			$tax_slug = 'rackcats';
			$tax_obj = get_taxonomy($tax_slug);
			$tax_name = $tax_obj->labels->name;
			$terms = get_terms($tax_slug, 'hide_empty=0');
				foreach ($terms as $term) { 
					if ($catalog[$c][$r][4]==$term->name) { $selected = "selected"; } else { $selected = ""; };
					$rack_html .= '<option value=' . $term->name . ' ' . $selected . '>' . $term->name .'</option>'; 
				}
							
							
							
							$rack_html .= '</select>'.								
							'</td>'.
							'<td><input name="ryuzine_rack_cat['.$c.']['.$r.'][5]" type="text" value="'.$catalog[$c][$r][5].'" /></td>'.
							'<td><select name="ryuzine_rack_cat['.$c.']['.$r.'][6]">';
							$mediatype = get_option('ryuzine_opt_rack');
							for ($m=0;$m<count($mediatype[0]);$m++) {
								if ($catalog[$c][$r][6]==$mediatype[0][$m]) { $selected = "selected"; } else { $selected = ""; };
								$rack_html .= '<option '.$selected.' value="'.$mediatype[0][$m].'">'.$mediatype[0][$m].'</option>';
							}
							$rack_html .= '</select>'.	
							'</td>'.
							'<td class="image_upload"><label for="upload_image" class="uploader">'.
							'<input id="catalog_'.$c.'_'.$r.'_thumbnail" name="ryuzine_rack_cat['.$c.']['.$r.'][7]" type="text" size="36" value="'.$catalog[$c][$r][7].'"  />'.
							'<input id="catalog_'.$c.'_'.$r.'_thumbnail_button" class="button" type="button" value="&#9656;" title="Open Media Library" />'.
							'</label></td>'.
							
							'<td class="image_upload"><label for="upload_image" class="uploader">'.
							'<input id="catalog_'.$c.'_'.$r.'_promo" name="ryuzine_rack_cat['.$c.']['.$r.'][8]" type="text" size="36" value="'.$catalog[$c][$r][8].'"  />'.
							'<input id="catalog_'.$c.'_'.$r.'_promo_button" class="button" type="button" value="&#9656;" title="Open Media Library" />'.
							'</label></td>'.

							'</tr>';
						}
						$rack_html .= '</tbody></table>';
						echo $rack_html;
				?>	
		</form>
	</td>
	<td rowspan="3" valign="bottom">
		<form method="post" action="">
		<?php wp_nonce_field( 'ryuzine-del-item', 'ryu_delitem_noncename');
			 	$catalog = get_option('ryuzine_rack_cat');
			 	$rows = count($catalog[$c]);
				$del_col = '<table id="del_col"><tr><td></td></tr>';
				for ($r=2; $r < $rows; $r++) {
				$del_col .= '<tr>'.
				'<td class="unstyle end"><input name="check_list['.$c.'][]" type="checkbox" value="'.$r.'" />'.
				'</tr>';
				}
				$del_col .='</table>';
				echo $del_col;
		?>
			<p id="del_items"><input name="del_items[]" type="submit" value="Delete Checked"/></p>
		</form>		
	</td>
	</tr>
		<td>
		<form method="post" action="">
		<?php wp_nonce_field( 'ryuzine-add-item', 'ryu_additem_noncename');?>
					<p><input name="add_item[<?php echo $c; ?>]" type="submit" value="Add Item" /></p>
		</form>
	</td>
	</tr>
	</table>
	<hr />	
<?php } ?>
</div>
<?php	break;
		case 'update':
?>	
<div class="tabbox">
		<h3>Install/Update Ryuzine Webapp to Plugin</h3>
		<table class="form-table">
		<tr><th scope="row">
<?php 
	if (!file_exists(ryuzine_pluginfo('plugin_path').'ryuzine/js/ryuzine.js') || get_option('ryuzine_app_installed') == 0 ) {
		if (!is_writable(ryuzine_pluginfo('plugin_path'))) { ?>
		<a href="https://github.com/ryumaru/ryuzine/releases/latest"><input type="button" class="button-primary button-download" value="Download Ryuzine WebApp" /></a>
		<p style="color:red;"><small>Plugin directory is read-only,<br />you will need to download<br />and manually install via FTP.</small></p>
		<?php } else { ?>
		<form method="post" action="">
		<?php wp_nonce_field( 'ryuzine-app_install', 'ryu_app_noncename');?>		
		<input name="install_ryuzine" type="submit" class="button-primary" style="min-width:140px;margin-left:10px;" value="Install Ryuzine WebApp" onclick="newMessage('Attempting to install Ryuzine WebApp.  This can take a while, please wait. . .');" />
		</form>
		<?php } ?>
		</th><td>Ryuzine Press is only a "bridge" that formats your blog data for the Ryuzine webapps. If the Ryuzine webapp is not installed, 
		you can attempt to automatically install it with the button to the left, but if your server's permissions 
		won't allow it, you will need to download the webapp yourself and manually install it to <em><?php echo plugin_dir_path( dirname( __FILE__ )); ?></em>
<?php } else {  ?>
		<form method="post" action="">
		<?php wp_nonce_field( 'ryuzine-update_install', 'ryu_update_noncename');?>
		<input name="update_check" type="submit" class="button-secondary" style="min-width: 140px;margin-left:10px;" value="Check for Update" />
		</form>
		</th><td>Press the button to the left to check for an update to the Ryuzine webapps.  If there is an update available you'll see a notice at the top 
		of this page and the button should convert to an "Install" button.  If your server is not set up to allow the installation you'll need to download 
		the Ryuzine package and install it manually.
<?php } ?>
		</td></tr></table>
		
<?php	if (file_exists(STYLESHEETPATH.'/single-ryuzine.php')) { ?>
		<h3>Uninstall Ryuzine Press From Theme</h3>
		<table class="form-table">
		<tr valign="top"><th scope="row">
		<?php if (!is_writable(STYLESHEETPATH)) { ?>
			<p style="color:red;"><small>Theme folder is read-only.<br />Remove file via FTP.</small></p>				
		<?php } else { ?>
				<form method="post" action="">
				<?php wp_nonce_field( 'ryuzine-fromtheme_uninstall', 'ryu_fromtheme_noncename'); ?>
				<input name="uninstall_from_theme" type="submit" class="button-secondary" style="min-width:140px;margin-left:10px;" value="Uninstall" />
				</form>
				<p><strong>Status:</strong><br /><em style="color:red">Ryuzine Press is installed<br />to current theme</em></p>
		<?php } ?>
		</th><td>Previous versions of Ryuzine Press required the <em>single-ryuzine.php</em> file be installed into the currently activated theme.  This is 
		no longer necessary and the file could be outdated.  The automatic removal didn't work so you'll need to remove it manually.
		</td></tr></table>
<?php };
		if (file_exists(STYLESHEETPATH.'/archive-ryuzine.php')) { ?>
		<h3>Uninstall Ryuzine Rack Archive From Theme</h3>
		<table class="form-table">
		<tr valign="top"><th scope="row">
		<?php if (!is_writable(STYLESHEETPATH)) { ?>
			<p style="color:red;"><small>Theme folder is read-only.<br />Remove file via FTP.</small></p>				
		<?php } else { ?>
			<form method="post" action="">
			<?php wp_nonce_field( 'ryuzine-rackfromtheme_uninstall', 'ryu_rackfromtheme_noncename'); ?>
			<input name="uninstall_rack_from_theme" type="submit" class="button-secondary" style="min-width:140px;margin-left:10px;" value="Uninstall" />
			</form>
			<p><strong>Status:</strong><br /><em style="color:red">Ryuzine Rack is installed<br />to current theme</em></p>
		<?php  }  ?>
		</th><td>Previous version of Ryuzine Press required the <em>archive-ryuzine.php</em> file be installed to the currently activated theme.  This is 
		no longer necessary and the file could be outdated.  The automatic removal didn't work so you'll need to remove it manually.
		<p>You can now enable/disable Ryuzine Rack on the <em>Ryuzine Press > Options > RyuzineRack</em> tab under "Archive Page"</p>		
		</td></tr></table>
<?php }; ?>

		
		<h3>Bulk Regenerate Issue-Specific Stylesheets</h3>
		<table class="form-table">
		<tr><th scope="row">
		<?php if (!is_writable(ryuzine_pluginfo('plugin_path').'css')) { ?>
		<input type="button" class="button-secondary"  style="min-width: 140px;margin-left:10px;opacity:.45;" value="Regenerate Stylesheets"/>
		<p style="color:red;"><small>Stylesheet folder is read-only.  You will have to use the in-page stylesheets.</small></p>		
		<?php } else { ?>
		<form method="post" action="">
		<?php wp_nonce_field( 'ryuzine-regenstyles_install', 'ryu_regenstyles_noncename');?>
		<input name="generate_stylesheets" type="submit" class="button-secondary"  style="min-width: 140px;margin-left:10px;" value="Regenerate Stylesheets" onclick="newMessage('Attempting to generate stylesheets. . .');" />
		</form>
		<?php } ?>
		</th>
		<td>Pressing the button will check all your Ryuzine Press editions for issue-specific styles.  If found the content will be used to 
		write/overwrite new external stylesheets.  If you've accidentally moved, deleted, or messed up a bunch of your stylesheets this tool 
		will fix them all.  <em>Note: If you have a lot of editions this can take a long time to complete!</em>
		</td>
		</tr>
		</table>
		
</div>
<?php  break;
     	endswitch;
	endif;
?>
<div class="tabbox standalone">
	<p><small>“Ryuzine” and the Ryuzine logos are trademarks of <a href="http://www.kmhcreative.com" target="_blank">K.M. Hansen</a> &amp; <a href="http://www.ryumaru.com" target="_blank">Ryu Maru</a>.  
	If you are distributing unaltered software, downloaded directly from Ryu Maru, to anyone in any way or for any purpose, no further permission is required.  
	Any other use of our trademarks requires prior authorization.</small></p>
</div>	
<script type="text/javascript">
var messagebox = document.getElementById('uploadnotice');
var messagetxt = document.getElementById('noticemsg');
function newMessage(txt) {
	messagebox.style.display = "block";
	messagetxt.innerHTML = txt;
}
</script>
</div>
<?php }



?>