<?php 
/*
Template Name: Ryuzine Archive
*/	
/* 	This is the Ryuzine Press Rack 1.1 template file 
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

		$issues = get_the_terms($post->ID,'issues');
		$ryupress = plugins_url()."/ryuzine-press/";
		$catalog = get_option('ryuzine_rack_cat');
			$cats = count($catalog);
		$options_cover = get_option('ryuzine_opt_covers');
		$options_page = get_option('ryuzine_opt_pages');
		$options_addon = get_option('ryuzine_opt_addons');
		$options_ad = get_option('ryuzine_opt_ads');
		$options_rack = get_option('ryuzine_opt_rack');
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
		// Adjust $pagecount based on cover settings
		if ($options_cover['autocover']=='0') {
			$x = 1;
		} else { 
			$pagecount = $pagecount-1; 	/* pages minus the Ryuzine itself */
			$x = 0;
		}
		if ($pagecount&1) { $pagecount = $pagecount+1; } /* Don't allow odd counts! */
		
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
		if ($id!='') { $url = '#'.$id; }	// backwards compat with old lightbox
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

/* Make App Icons On the Fly */
if ($options_cover['app_icon'] != '') {
	$icon = $options_cover['app_icon'];
	$favicon 	= aq_resize( $icon, 16, 16, true,true,true); 
	$ipad_icon 	= aq_resize( $icon, 72, 72, true,true,true);
	$retina_icon= aq_resize( $icon, 114, 114, true,true,true);
	$iphone_icon= aq_resize( $icon, 57, 57, true,true,true);
	$window_tile= aq_resize( $icon, 144, 144, true,true,true);
} else {
	$favicon = $ryupress.'ryuzine/images/app/icons/rack-favicon.png';
	$iphone_icon = $ryupress.'ryuzine/images/app/icons/rack-icon-03.png';
	$ipad_icon = $ryupress.'ryuzine/images/app/icons/rack-icon-02.png';
	$retina_icon = $ryupress.'ryuzine/images/app/icons/rack-icon-01.png';
	$window_tile = $ryupress.'ryuzine/images/app/icons/rack-tile.png';
}
?>
<!DOCTYPE html>
<html lang="<?php echo $options_addon['natlang']; ?>">
<head>
<meta content="text/html; charset=UTF-8" http-equiv="content-type">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="msapplication-TileColor" content="#ADDEFA" />
<meta name="msapplication-TileImage" content="<?php echo $window_tile; ?>" />
<link rel="icon" type="image/png" href="<?php echo $favicon; ?>" />
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $retina_icon; ?>" />
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $ipad_icon; ?>" />
<link rel="apple-touch-icon-precomposed" href="<?php echo $iphone_icon; ?>" />

<link rel="stylesheet" type="text/css" href="<?php echo $ryupress; ?>ryuzine/css/ui.css" id="ui_format" /> 
<link rel="stylesheet" type="text/css" href="<?php echo $ryupress; ?>ryuzine/css/rackgrid.css" id="screen_format" />
<link rel="stylesheet" type="text/css" href="<?php if ($options_addon['defaultTheme'] != '' && $options_addon['defaultTheme'] != null) { echo $ryupress.'ryuzine/theme/'.$options_addon['defaultTheme'].'/theme.css';} else {echo $ryupress.'ryuzine/css/blank.css'; }; ?>" id="ui_theme" />
<link rel="stylesheet" type="text/css" href="<?php echo $ryupress; ?>ryuzine/css/continuous.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $ryupress; ?>ryuzine/css/thisissue.css" id="this_issue" />
<link rel="stylesheet" type="text/css" href="<?php echo $ryupress; ?>ryuzine/css/blank.css" id="colortext" />
<title><?php 
	if ($options_rack['racktitle']!='') {
		echo $options_rack['racktitle'];
	} else {
		bloginfo('name'); 
	}
?></title>
<script type="text/javascript">
/*	RYUZINE READER/RACK CONFIG FILE
	Version 1.0
	
	Load this file before ryuzine.js or ryuzine.rack.js
*/

var RYU = RYU || {};
RYU.config = {
	/* 	LOCALIZATION SETTINGS	*/
	language	:	"<?php $options_addon['language']; ?>",		// ISO 639-1 language code (ignored is "localize" add-on is not loaded)
	/*	RYUZINE READER/RACK		*/
	binding		:	"left",	// ignored by ryuzine rack
	pgsize		:	0,		// ignored by ryuzine rack
	zoompan		:	<?php echo  $options_page['zoompan']; ?>,
	maxzoom		:	<?php echo  $options_page['maxzoom']; ?>,
	pgslider	:	0,		// ignored by ryuzine rack
	viewani		:	<?php echo $options_page['viewani']; ?>,
	bmarkData	:	[			// Default preset Reader bookmarks : ["label","URL"]
		["<?php echo $options_page['bmark1']; ?>","<?php echo $options_page['bmark1url']; ?>"],
		["<?php echo $options_page['bmark2']; ?>","<?php echo $options_page['bmark2url']; ?>"]
	],
	/*	RYUZINE RACK SETTINGS	*/
	rackTitle	:	"<?php if(isset($options_rack['racktitle'])){echo $options_rack['racktitle'];}else{echo 'Newsstand';} ?>",		// Optional name to insert as rack.htm title
	rackItems	:	<?php echo $options_rack['rackitems']; ?>,			// 0 = no pagination | value = number of items per page
	linkOpens	:	<?php echo $options_rack['linkopens']; ?>,			// 0 = default | 1 = _self | 2 = _blank | 3 = _parent | 4 = _top | 5 = inrack | "id" = window id
	rackData	:	[			// Data Catalog in /data/cat/ folder : ["Label","filename.htm"]
	<?php 
	for ($r=0;$r<$cats;$r++) {
		echo '["'.$catalog[$r][0][0].'","autocat'.$r.'"]';
		if ($r!=$cats-1){
			echo ",";
		}
	}
	?>
	],
	mediaType	:	[			// Media Types : ["type","label"]
		["<?php echo $options_rack[0][0]; ?>","<?php echo $options_rack[1][0]; ?>"],
		["<?php echo $options_rack[0][1]; ?>","<?php echo $options_rack[1][1]; ?>"],
		["<?php echo $options_rack[0][2]; ?>","<?php echo $options_rack[1][2]; ?>"],
		["<?php echo $options_rack[0][3]; ?>","<?php echo $options_rack[1][3]; ?>"],
		["<?php echo $options_rack[0][4]; ?>","<?php echo $options_rack[1][4]; ?>"]
	],
	/* 	THEME SETTINGS			*/
	swapThemes	:	<?php echo  $options_addon['swapThemes']; ?>,		// 0 = no | 1 = yes : Swap themes based on device type
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
	autopromo	:	<?php echo $options_rack['autopromo']; ?>,			// 0 = off | value = animation interval in seconds (Ryuzine Rack)
	maxpromos	:	<?php echo $options_rack['maxpromos']; ?>,			// Maximum number of promotions in carousel	(Ryuzine Rack)
	AddOns		: [<?php 
	if (isset($options_addon['selected_addons'])) {
		$addons = explode(',',$options_addon['selected_addons']); 
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
<script type="text/javascript" src="<?php echo plugins_url(); ?>/ryuzine-press/ryuzine/js/ryuzine.rack.js" ></script>

</head>
<body id="ryuzinerack">
<a href="#nav" id="skip2nav">Skip to Navigation</a>
<!--// publication content below this line //-->
<div id="ryu_mask">
	<div id="front_matter">
		<h1 id="splash_title"><?php 
			if (isset($options_cover['splashimg']) && $options_cover['splashimg'] == "1" && ($options_cover['mastheadimg'] != null || $options_cover['mastheadimg'] != '' ) ) { 
				echo '<img src="'.$options_cover['mastheadimg'].'" style="max-width:98%;height:auto;" />';
			} else {
				if ($options_rack['racktitle'] != '') {
					echo $options_rack['racktitle'];
				} else if ($options_cover['mastheadtext'] != '') { 
					echo $options_cover['mastheadtext']; 
				} else { bloginfo('name');} 
			}?></h1>		
		<p id="summary"><?php bloginfo('name'); ?> Newsstand webapp powered by Ryuzine&trade;</p>
		
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
</div><!--// end of front matter //-->
<div id="issue">
	<h1 class="section_head"></h1>
	<div id="page0" class="page_box">	
		<div id="racktop" class="live"></div>		
		<div id="item_list"></div>
		<div id="footer">		
		</div>	

	</div>
	<h1 class="section_head"></h1>
	<div id="page1" class="page_box">
		<div id="autocat0">
		<table cellpadding="5" cellspacing="0" border="1"><tr><td><strong>Masthead</strong> (optional):</td>
		<td title="masthead_img">
		<?php 
			if ($catalog[0][0][2] != null && $catalog[0][0][2] != '' ) { 
				echo ''.$catalog[0][0][2].'';
			} ?>
		</td></tr></table>
		<p></p>
		<table cellpadding="0" cellspacing="0" border="1" class="sortable">
		<thead>
		<tr>
		<?php 
			// creates the headers of the autocat0 data table
			$rack_html = '<th class="nosort">'.$catalog[0][1][0].'</th>'."\r\n".
			'<th>'.$catalog[0][1][1].'</th>'."\r\n".
			'<th>'.$catalog[0][1][2].'</th>'."\r\n".
			'<th class="nosort">'.$catalog[0][1][3].'</th>'."\r\n".
			'<th>'.$catalog[0][1][4].'</th>'."\r\n".
			'<th class="nosort">'.$catalog[0][1][5].'</th>'."\r\n".
			'<th>'.$catalog[0][1][6].'</th>'."\r\n".
			'<th class="nosort">'.$catalog[0][1][7].'</th>'."\r\n".
			'<th class="nosort">'.$catalog[0][1][8].'</th>'."\r\n";
			echo $rack_html;
		?>
		</tr>
		</thead>
		<tbody>
		<?php
			// adds Ryuzine Press Editions to front of table newest -> oldest 
			$x = 0;
			$args = array(
				'post_type' => 'ryuzine',
				'posts_per_archive_page' => -1,
				'orderby'=>'date',
				'order'=>'ASC'
				);

			query_posts($args);

				if ( have_posts() ) : while ( have_posts() ) : the_post();
		?>
		<tr>
		<td><?php echo $x ?></td>
		<td><?php the_date('M Y') ?></td>
		<td><?php the_title(); ?></td>
		<td><?php the_excerpt(); ?></td>
		<td><?php   
			$rackcat = get_the_terms( $post->ID, 'rackcats');
			if ($rackcat) {
				foreach ($rackcat as $rcat) {
					if ($rcat->name != '') {
						$thiscat = $rcat->name;
					}
				}
				if ($thiscat != '') { // assuming it isn't a blank name
					echo $thiscat;
				} else {
					echo 'Ryuzine Press Edition';
				}
			}
			// otherwise will show "Uncategorized" //
		?></td>
		<td><?php the_permalink(); ?></td>
		<td>Ryuzine</td>
		<td><?php
			$pattern = get_shortcode_regex();
			preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches );
			// find out if a cover image is set with shortcode
			if( is_array( $matches ) && array_key_exists( 2, $matches ) && in_array( 'ryucover', $matches[2] ) )
			{
				$first = true; // only take the first one you find //
				foreach ($matches[0] as $value) {
						$value = wpautop( $value, true );
						if ( preg_match_all('/ryucover/', $value, $covers) && $first ) {
						// if you find one, extract the img src and print it //
							if ( preg_match('/src="([^"]+)"/', $value, $cover) ) { 
							echo $cover[1];
							}
							$first = false;
						}
				};
			} else {
				// Look for a Featured Image //
				$post_image_id = get_post_thumbnail_id($post->ID);
					if ($post_image_id) {
						$hovertext = '';
						$thumbnail = wp_get_attachment_image_src( $post_image_id, 'medium', false);
						if (is_array($thumbnail)) $thumbnail = reset($thumbnail);
					}
				echo $thumbnail;
			}
	 ?></td>
		<td><?php
				// find out if a promo image is set with shortcode
				if( is_array( $matches ) && array_key_exists( 2, $matches ) && in_array( 'ryupromo', $matches[2] ) )
				{
					$firstpromo = true; // only take the first one you find //
					foreach ($matches[0] as $promovalue) {
							$promovalue = wpautop( $promovalue, true );
							if ( preg_match_all('/ryupromo/', $promovalue, $promos) && $firstpromo ) {
								// if you find one, extract the img href or src and print it //
								if ( preg_match('/auto/', $promovalue, $promo) ) {
									echo 'auto';
								}
								else if ( preg_match('/href="([^"]+)"/', $promovalue, $promo) ) { 
									echo $promo[1];
								} else if ( preg_match('/src="([^"]+)"/', $promovalue, $promo) ) {
									echo $promo[1];
								} else {};
								$firstpromo = false;
							}
					};
				}
	 ?></td>
		</tr>
		<?php $x = $x+1; endwhile; 
		endif; ?>
		<?php  wp_reset_query(); ?>
		<?php
			// adds Rack Builder Catalog 1 items (if any) to autocat0 data table
			$cats = count($catalog);
	for ($c=0;$c<$cats;$c++) {
		if ( $catalog[$c][0][1]==0 ) {
			$rows = count($catalog[$c]);
			$rack_html = '';
			for ($r=2; $r < $rows; $r++) {
					$index = ($x+$r)-1;
					$rack_html .= '<tr>'."\r\n".
					'<td>'.$index.'</td>'."\r\n".
					'<td>'.$catalog[$c][$r][1].'</td>'."\r\n".
					'<td>'.$catalog[$c][$r][2].'</td>'."\r\n".
					'<td>'.$catalog[$c][$r][3].'</td>'."\r\n".
					'<td>'.$catalog[$c][$r][4].'</td>'."\r\n".
					'<td>'.$catalog[$c][$r][5].'</td>'."\r\n".
					'<td>'.$catalog[$c][$r][6].'</td>'."\r\n".
					'<td>'.$catalog[$c][$r][7].'</td>'."\r\n".
					'<td>'.$catalog[$c][$r][8].'</td>'."\r\n".
					'</tr>'."\r\n";
				}
				echo $rack_html;
		};
	}
		?>
		</tbody>
		</table>
		</div> <!--// end of autocat0 //-->
	<?php 
	for ($c=0; $c<$cats;$c++) {
		if ( $catalog[$c][0][1]==1 ) { 
	?>
		<div id="autocat<?php echo $c; ?>">
		<table cellpadding="5" cellspacing="0" border="1"><tr><td><strong>Masthead</strong> (optional):</td>
		<td title="masthead_img<?php echo $c; ?>">
		<?php 
			if ($catalog[$c][0][2] != null && $catalog[$c][0][2] != '' ) { 
				echo ''.$catalog[$c][0][2].'';
			} ?>
		</td></tr></table>
		<p></p>
		<table cellpadding="0" cellspacing="0" border="1" class="sortable">
		<thead>
		<tr>
		<?php 
			$rack_html = '<th class="nosort">'.$catalog[$c][1][0].'</th>'."\r\n".
			'<th>'.$catalog[$c][1][1].'</th>'."\r\n".
			'<th>'.$catalog[$c][1][2].'</th>'."\r\n".
			'<th class="nosort">'.$catalog[$c][1][3].'</th>'."\r\n".
			'<th>'.$catalog[$c][1][4].'</th>'."\r\n".
			'<th class="nosort">'.$catalog[$c][1][5].'</th>'."\r\n".
			'<th>'.$catalog[$c][1][6].'</th>'."\r\n".
			'<th class="nosort">'.$catalog[$c][1][7].'</th>'."\r\n".
			'<th class="nosort">'.$catalog[$c][1][8].'</th>'."\r\n";
			echo $rack_html;
		?>
		</tr>
		</thead>
		<tbody>
	<?php	
			$rows = count($catalog[$c]);
			$rack_html = '';
			for ($r=2; $r < $rows; $r++) {
					$index = ($x+$r)-1;
					$rack_html .= '<tr>'."\r\n".
					'<td>'.$index.'</td>'."\r\n".
					'<td>'.$catalog[$c][$r][1].'</td>'."\r\n".
					'<td>'.$catalog[$c][$r][2].'</td>'."\r\n".
					'<td>'.$catalog[$c][$r][3].'</td>'."\r\n".
					'<td>'.$catalog[$c][$r][4].'</td>'."\r\n".
					'<td>'.$catalog[$c][$r][5].'</td>'."\r\n".
					'<td>'.$catalog[$c][$r][6].'</td>'."\r\n".
					'<td>'.$catalog[$c][$r][7].'</td>'."\r\n".
					'<td>'.$catalog[$c][$r][8].'</td>'."\r\n".
					'</tr>'."\r\n";
				}
				echo $rack_html;
		?>
		</tbody>
		</table>
		</div><!--// end of autocat<?php echo $c; ?> //-->		
	<?php	}
	}  ?>
	</div><!--// end of page1 //-->
</div><!--// End of Issue //-->
<div id="end_matter">

	<h1>Table of Contents</h1>
	<ul id="nav">
		<li><a href="#0">RyuzineRack</a></li>
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
	<div id="social_widget"><?php if ( dynamic_sidebar( 'ryuzinerack-share' ) ) : else : endif; ?></div>
</div><!--// end of end matter //-->
</div><!--// end of ryumask    //-->
<div id="splash">
<div id="splashcell">
<div id="splashblock">
<p style="display:none;">This page was made for the <a href="http://www.ryumaru.com/products/ryuzine/" target="_blank">Ryuzine&trade; webapp</a>.</p>
<noscript style="display:block;margin:0 20px;">
	<p><strong>App Error:</strong> Data Catalog cannot be loaded. Javascript <span style="display:none;">and Stylesheets</span> disabled!</p>
	<p>Enable <span style="display:none;">both</span> to view as a web application.  Disable stylesheets to view as a plain web page. Then reload/refresh.</p>
</noscript>
</div>
<p id="copyright" class="splash-fineprint">Ryuzine Copyright 2011-2020 K.M. Hansen &amp; Ryu Maru - All Rights Reserved</p>
</div>
</div>
</body>
</html>