<?php
/*
Plugin Name: Ryuzine Press
Plugin URI: http://www.ryumaru.com/products/ryuzine/ryuzine-press/
Description: A WordPress plugin to bridge to the Ryuzine webapp.
Version: 1.1
Author: K.M. Hansen
Author URI: http://www.kmhcreative.com
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

/*  Copyright 2012-2020  K.M. Hansen  (email : software@ryumaru.com)

    Ryuzine Press plugin is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

/* Minimum Version Checks */
	function rp_wp_version_check(){
		// if not using minimum WP and PHP versions, bail!
		$wp_version = get_bloginfo('version');
		global $pagenow;
		if ( is_admin() && $pagenow=="plugins.php" && ($wp_version < 3.5 || PHP_VERSION < 5.6 ) ) {
		echo "<div class='notice notice-error is-dismissible'><p><b>ERROR:</b> Ryuzine Press is <em>activated</em> but requires <b>WordPress 3.5</b> and <b>PHP 5.6</b> or greater to work.  You are currently running <b>Wordpress <span style='color:red;'>".$wp_version."</span></b> and <b>PHP <span style='color:red;'>".PHP_VERSION."</span></b>. Please upgrade.</p></div>";
			return;
		}
	};
	add_action('admin_notices', 'rp_wp_version_check');

// Ryuzine Plugin Info Function
function ryuzine_pluginfo($whichinfo = null) {
	global $ryuzine_pluginfo;
	if (empty($ryuzine_pluginfo) || $whichinfo == 'reset') {
		// Important to assign pluginfo as an array to begin with.
		$ryuzine_pluginfo = array();
		$ryuzine_coreinfo = wp_upload_dir();
		$ryuzine_addinfo = array(
				// if wp_upload_dir reports an error, capture it
				'error' => $ryuzine_coreinfo['error'],
				// upload_path-url
				'base_url' => trailingslashit($ryuzine_coreinfo['baseurl']),
				'base_path' => trailingslashit($ryuzine_coreinfo['basedir']),
				// Ryuzine plugin directory/url
				'plugin_file' => __FILE__,
				'plugin_url' => plugin_dir_url(__FILE__),
				'plugin_path' => plugin_dir_path(__FILE__),
				'plugin_basename' => plugin_basename(__FILE__),
				'version' => '1.1'
		);
		// Combine em.
		$ryuzine_pluginfo = array_merge($ryuzine_pluginfo, $ryuzine_addinfo);
	}
	if ($whichinfo) {
		if (isset($ryuzine_pluginfo[$whichinfo])) {
			return $ryuzine_pluginfo[$whichinfo];
		} else return false;
	}
	return $ryuzine_pluginfo;
}

add_action( 'init', 'create_ryuzine_type' );

function create_ryuzine_type() {
	register_post_type( 'ryuzine',
		array(
		'labels' => array(
			'menu_name' => ('Ryuzine Press'),
			'name' => __( 'Ryuzine Press Editions' ),
			'all_items'  => ('All Editions'),
			'singular_name' => __( 'Ryuzine Edition' ),
			'add_new' => __( 'Add New Edition' ),
			'add_new_item' => __( 'Add New Ryuzine' ),
			'edit' => __( 'Edit' ),
			'edit_item' => __( 'Edit Ryuzine' ),
			'new_item' => __( 'New Ryuzine' ),
			'view' => __( 'View Ryuzine' ),
			'view_item' => __( 'View Ryuzine' ),
			'search_items' => __( 'Search Ryuzine Editions' ),
			'not_found' => __( 'No Ryuzine Editions found' ),
			'not_found_in_trash' => __( 'No Ryuzine Editions found in Trash' )
		),
		'supports' => array( 'title', 'editor', 'page-attributes', 'custom-fields', 'post-tag', 'thumbnail', 'excerpt' ),
			'public' => true,
			'menu_position' => 5,
			'menu_icon' => plugins_url('images/ryuzine-press-favicon.png',__FILE__),
			'taxonomies' => array('post_tag'),
			'slug' => 'ryuzine-rack',
			'has_archive' => 'ryuzine-rack',
			'show_in_nav_menus' => true,
			'show_ui' => true
		)
	);
	// New Taxonomy for Ryuzine Press Issues
	register_taxonomy('issues', 'ryuzine',
		array(
		// Hierarchical taxonomy (like categories)
		'hierarchical' => true,
		'show_admin_column' => true,
		// This array of options controls the labels displayed in the WordPress Admin UI
		'labels' => array(
			'name' => _x( 'Ryuzine Issues', 'taxonomy general name' ),
			'singular_name' => _x( 'Ryuzine Issue', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Ryuzine Issues' ),
			'all_items' => __( 'All Ryuzine Issues' ),
			'parent_item' => __( 'Parent Ryuzine Issue' ),
			'parent_item_colon' => __( 'Parent Ryuzine Issue:' ),
			'edit_item' => __( 'Edit Ryuzine Issue' ),
			'update_item' => __( 'Update Ryuzine Issue' ),
			'add_new_item' => __( 'Add New Ryuzine Issue' ),
			'new_item_name' => __( 'New Ryuzine Issue Name' ),
			'menu_name' => __( 'Ryuzine Issues' )
		),
		// Control the slugs used for this taxonomy
		'rewrite' => array(
			'slug' => 'issues', // This controls the base slug that will display before each term
			'with_front' => false, // Don't display the category base before "/issues/"
			'hierarchical' => true 
		),
	));
	// Try to enable categories for Comic Easel
	if (function_exists('ceo_pluginfo')) {
		register_taxonomy_for_object_type('issues', 'comic');
	};
	if (defined('MP_FOLDER')) {
		register_taxonomy_for_object_type('issues', 'mangapress_comic');
	};
	register_taxonomy_for_object_type('issues', 'post');
		
	// New taxonomy for Ryuzine Rack Category
	register_taxonomy('rackcats','ryuzine',
		array( 
			'hierarchical' => false, 
			'show_admin_column' => true,
			'labels' => array(
				'name' => _x( 'Rack Categories','taxonomy general name'),
				'singular_name' => _x( 'Rack Category','taxonomy singular name'),
				'search_items' => __( 'Search Rack Categories'),
				'all_items' => __( 'All Rack Categories'),
				'popular_items' => NULL,
				'edit_item' => __( 'Edit Rack Category'),
				'update_item' => __( 'Update Rack Category'),
				'add_new_item' => __( 'Add New Rack Category'),
				'new_item_name' => __( 'New Rack Category Name' ),
				'menu_name' => __( 'Rack Categories' )
			),
			'query_var' => 'rackcats', 
			'rewrite' => array( 'slug' => 'rackcat' ) 
		) 
	);
	// This should make sure we automatically have a Ryuzine category for Rack...
	if (!term_exists('ryuzine','rackcats')) {
		wp_insert_term(
			'Ryuzine',	// Term Name
			'rackcats', // Taxonomy
			array(
				'description' => 'Default media category',
				'slug' => 'ryuzine'
			)
		);
	};

}


function ryuzine_activation() 
{

	// FLUSH PERMALINKS //
    // First, we "add" the custom post type via the above written function.
    // Note: "add" is written with quotes, as CPTs don't get added to the DB,
    // They are only referenced in the post_type column with a post entry, 
    // when you add a post of this CPT.
    create_ryuzine_type();
    // ATTENTION: This is *only* done during plugin activation hook in this example!
    // You should *NEVER EVER* do this on every page load!!
    flush_rewrite_rules();
}
// Add Custom Post Type and Flush Rewrite Rules
register_activation_hook( __FILE__, 'ryuzine_activation' );

// Define default option settings
function add_defaults_fn($reset = false) {
	$options = array(	
		'ryuzine_opt_covers' => array(
		'autocover' => 0,
		'headerfooter' => 'display:none;',
		'mastheadtype' => 0,
		'mastheadtext' => get_bloginfo('name'),
		'mastheadimg' => '',
		'splashimg' => 0,
		'show_featured' => 1,
		'featured' => '0',
		'use_cover' => 0,
		'poweredby' => 0,
		'app_logo' => '',
		'app_icon' => ''
		),
		
		'ryuzine_opt_pages' => array(
		'binding'  => 'left',
		'pgsize'  => 0,
		'wptheme2ryu' => 0,
		'byline' => 1,
		'postbody' => 1,
		'metadata' => 0,
		'comments' => 0,
		'AndApp'  => 0,
		'zoompan'  => 1,
		'maxzoom'  => 5,
		'pgslider' => 0,
		'viewani' => 0,
		'bmark1' => 'Ryuzine User Forums',
		'bmark1url' => 'http://www.ryumaru.com/forum/',
		'bmark2' => 'Ryu Maru Website',
		'bmark2url' => 'http://www.ryumaru.com'	
		),
		
		'ryuzine_opt_addons' => array(
		'natlang' => 'en',
		'localization' => 0,
		'language' => 'en',
		'pageshadow' => 1,
		'baseUI' => 0,
		'swapThemes' => 0,
		'defaultTheme' => '',
		'deskTheme' => '',
		'winTheme' => '',
		'macTheme' => '',
		'nixTheme' => '',
		'iOSTheme' => '',
		'andTheme' => '',
		'tabTheme' => '',
		'wOSTheme' => '',
		'bbtTheme' => '',
		'wp7Theme' => '',
		'w8mTheme' => '',
		'iscroll' => 5,
		'OVR' => 0,
		'curves' => 0
		),
		
		'ryuzine_opt_ads' => array(
		'splashad'  => 0,
		'splashtype' => 0,
		'splashoff' => 0,
		'splashcontent' => null,
		'splashlink' => '',
		'boxad'  => 0,
		'boxadtype' => 0,
		'boxadoff' => 0,
		'boxadcontent' => null,
		'boxlink' => '',
		'bannerad' => 'null',
		'bannertype' => 0,
		'banneroff' => 0,
		'bannercontent' => null,
		'bannerlink' => ''		
		),
		
		'ryuzine_opt_rack' => array(
			'racktitle' => 'Newsstand',
			array('Ryuzine','Download','PDF','Print','Website'),
			array('Read Now','Download ⬇','Download ⬇','$ Buy Now','View Site'),
			array('Magazine','Newsletter','Comic Book','Manga','Manual','Off-site'),
			'rackitems' => '10',	
			'autopromo' => '5',
			'maxpromos' => '5',
			'linkopens' => '0',
			'autocat'	=> 'ryuzine',
			'install'	=> '1'
		),
		
		'ryuzine_rack_cat' => array(
			array(
				array ('Catalog 1',0,''),
				array (
				'ID','Date','Title','Description','Category','URL','Type','Thumbnail','Promotion'
				)
			)
		),
		
		'ryuzine_opt_lightbox' => array(
		'links' => '0'
		),
	);
	if ( $reset === true) {
		update_option('ryu_default_options_db', 0);		
		update_option('ryu_admin_hide_text',0);
		update_option('ryuzine_opt_covers', $covers);
		update_option('ryuzine_opt_pages', $pages);
		update_option('ryuzine_opt_addons', $addons);
		update_option('ryuzine_opt_ads', $ads);
		update_option('ryuzine_opt_rack', $mediaType);
		update_option('ryuzine_rack_cat', $rackcat);
		update_option('ryuzine_opt_lightbox', $lightbox);
	};
	
	  	foreach ($options as $section => $settings) {
			$dbcheck = get_option($section);	// get section from database
			foreach ($settings as $key => &$value) {	// & passes as reference
				if (isset($dbcheck[$key])) {	// if option is set
					if ($dbcheck[$key] != $value) {	// if value is not default
						$value = $dbcheck[$key];	// update value to cutom setting
					}
				} else {
					// option is not set, use default
				}
			}
			update_option($section,$settings);
		}
}
// Add Database Fields If Needed //
register_activation_hook(__FILE__, 'add_defaults_fn');

	
// See if a previous beta install has Rack Media Categories Assigned
function ryuzine_rack_mediacat_import() {
	if (isset($ryuzine_opt_rack)) {
		$get_media_cats = get_option('ryuzine_opt_rack');
		if (!$get_media_cats[2]) {
			$new_media_cats = array();
		} else {
			$new_media_cats = $get_media_cats[2];
		}
		foreach ($new_media_cats as $media_cat) {
			// Insert into new "rackcats" Taxonomy
			wp_insert_term($media_cat,'rackcats');
		}
		unset($media_cat);
	}
}
register_activation_hook(__FILE__, 'ryuzine_rack_mediacat_import');

	/*	BETA --> v1.0 AUTOFIXES
		See if beta version vars need to be fixed on activate
		as some things changed between releases but vars may be set.
	*/
	function fix_vars_on_activate() {
		function fix_vars($opt, $old, $new) {
			$fix = get_option($opt);
			if (isset($fix[$old])){
				$fix[$new] = $fix[$old];
			}
			update_option($opt,$fix);
		}
		function del_vars($opt, $var) {
			$fix = get_option($opt);
			unset($fix[$var]);
			update_option($opt,$fix);
		}
		fix_vars('ryuzine_opt_covers','retina_icon','app_icon');
		fix_vars('ryuzine_opt_pages','pgslide',	'pgslider');
		fix_vars('ryuzine_opt_pages','zoomable','zoompan' );
		del_vars('ryuzine_opt_covers','favicon');
		del_vars('ryuzine_opt_covers','iphone_icon');
		del_vars('ryuzine_opt_covers','ipad_icon');
		del_vars('ryuzine_opt_covers','retina_icon');
		del_vars('ryuzine_opt_pages','pgslide');
		del_vars('ryuzine_opt_pages','zoomable');
		del_vars('ryuzine_opt_addons','baseUI');
		// find posts with custom configs and fix them
		$my_query = null;
		$my_query = new WP_Query( array('post_type' => 'ryuzine') );
		if( $my_query->have_posts() ) {
			while ($my_query->have_posts()) : $my_query->the_post();
				$ryu_config = get_post_meta( get_the_ID(), '_ryuconfig', true);
				if( !empty($ryu_config) ){
					$options = json_decode($ryu_config,true);
					if (isset($options['pgslide'])){
						$options['pgslider'] = $options['pgslide'];
						unset($options['pgslide']);
						array_values($options);
						update_post_meta( get_the_ID(), '_ryuconfig', json_encode($options));
					}
				}
			endwhile;
		}
		wp_reset_query(); 	
	}
	
register_activation_hook(__FILE__, 'fix_vars_on_activate');
// Load the Core Functions //
@require('functions/rp_core_functions.php');
@require('functions/aq_resizer.php');
	if (is_admin() ) {
		//	Admin only
			@require('functions/rp_admin_functions.php');
			@require('functions/rp_generate_css.php');
			@require('options/rp_admin_options.php');
			@require('tools/rp_admin_tools.php');
			@require('functions/rp_posts_management.php');
			@require('functions/rp_publish_post.php');
			@require('plugin-update-checker/plugin-update-checker.php');
		$RyuzinePressUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
		'https://github.com/ryumaru/ryuzine-press',
			__FILE__,'ryuzine-press'
		);
		$RyuzinePressUpdateChecker->getVcsApi()->enableReleaseAssets();
	}

function ryuzine_remove_old_templates() {
	ryuzine_uninstall('press');
	ryuzine_uninstall('rack');
	if (get_option('ryuzine_app_installed')) {delete_option('ryuzine_app_installed');}
}
register_activation_hook(__FILE__, 'ryuzine_remove_old_templates');

// Set up the templates
	function ryu_single_page($template) {
		if (is_singular('ryuzine')) {
			load_template(ryuzine_pluginfo('plugin_path').'templates/single-ryuzine.php');
		} else { 
			return $template; 
		}
	}
	add_action('template_include', 'ryu_single_page');
	function ryu_archive_page($template) {
		$rack_install = get_option('ryuzine_opt_rack');
		if (is_post_type_archive('ryuzine') && $rack_install['install'] == '1') {
			load_template(ryuzine_pluginfo('plugin_path').'templates/archive-ryuzine.php');
		} else { 
			return $template; 
		}
	}
	add_action('template_include', 'ryu_archive_page');

// If Ryuzine Webapp is not installed to plugin, give notice to do so - not in a function so it shows on activation //
	global $pagenow, $typenow;
	if (empty($typenow) && !empty($_GET['post'])) {
  	$post = get_post($_GET['post']);
  	$typenow = $post->post_type;
	}
	if ( is_admin() && ($pagenow=="plugins.php" || ( ($pagenow=="edit.php" || $pagenow=="edit-tags.php" || $pagenow=="post-new.php") 
//	&& 
//	$_GET['post_type'] == "ryuzine" 
	) ) ) {
		if (!file_exists(ryuzine_pluginfo('plugin_path').'ryuzine/js/ryuzine.js')) {
		add_action('admin_notices', 'ryu_install_app_notice');
		};
	}
	function ryu_install_app_notice(){
		echo '<div class="error">You still need to install the Ryuzine WebApp!  Go to <a href="'.get_admin_url().'edit.php?post_type=ryuzine&page=ryuzine-tools&tab=update">Tools&gt;Update Ryuzine&gt;Install/Update Ryuzine WebApp</a>.</div>';
	};

// Load all the widgets
foreach (glob(plugin_dir_path(__FILE__)  . 'widgets/*.php') as $widgefile) {
	require_once($widgefile);
}
?>