<?php
/*
	Ryuzine Press Plugin
	This file modifies the Edit Post page
	for Ryuzine Press Editions on the back-end.
*/
global $pagenow, $typenow;
	if (empty($typenow) && !empty($_GET['post'])) {
  	$post = get_post($_GET['post']);
  	$typenow = $post->post_type;
	}
if ( is_admin() && ($pagenow=="post.php" || $pagenow=="edit.php" || $pagenow=="post-new.php") && ($typenow == 'ryuzine' || $typenow == '')) {
// only do all this if post_type is "ryuzine" (or is not yet set because Add->New)
add_action('admin_head', 'ryu_editor_styles');
function ryu_editor_styles() {
    ?>
    <style type='text/css' id="ryu_editor_style">
        
	#poststuff .editor-toolbar {
		height: 30px;
	}        
	#poststuff .edButtonPreview, #poststuff .edButtonHTML {
		background-color: #F1F1F1;
		border-color: #DFDFDF #DFDFDF #CCC;
		color: #999;
	}
	.edButtonPreview, .edButtonHTML {
		height: 18px;
		margin: 5px 5px 0 0;
		padding: 4px 5px 2px;
		float: right;
		cursor: pointer;
		border-width: 1px;
		border-style: solid;
		-moz-border-radius: 3px 3px 0 0;
		-webkit-border-top-right-radius: 3px;
		-webkit-border-top-left-radius: 3px;
		-khtml-border-top-right-radius: 3px;
		-khtml-border-top-left-radius: 3px;
		border-top-right-radius: 3px;
		border-top-left-radius: 3px;
	}        
    #poststuff .editor-toolbar .active {
		border-color: #CCC #CCC #E9E9E9;
		background-color: #E9E9E9;
		color: #333;
	}   
.column-id {
	width: 75px;
}
    </style>
<?php
}

// Custom Columns in Editions Lists 
function add_new_ryuzine_columns($posts_columns) {
		$posts_columns['id'] = __('ID');
		return $posts_columns;
	}
function manage_ryuzine_columns($column_name, $id) {
		global $wpdb;
		switch ($column_name) {
		case 'id':
			echo $id;
		        break;
		// I may want to add others in the future //
		default:
			break;
		} // end switch
}

// Rack Media Categories (rackcats taxonomy) is non-hierarchical but we want radios //
// Adapted from: http://wp.tutsplus.com/tutorials/creative-coding/how-to-use-radio-buttons-with-taxonomies/
add_action( 'admin_menu', 'rackcats_remove_meta_box');
function rackcats_remove_meta_box() {
	remove_meta_box('tagsdiv-rackcats','ryuzine','normal');
}
//Add new taxonomy meta box
 add_action( 'add_meta_boxes', 'rackcats_add_meta_box');
 function rackcats_add_meta_box() {
     add_meta_box( 'rackcats_id', 'Rack Category','custom_rackcats_metabox','ryuzine' ,'side','core');
 }
  function custom_rackcats_metabox( $post ) {
    //Get taxonomy and terms  
    $taxonomy = 'rackcats';  
  
    //Set up the taxonomy object and get terms  
    $tax = get_taxonomy($taxonomy);  
    $terms = get_terms($taxonomy,array('hide_empty' => 0));  
  
    //Name of the form  
    $name = 'tax_input[' . $taxonomy . ']';  
  
    //Get current terms   
    $postterms = get_the_terms( $post->ID,$taxonomy );  
    $current = ($postterms ? array_pop($postterms) : false);  
    $current = ($current ? $current->term_id : 0);  
    ?>  
  
    <div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">  
  
        <!-- Display tabs-->  
        <ul id="<?php echo $taxonomy; ?>-tabs" class="category-tabs">  
            <li class="tabs"><?php echo $tax->labels->all_items; ?></li>  
        </ul>  
  
        <!-- Display taxonomy terms -->  
        <div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">  
            <ul id="<?php echo $taxonomy; ?>checklist" data-wp-lists="list:<?php echo $taxonomy?>" class="categorychecklist form-no-clear">  
                <?php   foreach($terms as $term){  
                    $id = $taxonomy.'-'.$term->term_id;  
                    echo "<li id='$id'><label class='selectit'>";  
                    echo "<input type='radio' id='in-$id' name='{$name}'".checked($current,$term->term_id,false)."value='$term->slug' />$term->name<br />"; 
                   echo "</label></li>";  
                }?>  
           </ul>  
        </div>  
  
    </div>  
    <?php  
}
/* 	On Manage Posts -> Quick Edit the meta box still looks like a "tags" one
	Since Ryuzine Rack cannot use multiple Media Categories we need to HIDE
	the one on the Quick Edit so people do not enter bad data, but there is
	no "quick_edit_remove_meta_box" so we need to use jQuery to hide it.
*/
add_action( 'admin_head-edit.php', 'wpse_59871_script_enqueuer' );
function wpse_59871_script_enqueuer() 
{    
    /**
       /wp-admin/edit.php?post_type=post
       /wp-admin/edit.php?post_type=page
       /wp-admin/edit.php?post_type=cpt  == gallery in this example
     */

    global $current_screen;
    if( 'edit-ryuzine' != $current_screen->id )
        return;
    ?>
    <script type="text/javascript">        
        jQuery(document).ready( function($) {
            $('span:contains("Rack Categories")').each(function (i) {
                $(this).parent().remove();
            });
        });    
    </script>
    <?php 
}

// 	This automatically adds published Ryuzine Press Editions to a "Ryuzine Press Editions"
//	Category, and creates that category if it doesn't exist, because normally you cannot
//	see the Category option anymore, unless you've enabled it for Ryuzine Rack
function add_ryuzine_category_automatically($post_ID) {
	global $wpdb;
	if(!has_term('','category',$post_ID)){
		wp_set_object_terms($post_ID, 'Ryuzine Press Editions', 'category');
	}
	$autocat = get_option('ryuzine_opt_rack');
	if(!has_term('','rackcats',$post_ID) && $autocat['autocat'] != ''){
		wp_set_object_terms($post_ID, $autocat['autocat'], 'rackcats');
	}
}
add_action('publish_ryuzine', 'add_ryuzine_category_automatically');

// Set Up Edit Page //
add_action('admin_init','ryu_custom_post_boxes');
add_action('save_post','save_issue_specific_data');

function ryu_custom_post_boxes() {
 	add_meta_box(	'ryuhello_section', __('Welcome Message (optional)'),  'ryu_hello_metabox', 'ryuzine', 'normal', 'core');
 	add_meta_box(	'ryuthank_section', __('Thank You Message (optional)'),  'ryu_thank_metabox', 'ryuzine', 'normal', 'core');
 	add_meta_box(	'ryustyles_section', __('Issue-Specific Styles (optional)'),  'ryu_styles_metabox', 'ryuzine', 'normal', 'core');
	add_meta_box(	'ryuconfig_section', __('Custom Configuration (optional)'), 'ryu_config_metabox', 'ryuzine', 'advanced', 'core');
// I also want the ID column in Editions Post list if people need to manually create external stylesshets //
add_filter('manage_edit-ryuzine_columns', 'add_new_ryuzine_columns');
add_action('manage_ryuzine_posts_custom_column', 'manage_ryuzine_columns', 10, 2);

}

	function ryu_hello_metabox($post) {
		$ryu_hello = get_post_meta($post->ID, '_ryuhello', TRUE);
		if (!$ryu_hello) $ryu_hello = '';
		wp_nonce_field( 'ryuzine'.$post->ID, 'ryu_hello_noncename');		
		wp_editor( $ryu_hello, 'ryu_hello', array('textarea_rows'=>'5'));
	}
	function ryu_thank_metabox($post) {
		$ryu_thank = get_post_meta($post->ID, '_ryuthank', TRUE);
		if (!$ryu_thank) $ryu_thank = ''; 
		wp_nonce_field( 'ryuzine'.$post->ID, 'ryu_thank_noncename');
		wp_editor( $ryu_thank, 'ryu_thank', array('textarea_rows'=>'5'));
	}

/*
	function ryu_hello_metabox($post) {
		$ryu_hello = get_post_meta($post->ID, '_ryuhello', TRUE);
		if (!$ryu_hello) $ryu_hello = ''; 	 
		wp_nonce_field( 'ryuzine'.$post->ID, 'ryu_hello_noncename');?>
<div class="editor-toolbar">
    <a id="edButtonPreview1" class="edButtonPreview" onclick="toggleEditor(1,'html');" >HTML</a>
    <a id="edButtonHTML1" class="edButtonHTML active" onclick="toggleEditor(1,'visual');">Visual</a>
</div>
		<div class="customEditor" style="margin:0 0 25px 0; border:1px solid #ccc;background:#fff;">	
		<textarea name="ryu_hello" cols="40" rows="5" style="margin:0;border:0;width:100%;background:#fff;"><?php echo $ryu_hello; ?></textarea>
		</div>
<?php
	}

	
	function ryu_thank_metabox($post) {
		$ryu_thank = get_post_meta($post->ID, '_ryuthank', TRUE);
		if (!$ryu_thank) $ryu_thank = ''; 	 
		wp_nonce_field( 'ryuzine'.$post->ID, 'ryu_thank_noncename');?>
<div class="editor-toolbar">
    <a id="edButtonPreview2" class="edButtonPreview" onclick="toggleEditor(2,'html');" >HTML</a>
    <a id="edButtonHTML2" class="edButtonHTML active" onclick="toggleEditor(2,'visual');">Visual</a>
</div>
		<div class="customEditor" style="margin:0 0 25px 0; border:1px solid #ccc;background:#fff;"><textarea name="ryu_thank" cols="40" rows="5" style="margin:0;border:0;width:100%;background:#fff;"><?php echo $ryu_thank; ?></textarea></div>
<?php
	}
*/
	function ryu_styles_metabox($post) {
		$ryu_styles = get_post_meta($post->ID, '_ryustyles', TRUE);
		if (!$ryu_styles) $ryu_styles = ''; 	 
		wp_nonce_field( 'ryuzine'.$post->ID, 'ryu_styles_noncename');?>
		<textarea name="ryu_styles" cols="40" rows="5" style="margin:0;border:0;width:100%;background:#fff;"><?php echo $ryu_styles; ?></textarea>
		<p><small>Enter only valid CSS code!  Do <b>not</b> enclose in &lt;style&gt; tags.</small></p>
		<p><input name="ryu_styles_overwrite" type="checkbox" value="1" /> <strong>Write content into stylesheet</strong></p> 
		<p><small>If checked, on Save/Publish/Update Ryuzine Press will attempt to create or overwrite an external stylesheet from this code.  If the stylesheet folder 
		is not writable it will default to including the styles in-page (however this means they cannot be stripped from Alternative Views).</small></p>
<?php
	}

function save_issue_specific_data($post_id) {
	// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
		return $post_id;


	// verify this came from the our screen and with proper authorization.
	if ( isset($_POST['ryu_hello_noncename']) && !wp_verify_nonce( $_POST['ryu_hello_noncename'], 'ryuzine'.$post_id ) || 
		 isset($_POST['ryu_thank_noncename']) && !wp_verify_nonce( $_POST['ryu_thank_noncename'], 'ryuzine'.$post_id ) || 
		 isset($_POST['ryu_styles_noncename']) && !wp_verify_nonce( $_POST['ryu_styles_noncename'], 'ryuzine'.$post_id )||
		 isset($_POST['ryu_config_noncename']) && !wp_verify_nonce( $_POST['ryu_config_noncename'], 'ryuzine'.$post_id ) ) {
		return $post_id;
	}


	// Check permissions
	if ( !current_user_can( 'edit_post', $post_id ) )
		return $post_id;
 
 
	// OK, we're authenticated: we need to find and save the data	
	$post = get_post($post_id);
	if ($post->post_type == 'ryuzine') { 
		if (isset($_POST['ryu_hello'])) {
		update_post_meta($post_id, '_ryuhello', $_POST['ryu_hello'] );
		}
		if (isset($_POST['ryu_thank'])) {
		update_post_meta($post_id, '_ryuthank', $_POST['ryu_thank'] );
		}
		if (isset($_POST['ryu_styles'])) {
		update_post_meta($post_id, '_ryustyles', $_POST['ryu_styles'] );
		if ($_POST['ryu_styles']!='' && $_POST['ryu_styles_overwrite']=="1") {
		ryu_create_css($_POST['ryu_styles'],$post_id);
		}
		}
		if (isset($_POST['ryu_config_opt']) && $_POST['ryu_config_opt']=='1') {
			$ryu_config = array(
				'config' 	=>	'1',
				'headerfooter' 	=>	$_POST['ryuzine_opt_covers_headerfooter'],
				'autocover'	=>	$_POST['ryuzine_opt_covers_autocover'],
				'overlay'	=>	$_POST['ryuzine_opt_covers_overlay'],
				'use_cover' =>	$_POST['ryuzine_opt_covers_use_cover'],
				'binding'	=>	$_POST['ryuzine_opt_pages_binding'],
				'pgsize'	=>	$_POST['ryuzine_opt_pages_pgsize'],
				'byline'	=>	$_POST['ryuzine_opt_pages_byline'],
				'postbody'	=>	$_POST['ryuzine_opt_pages_postbody'],
				'metadata'	=>	$_POST['ryuzine_opt_pages_metadata'],
				'comments'	=>	$_POST['ryuzine_opt_pages_comments'],
				'pgslider'	=>	$_POST['ryuzine_opt_pages_pgslider'],
				'natlang'	=>	$_POST['ryuzine_opt_addons_natlang'],
				'localization'	=>	$_POST['ryuzine_opt_addons_localization'],
				'language'	=>	$_POST['ryuzine_opt_addons_language'],
				'defaultTheme'	=>	$_POST['ryuzine_opt_addons_defaultTheme'],
				'swapThemes'	=>	$_POST['ryuzine_opt_addons_swapThemes']
			);		
		update_post_meta($post_id, '_ryuconfig', json_encode($ryu_config));		
		} else { 
			// if use config is off, nuke custom config entry from db
			delete_post_meta($post_id, '_ryuconfig');
		}
//		return(esc_attr($_POST['ryu_hello']));
	}
	return $post_id;
}

// Only Add these if we're on an Edit Page for our custom Post Type! //
if ($pagenow=="post.php" ) {
	add_action('admin_head-post.php', 'check_css_status');
}

// Edit Post Update for External issue Specific Stylesheet Creation //
function check_css_status() {
	$status = get_option('ryu_css_admin');
	if ($status == "1") { // check status success
		add_action('admin_notices', 'ryu_styles_written');
	} else if ($status == "2") { // check status failure
		add_action('admin_notices', 'ryu_styles_error');
	} else {}	
		update_option('ryu_css_admin',0); // disable
}

function ryu_styles_written() {
	echo '<div class="updated">Issue-specific styles successfully written to <em>'.ryuzine_pluginfo('plugin_url').'css/'.'</em> folder.</div>';
}
function ryu_styles_error() {
	echo 'div class="error">External issue-specific styles could not be created due to permissions.  You can either just use the in-page styles or create the files manually in <em>'.plugins_url('css/',__FILE__).'</em></div>';
}

	function ryu_config_metabox($post) {
		// Edition-specific configuration settings
		$ryu_config = get_post_meta($post->ID, '_ryuconfig', TRUE);
		if (!$ryu_config) {
			$options_covers	= get_option('ryuzine_opt_covers');
			$options_pages 	= get_option('ryuzine_opt_pages');
			$options_addons = get_option('ryuzine_opt_addons');
		$options = array(
			'config' 	=> 	'0',
			'headerfooter' => '0',
			'autocover'	=> 	$options_covers['autocover'],
			'overlay'	=>	'1',
			'use_cover' =>	$options_covers['use_cover'],
			'binding'	=> 	$options_pages['binding'],
			'pgsize'	=>	$options_pages['pgsize'],
			'byline'	=>	$options_pages['byline'],
			'postbody'	=>	$options_pages['postbody'],
			'metadata'	=>	$options_pages['metadata'],
			'comments'	=>	$options_pages['comments'],
			'pgslider'	=>	$options_pages['pgslider'],
			'natlang'	=>	$options_addons['natlang'],
			'localization'	=>	$options_addons['localization'],
			'language'	=>	$options_addons['language'],
			'defaultTheme'	=>	$options_addons['defaultTheme'],
			'swapThemes'	=>	$options_addons['swapThemes']
		);
		} else {
		$options = json_decode($ryu_config,true);
		}
		wp_nonce_field( 'ryuzine'.$post->ID, 'ryu_config_noncename');
		?>
		<small>Create a custom configuration applied only to <em>this</em> Edition.  If you set it to "default" any previous 
		custom configurations for this Edition are deleted on updating the post.</small><br/>
		<p>	<input name="ryu_config_opt" type="radio" value="0" <?php checked( '0', $options['config'] ); ?> /> Use default configuration 
			<input name="ryu_config_opt" type="radio" value="1" <?php checked( '1', $options['config'] ); ?> /> Use <strong>custom</strong> configuration</p>

	<table class="form-table">
		<tr valign="top"><th scope="row">Cover Headers &amp; Footers</th>
			<td>
			<input name="ryuzine_opt_covers_headerfooter" type="radio" value="0" <?php checked( '0', $options['headerfooter'] );  ?> /> Hide and remove spacing</label>
			<br />
			<input name="ryuzine_opt_covers_headerfooter" type="radio" value="1" <?php checked( '1', $options['headerfooter'] );  ?> /> Hide but keep spacing</label>
			<br />
			<input name="ryuzine_opt_covers_headerfooter" type="radio" value="2" <?php checked( '2', $options['headerfooter'] );  ?> /> Show both header and footer on front &amp; back covers</label>					
			</td>
		</tr>	
		<tr valign="top"><th scope="row">Cover Source</th>
			<td>
			<input name="ryuzine_opt_covers_autocover" type="radio" value="0" <?php checked( '0', $options['autocover'] );  ?> /> Generate Automatically<br/>
			<input name="ryuzine_opt_covers_autocover" type="radio" value="1" <?php checked( '1', $options['autocover'] );  ?> /> Use Oldest Post Assigned to Edition<br />
			</td>
		</tr>
		<tr valign="top"><th scope="row">Cover Overlays</th>
			<td>
			<input name="ryuzine_opt_covers_overlay" type="radio" value="0" <?php checked( '0', $options['overlay'] ); ?> /> Use any Masthead/Featured List<br/>
			<input name="ryuzine_opt_covers_overlay" type="radio" value="1" <?php checked( '1', $options['overlay'] ); ?> /> Exclude Masthead/Featured List
			<br/><small>Masthead/Featured List content is defined on <em>Ryuzine Press > Options > Covers > Splash Screen & Auto-Generated Cover Settings</em></small>
			</td>
		<tr valign="top"><th scope="row">Cover Image</th>
			<td>
			<input name="ryuzine_opt_covers_use_cover" type="radio" value="0" <?php checked( '0', $options['use_cover'] ); ?> /> Use Post "Featured Image"<br/>
			<input name="ryuzine_opt_covers_use_cover" type="radio" value="1" <?php checked( '1', $options['use_cover'] ); ?> /> Use [ryucover] shortcode
			<br/><small>(ignored if "Cover Source" is set to use oldest post)</small>
			</td>
        <tr valign="top">
        <th scope="row">Binding</th>
        <td>
        <input name="ryuzine_opt_pages_binding" type="radio" value="left" <?php checked( 'left', $options['binding'] ); ?>/> Left
		<input name="ryuzine_opt_pages_binding" type="radio" value="right" <?php checked( 'right', $options['binding'] ); ?> /> Right
		</td>
        </tr>
        <tr valign="top">
        <th scope="row">Page Fill</th>
        <td>
    	<input name="ryuzine_opt_pages_pgsize" type="radio" value="0" <?php checked( '0', $options['pgsize'] ); ?>/> Magazine (Square)<br/>
		<input name="ryuzine_opt_pages_pgsize" type="radio" value="1" <?php checked( '1', $options['pgsize'] ); ?> /> Comic Book (Tall)<br/>
		<input name="ryuzine_opt_pages_pgsize" type="radio" value="2" <?php checked( '2', $options['pgsize'] ); ?> /> Fill All (fluid layout)
        <br /><small>When "Fill All" is enabled pages grow to fill the available space.</small>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row">Bylines</th>
        <td>
        <input name="ryuzine_opt_pages_byline" type="radio" value="0" <?php checked( '0', $options['byline'] ); ?>/> No post author and date<br/>
		<input name="ryuzine_opt_pages_byline" type="radio" value="1" <?php checked( '1', $options['byline'] ); ?> /> Include post author and date <br />
		</td>
        </tr>

        <tr valign="top">
        <th scope="row">Comic Post Body</th>
        <td>
        <input name="ryuzine_opt_pages_postbody" type="radio" value="0" <?php checked( '0', $options['postbody'] ); ?>/> Suppress post text and only show comic image.<br/>
		<input name="ryuzine_opt_pages_postbody" type="radio" value="1" <?php checked( '1', $options['postbody'] ); ?> /> Show comic image and post text.<br />
		<small>Ignored if no supported webcomic plugin is activated.</small>
		</td>
        </tr>

        <tr valign="top">
        <th scope="row">Meta Data</th>
        <td>
        <input name="ryuzine_opt_pages_metadata" type="radio" value="0" <?php checked( '0', $options['metadata'] ); ?>/> No tags, catgories, comment counts, etc.<br/>
		<input name="ryuzine_opt_pages_metadata" type="radio" value="1" <?php checked( '1', $options['metadata'] ); ?> /> Include tags, categories, comment counts, etc. <br />
		</td>
        </tr>
      
        <tr valign="top">
        <th scope="row">Comments</th>
        <td>
        <input name="ryuzine_opt_pages_comments" type="radio" value="0" <?php checked( '0', $options['comments'] ); ?>/> No Comments on pages<br/>
		<input name="ryuzine_opt_pages_comments" type="radio" value="1" <?php checked( '1', $options['comments'] ); ?> /> Include Comments on pages<br />
		<small>Note: Comments form submission takes the reader to the corresponding blog post page and away from the Ryuzine version.</small>
		</td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Page Slider</th>
        <td>
        <input name="ryuzine_opt_pages_pgslider" type="radio" value="0" <?php checked( '0', $options['pgslider'] ); ?>/> Use Table of Contents Panel<br/>
		<input name="ryuzine_opt_pages_pgslider" type="radio" value="1" <?php checked( '1', $options['pgslider'] ); ?> /> Use Page Slider Navigation<br />
		</td>
        </tr> 
		<tr valign="top">
		<th scope="row">HTML Natural Language</th>
		<td>
		 <select name="ryuzine_opt_addons_natlang">
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
		<th scope="row">UI Localization</th>
		<td>
    	<input name="ryuzine_opt_addons_localization" type="radio" value="0" <?php checked( '0', $options['localization'] ); ?>/> Off
		<input name="ryuzine_opt_addons_localization" type="radio" value="1" <?php checked( '1', $options['localization'] ); ?> /> On
		<br/><small>To localize the UI turn this "On" and select a langage below.</small>
		</td>
		</tr>
		<tr valign="top">
		<th scope="row">UI Language Code</th>
		<td>
		 <select name="ryuzine_opt_addons_language">
			<option <?php selected( 'da', $options['language'] ); ?> value="da">da Danish</option>
			<option <?php selected( 'de', $options['language'] ); ?> value="de">de German</option>
			<option <?php selected( 'el', $options['language'] ); ?> value="el">el Greek</option>
			<option <?php selected( 'en', $options['language'] ); ?> value="en" selected>en English</option>
			<option <?php selected( 'es', $options['language'] ); ?> value="es">es Spanish</option>
			<option <?php selected( 'fr', $options['language'] ); ?> value="fr">fr French</option>
			<option <?php selected( 'hi', $options['language'] ); ?> value="hi">hi Hindi</option>
			<option <?php selected( 'it', $options['language'] ); ?> value="it">it Italy</option>
			<option <?php selected( 'ja', $options['language'] ); ?> value="ja">ja Japanese</option>
			<option <?php selected( 'ko', $options['language'] ); ?> value="hi">ko Korean</option>
			<option <?php selected( 'no', $options['language'] ); ?> value="no">hi Norwegian</option>
			<option <?php selected( 'pt', $options['language'] ); ?> value="pt">pt Portuguese</option>
			<option <?php selected( 'ru', $options['language'] ); ?> value="ru">ru Russian</option>
			<option <?php selected( 'sv', $options['language'] ); ?> value="sv">sv Swedish</option>
			<option <?php selected( 'zh_HANS', $options['language'] ); ?> value="zh_HANS">zh_HANS Simplified  Chinese</option>
			<option <?php selected( 'zh_HANT', $options['language'] ); ?> value="zh_HANT">zh_HANT Traditional Chinese</option>
		 </select>
		</td>
		</tr>
		<tr>
		<th scope="row">Non-Swap Theme</th>
		<td>
<?php
// GET LIST OF INSTALLED ADD-ONS
$themes = glob(WP_PLUGIN_DIR.'/ryuzine-press/ryuzine/theme/*' , GLOB_ONLYDIR);
// strip out relative path
for ($t=0;$t<count($themes);$t++) {
	$themes[$t] = preg_replace("~".WP_PLUGIN_DIR."/ryuzine-press/ryuzine/theme/~", "", $themes[$t] );
} ?>
<select name="ryuzine_opt_addons_defaultTheme">
	<option value="">None</option>
<?php 
for ($t=0;$t<count($themes);$t++) { 
	if ($options['defaultTheme'] == $themes[$t]) { $selected = 'selected';} else { $selected = '';};
	echo '<option value="'.$themes[$t].'" '.$selected.'>'.$themes[$t].'</option>';
} ?>
</select><br/><small>This theme is over-ridden if Swap Themes is enabled below.</small>
		</td></tr>
		<tr>
		<th scope="row">Swap Themes</th>
		<td>
    	<input name="ryuzine_opt_addons_swapThemes" type="radio" value="0" <?php checked( '0', $options['swapThemes'] ); ?>/> Off
		<input name="ryuzine_opt_addons_swapThemes" type="radio" value="1" <?php checked( '1', $options['swapThemes'] ); ?> /> On
		<br /><small>If enabled, uses platform themes defined under <em>Ryuzine Press > Options</em> page.</small>
		</td>
		</tr>
		<tr>
    </table>

		<?php 
	}
} // end of if $typenow
?>