<?php
/*
	Ryuzine Press Plugin
	This file contains all the "core" functions
	needed on both the front and back-ends.
*/

// Check for CSS File from within Ryuzine //
function ryu_check_CSS($content,$config = false) {
	$cssFileName = "issue_".get_the_ID();
	$cssFile = WP_PLUGIN_DIR.'/ryuzine-press/css/'.$cssFileName.'.css';
	$options_cover = get_option('ryuzine_opt_covers');
	if ($config) { 
		$headerfooter = $config['headerfooter'];
		// sent as number because CSS screws up the json encode
		if ($headerfooter == '0') {
			$headerfooter = 'display:none;';
		} else if ($headerfooter == '1') {
			$headerfooter = 'visibility:hidden;';
		} else {
			$headerfooter = 'display:block;';
		}
	} else {
		$headerfooter = $options_cover['headerfooter'];
	}
	if ($headerfooter != 'display:block;') {
	$header_footer = '#page0 .header, #page0 .footer, .covercorners-back .header, .covercorners-back .footer {'.$headerfooter.'}';

	} else {
	$header_footer = '';
	}
	// See if the issue-specific stylesheet exists //
	if (file_exists($cssFile)) {
		echo '<link rel="stylesheet" type="text/css" href="'.plugins_url().'/ryuzine-press/css/'.$cssFileName.'.css" id="this_issue" />';
		if ($header_footer != '') {
			echo "<style type='text/css'>\n".$header_footer."\n</style>";
		}
	} else {
		// We have to just write the into the page //
		$cssFileName = "thisissue";
		echo '<link rel="stylesheet" type="text/css" href="'.plugins_url().'/ryuzine-press/css/thisissue.css" id="this_issue" />';
		echo "<style type='text/css'>\n".$content."\n".$header_footer."\n</style>";
	}
	return $cssFileName;
}

// If Ryuzine Rack is installed over-ride Settings > Reading > Blog pages show at most X //

function ryuzine_archive_pagesize( $query ) {
	if (file_exists(STYLESHEETPATH.'/archive-ryuzine.php')) {	
		if ( is_admin() || ! $query->is_main_query() )
			return;
		if ( is_post_type_archive( 'ryuzine' ) ) {
			$query->set( 'posts_per_page', -1 );
			return;
		}
	}
}
	add_action( 'pre_get_posts', 'ryuzine_archive_pagesize', 1 );
	
// Get clean image URLs since clickable images mess up Ryuzine
function ryuzine_display_comic() {
	global $post;
	$output = '';
	// ComicPress 2.x
	if (function_exists('comicpress_the_hovertext')) {
		$comics = get_comic_path('comic',$post);
		if (is_array($comics)) {
			foreach ($comics as $comic) {
				$cdn_url = comicpress_themeinfo('cdn_url');
				if (!empty($cdn_url)) {
					$thumbnail = trailingslashit($cdn_url) . comicpress_clean_url($comic);
				} else {
					$thumbnail = comicpress_themeinfo('baseurl') . comicpress_clean_url($comic);
				}
				$hovertext = comicpress_the_hovertext($post);	
			}
		}
	}
	// Comic Easel (ComicPress 4)
	if (function_exists('ceo_the_hovertext')) {
		$post_image_id = get_post_thumbnail_id($post->ID);
		if ($post_image_id) { // If there's a featured image.
			$hovertext = ceo_the_hovertext();
			$thumbnail = wp_get_attachment_image_src( $post_image_id,'full', false);
			if (is_array($thumbnail)) $thumbnail = reset($thumbnail);
		}
	};
	// MangaPress
	if (defined('MP_FOLDER')) {
		$post_image_id = get_post_thumbnail_id($post->ID);
		if ($post_image_id) {
			$hovertext = '';
			$thumbnail = wp_get_attachment_image_src( $post_image_id, 'full', false);
			if (is_array($thumbnail)) $thumbnail = reset($thumbnail);
		}
	}
	$output .= '<img src="'.$thumbnail.'" alt="'.$hovertext.'" title="'.$hovertext.'" />';
	return apply_filters('ryuzine_display_comic', $output);
};

// Register Lightbox Link Shortcode //
function ryuzine_lightbox_link( $atts, $content = null ) {
	$opt = get_option('ryuzine_opt_lightbox');
	if ($opt['links'] == "1") {
		extract(shortcode_atts(array(
			'url' => '#',
			'id' => '',
			'type' => '',
			'title' => '',
			), $atts));
		if ($id!='') {$url = '#'.$id; }
		if ($type=='') { $type = 'lightboxbutton'; } else { $type = 'lb_'.$type; };
		if ($title!='')  { $title = 'title="'.$title.'" ';}
		$lightboxlink ='<a href="'.$url.'" rel="lightbox" '.$title.'class="'.$type.'">'.$content.'</a>';
		return $lightboxlink;
	} else {
		// do nothing! //
	}
}
add_shortcode('ryuboxlink', 'ryuzine_lightbox_link');

function ryuzine_lightbox( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'id' => '',
		'orient' => 'land'
		), $atts));
	if ( "ryuzine" == get_post_type() ) {
		$lightbox = '<figure id="'.$id.'" class="light_boxed '.$orient.'">'.$content.'</figure>';
	} else {
		$lightbox = '';
	}
		return $lightbox;
}
add_shortcode('ryubox', 'ryuzine_lightbox');

function ryuzine_rack_cover( $atts, $content = null) {
	return $content;
}
add_shortcode('ryucover','ryuzine_rack_cover');

function ryuzine_rack_promo( $atts, $content = null) {
	return $content;
}
add_shortcode('ryupromo','ryuzine_rack_promo');

function ryuzine_embed_in_page($atts, $content = null) {
	extract(shortcode_atts(array(
		'url' => '',
		'title' => '',
		'page' => '',
		'size' => '',
		'height' => '',
		'width' => '',
		'class' => '',
		'style' => ''
		), $atts));
		STATIC $i = 0; // we need each instance to have a unique-id
		$i++;
	if ( $size ) {
		if ($size == 'large') { $height = 768; $width = 1024; }
		elseif ($size == 'medium') { $height = 640; $width = 480; }
		else { $height = 480; $width = 320; };
	}
	if (!preg_match("/%/i", $width)) { $divwide = $width . 'px'; } else { $divwide = $width;};
	if (!$style) { $style = "border:1px solid black;"; }

	if ($url && $url != '') {
		if ($url == get_permalink( url_to_postid( $url ) ) && get_post_type( url_to_postid( $url) ) == 'ryuzine' ) {
			// pass $url through, it is a valid ryuzine on this site
		} else {
			$url = ''; // some other kind of post, block it
		}
	} else {
		if ($title != '' && strtolower($title) != 'ryuzine-rack' ) {
			if (get_page_by_title($title, OBJECT, 'ryuzine') ) {
				$url = esc_url( get_permalink( get_page_by_title($title, OBJECT, 'ryuzine') ) );
			} else {
				$url = '';
			}
		} elseif ( strtolower($title) == 'ryuzine-rack' ) {
			if (file_exists(STYLESHEETPATH.'/archive-ryuzine.php')) {
				$url = esc_url( get_post_type_archive_link( 'ryuzine' ) );
			} else {
				$url = '';
			}
		} else {
			$url = '';
		}
	}
	if ($page && $url != '') { $url = $url . '#' . $page; }
	if ($url != '') { ?>
		<div id="ryuzine_embed_<?php echo $i; ?>" class="ryuzine_embed" style="display:table;width:<?php echo $divwide; ?>;margin:10px auto;">
			<iframe border="0" height="<?php echo $height; ?>" width="<?php echo $width; ?>" class="<?php echo $class; ?>" style="<?php echo $style; ?>" src="<?php echo $url; ?>" scrolling="no" sandbox="allow-same-origin allow-forms allow-scripts"></iframe>
			<a hre="javascript:void(0);" class="ryu_embed_tab" style="float:left;padding:2px;" onclick="var tabbox = document.getElementById('ryu_embed_tabbox_<?php echo $i; ?>');if (tabbox.style.display == 'none') {tabbox.style.display='block';this.style.backgroundColor='#ccc';}else{tabbox.style.display='none';this.style.backgroundColor='transparent';};">Embed Code</a>
			<a href="<?php echo $url; ?>" target="_blank" class="ryu_embed_tab" style="float:right;padding:2px;">Pop Out</a>
			<div style="clear:both;"></div>
			<div id="ryu_embed_tabbox_<?php echo $i; ?>" class="ryu_embed_tabbox" style="display:none;">
				<form name="ryu_embed_form_<?php echo $i; ?>" style="background:#ccc;padding:2px;">
				<textarea id="ryu_embed_output_<?php echo $i; ?>" rows="5" style="width:98%"><iframe border="0" height="<?php echo $height; ?>" width="<?php echo $width; ?>" class="<?php echo $class; ?>" style="<?php echo $style; ?>" src="<?php echo $url; ?>" scrolling="no" sandbox="allow-same-origin allow-forms allow-scripts"></iframe><a href="<?php echo $url; ?>" target="_blank" class="ryu_embed_tab" style="float:right;">Pop Out</a></textarea>
				<p>Size: 
					<select onchange="
						if (this.value == 'medium') {
							var w = 480;
							var h = 640;
						} else if ( this.value == 'large') {
							var w = 768;
							var h = 1024;
						} else if ( this.value == 'spread') {
							var w = 1024;
							var h = 768;
						} else if ( this.value == 'custom') {
							var w = document.getElementById('ryu_embed_winput_<?php echo $i; ?>').value;
							var h = document.getElementById('ryu_embed_hinput_<?php echo $i; ?>').value;
						} else {
							var w = 320;
							var h = 480;
						}
						ryu_embed_code.embed<?php echo $i; ?>(w,h);
					">
						<option value="small">Small</option>
						<option value="medium">Medium</option>
						<option value="large">Large</option>
						<option value="spread">Spread</option>
						<option value="custom">Custom</option>
					</select>
					<input id="ryu_embed_winput_<?php echo $i; ?>" class="ryu_embed_input" type="text" onblur="if(!this.value.match(/^[0-9]{1,4}$/)){this.value='';if(this.value!=''){ryu_embed_code.embed<?php echo $i; ?>(this.value,null);};"/> x <input id="ryu_embed_hinput_<?php echo $i; ?>" class="ryu_embed_input" type="text" onblur="if(!this.value.match(/^[0-9]{1,4}$/)){this.value='';};if(this.value!=''){ryu_embed_code.embed<?php echo $i; ?>(null,this.value);};"/>
				</p>
				</form>
			</div>
			<script type="text/javascript">
				var ryu_embed_code = ryu_embed_code || {};
					ryu_embed_code.embed<?php echo $i; ?> = function(embed_w,embed_h) {
						var winput = document.getElementById('ryu_embed_winput_<?php echo $i; ?>');
						var hinput = document.getElementById('ryu_embed_hinput_<?php echo $i; ?>');
						if (embed_w == null || embed_w == '') {
							embed_w = winput.value;
						}
						if (embed_h == null || embed_h == '') {
							embed_h = hinput.value;
						}
						winput.value = embed_w;
						hinput.value = embed_h;
						var output = '<iframe border="0" height="'+embed_h+'" width="'+embed_w+'" class="<?php echo $class; ?>" style="<?php echo $style; ?>" src="<?php echo $url; ?>" scrolling="no" sandbox="allow-same-origin allow-forms allow-scripts"></iframe><a href="<?php echo $url; ?>" target="_blank" class="ryu_embed_tab" style="float:right;padding:2px;">Pop Out</a>';
						document.getElementById('ryu_embed_output_<?php echo $i; ?>').value = output;
					}
			</script>
		</div>
<?php
	} else {	// nothing to embed!
		$embed = '';
	}
	return $embed;
}
add_shortcode('ryuzine','ryuzine_embed_in_page');
?>