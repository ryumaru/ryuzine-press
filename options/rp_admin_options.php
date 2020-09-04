<?php
/*
	Ryuzine Press Plugin
	This file creates the tabbed pages
	under Ryuzine Press > Options
	on the Admin back-end.
*/

// Set Up Options Page //
add_action('admin_init', 'ryuzine_options_init' );
add_action('admin_menu', 'ryuzine_options_add_page');

// Init plugin options to white list our options
function ryuzine_options_init(){
	register_setting( 'ryuzine_opt_covers', 'ryuzine_opt_covers', 'ryuzine_options_validate' );
	register_setting( 'ryuzine_opt_pages', 'ryuzine_opt_pages', 'ryuzine_options_validate' );
	register_setting( 'ryuzine_opt_addons', 'ryuzine_opt_addons', 'ryuzine_options_validate' );
	register_setting( 'ryuzine_opt_ads', 'ryuzine_opt_ads', 'ryuzine_options_validate' );
	register_setting( 'ryuzine_opt_lightbox', 'ryuzine_opt_lightbox', 'ryuzine_options_validate' );
}

function ryuzine_get_options_tabs() {
     $tabs = array(
     	  'covers' => 'Covers',
          'pages' => 'Pages',
          'addons' => 'Add-Ons',
          'ads' => 'Advertisting',
          'shortcodes' => 'Shortcodes',
          'rack' => 'RyuzineRack'
     );
     return $tabs;
}
function ryuzine_options_tabs( $current = 'covers' ) {
     if ( isset ( $_GET['tab'] ) ) :
          $current = $_GET['tab'];
     else:
          $current = 'covers';
     endif;
     $tabs = ryuzine_get_options_tabs();
     $links = array();
     foreach( $tabs as $tab => $name ) :
          if ( $tab == $current ) :
               $links[] = '<a class="nav-tab nav-tab-active" href="?post_type=ryuzine&page=ryuzine-settings&tab='.$tab.'">'.$name.'</a>';
          else :
               $links[] = '<a class="nav-tab" href="?post_type=ryuzine&page=ryuzine-settings&tab='.$tab.'">'.$name.'</a>';
          endif;
     endforeach;
     echo '<h2 class="nav-tab-wrapper">';
     foreach ( $links as $link )
          echo $link;
     echo '</h2>';
}



// Add menu page
function ryuzine_options_add_page() {
/*	add_options_page('Ryuzine Options', 'Ryuzine Options', 'manage_options', 'ryuzine_options', 'ryuzine_options_page'); */
add_submenu_page('edit.php?post_type=ryuzine', 'Options', 'Options','manage_options','ryuzine-settings','ryuzine_options_page');
}

// Draw the menu page itself
function ryuzine_options_page() {
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
div .cloak {
	visibility: hidden;
}

.url_box {
	resize: none;
	height: 23px;
	width: 284px;
	white-space: nowrap; 
	overflow: hidden;
	float:left;
	margin-right:5px;
}

.text_area {
	min-height: 284px;
	min-width: 284px;
}
.disabled {
	opacity:0.25;
}
.offchk {
	margin-left: 50px;
}
.previewbox {
border:1px solid #ccc;
-moz-border-radius:5px;
-webkit-border-radius:5px;
border-radius:5px;
padding: 0px 10px 10px;
overflow:auto;
max-height:250px;
background:#F8F8F8;
}
.iconbox {
	width: 20%;
	text-align: center;
	float: left;
}
</style>
	<div class="wrap">
	<?php if ( isset( $_GET['settings-updated'] ) ) {
    echo "<div class='updated'><p>Ryuzine options updated successfully.</p></div>";
} ?>
<h2 class="subtitle"><img src="<?php echo plugins_url('../images/ryuzine-press-icon-03.png',__FILE__); ?>" style="vertical-align:middle;height:32px;width:32px;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;margin-top:-7px;"> Ryuzine<sup style="font-size:xx-small;">&trade;</sup> Press Global Options</h2>


<?php $install = get_option('ryuzine_press_installed'); ?>

<?php ryuzine_options_tabs(); ?>

<?php global $pagenow;
if ($pagenow=="edit.php" && $_GET['post_type'] == "ryuzine" && isset( $_GET['page'] ) && 'ryuzine-settings' == $_GET['page'] ) :
     if ( isset ( $_GET['tab'] ) ) :
          $tab = $_GET['tab'];
     else:
          $tab = 'covers';
     endif;
     switch ( $tab ) :
          case 'covers':
?>
<form method="post" enctype="multipart/form-data" action="options.php">
	<?php if(isset($_GET['tab'])){$tab = $_GET['tab'];} ?>
	<?php settings_fields('ryuzine_opt_covers'); ?>
	<?php do_settings_sections('ryuzine-settings&tab=covers');  ?>
	<?php $options = get_option('ryuzine_opt_covers'); ?>
	<div class="tabbox">
			<h3>Cover Settings</h3>
			<p>The default setting is for Ryuzine to automatically generate a cover for each issue and to not display the headers and footers.  However you can choose to keep the 
			spacing of the header and footer without showing them, or choose to show them.  You can also opt to use a post for the cover (which will be the oldest of the posts you 
			assign to the edition).<p>

			<table class="form-table">
				<tr valign="top"><th scope="row">Cover Headers &amp; Footers</th>
					<td>
					<input name="ryuzine_opt_covers[headerfooter]" type="radio" value="display:none;" <?php checked('display:none;', $options['headerfooter'] );  ?> /> Hide and remove spacing</label>
					<br />
					<input name="ryuzine_opt_covers[headerfooter]" type="radio" value="visibility:hidden;" <?php checked('visibility:hidden;', $options['headerfooter']);  ?> /> Hide but keep spacing</label>
					<br />
					<input name="ryuzine_opt_covers[headerfooter]" type="radio" value="display:block;" <?php checked('display:block;', $options['headerfooter'] );  ?> /> Show both header and footer on front &amp; back covers</label>					
					</td>
				</tr>	
				<tr valign="top"><th scope="row">Cover Source</th>
					<td>
					<input name="ryuzine_opt_covers[autocover]" type="radio" value="0" <?php checked( '0', $options['autocover'] );  ?> /> Generate Automatically<br/>
					<input name="ryuzine_opt_covers[autocover]" type="radio" value="1" <?php checked( '1', $options['autocover'] );  ?> /> Use Oldest Post Assigned to Edition<br />
					</td>
				</tr>
				<tr><td colspan="2">
				<table class="tabbox standalone">	
					<tr><td colspan="2">
					<h3>Splash Screen &amp; Auto-Generated Cover Settings</h3>
					</td></tr>
					<tr valign-"top"><th scope="row">Masthead Type</th>
						<td>
						<input name="ryuzine_opt_covers[mastheadtype]" type="radio" value="0" <?php checked( '0', $options['mastheadtype'] ); ?> /> Text 
						<input name="ryuzine_opt_covers[mastheadtype]" type="radio" value="1" <?php checked( '1', $options['mastheadtype'] ); ?> /> Image
						<input name="ryuzine_opt_covers[mastheadtype]" type="radio" value="2" <?php checked( '2', $options['mastheadtype'] ); ?> /> None
						</td>
					</tr>
					
					<tr valign="top"><th scope="row">Masthead Text</th>
						<td>
						<input type="text" name="ryuzine_opt_covers[mastheadtext]" value="<?php echo $options['mastheadtext']; ?>" /><br />
						<small>Also becomes title on Splash and in page footers.  Used as alt-text if Masthead Type is set to Image.  If empty Blog Name will be used.</small>
						</td>
					</tr>
					
					<tr valign="top"><th scope="row">Masthead Image</th>
						<td>
						<label for="upload_image" class="uploader">
							<input id="masthead_image" name="ryuzine_opt_covers[mastheadimg]" type="text" size="36" value="<?php echo $options['mastheadimg']; ?>"  />
							<input id="masthead_image_button" class="button" type="button" value="Media Library" /><br/>
							<input name="ryuzine_opt_covers[splashimg]" type="checkbox" class="offchk" value="1" <?php if (isset($options['splashimg'])) { checked('1', $options['splashimg']);} ?> /> <strong>Also use this on Splash Screen instead of text</strong> <em>(Note: If you set a Splash Ad it will override this)</em></br />
							<br/>
							Select an image from your Media Library, Upload a new image, or enter an image URL. 
							If used on Splash but no App Logo is set this image will also be scaled for use in the phone navbar in place of the Edition title. 
							This is ignored if Masthead Type is set to TEXT.  Image is automatically scaled to fit.
							<br/>
							<?php if ( $options['mastheadimg'] != "" ) {
								echo '<div style="border:1px solid #ccc;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;padding: 0px 10px 10px;overflow:auto;max-height:250px;background:#F8F8F8 ;"><h4>Current Masthead Preview:</h4>';
								echo '<img id="mastheadpreview" src="'.$options['mastheadimg'].'" />';
								echo '</div>';
							}
							?>
							</label>
						</td>
					</tr>
					
					<tr valign="top"><th scope="row">Featured Articles Category</th>
						<td>
						<?php $categories= get_categories(); 
						?>
						<select name="ryuzine_opt_covers[featured]">
						<?php 
						foreach ($categories as $cat) { 
							$selected = ($cat->cat_name==$options['featured']) ? 'selected="selected"' : ''; 
							echo "<option value='$cat->cat_name' $selected>$cat->name</option>"; 
						} 
						echo "</select>"; 
						?>
						<input name="ryuzine_opt_covers[show_featured]" type="checkbox" value="1" <?php if (isset($options['show_featured'])) { checked('1', $options['show_featured']);} ?> /> Show Links on Cover<br />
						<small>(used to build featured articles list on auto-generated covers)</small>
						</td>
					</tr>
					<tr valign="top"><th scope="row">Cover Image</th>
						<td>
						<input name="ryuzine_opt_covers[use_cover]" type="radio" value="0" <?php checked( '0', $options['use_cover'] ); ?> /> Use "Featured Image" attached to Edition<br/>
						<input name="ryuzine_opt_covers[use_cover]" type="radio" value="1" <?php checked( '1', $options['use_cover'] ); ?> /> Use [ryucover] shortcode content
						<br/><small>Other enabled, auto-generated cover items are composited on top of this image.</small>
						</td>
					</tr>
					<tr valign-"top"><th scope="row">"Powered By" Splash Footer</th>
						<td>
						<input name="ryuzine_opt_covers[poweredby]" type="radio" value="0" <?php checked( '0', $options['poweredby'] ); ?> /> Disable
						<input name="ryuzine_opt_covers[poweredby]" type="radio" value="1" <?php checked( '1', $options['poweredby'] ); ?> /> Enable
						<p><small>If enabled this only shows on the Ryuzine webapp "splash" screen.  It is not added to your blog footer.</small></p>
						</td>
					</tr>
					</table>
				</td></tr>
				</table>

			<h3>App Logo / Icons</h3>
			<p>The App Logo appears in the phone navbar in place of the Edition title.  The App Icon is used for the browser favicon and 
			to represent your publication when it is bookmarked to the homescreen on mobile devices like the iPhone and iPad.
			<br /><em>Images are automatically scaled up/down to the correct sizes, but you should use an image the same size or larger 
			than the "High-Res" image (114x114) or it may look pixelated/blurry on high-density devices.</em><p>				
				
				<table class="form-table">
				<tr valign="top"><th scope="row">App Logo</th>
					<td>
					<label for="upload_image" class="uploader">
						<input id="applogo_image" name="ryuzine_opt_covers[app_logo]" type="text" size="36" value="<?php if(isset($options['app_logo'])){echo $options['app_logo'];} ?>"  />
						<input id="applogo_image_button" class="button" type="button" value="Media Library" />
						<br />Select an image from your Media Library, Upload a new image, or enter an image URL.<br />
						This will be displayed in the phone navbar in place of the edition title.
					</label>
					</td>
				</tr>
				<tr><th scope="row">App Icon</em></th>
					<td>
					<label for="upload_image" class="uploader">
						<input id="appicon_image" name="ryuzine_opt_covers[app_icon]" type="text" size="36" value="<?php if(isset($options['app_icon'])){echo $options['app_icon'];} ?>"  />
						<input id="appicon_image_button" class="button" type="button" value="Media Library" />
						<br />Select an image from your Media Library, Upload a new image, or enter an image URL.<br />
						This is used as the "App Icon" when you bookmark a Ryuzine Press edition to the home screen.<br />
					</label>

					</td>
				</tr>
			<th scope="row"></th>
					<td>
						<?php 
							echo '<div class="previewbox"><h4>App Logo &amp; Icon Previews:</h4>';
							if (isset($options['app_logo'])) {  $applogo = $options['app_logo']; } else { $applogo = ""; }
							echo '<div class="iconbox"><img id="applogopreview" src="'.$applogo.'" style="width:auto;height:50px;"/></div>';
							/* Make App Icons On the Fly */
							if (isset($options['app_icon']) && $options['app_icon'] != '') {
								$icon = $options['app_icon'];
								$favicon 	= aq_resize( $icon, 16, 16, true,true,true); 
								$ipad_icon 	= aq_resize( $icon, 72, 72, true,true,true);
								$retina_icon= aq_resize( $icon, 114, 114, true,true,true);
								$iphone_icon= aq_resize( $icon, 57, 57, true,true,true);
							} else {
								$favicon = plugins_url('ryuzine/images/app/icons/ryuzine-favicon.png',dirname(__FILE__));
								$iphone_icon = plugins_url('ryuzine/images/app/icons/ryuzine-icon-03.png',dirname(__FILE__));
								$ipad_icon = plugins_url('ryuzine/images/app/icons/ryuzine-icon-02.png',dirname(__FILE__));
								$retina_icon = plugins_url('ryuzine/images/app/icons/ryuzine-icon-01.png',dirname(__FILE__));
							}
							echo '<div class="iconbox"><img id="faviconpreview" src="'.$favicon.'"/></div>';
							echo '<div class="iconbox"><img id="iphonepreview" src="'.$iphone_icon.'"/></div>';
							echo '<div class="iconbox"><img id="ipadpreview" src="'.$ipad_icon.'" /></div>';
							echo '<div class="iconbox"><img id="retinapreview" src="'.$retina_icon.'" /></div>';
							echo '<div style="clear:both;"></div>';
							echo '<div class="iconbox"><strong>App Logo</strong></div>';
							echo '<div class="iconbox"><strong>Favicon</strong></div>';
							echo '<div class="iconbox"><strong>Phones</strong></div>';
							echo '<div class="iconbox"><strong>Tablets</strong></div>';
							echo '<div class="iconbox"><strong>High-Res</strong></div>';
							echo '<div style="clear:both;"></div></div>';
						?>
					</td>
				</tr>
			</table>
</div>
<?php	break;
		case 'pages':
?>
<form method="post" enctype="multipart/form-data" action="options.php">
	<?php $tab = $_GET['tab']; ?>
	<?php settings_fields('ryuzine_opt_pages'); ?>
	<?php do_settings_sections('ryuzine-settings&tab=pages');  ?>
	<?php $options = get_option('ryuzine_opt_pages'); ?>
<div class="tabbox">
	<h3>Pages Set-Up</h3>
	
	<table class="form-table">
        <tr valign="top">
        <th scope="row">Binding</th>
        <td>
        <input name="ryuzine_opt_pages[binding]" type="radio" value="left" <?php checked( 'left', $options['binding'] ); ?>/> Left
		<input name="ryuzine_opt_pages[binding]" type="radio" value="right" <?php checked( 'right', $options['binding'] ); ?> /> Right
		</td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Page Fill</th>
        <td>
    	<input name="ryuzine_opt_pages[pgsize]" type="radio" value="0" <?php checked( '0', $options['pgsize'] ); ?>/> Magazine (Square)
		<input name="ryuzine_opt_pages[pgsize]" type="radio" value="1" <?php checked( '1', $options['pgsize'] ); ?> /> Comic Book (Tall)
		<input name="ryuzine_opt_pages[pgsize]" type="radio" value="2" <?php checked( '2', $options['pgsize'] ); ?> /> Fill All (fluid layout)
        <br /><small>When "Fill All" is enabled pages grow to fill the available space.</small>
        </td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Apply WP Theme</th>
        <td>
        <input name="ryuzine_opt_pages[wptheme2ryu]" type="radio" value="0" <?php checked( '0', $options['wptheme2ryu'] ); ?>/> No
		<input name="ryuzine_opt_pages[wptheme2ryu]" type="radio" value="1" <?php checked( '1', $options['wptheme2ryu'] ); ?> /> Yes<br/>
		<small>Depending on the WP theme applying may break the Ryuzine layout or need Issue-Specific styling overrides.</small>
		</td>
        </tr>

        <tr valign="top">
        <th scope="row">Bylines</th>
        <td>
        <input name="ryuzine_opt_pages[byline]" type="radio" value="0" <?php checked( '0', $options['byline'] ); ?>/> No post author and date<br/>
		<input name="ryuzine_opt_pages[byline]" type="radio" value="1" <?php checked( '1', $options['byline'] ); ?> /> Include post author and date <br />
		</td>
        </tr>

        <tr valign="top">
        <th scope="row">Comic Post Body</th>
        <td>
        <input name="ryuzine_opt_pages[postbody]" type="radio" value="0" <?php checked( '0', $options['postbody'] ); ?>/> Suppress post text and only show comic image.<br/>
		<input name="ryuzine_opt_pages[postbody]" type="radio" value="1" <?php checked( '1', $options['postbody'] ); ?> /> Show comic image and post text.<br />
		<small>Only works if ComicPress, Webcomic, or Easel is installed and activated.  Only affects posts in comics categories/storylines</small>
		</td>
        </tr>

        <tr valign="top">
        <th scope="row">Meta Data</th>
        <td>
        <input name="ryuzine_opt_pages[metadata]" type="radio" value="0" <?php checked( '0', $options['metadata'] ); ?>/> No tags, catgories, comment counts, etc.<br/>
		<input name="ryuzine_opt_pages[metadata]" type="radio" value="1" <?php checked( '1', $options['metadata'] ); ?> /> Include tags, categories, comment counts, etc. <br />
		</td>
        </tr>
      
        <tr valign="top">
        <th scope="row">Comments</th>
        <td>
        <input name="ryuzine_opt_pages[comments]" type="radio" value="0" <?php checked( '0', $options['comments'] ); ?>/> No Comments on pages<br/>
		<input name="ryuzine_opt_pages[comments]" type="radio" value="1" <?php checked( '1', $options['comments'] ); ?> /> Include Comments on pages<br />
		<small>Note: Comments form submission takes the reader to the corresponding blog post page and away from the Ryuzine version.</small>
		</td>
        </tr>
        
<!--//        <tr valign="top">
        <th scope="row">Android App View</th>
        <td>
    	<input name="ryuzine_opt_pages[AndApp]" type="radio" value="0" <?php checked( '0', $options['pgsize'] ); ?>/> Off
		<input name="ryuzine_opt_pages[AndApp]" type="radio" value="1" <?php checked( '1', $options['pgsize'] ); ?> /> On
        <br/><small>Enables a native-app-like view on some Android devices</small>
        </td>
        </tr>
//-->        
        <tr valign="top">
        <th scope="row">Page Slider</th>
        <td>
        <input name="ryuzine_opt_pages[pgslider]" type="radio" value="0" <?php checked( '0', $options['pgslider'] ); ?>/> Use Table of Contents Panel<br/>
		<input name="ryuzine_opt_pages[pgslider]" type="radio" value="1" <?php checked( '1', $options['pgslider'] ); ?> /> Use Page Slider Navigation<br />
		</td>
        </tr>        
 
         <tr valign="top">
        <th scope="row">Animate View Changes</th>
        <td>
        <input name="ryuzine_opt_pages[viewani]" type="radio" value="0" <?php checked( '0', $options['viewani'] ); ?>/> Disable View Change Animations<br/>
		<input name="ryuzine_opt_pages[viewani]" type="radio" value="1" <?php checked( '1', $options['viewani'] ); ?> /> Enable View Change Animations<br />
		<small>Only works if browser supports regular page animations and end user has not turned them off in the Options Panel</small>
		</td>
        </tr> 
        
        </table>
        
        <h3>Zoom &amp; Pan</h3>
        <p>Enables or Disables zooming and panning on load for touch-enabled devices (it is still user selectable in the webapp's Options panel)</p>
        
        <table class="form-table">
        <tr valign="top">
        <th scope="row">Zoomable</th>
        <td>
    	<input name="ryuzine_opt_pages[zoompan]" type="radio" value="0" <?php checked( '0', $options['zoompan'] ); ?>/> Off
		<input name="ryuzine_opt_pages[zoompan]" type="radio" value="1" <?php checked( '1', $options['zoompan'] ); ?> /> On
        </td>
		</tr>
        <tr valign="top">
        <th scope="row">Maximum Zoom Amount</th>
        <td><input type="text" name="ryuzine_opt_pages[maxzoom]" value="<?php echo $options['maxzoom']; ?>" /> (Value 0 - 10)</td>
		</tr>
		</table>
		
        <h3>Default Bookmarks</h3>
        <p>The In-App Bookmark Management panel is pre-populated with a couple bookmarks end users cannot delete.</p>
        
        <table class="form-table">
        <tr valign="top">
        <th scope="row">Bookmark 1</th>
        <td>
    	Text: <input name="ryuzine_opt_pages[bmark1]" type="text" value="<?php echo $options['bmark1']; ?>" /><br />
		Link: <input name="ryuzine_opt_pages[bmark1url]" type="text" value="<?php echo $options['bmark1url']; ?>" />
        </td>
		</tr>
        <tr valign="top">
        <th scope="row">Bookmark 2</th>
        <td>
    	Text: <input name="ryuzine_opt_pages[bmark2]" type="text" value="<?php echo $options['bmark2']; ?>" /><br />
		Link: <input name="ryuzine_opt_pages[bmark2url]" type="text" value="<?php echo $options['bmark2url']; ?>" />
        </td>
		</tr>
		</table>
</div>
<?php 	break;
		case 'addons':
?>
<form method="post" enctype="multipart/form-data" action="options.php">
	<?php   $tab = $_GET['tab']; ?>
	<?php settings_fields('ryuzine_opt_addons'); ?>
	<?php do_settings_sections('ryuzine-settings&tab=addons');  ?>
	<?php $options = get_option('ryuzine_opt_addons'); ?>
<div class="tabbox">
		<h3>Language Settings</h3>
		<p>The "Natural Language" is used by auto-translation services and operating systems to display the document 
		in the correct language (it does not, however, translate the document).  The "UI Language Code" is ignored if 
		the "localize" Add-On is not loaded below.  Again, this does not translate the document.  It only changes the 
		language on the User Interface elements.</p>
		<table class="form-table">
		<tr valign="top">
		<th scope="row">HTML Natural Language</th>
		<td>
		 <select name="ryuzine_opt_addons[natlang]">
			<option <?php selected( 'aa', $options['natlang'] ); ?> value="aa">aa Afar</option>
			<option <?php selected( 'ab', $options['natlang'] ); ?> value="ab">ab Abkhazian</option>
			<option <?php selected( 'af', $options['natlang'] ); ?> value="af">af Afrikaans</option>
			<option <?php selected( 'an', $options['natlang'] ); ?> value="am">am Amharic</option>
			<option <?php selected( 'ar', $options['natlang'] ); ?> value="ar">ar Arabic</option>
			<option <?php selected( 'as', $options['natlang'] ); ?> value="as">as Assamese</option>
			<option <?php selected( 'ay', $options['natlang'] ); ?> value="ay">ay Aymara</option>
			<option <?php selected( 'az', $options['natlang'] ); ?> value="az">az Azerbaijani</option>
			
			<option <?php selected( 'ba', $options['natlang'] ); ?> value="ba">ba Bashkir</option>
			<option <?php selected( 'be', $options['natlang'] ); ?> value="be">be Byelorussian</option>
			<option <?php selected( 'bg', $options['natlang'] ); ?> value="bg">bg Bulgarian</option>
			<option <?php selected( 'bh', $options['natlang'] ); ?> value="bh">bh Bihari</option>
			<option <?php selected( 'bi', $options['natlang'] ); ?> value="bi">bi Bislama</option>
			<option <?php selected( 'bn', $options['natlang'] ); ?> value="bn">bn Bengali; Bangla</option>
			<option <?php selected( 'bo', $options['natlang'] ); ?> value="bo">bo Tibetan</option>
			<option <?php selected( 'br', $options['natlang'] ); ?> value="br">br Breton</option>
			
			<option <?php selected( 'ca', $options['natlang'] ); ?> value="ca">ca Catalan</option>
			<option <?php selected( 'co', $options['natlang'] ); ?> value="co">co Corsican</option>
			<option <?php selected( 'cs', $options['natlang'] ); ?> value="cs">cs Czech</option>
			<option <?php selected( 'cy', $options['natlang'] ); ?> value="cy">cy Welsh</option>
			
			<option <?php selected( 'da', $options['natlang'] ); ?> value="da">da danish</option>
			<option <?php selected( 'de', $options['natlang'] ); ?> value="de">de german</option>
			<option <?php selected( 'dz', $options['natlang'] ); ?> value="dz">dz Bhutani</option>
			
			<option <?php selected( 'el', $options['natlang'] ); ?> value="el">el Greek</option>
			<option <?php selected( 'en', $options['natlang'] ); ?> value="en" selected>en English</option>
			<option <?php selected( 'eo', $options['natlang'] ); ?> value="eo">eo Esperanto</option>
			<option <?php selected( 'es', $options['natlang'] ); ?> value="es">es Spanish</option>
			<option <?php selected( 'et', $options['natlang'] ); ?> value="et">et Estonian</option>
			<option <?php selected( 'eu', $options['natlang'] ); ?> value="eu">eu Basque</option>
			
			<option <?php selected( 'fa', $options['natlang'] ); ?> value="fa">fa Persian</option>
			<option <?php selected( 'fi', $options['natlang'] ); ?> value="fi">fi Finnish</option>
			<option <?php selected( 'fj', $options['natlang'] ); ?> value="fj">fj Fiji</option>
			<option <?php selected( 'fo', $options['natlang'] ); ?> value="fo">fo Faeroese</option>
			<option <?php selected( 'fr', $options['natlang'] ); ?> value="fr">fr French</option>
			<option <?php selected( 'fy', $options['natlang'] ); ?> value="fy">fy Frisian</option>
			
			<option <?php selected( 'ga', $options['natlang'] ); ?> value="ga">ga Irish</option>
			<option <?php selected( 'gd', $options['natlang'] ); ?> value="gd">gd Scots Gaelic</option>
			<option <?php selected( 'gl', $options['natlang'] ); ?> value="gl">gl Galician</option>
			<option <?php selected( 'gn', $options['natlang'] ); ?> value="gn">gn Guarani</option>
			<option <?php selected( 'gu', $options['natlang'] ); ?> value="gu">gu Gujarati</option>
			
			<option <?php selected( 'ha', $options['natlang'] ); ?> value="en">ha Hausa</option>
			<option <?php selected( 'hi', $options['natlang'] ); ?> value="en">hi Hindi</option>
			<option <?php selected( 'hr', $options['natlang'] ); ?> value="en">hr Croatian</option>
			<option <?php selected( 'hu', $options['natlang'] ); ?> value="en">hu Hungarian</option>
			<option <?php selected( 'hy', $options['natlang'] ); ?> value="en">hy Armenian</option>
			
			<option <?php selected( 'ia', $options['natlang'] ); ?> value="ia">ia Interlingua</option>
			<option <?php selected( 'ie', $options['natlang'] ); ?> value="ie">ie Interlingue</option>
			<option <?php selected( 'ik', $options['natlang'] ); ?> value="ik">ik Inupiak</option>
			<option <?php selected( 'in', $options['natlang'] ); ?> value="in">in Indonesian</option>
			<option <?php selected( 'is', $options['natlang'] ); ?> value="is">is Icelandic</option>
			<option <?php selected( 'it', $options['natlang'] ); ?> value="it">it Italian</option>
			<option <?php selected( 'iw', $options['natlang'] ); ?> value="iw">iw Hebrew</option>
			
			<option <?php selected( 'ja', $options['natlang'] ); ?> value="ja">ja Japanese</option>
			<option <?php selected( 'ji', $options['natlang'] ); ?> value="ji">ji Yiddish</option>
			<option <?php selected( 'jw', $options['natlang'] ); ?> value="jw">jw Javanese</option>
			
			<option <?php selected( 'ka', $options['natlang'] ); ?> value="ka">ka Georgian</option>
			<option <?php selected( 'kk', $options['natlang'] ); ?> value="kk">kk Kazakh</option>
			<option <?php selected( 'kl', $options['natlang'] ); ?> value="kl">kl Greenlandic</option>
			<option <?php selected( 'km', $options['natlang'] ); ?> value="km">km Cambodian</option>
			<option <?php selected( 'kn', $options['natlang'] ); ?> value="kn">kn Kannada</option>
			<option <?php selected( 'ko', $options['natlang'] ); ?> value="ko">ko Korean</option>
			<option <?php selected( 'ks', $options['natlang'] ); ?> value="ks">ks Kashmiri</option>
			<option <?php selected( 'ku', $options['natlang'] ); ?> value="ku">ku Kurdish</option>
			<option <?php selected( 'ky', $options['natlang'] ); ?> value="ky">ky Kirghiz</option>
			
			<option <?php selected( 'la', $options['natlang'] ); ?> value="la">la Latin</option>
			<option <?php selected( 'ln', $options['natlang'] ); ?> value="ln">ln Lingala</option>
			<option <?php selected( 'lo', $options['natlang'] ); ?> value="lo">lo Laothian</option>
			<option <?php selected( 'lt', $options['natlang'] ); ?> value="lt">lt Lithuanian</option>
			<option <?php selected( 'lv', $options['natlang'] ); ?> value="lv">lv Latvian, Lettish</option>
			
			<option <?php selected( 'mg', $options['natlang'] ); ?> value="mg">mg Malagasy</option>
			<option <?php selected( 'mi', $options['natlang'] ); ?> value="mi">mi Maori</option>
			<option <?php selected( 'mk', $options['natlang'] ); ?> value="mk">mk Macedonian</option>
			<option <?php selected( 'ml', $options['natlang'] ); ?> value="ml">ml Malayalam</option>
			<option <?php selected( 'mn', $options['natlang'] ); ?> value="mn">mn Mongolian</option>
			<option <?php selected( 'mo', $options['natlang'] ); ?> value="mo">mo Moldavian</option>
			<option <?php selected( 'mr', $options['natlang'] ); ?> value="mr">mr Marathi</option>
			<option <?php selected( 'ms', $options['natlang'] ); ?> value="ms">ms Malay</option>
			<option <?php selected( 'mt', $options['natlang'] ); ?> value="mt">mt Maltese</option>
			<option <?php selected( 'my', $options['natlang'] ); ?> value="my">my Burmese</option>
			
			<option <?php selected( 'na', $options['natlang'] ); ?> value="na">na Nauru</option>
			<option <?php selected( 'ne', $options['natlang'] ); ?> value="ne">ne Nepali</option>
			<option <?php selected( 'nl', $options['natlang'] ); ?> value="nl">nl Dutch</option>
			<option <?php selected( 'no', $options['natlang'] ); ?> value="no">no Norwegian</option>
			
			<option <?php selected( 'oc', $options['natlang'] ); ?> value="oc">oc Occitan</option>
			<option <?php selected( 'om', $options['natlang'] ); ?> value="om">om (Afan) Oromo</option>
			<option <?php selected( 'or', $options['natlang'] ); ?> value="or">or Oriya</option>
			
			<option <?php selected( 'pa', $options['natlang'] ); ?> value="pa">pa Punjabi</option>
			<option <?php selected( 'pl', $options['natlang'] ); ?> value="pl">pl Polish</option>
			<option <?php selected( 'ps', $options['natlang'] ); ?> value="ps">ps Pashto, Pushto</option>
			<option <?php selected( 'pt', $options['natlang'] ); ?> value="pt">pt Portuguese</option>
			
			<option <?php selected( 'qu', $options['natlang'] ); ?> value="qu">qu Quechua</option>
			
			<option <?php selected( 'rm', $options['natlang'] ); ?> value="rm">rm Rhaeto-Romance</option>
			<option <?php selected( 'rn', $options['natlang'] ); ?> value="rn">rn Kirundi</option>
			<option <?php selected( 'ro', $options['natlang'] ); ?> value="ro">ro Romanian</option>
			<option <?php selected( 'ru', $options['natlang'] ); ?> value="ru">ru Russian</option>
			<option <?php selected( 'rw', $options['natlang'] ); ?> value="rw">rw Kinyarwanda</option>
			
			<option <?php selected( 'sa', $options['natlang'] ); ?> value="sa">sa Sanskrit</option>
			<option <?php selected( 'sd', $options['natlang'] ); ?> value="sd">sd Sindhi</option>
			<option <?php selected( 'sg', $options['natlang'] ); ?> value="sg">sg Sangro</option>
			<option <?php selected( 'sh', $options['natlang'] ); ?> value="sh">sh Serbo-Croatian</option>
			<option <?php selected( 'si', $options['natlang'] ); ?> value="si">si Singhalese</option>
			<option <?php selected( 'sk', $options['natlang'] ); ?> value="sk">sk Slovak</option>
			<option <?php selected( 'sl', $options['natlang'] ); ?> value="sl">sl Slovenian</option>
			<option <?php selected( 'sm', $options['natlang'] ); ?> value="sm">sm Samoan</option>
			<option <?php selected( 'sn', $options['natlang'] ); ?> value="sn">sn Shona</option>
			<option <?php selected( 'so', $options['natlang'] ); ?> value="so">so Somali</option>
			<option <?php selected( 'sq', $options['natlang'] ); ?> value="sq">sq Albanian</option>
			<option <?php selected( 'sr', $options['natlang'] ); ?> value="sr">sr Serbian</option>
			<option <?php selected( 'ss', $options['natlang'] ); ?> value="ss">ss Siswati</option>
			<option <?php selected( 'st', $options['natlang'] ); ?> value="st">st Sesotho</option>
			<option <?php selected( 'su', $options['natlang'] ); ?> value="su">su Sudanese</option>
			<option <?php selected( 'sv', $options['natlang'] ); ?> value="sv">sv Swedish</option>
			<option <?php selected( 'sw', $options['natlang'] ); ?> value="sw">sw Swahili</option>
			
			<option <?php selected( 'ta', $options['natlang'] ); ?> value="ta">ta Tamil</option>
			
			<option <?php selected( 'te', $options['natlang'] ); ?> value="te">te Telugu</option>
			<option <?php selected( 'tg', $options['natlang'] ); ?> value="tg">tg Tajik</option>
			<option <?php selected( 'th', $options['natlang'] ); ?> value="th">th Thai</option>
			<option <?php selected( 'ti', $options['natlang'] ); ?> value="ti">ti Tigrinya</option>
			<option <?php selected( 'tk', $options['natlang'] ); ?> value="tk">tk Turkmen</option>
			<option <?php selected( 'tl', $options['natlang'] ); ?> value="tl">tl Tagalog</option>
			<option <?php selected( 'tn', $options['natlang'] ); ?> value="tn">tn Setswana</option>
			<option <?php selected( 'to', $options['natlang'] ); ?> value="to">to Tonga</option>
			<option <?php selected( 'tr', $options['natlang'] ); ?> value="tr">tr Turkish</option>
			<option <?php selected( 'ts', $options['natlang'] ); ?> value="ts">ts Tsonga</option>
			<option <?php selected( 'tt', $options['natlang'] ); ?> value="tt">tt Tatar</option>
			<option <?php selected( 'tw', $options['natlang'] ); ?> value="tw">tw Twi</option>
			
			<option <?php selected( 'uk', $options['natlang'] ); ?> value="uk">uk Ukrainian</option>
			<option <?php selected( 'ur', $options['natlang'] ); ?> value="ur">ur Urdu</option>
			<option <?php selected( 'uz', $options['natlang'] ); ?> value="uz">uz Uzbek</option>
			
			<option <?php selected( 'vi', $options['natlang'] ); ?> value="vi">vi Vietnamese</option>
			<option <?php selected( 'vo', $options['natlang'] ); ?> value="vo">vo Volapuk</option>
			
			<option <?php selected( 'wo', $options['natlang'] ); ?> value="ewo">wo Wolof</option>
			
			<option <?php selected( 'xh', $options['natlang'] ); ?> value="xh">xh Xhosa</option>
			
			<option <?php selected( 'yo', $options['natlang'] ); ?> value="yo">yo Yoruba</option>
			
			<option <?php selected( 'zh', $options['natlang'] ); ?> value="zh">zh Chinese</option>
			<option <?php selected( 'zu', $options['natlang'] ); ?> value="zu">zu Zulu</option>
		 </select>
		</td>
		</tr>
		<tr valign="top">
		<th scope="row">UI Language Code</th>
		<td>
		 <select name="ryuzine_opt_addons[language]">
			<option <?php selected( 'da', $options['language'] ); ?> value="da">da Danish</option>
			<option <?php selected( 'de', $options['language'] ); ?> value="de">de German</option>
			<option <?php selected( 'el', $options['language'] ); ?> value="el">el Greek</option>
			<option <?php selected( 'en', $options['language'] ); ?> value="en" selected>en English</option>
			<option <?php selected( 'es', $options['language'] ); ?> value="es">es Spanish</option>
			<option <?php selected( 'fr', $options['language'] ); ?> value="fr">fr French</option>
			<option <?php selected( 'hi', $options['language'] ); ?> value="hi">hi Hindi</option>
			<option <?php selected( 'it', $options['language'] ); ?> value="it">it Italy</option>
			<option <?php selected( 'ja', $options['language'] ); ?> value="ja">ja Japanese</option>
			<option <?php selected( 'ko', $options['language'] ); ?> value="ko">ko Korean</option>
			<option <?php selected( 'no', $options['language'] ); ?> value="no">hi Norwegian</option>
			<option <?php selected( 'pt', $options['language'] ); ?> value="pt">pt Portuguese</option>
			<option <?php selected( 'ru', $options['language'] ); ?> value="ru">ru Russian</option>
			<option <?php selected( 'sv', $options['language'] ); ?> value="sv">sv Swedish</option>
			<option <?php selected( 'zh_HANS', $options['language'] ); ?> value="zh_HANS">zh_HANS Simplified  Chinese</option>
			<option <?php selected( 'zh_HANT', $options['language'] ); ?> value="zh_HANT">zh_HANT Traditional Chinese</option>
		 </select>
		</td>
		</tr>
		</table>
		
		<h3>Optional Add-Ons</h3>
		<p>Select which, if any, of the available Add-Ons you would like to use with your Ryuzine Press Editions.  They will be loaded in the 
		order you select them.</p>
<?php
// GET LIST OF INSTALLED ADD-ONS
$addons = glob(ryuzine_pluginfo('plugin_path').'ryuzine/addons/*' , GLOB_ONLYDIR);
// strip out relative path
$nfo = array();
for ($a=0;$a<count($addons);$a++) {
	$addons[$a] = preg_replace("~".ryuzine_pluginfo('plugin_path')."ryuzine/addons/~", "", $addons[$a] );
	$fileinfo = array();
	if (file_exists(ryuzine_pluginfo('plugin_path').'ryuzine/addons/'.$addons[$a].'/'.$addons[$a].'.config.js')) {
		$exists = true;
		$file = new SplFileObject(ryuzine_pluginfo('plugin_path').'ryuzine/addons/'.$addons[$a].'/'.$addons[$a].'.config.js');
		$fileIterator = new LimitIterator($file, 1, 6);
		foreach($fileIterator as $line) {
			$line = preg_replace("/\'|\"|,/",'',$line);
			$line = preg_split("/:(?!\/\/)/",$line);
			$fileinfo=array_merge($fileinfo,array(''.trim($line[0]).''=>''.trim($line[1]).''));
		}
	} else { $exists = false;}
	if ($exists == false) {
		$about = '<span style="color:red;">The config file for this add-on is missing.  If loaded it will not work.  You should remove it from the /addons folder.</span>';
	} else {
		$about = 'The config file did not contain information about this add-on.';
	}
	// now, catch missing or empty info
	$fillinfo = array(
		'name' => $addons[$a],
		'version' => 'Unknown',
		'author' => 'Unknown',
		'url' => '#',
		'license' => 'Unknown',
		'about' => $about		
	);
	foreach($fillinfo as $key=>$val) {
		if ( isset($fileinfo[$key]) || array_key_exists($key,$fileinfo)) {
			if ($fileinfo[$key]=='' || $fileinfo['name']==null) {
				$fileinfo[$key] = $val;
			}
		} else {
			$fileinfo[$key] = $val;
		}
	}
	$nfo[] = $fileinfo;
} 

?>
<table class="wp-list-table widefat plugins" id="available_addons">
<thead>
	<tr>
		<th scope="col" class="manage-column column-cb check-column">
			<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
			<input type="checkbox" onclick="if(this.checked){all_selected(true);}else{all_selected(false);}">
		</th>
		<th scope="col" class="manage-column column-name">Add-On</th>
		<th scope="col" class="manage-column column-description">Description</th>
	</tr>
</thead>
<tbody>
<?php 
for ($a=0;$a<count($addons);$a++) {
	if ($addons[$a]!='socialwidget') { // socialwidget is automatic if no wp widget loads
	echo '<tr class="inactive"><th scope="row" class="check-column">';
	echo '<input type="checkbox" value="'.$addons[$a].'" onclick="if(this.checked){add_selected(this.value);}else{cut_selected(this.value);}" /></th><td class="plugin-title"><strong>'.$nfo[$a]['name'].'</strong></td>';
	echo '<td class="column-description desc"><div class="plugin-description"><p>'.$nfo[$a]['about'].'</p></div><div class="second plugin-version-author-uri">Version '.$nfo[$a]['version'].' | <a href="'.$nfo[$a]['url'].'">'.$nfo[$a]['author'].'</a> | '.$nfo[$a]['license'].' License</div>';
	echo '</td></tr>';
	}
} 
	echo '</tbody><tfoot><tr>';
	echo '	<th scope="col" class="manage-column column-cb check-column">';
	echo '		<label class="screen-reader-text" for="cb-select-all-1">Select All</label>';
	echo '		<input type="checkbox" onclick="if(this.checked){all_selected(true);}else{all_selected(false);}">';
	echo '	</th>';
	echo '	<th scope="col" class="manage-column column-name">Add-On</th>';
	echo '	<th scope="col" class="manage-column column-description">Description</th>';
	echo '</tr>';
	echo '</tfoot>';
	echo '<table>';
	echo '<p id="load_order"><strong>Add-On Load Order:</strong> (no addons will be loaded) </p>';
	echo '<input name="ryuzine_opt_addons[selected_addons]" type="hidden" id="ryuzine_opt_addons[selected_addons]" value="'.$options['selected_addons'].'"/>';
?>

<script type="text/javascript">
if (document.getElementById('ryuzine_opt_addons[selected_addons]').value != '') {
	var selected_addons = document.getElementById('ryuzine_opt_addons[selected_addons]').value.split(',');
	var checkboxes = document.getElementById('available_addons').getElementsByTagName('input');
	var addons = [];
	for (var s=selected_addons.length-1;s>=0;s--) {
		for (var c=1; c < checkboxes.length-1; c++) {
			if (selected_addons[s]==checkboxes[c].value) {
				checkboxes[c].checked = true;
				checkboxes[c].parentNode.parentNode.className="active";
			}
			addons.push(''+checkboxes[c].value+'');
		}
		if (addons.indexOf(selected_addons[s])==-1) {	// list has an add-on that is not in folder!
			selected_addons.splice(s,1);	// remove from list
		}
	}
	document.getElementById('ryuzine_opt_addons[selected_addons]').value = selected_addons;	// revise value
	document.getElementById('load_order').innerHTML = '<strong>Add-On Load Order:</strong> '+selected_addons.toString().replace(/,/gi,', ');
} else {
	var selected_addons = [];
}
function all_selected(state) {
	selected_addons = [];	// clear array
	if (state==true) {
		for (var c=1; c < checkboxes.length-1; c++) {
			add_selected(checkboxes[c].value);
			checkboxes[c].parentNode.parentNode.className="active";
		}
	} else {
		for (var c=1; c < checkboxes.length-1; c++) {
			cut_selected(checkboxes[c].value);
			checkboxes[c].parentNode.parentNode.className="inactive";
		}
	}
	
}
function add_selected(value) {
	if (selected_addons.indexOf(''+value+'')==-1) {
		selected_addons.push(''+value+'');
	}
	document.getElementById('ryuzine_opt_addons[selected_addons]').value = selected_addons;
	document.getElementById('load_order').innerHTML = '<strong>Add-On Load Order:</strong> '+selected_addons.toString().replace(/,/gi,', ');
	update_localization();
}
function cut_selected(value) {
	if (selected_addons.indexOf(''+value+'')!=-1) {
		selected_addons.splice(selected_addons.indexOf(''+value+''),1);
	}
	document.getElementById('ryuzine_opt_addons[selected_addons]').value = selected_addons;
	document.getElementById('load_order').innerHTML = '<strong>Add-On Load Order:</strong> '+selected_addons.toString().replace(/,/gi,', ');
	update_localization();
}
function update_localization() {
	if (selected_addons.indexOf('localize') !== -1) {
		document.getElementById("ryuzine_opt_addons[localization]").value = 1;
	} else {
		document.getElementById("ryuzine_opt_addons[localization]").value = 0;
	}
}
</script>
<input name="ryuzine_opt_addons[localization]" id="ryuzine_opt_addons[localization]" type="hidden" value="<?php echo $options['localization']; ?>" />		
		<h3>Ryuzine Theme Settings</h3>
		<p>Ryuzine themes change the appearance of the webapp User Interface.  You can also send different themes to different 
		platforms (for example to give your publication a "native app" appearance). These themes are completely separate from your WordPress theme. All themes must be installed in:<em><?php echo plugin_dir_path( dirname( __FILE__ )); ?>ryuzine/theme/</em></p> 

		<table class="form-table">
		<tr valign="top">
		<th scope="row">Page Shadows</th>
		<td>
    	<input name="ryuzine_opt_addons[pageshadow]" type="radio" value="0" <?php checked( '0', $options['pageshadow'] ); ?>/> Off
		<input name="ryuzine_opt_addons[pageshadow]" type="radio" value="1" <?php checked( '1', $options['pageshadow'] ); ?> /> On		
		<br /><small>If disabled page shadows only work if user manually enables them in webapp's Options panel</small>
		</td>
		</tr>
		<tr>
		<th scope="row">Default Theme</th>
		<td>
<?php
// GET LIST OF INSTALLED THEMES
$themes = glob(ryuzine_pluginfo('plugin_path').'ryuzine/theme/*' , GLOB_ONLYDIR);
// strip out relative path
for ($t=0;$t<count($themes);$t++) {
	$themes[$t] = preg_replace("~".ryuzine_pluginfo('plugin_path')."ryuzine/theme/~", "", $themes[$t] );
} ?>
<select name="ryuzine_opt_addons[defaultTheme]">
	<option value="">None</option>
<?php 
for ($t=0;$t<count($themes);$t++) { 
	if ($options['defaultTheme'] == $themes[$t]) { $selected = 'selected';} else { $selected = '';};
	echo '<option value="'.$themes[$t].'" '.$selected.'>'.$themes[$t].'</option>';
} ?>
</select>
		<br /><small>This theme is loaded in the head of the Ryuzine.  Use this if you want to theme your publication, but are not swapping themes or have a base theme you want to load with the page.  Users can still "untheme" the interface by turning it off in the webapp's Options panel.</small>
		</td></tr>
		<tr>
		<th scope="row">Swap Themes</th>
		<td>
    	<input name="ryuzine_opt_addons[swapThemes]" type="radio" value="0" <?php checked( '0', $options['swapThemes'] ); ?>/> Off
		<input name="ryuzine_opt_addons[swapThemes]" type="radio" value="1" <?php checked( '1', $options['swapThemes'] ); ?> /> On
		<br /><small>If disabled Platform Themes below are ignored and only default theme is used</small>
		</td>
		</tr>
		<tr>
		<th scope="row">Platform Themes</th>
		<td>
<?php
$platforms = array(
	array('deskTheme','Desktop General/Fallback'),
	array('winTheme','Windows Systems'),
	array('macTheme','Mac OS Systems'),
	array('nixTheme','Linux Systems'),
	array('iOSTheme','iOS Devices'),
	array('andTheme','Android Devices'),
	array('wp7Theme','Windows Phone 7'),
	array('w8mTheme','Windows 8 Metro View'),
	array('bbtTheme','BlackBerry Devices')
);
for ($pf=0; $pf<count($platforms);$pf++) {	
	echo '<select name="ryuzine_opt_addons['.$platforms[$pf][0].']">';
	echo '	<option value="">None</option>'; 
	for ($t=0;$t<count($themes);$t++) { 
		if ($options[$platforms[$pf][0]] == $themes[$t]) { $selected = 'selected';} else { $selected = '';};
		echo '<option value="'.$themes[$t].'" '.$selected.'>'.$themes[$t].'</option>';
	};
	echo '</select> '.$platforms[$pf][1].'<br/>';
} ?>

		</td>
		</tr>
		</table>
</div>
<?php 	break;
		case 'ads':
?>
<form method="post" enctype="multipart/form-data" action="options.php">
	<?php   $tab = $_GET['tab']; ?>
	<?php settings_fields('ryuzine_opt_ads'); ?>
	<?php do_settings_sections('ryuzine-settings&tab=ads');  ?>
	<?php $options = get_option('ryuzine_opt_ads'); ?>
<div class="tabbox">
		<p>Ryuzine Press has three built-in spaces to place advertisements that are outside the page content of the magazine.  These spaces include the webapp's "Splash Page," a box-ad using the built-in 
		lightbox feature, and a mobile-style banner-ad space like those commonly seen on ad-supported mobile apps.
		<p><em>Please Note: Revenue generating publications require a Commercial Use License available at <a href="http://www.ryumaru.com/products/ryuzine/downloads/" target="_blank">www.ryumaru.com</a>.  
		Please read the license file or email sales@ryumaru.com if you are not sure if you need a Commercial Use license. 
		Only advertising placed in the Ryuzine webapp itself is taken into consideration for licensing terms, advertising incorporated into your regular blog theme is not subject to the Ryuzine licensing requirements.</em></p>


		<h3>Ad Display Settings</h3>
		<p>Place advertisements with the options below this section.</p>

		<table class="form-table">
        <tr valign="top">
        <th scope="row">Splash Ad</th>
        <td><input type="text" id="ryuzine_opt_ads[splashad]" name="ryuzine_opt_ads[splashad]" value="<?php echo $options['splashad']; ?>" /> ( 0 = disabled | value = seconds to display | p = persistent )<br/><small>Sets number of seconds to display splash screen (normally it is dismissed automatically on page load completion)</small></td>
        </tr>
        <tr valign="top">
        <th scope="row">Box Ad</th>
        <td><input type="text" name="ryuzine_opt_ads[boxad]" value="<?php echo $options['boxad']; ?>" /> ( 0 = disabled | value = seconds to display | p = persistent )<br /><small>Length of time in seconds to display a lightboxed ad</small></td>
        </tr>
        <tr valign="top">
        <th scope="row">Banner Ad</th>
        <td><input type="text" name="ryuzine_opt_ads[bannerad]" value="<?php echo $options['bannerad']; ?>" /> ( 0 = disabled | value = seconds to display | p = persistent )<br /><small>Interval in seconds to hide/display a mobile-style banner ad</small></td>
		</tr>
		</table>
		
		<h3>Ad Content Settings</h3>
		<p>For each of the advertising spaces you can choose one image with a link or opt for some other kind of content (which allows you to embed Flash ads or place a script for ad services or just to rotate banners).</p>
		
		<table class="form-table">
        <tr valign="top">
        <th scope="row">Splash Ad Content Type</th>
        <td>
    	<input name="ryuzine_opt_ads[splashtype]" type="radio" value="0" <?php checked( '0', $options['splashtype'] ); ?> onclick="if(this.checked){document.getElementById('splashadsection').className='enabled';document.getElementById('splash_image').className='url_box';document.getElementById('splash_image_button').style.display='inline';}"/> Image with Link
		<input name="ryuzine_opt_ads[splashtype]" type="radio" value="1" <?php checked( '1', $options['splashtype'] ); ?> onclick="if(this.checked){document.getElementById('splashadsection').className='enabled';document.getElementById('splash_image').className='text_area';document.getElementById('splash_image_button').style.display='none';}"/> Other Type
		<input name="ryuzine_opt_ads[splashoff]" type="checkbox" class="offchk" value="1" <?php if (isset($options['splashoff'])) { checked('1', $options['splashoff']);} ?> onclick="if(this.checked){document.getElementById('splashadsection').className='disabled';document.getElementById('ryuzine_opt_ads[splashad]').value='0';}else{document.getElementById('splashadsection').className='enabled';}"/> Disable this ad
		</td>
        </tr>
        <tr valign="top">
        <th scope="row">Splash Ad Content<br/><small>This replaces the blog name or masthead image on the splash page with an advertisement.</small></th>
        <td>
        <div id="splashadsection" class="enabled">
		<label for="upload_image" class="uploader">
			<textarea id="splash_image" name="ryuzine_opt_ads[splashcontent]" class="url_box"><?php echo $options['splashcontent']; ?></textarea>
			<input id="splash_image_button" class="button" type="button" value="Media Library" />
			<br />Select an image from your Media Library, Upload a new image, or enter an image URL.<br />
			<input type="text" name="ryuzine_opt_ads[splashlink]" size="36" value="<?php echo $options['splashlink']; ?>" /> Ad Link URL<br />
			<?php if ( $options['splashcontent'] != "" || $options['splashcontent'] != null ) {
				echo '<div style="border:1px solid #ccc;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;padding: 0px 10px 10px;overflow:auto;max-height:250px;max-width:550px;background:#F8F8F8 ;"><h4>Splash Ad Preview:</h4>';
				echo '<img id="splashadpreview" src="'.$options['splashcontent'].'" />';
				echo '</div>';
			}
			?>
			</label>
		</div>
        </td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Box Ad Content Type</th>
        <td>
    	<input name="ryuzine_opt_ads[boxadtype]" type="radio" value="0" <?php checked( '0', $options['boxadtype'] ); ?> onclick="if(this.checked){document.getElementById('boxad_image').className='url_box';document.getElementById('boxad_image_button').style.display='inline';}"/> Image with Link
		<input name="ryuzine_opt_ads[boxadtype]" type="radio" value="1" <?php checked( '1', $options['boxadtype'] ); ?> onclick="if(this.checked){document.getElementById('boxad_image').className='text_area';document.getElementById('boxad_image_button').style.display='none';}"/> Other Type
		</td>
        </tr>
        <tr valign="top">
        <th scope="row">Box Ad Content</th>
        <td>
		<label for="upload_image" class="uploader">
			<textarea id="boxad_image" name="ryuzine_opt_ads[boxadcontent]" class="url_box" ><?php echo $options['boxadcontent']; ?></textarea>
			<input id="boxad_image_button" class="button" type="button" value="Media Library" />
			<br />Select an image from your Media Library, Upload a new image, or enter an image URL.<br />
			<input type="text" name="ryuzine_opt_ads[boxlink]" size="36" value="<?php echo $options['boxlink']; ?>" /> Ad Link URL<br />
			<?php if ( $options['boxadcontent'] != "" ) {
				echo '<div style="border:1px solid #ccc;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;padding: 0px 10px 10px;overflow:auto;max-height:250px;max-width:550px;background:#F8F8F8 ;"><h4>Box Ad Preview:</h4>';
				echo '<img id="boxadpreview" src="'.$options['boxadcontent'].'" />';
				echo '</div>';
			}
			?>
			</label>
        </td>
        </tr>
        
       <tr valign="top">
        <th scope="row">Banner Ad Content Type</th>
        <td>
    	<input name="ryuzine_opt_ads[bannertype]" type="radio" value="0" <?php checked( '0', $options['bannertype'] ); ?> onclick="if(this.checked){document.getElementById('banner_image').className='url_box';document.getElementById('banner_image_button').style.display='inline';}"/> Image with Link
		<input name="ryuzine_opt_ads[bannertype]" type="radio" value="1" <?php checked( '1', $options['bannertype'] ); ?> onclick="if(this.checked){document.getElementById('banner_image').className='text_area';document.getElementById('banner_image_button').style.display='none';}"/> Other Type
		</td>
        </tr>
        <tr valign="top">
        <th scope="row">Banner Ad Content</th>
        <td>
		<label for="upload_image" class="uploader">
			<textarea id="banner_image" class="url_box" name="ryuzine_opt_ads[bannercontent]" size="36" ><?php echo $options['bannercontent']; ?></textarea>
			<input id="banner_image_button" class="button" type="button" value="Media Library" />
			<br />Select an image from your Media Library, Upload a new image, or enter an image URL.<br />
			<input type="text" name="ryuzine_opt_ads[bannerlink]" size="36" value="<?php echo $options['bannerlink']; ?>" /> Ad Link URL<br />
			<?php if ( $options['bannercontent'] != "" || $options['bannercontent'] != null ) {
				echo '<div style="border:1px solid #ccc;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;padding: 0px 10px 10px;overflow:auto;max-height:250px;max-width:550px;background:#F8F8F8 ;"><h4>Banner Ad Preview:</h4>';
				echo '<img id="bannerpreview" src="'.$options['bannercontent'].'" />';
				echo '</div>';
			}
			?>
			</label>
        </td>
        </tr>        
        
        
		</table>
			<script type="text/javascript">
				var splashtype = <?php echo $options['splashtype']; ?>;
				var splashoff = <?php if(isset($options['splashoff'])){ echo $options['splashoff'];}else{ echo "0";}; ?>;
				if (splashtype == "1" ) {
					document.getElementById('splash_image').className = "text_area";
					document.getElementById('splash_image_button').style.display = "none";
				} else {
					document.getElementById('splash_image').className = "url_box";
					document.getElementById('splash_image_button').style.display = "inline";
				}
				if (splashoff == "1" ) {
					document.getElementById('splashadsection').className = "disabled";
				} else {
					document.getElementById('splashadsection').className = "enabled";
				}
				var boxadtype = <?php echo $options['boxadtype']; ?>;
				if (boxadtype == "1" ) {
					document.getElementById('boxad_image').className = "text_area";
					document.getElementById('boxad_image_button').style.display = "none";
				} else {
					document.getElementById('boxad_image').className = "url_box";
					document.getElementById('boxad_image_button').style.display = "inline";
				}
				var bannertype = <?php echo $options['bannertype']; ?>;
				if (bannertype == "1" ) {
					document.getElementById('banner_image').className = "text_area";
					document.getElementById('banner_image_button').style.display = "none";
				} else {
					document.getElementById('banner_image').className = "url_box";
					document.getElementById('banner_image_button').style.display = "inline";
				}
			</script>		
</div>	
<?php	break;
		case 'shortcodes':
?>
<form method="post" enctype="multipart/form-data" action="options.php">
	<?php   $tab = $_GET['tab']; ?>
	<?php settings_fields('ryuzine_opt_lightbox'); ?>
	<?php do_settings_sections('ryuzine-settings&tab=shortcodes');  ?>
	<?php $options = get_option('ryuzine_opt_lightbox'); ?>
<div class="tabbox">
	<h3>Ryuzine Lightbox</h3>
	<p>Even if you are using a lightbox plugin or script elsewhere on your blog it probably won't work in your Ryuzine Press editions 
	because they do not use the standard WordPress headers and footers (where the lightbox plugins typically enqueue their scripts).  
	So Ryuzine Press has its own lightbox add-on, which is accessible through two WordPress shortcodes.  Note that these <em>only</em> produce a 
	lightbox effect if you also enabled a lightbox add-on in <em>Ryuzine Press > Options > Add-Ons</em>, otherwise the content will be displayed 
	in a simple dialog box.</p>
	<p>When you go to <em>Ryuzine > Add New</em> you can create your "lightbox gallery" in the post compose textbox.  Define each item 
	with the shortcode:</p>
	<blockquote>
	<strong>[ryubox id="</strong><em>your_id</em><strong>" orient="</strong><em>land|port</em><strong>]. . .whatever content you want in the lightbox. . .[/ryubox]</strong>
	</blockquote>
	<p>You can use anything you want for the "id" value, you'll just need to know what it is when you create the link to open it.  The "orient" attribute is optional for landscape 
	orientation content ("land" is automatically assumed), however if the content in the lightbox is portrait orientation set the value to "port" so it will fit by height, rather than width, in the webapp.</p>
	<p>The lightbox is primarily intended for displaying images, but it can also be used to showcase other content.  However, if you use non-image content either code it with max-width and max-height or percentages 
	so it can scale, or add scaling by media-query in the issue stylesheet.  If you don't the content may not fit in the browser window on smaller screen devices.</p>
	<p><em>Note: The [ryubox] shortcode ONLY works inside Ryuzine Press posts</em></p>
	<h3>Lightbox Links</h3>
	<p>The new lightbox link method doesn't require a shortcode, so you can enter links similar to this in your body text:</p>
	<blockquote>
	<strong>&lt;a href="</strong><em>your_url</em><strong>" rel="lightbox" class="</strong><em>lb_video</em><strong>"&gt;...link text or thumbnail image...</em><strong>&lt;/a&gt;</strong>
	</blockquote>
	<p>If you still want to use the shortcode, though, (for example to hide the links on your regular blog front-end) you can enter them like this:</p>
	<blockquote>
	<strong>[ryuboxlink url="</strong><em>your_url</em></strong>" type="</strong><em>video</em><strong>"]</strong><em>. . .link text or (preferably) thumbnail image. . .</em><strong>[/ryuboxlink]</strong>
	</blockquote>
	<p>Whether you use the &lt;a&gt; tags or the shortcode, you have a number of additional parameters you can enter:</p>
	<table border="1">
		<tr>
			<td nowrap><strong>&lt;a rel="lightbox"&gt;</strong></td><td><strong>[ryuboxlink]</strong></td><td><strong>Description</strong></td>
		</tr><tr>
			<td>href</td><td>url</td><td>Use "#" for embedded lightbox content, or just enter any URL if not embedded.</td>
		</tr><tr>
			<td nowrap>data-linkid</td><td>id</td><td>"id" maps to href="#[id]" (for backwards compatibility with existing links using old method)</td>
		</tr><tr>
			<td nowrap>data-layout</td><td>layout</td><td>whether image is "landscape" or "portrait" orientation which may or may not be used by the lightbox script.</td>
		</tr><tr>
			<td>title</td><td>title</td><td>Revealed on mouseover of link, some lightbox scripts may use it for the caption</td>
		</tr><tr>
			<td>class</td><td>type</td><td>Entered as class="lb_video" in &lt;a&gt; tags, type="video" in [ryuboxlink] shortcode.  Valid types are:<br/>
			<em>3d, document, info, larger, magnify, movie, photo, video, website, minus</em><br/>
			Supplying no link type will use the standard [+] icon.</td>
		</tr><tr>
			<td nowrap>data-caption</td><td>caption</td><td>Some scripts will use this to generate a caption. Useful for scripts that will lightbox offsite content.</td>
		</tr><tr>
			<td nowrap>data-gallery</td><td>gallery</td><td>An arbitrary name for the gallery to which this link content belongs (if any).</td>
		</tr><tr>
			<td nowrap>data-aspect</td><td>aspect</td><td>square (or omit) = 4:3 | wide = 16:9 | book = 3:4 | tall = 9:16</td>
		</tr><tr>
	</table>
	<p><strong>IMPORTANT:</strong> <em>If you wrap a thumbnail image in a [ryuboxlink] shortcode make sure that image does NOT have a link set for it!  If it does it will try to nest an &lt;a&gt; tag inside another &lt;a&gt; which will prevent the wrapped content from being shown.</em></p>
	<p>Normally any [ryuboxlink] content will not be visible in the 
	post when viewed in your regular blog theme.  However, if you want it to appear in the blog as well (for example to trigger a regular 
	WordPress plugin's lightbox effect) change the setting below:</p>
    <table class="form-table">
    <tr valign="top">
        <th scope="row">Lightbox Links</th>
        <td>
        <input name="ryuzine_opt_lightbox[links]" type="radio" value="0" <?php checked( '0', $options['links'] ); ?> /> Hide Lightbox Links elsewhere on blog<br/>
		<input name="ryuzine_opt_lightbox[links]" type="radio" value="1" <?php checked( '1', $options['links'] ); ?> /> Show Lightbox Links content on blog<br />
		</td>
        </tr>
    </table>
    <h3>Cover Image &amp; Promotions</h3>
    <p><strong>[ryucover bleed="<strong><em>0|1|cover|2|contain|3|width|4|height</em></strong>" color="<strong><em>white|blue|#ccc</em></strong>" shift="<strong><em>Xpx Ypx</em></strong>"].....[/ryucover]</strong></p>
    <p>In your Ryuzine Press editions you can now surround an image placed from your Media Library with the "ryucover" shortcode.  This 
    image is used by the optional Ryuzine Rack newsstand webapp (which replaces the auto-generated Archive page) and is also used if you 
    have set "Use [ryucover] shortcode image" as the Cover Source under the "Covers" tab above.</p>
    <p>The [ryucover] shortcode now accepts up to three optional parameters, "bleed," "color," and/or "shift." The "bleed" option accepts the following settings:</p>
    <ul><li><strong>0 | null :</strong> Set it to zero or no value (or omit the parameter entirely) and any image wrapped by the shortcode will be embedded in the page content, rather than set as the cover's background.</li>
	<li><strong>1 | cover :</strong> Fits the image, maintaining the aspect ratio, to the LARGEST size it can be that fits BOTH the width and height.</li>
	<li><strong>2 | contain :</strong> Fits the image, maintaining the aspect ratio, to the SMALLEST size that will fit BOTH the width and height.</li>
    <li><strong>3 | width :</strong> Will take any image wrapped in the shortcode and set it as the cover's background, center it, and scale (maintaining the aspect ratio) by width.  Any part of the image that does not fit spills beyond the borders of the page.</li>
    <li><strong>4 | height :</strong> Will scale the background image (maintaining aspect ratio) to fit by height with any parts of the image that do not fit spilling beyond the borders of the page.</li>
    </ul>
    <p>Generally the "cover" and "width" settings are similar and "contain" and "height" are similar, but they may render with slightly different bordering if they are not an exact fit for the space.</p>
    <p>The "color" parameter, if omitted or left empty, defaults to "white."  Otherwise you can set it to any valid HTML color by name (such as "blue"), hex code ("#cccccc"), hex code shorthand ("#ccc"), or rgb/rgba color set.  If you set an invalid 
    value your background image also will not show</p>
    <p>The "shift" parameter allows you to adjust the positioning of the background image.  The default position is "center 0" if you omit this parameter, which is what you most likely want.  However, if you want to change the positioning the syntax is "Xpx Ypx" with a space between the values, which need to be set in pixels. 
    Other valid values include "center center" (may move top of image out of view), "0 0" (does not need "px"), "left top" (same as "0 0"), "right bottom" (or similar word combinations).</p>
    <p><strong>[ryupromo].....[/ryupromo]</strong></p>
    <p>If you include a promotional image it is used by the optional Ryuzine Rack in the animated carousel at the top of the webapp.  Ryuzine Rack 
    will always grab the newest item and promote it (if there is no promo image it automatically builds a promotion using the catalog info).  Additional 
    promotions (if any) are displayed by date, newest to oldest, up to the maximum number of promotions you set on the Ryuzine Rack Options tab.  The [ryupromo] shortcode has no additional parameters.</p>
    <p>Here are things to keep in mind when including a "ryucover" and "ryupromo" shortcodes:</p>
    <blockquote>
    	<p>Only the first instance of the shortcode will be used.  Any additional ones for the same Edition are ignored by both Ryuzine Press and Ryuzine Rack. However they may still be displayed if you are using 
    	the auto-generated Archive page and a theme which displays full content in archive search results.</p>
    	<p>The shortcode-wrapped images are automatically scaled to fit their respective spaces.  If they are too small or low resolution they'll look terrible when scaled up.</p>
    	<p>Ideally place at least a "Medium" sized image that links to the full-size image.  Ryuzine Rack will prefer the thumbnail, Ryuzine Press will prefer the 
    	full-size image.  If you place an image without a link both will end up using the same image.</p>
    	<p>Cover images should be in "portrait" orientation, not "landscape."  The cover images are distorted to fit the aspect ratio of the cover thumbnail.  Images used as Edition 
    	covers, though, are scaled to fit by width while maintaining the original aspect ratio of the image.</p>
    	<p>Promotional banners should be in "landscape" mode, not "portrait."  The promotional images are scaled to fit the carousel by width while maintaining the aspect ratio (i.e., they are not distorted).</p>
    	<p>The maximum display sizes are 150x230 pixels for Ryuzine Rack cover thumbnails and about 640x800 for Ryuzine Press Edition covers.</p>
    	<p>All promotional images should be 640x300 pixels.  If you do not want to create one simply do this: [ryupromo]auto[/ryupromo] and Ryuzine Rack will automatically 
    	construct a promotion for that item.</p>
    	<p>For better image quality on high-density displays (like on the iPad 3 and iPhone 4 or 5) you need to use images around 2x larger than the dimensions given 
    	immediately above.</p>
    	<p>To make Ryuzine Rack load more quickly you can sacrifice image quality and use smaller images that will load faster.  You can also reduce the maximum number of promotions and promotional images.</p>
    </blockquote>
    <h3>Ryuzine Embed</h3>
    <p><strong>[ryuzine title="<em>Ryuzine Press Edition Title</em>"]</strong></p>
    <p>This shortcode allows you to easily embed any Ryuzine Press Edition in a blog post or page (similar to how you can embed videos) by just using the Edition's title.  The shortcode accepts the following parameters:</p>
    <ul>
    	<li><strong>title:</strong> The actual title of a Ryuzine Press Edition or "ryuzine-rack" to embed the newsstand (assuming it is installed to the current theme).</li>
    	<li><strong>url:</strong> Allows entering a raw URL (over-rides any "title" setting) but is restricted to YOUR site and only Ryuzine Press Editions*</li>
    	<li><strong>page:</strong> On load flip the Edition to a specific page number.</li>
    	<li><strong>height:</strong> The height of the embedded publication in pixels or percent (add "%" sign if percentage)</li>
    	<li><strong>width:</strong> The width of the embedded publication in pixels or percent (add "%" sign if percentage)</li>
    	<li><strong>size:</strong> <em>small|medium|large|spread</em> enter one of these preset sizes (if present over-rides height and width)</li>
    	<li><strong>class:</strong> a stylesheet class name to apply to the iframe</li>
    	<li><strong>style:</strong> inline styles to apply to the iframe</li>
    </ul>
    <p>Everything other than the "title" is optional.  You can also style the different elements of the embed by the following classnames:</p>
    <ul>
    	<li><em>ryuzine_embed</em> - the DIV container for the embed</li>
    	<li><em>ryu_embed_tab</em> - the links below the embedded Ryuzine</li>
    	<li><em>ryu_embed_tabbox"</em> - the main container for the "Embed Code" form (div container not form tag)</li>
    	<li><em>ryu_embed_input</em> - the Height and Width text inputs</li>
    </ul>
    <small>* Embedding off-site Ryuzine URLs is blocked by design.  Because you do not control a third-party site embedding represents a security risk. 
    If you want to cross-promote with other Ryuzine publishers use the Ryuzine Rack Catalog Builder to create an entries for their content and install 
    Ryuzine Rack to your theme.</small>
	</div>
<?php	break;
		case 'rack':
?>
<form method="post" enctype="multipart/form-data" action="options.php">
	<?php   $tab = $_GET['tab']; ?>
	<?php settings_fields('ryuzine_opt_rack'); ?>
	<?php do_settings_sections('ryuzine-settings&tab=rack');  ?>
	<?php $options = get_option('ryuzine_opt_rack'); ?>
<div class="tabbox">
	<h3>RyuzineRack</h3>
	<p>RyuzineRack is an optional newsstand webapp you can use to showcase your own Ryuzine Press Publications and other online content.
	If you are not using it you can safely ignore this settings tab.  Otherwise this is where you can define the Media Types, Button Label text 
	for each type, and select the default Rack Category.</p>
	</p>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">Archive Page</th>
			<td>
			<input name="ryuzine_opt_rack[install]" type="radio" value="1" <?php checked( '1', $options['install'] );  ?> /> Use <strong>Ryuzine Rack</strong> for Archive&nbsp;&nbsp;&nbsp;
			<input name="ryuzine_opt_rack[install]" type="radio" value="0" <?php checked( '0', $options['install'] );  ?> /> Use Theme's Archive Page<br />
			</td>
		</tr>
		<tr valign="top">
		<th scope="row">Rack Title</th>
		<td><input type="text" id="ryuzine_opt_rack[racktitle]" name="ryuzine_opt_rack[racktitle]" value="<?php if(isset($options['racktitle'])){echo $options['racktitle'];}; ?>" /><br/>
		<small>If empty, Cover Masthead will be used.  If Cover Masthead is empty or none, blog title will be used.</small>
		</tr>
        <tr valign="top">
        <th scope="row">Promo Animation</th>
        <td><input type="text" id="ryuzine_opt_rack[autopromo]" name="ryuzine_opt_rack[autopromo]" value="<?php echo $options['autopromo']; ?>" /> ( 0 = disabled ) <br/><small>Sets interval in seconds to animate promotional ads (interval is canceled if user manually clicks carousel buttons)</small></td>
        </tr>
		<tr valign="top">
		<th scope="row">Maximum Promotions</td>
		<td>
		 <select name="ryuzine_opt_rack[maxpromos]">
		 	<option <?php selected( '0', $options['maxpromos'] ); ?> value="0">0</option>
			<option <?php selected( '1', $options['maxpromos'] ); ?> value="1">1</option>
			<option <?php selected( '3', $options['maxpromos'] ); ?> value="3">3</option>
			<option <?php selected( '5', $options['maxpromos'] ); ?> value="5">5</option>
			<option <?php selected( '7', $options['maxpromos'] ); ?> value="7">7</option>
			<option <?php selected( '9', $options['maxpromos'] ); ?> value="9">9</option>
			<option <?php selected( '11', $options['maxpromos'] ); ?> value="11">11</option>
			<option <?php selected( '13', $options['maxpromos'] ); ?> value="13">13</option>
			<option <?php selected( '15', $options['maxpromos'] ); ?> value="15">15</option>
		 </select><br/>
		 <small>Maximum promotions to show in carousel. Fewer promos = faster loading. End users can hide the carousel in the Options panel within the webapp. Set to zero to fully disable promos.</small>
		</td>
		</tr>
		<tr valign="top">
		<th scope="row">Items Per Page</td>
		<td>
		 <select name="ryuzine_opt_rack[rackitems]">
			<option <?php selected( '5', $options['rackitems'] ); ?> value="5">5</option>
			<option <?php selected( '10', $options['rackitems'] ); ?> value="10">10</option>
			<option <?php selected( '20', $options['rackitems'] ); ?> value="20">20</option>
			<option <?php selected( '50', $options['rackitems'] ); ?> value="50">50</option>
			<option <?php selected( '100', $options['rackitems'] ); ?> value="100">100</option>
		 </select><br/>
		 <small>Fewer items per page = faster loading.  This is the initial/default number of items per page. 
		 End users can over-ride this in the Options panel within the webapp.</small>
		</td>
		</tr>
		<tr valign="top">
		<th scope="row">Links Open In:</td>
		<td>
		 <select name="ryuzine_opt_rack[linkopens]">
			<option <?php selected( '0', $options['linkopens'] ); ?> value="0">default</option>
			<option <?php selected( '1', $options['linkopens'] ); ?> value="1">_self</option>
			<option <?php selected( '2', $options['linkopens'] ); ?> value="2">_blank</option>
			<option <?php selected( '3', $options['linkopens'] ); ?> value="3">_parent</option>
			<option <?php selected( '4', $options['linkopens'] ); ?> value="4">_top</option>
			<option <?php selected( '5', $options['linkopens'] ); ?> value="5">In-App*</option>
		 </select><br/>
		 <small>Sets how the buttons open the content to which they are linked.<br/>* In-App is an <em>experimental</em> option 
		 which opens items inside Ryuzine Rack via an IFRAME.  It may not work with some kinds of linked content.</small>
		</td>
		</tr>
	<?php 
	$count = count($options[0]);
	for ($m=0;$m<$count;$m++) {
		echo '<tr valign="top">';
		echo '<th scope="row">Media Type '.$m.'</th>';
		echo '<td><input type="text" id="ryuzine_opt_rack[0]['.$m.']" name="ryuzine_opt_rack[0]['.$m.']" value="'.$options[0][$m].'" />';
		echo '</tr><tr valign="top" style="border-bottom: 1px solid #ccc;">';
		echo '<th scope="row">Button Label '.$m.'</th>';
		echo '<td><input type="text" id="ryuzine_opt_rack[1]['.$m.']" name="ryuzine_opt_rack[1]['.$m.']" value="'.$options[1][$m].'" />';
		echo '</tr>';
	}	?>
		<tr valign="top"><th scope="row">Default Rack Media Category</th>
			<td>
			<?php 
			$taxonomy = 'rackcats';
			$tax = get_taxonomy($taxonomy);
			$terms = get_terms($taxonomy,array('hide_empty' => 0)); 
			?>
			<select name="ryuzine_opt_rack[autocat]">
			<?php 
			foreach ($terms as $term) { 
				$selected = ($term->slug==$options['autocat']) ? 'selected="selected"' : ''; 
				echo "<option value='$term->slug' $selected>$term->name</option>"; 
			} 
			echo "</select>"; 
			?>
			Edit list on the <a href="edit-tags.php?taxonomy=rackcats&post_type=ryuzine">Rack Categories</a> screen.
			</td>
		</tr>
	</table>
</div>
<?php  break;
     	endswitch;
	endif;
?>

<div class="tabbox standalone">
	<table style="border:none;padding:0;margin:0;width:100%;"><tr>
	<td  style="text-align:left;">
	<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p></td>
	</form>
	<td  style="text-align:right;">
	<form id="rp_reset" method="post" action="">
        <?php wp_nonce_field('rp_reset','rp_reset_nonce'); ?>
        <input type="hidden" name="ryuzine_reset" value="1" />
        <input type="button" type="submit" name="resetbutton" class="reset button secondary-button" value="Reset to defaults" style="float:right;" />
        <div style="clear:both;"></div>
        </form> 
	</td>
	</tr></table>
</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$( '#rp_reset input.reset' ).on( 'click', function(){
			if ( confirm("ARE YOU SURE?  Resetting ALL the Ryuzine Press settings CANNOT BE UNDONE!" ) ){
				$('#rp_reset').submit();
			}
		} );
	});
</script>

	<?php	
}


?>