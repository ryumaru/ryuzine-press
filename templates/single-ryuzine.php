<?php 	
/* This is the Ryuzine Press 1.0 template file
*/

/*  Copyright 2012-2015  K.M. Hansen  (email : software@ryumaru.com)

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

		$issues = get_the_terms($post->ID,'issues');
		$ryupress = plugins_url()."/ryuzine-press/";
		$options_cover = get_option('ryuzine_opt_covers');
		$options_page = get_option('ryuzine_opt_pages');
		$options_addon = get_option('ryuzine_opt_addons');
		$options_ad = get_option('ryuzine_opt_ads');
		$featured = get_cat_ID($options_cover['featured']);
		$pagecount = 0;
		$issue_id = array();	// May be more than one ID
		$issue_id_output = '';
		foreach($issues as $issue) {
			$pagecount = $pagecount + $issue->count;	// initial post count in taxonomies
			array_push($issue_id, $issue->term_id);
			$issue_id_output .= $issue->term_id.',';
			unset($issue);
		}
		// Check for Edition-specific Custom Configuration
		if (get_post_meta($post->ID, '_ryuconfig', TRUE)) {
			$config = get_post_meta($post->ID, '_ryuconfig', TRUE);
			$config = json_decode($config,true);
		} else {
			$config = false;
		}	

/*	This is the "Master Query" for this Ryuzine Edition, we will pull the data once
	and store it in variables for re-use below without running more queries on the db
	because doing it this way is super, super fast by comparison to multi-queries.
*/
	$postcount = 0;
		$args = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'issues',
					'field' => 'term_id',
					'terms' => $issue_id
				)
			),
			'posts_per_page'=>-1,
			'orderby'=>'date',
			'order'=>'ASC'
			);

		query_posts($args);
	$articles = array();	// Array to hold articles
	$ryuzines = array();	// Array of post_type "ryuzine"
		while (have_posts()) : the_post();
			if (get_post_type()=='ryuzine') {
				array_push($ryuzines, get_the_ID());
			} else {
				array_push($articles, get_the_ID());
				$postcount++;
			}
		endwhile; 

		// Adjust $pagecount based on cover settings
		// 0 = generated cover | 1 = use oldest post
		if ($config) { 
			$autocover = $config['autocover']; 
		} else { 
			$autocover = $options_cover['autocover'];
		}
		if ($autocover=='0') {
			$x = 1;
			$postcount = ($postcount+1); // add this Ryuzine back to count
		} else { 
			$x = 0;
		}
	// Subtract posts of type "ryuzine" from total post count
	$pagecount = ($pagecount - ($pagecount - $postcount) );
	// Do not allow odd page counts!
	if ($pagecount&1) { $pagecount = $pagecount+1; };
	wp_reset_query();

// Re-Register Lightbox Shortcode //
function in_ryuzine_lightbox_link( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'url' => '#',
			'id' => '',
			'linkid' => '',
			'type' => '',
			'gallery' => '',
			'aspect' => '',
			'layout' => '',
			'caption' => '',
			'title' => '',
			), $atts));
		if ($id!='') { $url = '#'.$id; }	// backwards compat with beta version lightbox
		if ($linkid!='') { $linkid = 'data-linkid="'.$linkid.'" ';}
		if ($type=='') { $type = 'lightboxbutton'; } else { $type = 'lb_'.$type; };
		if ($gallery!='') {$gallery = 'data-gallery="'.$gallery.'" ';}
		if ($aspect!='') { $aspect = 'data-aspect="'.$aspect.'" ';}
		if ($layout!='') { $layout = 'data-layout="'.$layout.'" ';}
		if ($caption!=''){ $caption = 'data-caption="'.$caption.'" ';}
		if ($title!='')  { $title = 'title="'.$title.'" ';}
		$lightboxlink ='<a href="'.$url.'" rel="lightbox" '.$linkid.$gallery.$aspect.$layout.$caption.$title.'class="'.$type.'">'.$content.'</a>';
		return $lightboxlink;
}
add_shortcode('ryuboxlink', 'in_ryuzine_lightbox_link');
function in_ryuzine_rack_cover( $atts, $content = null) {
	// hide cover thumbnail
}
add_shortcode('ryucover','in_ryuzine_rack_cover');
function in_ryuzine_rack_promo( $atts, $content = null) {
	// hide promos
}
add_shortcode('ryupromo','in_ryuzine_rack_promo');

/* Make App Icons On the Fly */
if ($options_cover['app_icon'] != '') {
	$icon = $options_cover['app_icon'];
	$favicon 	= aq_resize( $icon, 16, 16, true,true,true); 
	$ipad_icon 	= aq_resize( $icon, 72, 72, true,true,true);
	$retina_icon= aq_resize( $icon, 114, 114, true,true,true);
	$iphone_icon= aq_resize( $icon, 57, 57, true,true,true);
	$window_tile= aq_resize( $icon, 144, 144, true,true,true);
} else {
	$favicon = $ryupress.'ryuzine/images/app/icons/ryuzine-favicon.png';
	$iphone_icon = $ryupress.'ryuzine/images/app/icons/ryuzine-icon-03.png';
	$ipad_icon = $ryupress.'ryuzine/images/app/icons/ryuzine-icon-02.png';
	$retina_icon = $ryupress.'ryuzine/images/app/icons/ryuzine-icon-01.png';
	$window_tile = $ryupress.'ryuzine/images/app/icons/ryuzine-tile.png';
}
?>
<!DOCTYPE html>
<html lang="<?php if ($config) { echo $config['natlang']; } else { echo $options_addon['natlang'];}; ?>">
<head>
<meta content="text/html; charset=UTF-8" http-equiv="content-type">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="msapplication-TileColor" content="#ADDEFA" />
<meta name="msapplication-TileImage" content="<?php echo $window_tile; ?>" />
<link rel="icon" type="image/png" href="<?php echo $favicon; ?>" />
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $retina_icon; ?>" />
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $ipad_icon; ?>" />
<link rel="apple-touch-icon-precomposed" href="<?php echo $iphone_icon; ?>" />
<?php if ($options_page['wptheme2ryu']== '1') { ?>
<link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_uri(); ?>" />
<?php } ?>
<link rel="stylesheet" type="text/css" href="<?php echo $ryupress; ?>ryuzine/css/ui.css" id="ui_format" /> 
<link rel="stylesheet" type="text/css" href="<?php 
if ($config) { $defaultTheme = $config['defaultTheme']; } else { $defaultTheme = $options_addon['defaultTheme'];};
if ($defaultTheme != '' && $defaultTheme != null) { 
	echo $ryupress.'ryuzine/theme/'.$defaultTheme.'/theme.css';
} else {
	echo $ryupress.'ryuzine/theme/dark/theme.css'; }; ?>" id="ui_theme" />
<link rel="stylesheet" type="text/css" href="<?php echo plugins_url(); ?>/ryuzine-press/ryuzine/css/ryuzine_leftbound.css" id="screen_format" />
<?php	
 	$stylesheet = get_post_meta($post->ID,'_ryustyles',TRUE);
 	// If it exists, use external stylesheet //
 	// if not styles will be written in page //
 	$cssFileName = ryu_check_CSS($stylesheet,$config);
 ?>
<link rel="stylesheet" type="text/css" href="<?php echo $ryupress; ?>ryuzine/css/blank.css" id="colortext" />
<title><?php if ($options_cover['mastheadtext'] != '') { echo $options_cover['mastheadtext'].' - ';the_title(); } else { bloginfo('name').' - ';the_title();;} ?></title>
<script type="text/javascript">
/*	RYUZINE READER/RACK CONFIG FILE
	Version 1.0
	
	Load this file before ryuzine.js or ryuzine.rack.js
*/

var RYU = RYU || {};
RYU.config = {
	/* 	LOCALIZATION SETTINGS	*/
	language	:	"<?php if ($config){echo $config['language']; } else { echo  $options_addon['language']; }; ?>",		// ISO 639-1 language code (ignored is "localize" add-on is not loaded)
	/*	RYUZINE READER/RACK		*/
	binding		:	"<?php if ($config) {echo $config['binding'];} else {echo  $options_page['binding'];}; ?>",
	pgsize		:	<?php if ($config) {echo $config['pgsize'];} else { echo  $options_page['pgsize'];}; ?>,
	zoompan		:	<?php echo  $options_page['zoompan']; ?>,
	maxzoom		:	<?php echo  $options_page['maxzoom']; ?>,
	pgslider	:	<?php if($config) { echo $config['pgslider'];} else { echo $options_page['pgslider'];}; ?>,
	viewani		:	<?php echo $options_page['viewani']; ?>,
	bmarkData	:	[			// Default preset Reader bookmarks : ["label","URL"]
		["<?php echo $options_page['bmark1']; ?>","<?php echo $options_page['bmark1url']; ?>"],
		["<?php echo $options_page['bmark2']; ?>","<?php echo $options_page['bmark2url']; ?>"]
	],
	/* 	THEME SETTINGS			*/
	swapThemes	:	<?php if ($config) { echo $config['swapThemes']; } else { echo  $options_addon['swapThemes']; }; ?>,				// 0 = no | 1 = yes : Swap themes based on device type
	deskTheme	:	"<?php echo  $options_addon['deskTheme']; ?>",		// Fallback theme for unspecified desktop/laptop OS
	winTheme	:	"<?php echo $options_addon['winTheme']; ?>",		// General Windows desktop/laptop systems
	macTheme	:	"<?php echo $options_addon['macTheme']; ?>",	// Macintosh Systems
	nixTheme	:	"<?php echo $options_addon['nixTheme']; ?>",				// Linux Systems
	iOSTheme	:	"<?php echo  $options_addon['iOSTheme']; ?>",	// iOS Devices (iPad, iPhone, iPod Touch)
	andTheme	:	"<?php echo  $options_addon['andTheme']; ?>",		// Android Phones and Tablets
	wp7Theme	:	"<?php echo  $options_addon['wp7Theme']; ?>",		// Windows Phone 7 devices
	w8mTheme	:	"<?php echo  $options_addon['w8mTheme']; ?>",		// Windows 8.x desktop/laptops in "Metro" mode
	bbtTheme	:	"",				// BlackBerry Tablet (legacy device)
	/* 	INTEGRATED ADVERTISING 	*/
	splashad	:	<?php echo  $options_ad['splashad']; ?>,
	boxad		:	<?php echo  $options_ad['boxad']; ?>,			// 0 = no box ad	| value = display time in seconds | "x" = persistent (user must close)
	appbanner	:	<?php echo  $options_ad['bannerad']; ?>,			// 0 = no banner ad	| value = display time in seconds | "x" = persistent (user must close)
	AddOns		: [<?php 
	if (isset($options_addon['selected_addons'])) {
		$addons = explode(',',$options_addon['selected_addons']); 
		for ($a=0;$a<count($addons);$a++) {
			if ($addons[$a] != '') {
				echo "'".$addons[$a]."'";
				if ($a!=count($addons)-1){
					echo ",";
				}
			}
		}
}?>]
};
RYU.php = {
	baseurl : "<?php echo $ryupress; ?>"
}
</script>
<script type="text/javascript" src="<?php echo $ryupress; ?>ryuzine/js/sniffer.js" ></script>
<script type="text/javascript" src="<?php echo $ryupress; ?>ryuzine/js/ryuzine.js" ></script>

</head>
<body id="ryuzinereader">
<a href="#nav" id="skip2nav">Skip to Navigation</a>
<!--// publication content below this line //-->
<div id="ryu_mask">
	<div id="front_matter">
		<h1 id="splash_title"><?php 
			if (isset($options_cover['splashimg']) && $options_cover['splashimg'] == "1" && ($options_cover['mastheadimg'] != null || $options_cover['mastheadimg'] != '' ) ) { 
				echo '<img src="'.$options_cover['mastheadimg'].'" style="max-width:98%;height:auto;" />';
			} else {
				if ($options_cover['mastheadtext'] != '') { echo $options_cover['mastheadtext']; } else { bloginfo('name');} 
			}?></h1>		
		<p id="summary"><?php 
			// prevent summary from containing shortcodes or HTML tags:
			if ( has_excerpt( $post->ID ) ) {
   				echo strip_shortcodes(get_the_excerpt());
			} else {
   				echo wp_strip_all_tags( strip_shortcodes(get_the_content()) );
		} ?></p>
		
		<?php if ($options_cover['app_logo']!='') { 
			echo '<div id="app_logo"><img src="'.$options_cover['app_logo'].'"/></div>';
		} ?>
		
		<div id="splash_screen">
		<?php
		if ( !isset($options_ad['splashoff']) && ($options_ad['splashcontent'] != null || $options_ad['splashcontent'] != '') ) {
			if ($options_ad['splashtype'] == "1") {
				echo $options_ad['splashcontent'];
			} else { ?>
				<p><?php if ($options_cover['mastheadtext'] != '') { echo $options_cover['mastheadtext']; } else { bloginfo('name');} ?> is sponsored in part by</p>
		<?php
				echo '<a href="'.$options_ad['splashlink'].'" target="_blank"><img src="'.$options_ad['splashcontent'].'" style="max-width:98%;height:auto;" /></a></div>';
			}
		} else {} ?></div>
		
		<div id="copy_right">Copyright &copy;<?php the_time('Y'); ?> <a href="<?php echo get_option('home'); ?>"><?php bloginfo('name'); ?></a>
		<?php
			if ($options_cover['poweredby'] == "1") { ?>
		| Powered by <a href="http://wordpress.org/">WordPress</a> &amp; <a href="http://www.ryumaru.com/products/ryuzine/">Ryuzine Press</a>
		<?php } ?>
		</div>


		<div id="appbanner">
		<?php 
			if ( $options_ad['bannercontent'] != null || $options_ad['bannercontent'] != '' ) {
				if ($option_ad['bannertype'] == "1") {
				echo $options_ad['bannercontent'];
				} else {
				echo '<a href="'.$options_ad['bannerlink'].'" target="_blank"><img src="'.$options_ad['bannercontent'].'" /></a>';
				}
			}
		?>
		</div>

		<div id="welcome_sign">
		<?php echo get_post_meta($post->ID, '_ryuhello', TRUE); ?>
		</div>

</div><!--// end of front matter //-->

<div id="issue">

	<!--// Dynamically Generate Cover //-->
	<?php 
	if ($autocover == '0') { ?>
		<h1 class="section_head">Cover</h1>
		<div id="page0" class="page_box">
	<?php 
	if ($config) { $covercode = $config['use_cover']; } else { $covercode = $options_cover['use_cover']; }
	if ($covercode == "1") {	
			$pattern = get_shortcode_regex();
			preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches );
			// find out if a cover image is set with shortcode
			if( is_array( $matches ) && array_key_exists( 2, $matches ) && in_array( 'ryucover', $matches[2] ) )
			{
				$first = true; // only take the first one you find //
				foreach ($matches[0] as $value) {
						$value = wpautop( $value, true );
						if ( preg_match_all('/ryucover/', $value, $covers) && $first ) {
							// See if it is set to full bleed or not //
							if ( preg_match('/bleed="([^"]+)"/', $value, $bleed) ) {
								if ( $bleed[1] == "1" || $bleed[1] ==  "cover") {
									$fullbleed = "cover";
								} else if ( $bleed[1] == "2" || $bleed[1] == "contain" ) {
									$fullbleed = "contain";
								} else if ( $bleed[1] == "3" || $bleed[1] == "width" ) {
									$fullbleed = "100% auto";
								} else if ( $bleed[1] == "4" || $bleed[1] == "height" ) {
									$fullbleed = "auto 100%";
								} else { // prevent bad value getting thru
									$fullbleed = "0";
								}
							} else {$fullbleed = "0"; };
							// See if it has a custom background color set //
							if ( preg_match('/color="([^"]+)"/', $value, $color) ) { 
									$bgcolor = $color[1];
							} else {$bgcolor = "white";}; 
							// See if it has a custom background color set //
							if ( preg_match('/shift="([^"]+)"/', $value, $position) ) { 
									$pos = $position[1];
							} else {$pos = "center 0";}; 
						// if you find one, extract the img src and print it //
							// if you find one, extract the img href or src and print it //
							if ( preg_match('/href="([^"]+)"/', $value, $cover) ) { 
								if ($fullbleed != '0' ) {
									echo '<style type="text/css">#page0 { background: '.$bgcolor.' url(\''.$cover[1].'\') '.$pos.' no-repeat; -webkit-background-size: '.$fullbleed.';-moz-background-size: '.$fullbleed.';-ms-background-size:'.$fullbleed.';-o-background-size:'.$fullbleed.';background-size:'.$fullbleed.';}</style>';
								} else {
									echo '<img src="'.$cover[1].'" width="100%" height="auto" />';
								}
							} else if ( preg_match('/src="([^"]+)"/', $value, $cover) ) {
								if ($fullbleed != '0' ) {
									echo '<style type="text/css">#page0 { background: '.$bgcolor.' url(\''.$cover[1].'\') '.$pos.' no-repeat; -webkit-background-size: '.$fullbleed.';-moz-background-size: '.$fullbleed.';-ms-background-size:'.$fullbleed.';-o-background-size:'.$fullbleed.';background-size:'.$fullbleed.';}</style>';
								} else {
									echo '<img src="'.$cover[1].'" width="100%" height="auto" />';
								}
							} else {};
							$first = false;
						}
				};
			} else {
				// No shortcode so look for a Featured Image //
				$post_image_id = get_post_thumbnail_id($post->ID);
					if ($post_image_id) {
						$hovertext = '';
						$thumbnail = wp_get_attachment_image_src( $post_image_id, 'full', false);
						if (is_array($thumbnail)) $thumbnail = reset($thumbnail);
					}
				echo '<img src="'.$thumbnail.'" alt="'.$hovertext.'" title="'.$hovertext.'" />';	
			}
	} else {
		// Ignore cover shortcode and use Featured Image //
		$post_image_id = get_post_thumbnail_id($post->ID);
		if ($post_image_id) {
			$hovertext = '';
			$thumbnail = wp_get_attachment_image_src( $post_image_id, 'full', false);
			if (is_array($thumbnail)) $thumbnail = reset($thumbnail);
			echo '<img src="'.$thumbnail.'" alt="'.$hovertext.'" title="'.$hovertext.'" />';
		}
	}
		echo '<div style="position:absolute;top:0;left:0;width:100%;">';
		if ($config) { $overlay = $config['overlay']; } else { $overlay = '0'; };
		if ( $overlay == '0' ) {
			if ( $options_cover['mastheadtype'] == "0" ) { 
				echo "<h1 class='masthead'>".$options_cover['mastheadtext']."</h1>";
			} else if ($options_cover['mastheadtype'] == "1" ) {  
				echo "<img class='masthead' src='".$options_cover['mastheadimg']."' alt='".$options_cover['mastheadtext']."' width='100%' />";
			} else {};
			if ( $options_cover['mastheadtype'] == "0" || $options_cover['mastheadtype'] == "1" ) {
			?> 
			<p id="cover_dateline"><?php the_title(); ?> - <?php the_time('F jS, Y') ?></p> 
			<?php 
			} else {};
			if ( isset($options_cover['show_featured']) && $options_cover['show_featured'] == "1" ) {
		?>
			<ul id="features">		
			<?php	
				/* 	FIND ARTICLES IN FEATURED CATEGORY
					for this we just use our stored array of articles so we don't have to
					do another full query to the db because get_post() is faster.
				*/
				foreach( $articles as $article) {
					$post = get_post($article);
					if ( in_category($featured) ) {
						if(!$post) {
						} else if ( $post->post_title == '') { ?>
							<li class="list_up"><a href="#page<?php echo $x; ?>">Page <?php echo $x; ?></a></li>
			<?php		} else { ?>
							<li class="list_up"><a href="#page<?php echo $x; ?>"><?php	the_title(); ?></a></li>
			<?php		} 
					}
					$x = $x+1;
				} 
				if($autocover=='0') {$x = 1;}else{ $x = 0; } ?>
			</ul>
	<?php } 
		}
	//if ($covercode == "1") { 
	echo '</div>';
	//}
	?>
		</div>
	<?php 	} ?>
	<!--// Build All the Pages //-->
	<?php	
		/*	We need to run a new query for this because we do NOT want any "ryuzine"
			post-type in this and we need to sort them Ascending old->new.
		*/
			$args = array(
				'tax_query' => array(
					array(
						'taxonomy' => 'issues',
						'field' => 'term_id',
						'terms' => $issue_id
					)
				),
				'posts_per_page'=>-1,
				'orderby'=>'date',
				'order'=>'ASC',
				'post_type'=>array('post','webcomic_post','comic','mangapress_comic')
				);

			$the_query = new WP_Query($args);
			if ( have_posts() ) : while ( $x < $pagecount) : $the_query->the_post();
	?>
		<h1 class="section_head"><?php if(!$post || $post->post_title == '') {echo "Page ".$x;}else{the_title();} ?></h1>
		<div id="page<?php echo $x ?>" class="page_box">
		<?php if (!$post) { ?>
				<p>This Page was intentionally left blank</p>
			<?php } else {  
			if ($config) { $byline = $config['byline']; } else { $byline = $options_page['byline']; }
			if($byline=="1"){ echo '<small>'.the_time('F jS, Y')." by ".the_author_posts_link().'<small>';};
			if ($config) { $postbody = $config['postbody']; } else { $postbody = $options_page['postbody']; };
	
			if (function_exists('comicpress_display_comic') && comicpress_in_comic_category() ) {	// ComicPress support
					echo ryuzine_display_comic();
					if ($postbody == "1") {the_content();} // If in comics category and text enabled
			} else if ('webcomic_post'==get_post_type() ) {
				the_webcomic_object( 'full');
					if ($postbody == "1") {the_content();} // If in comics category and text enabled
			} else if ('comic'==get_post_type() ) {
				if (function_exists('easel_comics_display_comic')) {
					echo easel_comics_display_comic('full');
				} else if (function_exists('ceo_pluginfo')) {
					echo ryuzine_display_comic();
				} else {};
					if ($postbody == "1") {the_content();} // If in comics category and text enabled
			} else if ( 'mangapress_comic'==get_post_type() ) {
					echo ryuzine_display_comic();
			} else {
				the_content();
			}
			if ($config) { $metadata = $config['metadata']; } else { $metadata = $options_page['metadata'];};
			if ( $metadata == "1" ) { ?>

			<small class="metadata">
				<span class="category">Filed under: <?php the_category(', ') ?> <? if(!is_single()) echo "|"; ?> <?php edit_post_link('Edit', '', ' | '); ?> <?php comments_popup_link('Comment (0)', ' Comment (1)', 'Comments (%)'); ?></span>
				<?php if ( function_exists('wp_tag_cloud') ) : ?>
				<?php the_tags('<span class="tags">Article tags: ', ', ' , '</span>'); ?>
				<?php endif; ?>
			</small>
			<?php }; 
			if ($config) { $comments = $config['comments']; } else { $comments = $options_page['comments']; };
/*			if ( $comments == "1" ) {
					$themepath = get_stylesheet_directory_uri().'/comments.php';
					echo $themepath.'<br/>';
					$withcomments = true; 			
					comments_template( ''.$themepath.'',true ); // Get wp-comments.php template 
*/
if ( $comments == "1") {
	if (comments_open($post->ID)) {
		comments_template();
		echo '<p><em>Note: After submitting a comment you will be taken to the corresponding Blog Post page</em></p>';
	} else {
//		echo '<p><em>(Comments are closed for this page.)</em></p>';
	}
}

					?>

					

	<?php //	};
			/* Check for custom styling field */
			$custom_fields = get_post_custom();
			if (isset($custom_field['ryuzine'])){
				$ryuzine_field = $custom_fields['ryuzine'];
					if ($ryuzine_field != '') {
						foreach ( $ryuzine_field as $key => $value )
						echo $value;
					}
			}
		} ?>
		</div>
	<?php $x = $x+1; ?>
	<?php endwhile; 
	wp_reset_query(); 
	else: ?>
	<?php 
	echo '<p>'._e('Sorry, no posts matched your criteria.').'</p>';
	endif; 
	if($autocover=='0') {$x = 1;}else{ $x = 0; } 
	?>
</div><!--// End of Issue //-->

<div id="end_matter">

	<div id="exit_sign">
	<?php echo get_post_meta($post->ID, '_ryuthank', TRUE); ?>
	</div>

	<h1>Table of Contents</h1>
	<ul id="nav">
	<!--// Build Table of Contents //-->
	<?php if ($autocover == '0') { ?>
		<li class="list_up"><a href="#page0">Cover</a></li>
	<?php } ?>
	<?php 	
		/*	Again, let's not do a full query to the db
			and just use get_post() again for TOC content
		*/

		foreach( $articles as $article) {
			$post = get_post($article);
	?>
			<li class="list_up"><a href="#page<?php echo $x ?>"><?php 
			if(!$post || $post->post_title == '') {
			echo "Page ".$x;
			}else{
			the_title();
			} ?></a></li>
	<?php	$x = $x+1; 
		};
	if($autocover=='0') {$x = 1;}else{ $x = 0; } ?>
	</ul>

	<?php 
	if ( $options_ad['boxadcontent'] != null || $options_ad['boxadcontent'] != '' ) {
		if ($options_ad['boxadtype'] == "1") {
		echo '<figure id="boxad" class="light_boxed">'.$options_ad['boxadcontent'].'</figure>';
		} else {
		echo '<figure id="boxad" class="light_boxed"><p class="xbox">Advertisement</p><a href="'.$options_ad['boxlink'].'" target="_blank"><img src="'.$options_ad['boxadcontent'].'" /></a></figure>';
		}
	}
	?> 
	<?php  
	/* 	We got a list of post_type = "ryuzine" at the top.
		Now recall that list so we can get any lightbox content
		Note: This is WAY faster than running another query
	*/
		foreach ($ryuzines as $zine) {
			$post = get_post($zine);
			$pattern = get_shortcode_regex();
			preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches );
		
			if( is_array( $matches ) && array_key_exists( 2, $matches ) && in_array( 'ryubox', $matches[2] ) )
			{
				foreach ($matches[0] as $value) {
					$value = wpautop( $value, true );
					echo do_shortcode($value);
				}
			} else {
				// Do Nothing
			}
		}
	 ?>
	<div id="social_widget"><?php if ( dynamic_sidebar( 'ryuzine-share' ) ) : else : endif; ?></div>

</div><!--// end of end matter //-->
</div><!--// end of ryumask //-->
<div id="splash">
	<div id="splashcell">
		<div id="splashblock">
			<div style="background:transparent url('<?php echo $ryupress; ?>ryuzine/images/app/icons/ryuzine-icon-02.png') 0 0 no-repeat;height:72px;width:72px;margin:0 auto;-webkit-border-radius:8px;-moz-border-radius:8px;-o-border-radius:8px;-ms-border-radius:8px;border-radius:8px;-webkit-box-shadow:0px 0px 1000px #fff;-moz-box-shadow:0px 0px 1000px #fff;box-shadow:0px 0px 1000px #fff;">
				<p style="display:none;">This page was made for the <img src="<?php echo $ryupress; ?>ryuzine/images/app/icons/ryuzine-icon-02.png" alt="Ryuzine Icon" height="16" width="16" align="bottom" /> <a href="http://www.ryumaru.com/products/ryuzine/" target="_blank">Ryuzine&trade; webapp</a>.</p>
			</div>
			<noscript id="error_msg" style="display:block;margin:0 20px;">
				<p><strong>App Error:</strong> Javascript <span style="display:none;">and Stylesheets</span> disabled!</p>
				<p>Enable <span style="display:none;">both</span> to view as a web application.  Disable stylesheets to view as a plain web page. Then reload/refresh.</p>
			</noscript>
		</div>
		<p class="splash-fineprint">Ryuzine Copyright 2011,2012 K.M. Hansen &amp; Ryu Maru - All Rights Reserved</p>
	</div>
</div>
</body>
</html>