<?php
/*
Widget Name: Ryuzine List Widget
Widget URI: http://www.ryumaru.com
Description: Show list of Ryuzine Press Editions
Author: K.M. Hansen
Author URI: http://www.kmhcreative.com/labs/
Version: 1.3
*/

class ryuzine_list_widget extends WP_Widget {
	protected $defaults;
	function __construct() {
		// setup widget area
		$this->defaults = array(
 			'title' => '', 
 			'hidecontent' => false, 
 			'onlyhome' => false, 
 			'listsize' => -1, 
 			'ascending' => true, 
 			'coverthumbs' => 0, 
 			'restrict' => false, 
 			'coversize' => '125', 
 			'coverhw' => true, 
 			'racktitle' => '', 
 			'showrack' => false  		
		);
		parent::__construct(
			__CLASS__, // Base ID
			__( 'Ryuzine Press Editions'), // Name
			array( 'classname' => __CLASS__, 'description' => __( 'Displays your Ryuzine Press Editions')) //Args
		);
	}	
	
	function widget($args, $instance) {
		global $post, $wp_query;
		extract($args);
		if (!is_home() && $instance['onlyhome']) return;
		$listsize = empty($instance['listsize']) ? -1 : $instance['listsize'];	// make sure list size is not empty!
		if (!is_numeric($listsize)) { $listsize = -1; }							// make sure list size is a number!
		if ($instance['ascending']) { $order = 'ASC'; } else { $order = 'DESC';}
			$q = array(
					'posts_per_page' => $listsize,
					'order' => $order,
					'post_type' => 'ryuzine'
					);
			$posts = get_posts($q);
			$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
			if ($before_widget == '') {$before_widget = '<aside class="widget"><div class="widget-content">';};
			if ($after_widget == '') { $after_widget = '</div></aside>';};
		if ($instance['restrict'] && !empty($instance['coversize']) ) {
			$cs = $instance['coversize'];
			if (is_numeric($cs) && $cs > 0 && $cs == round($cs)) {
				// everything is good, use it!
			} else { 
				$cs = '125';	// use default value
			}
			if ($instance['coverhw'] == 0) { 
				$hw = 'height="'.$cs.'" width="auto"'; 
			} else {
				$hw = 'height="auto" width="'.$cs.'"';
			}
		} else { 
			$hw = '';
		}
			// If "Hide" is checked only do this if there are posts to show!
			if ( !( $instance['hidecontent'] && empty($posts) ) ) {
				if ($title == '') { $title = 'Ryuzine Press Editions'; }
				echo $before_widget;
				echo "<h2 class=\"widgettitle widget-title\">".$title."</h2>";
				echo "<ul>";
				foreach ($posts as $post) {
					setup_postdata($post);
					if (!($instance['hidecontent'] && empty($post->post_content))) {
						$temp_query = $wp_query->is_single;
						$wp_query->is_single = true;
						echo '<li class="cat-item"><a href="'.get_permalink().'">';
						if ( $instance['coverthumbs'] == 1 || ($instance['coverthumbs']==0 && (($post == $posts[0] && $order == 'DESC') || ($post == $posts[count($posts)-1] && $order == 'ASC'))) ) {
							$pattern = get_shortcode_regex();
							preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches );
							// find out if a cover image is set with shortcode
							if( is_array( $matches ) && array_key_exists( 2, $matches ) && in_array( 'ryucover', $matches[2] ) )
							{
								$first = true;
								foreach ($matches[0] as $value) {
										$value = wpautop( $value, true );
										if ( preg_match_all('/ryucover/', $value, $covers) && $first ) {
										// if you find one, extract the img src and print it //
											if ( preg_match('/src="([^"]+)"/', $value, $cover) ) { 
											echo '<img src="'.$cover[1].'" '.$hw.' class="ryucover" /><br/>';
											}
											if ($instance['coverthumbs']==0) {
												$first = false;	// stop after the first one
											}
										}
								};
							} else {
								if ( $instance['coverthumbs'] == 1 || ($instance['coverthumbs']==0 && (($post == $posts[0] && $order == 'DESC') || ($post == $posts[count($posts)-1] && $order == 'ASC'))) ) {
									// Look for a Featured Image //
									$post_image_id = get_post_thumbnail_id($post->ID);
										if ($post_image_id) {
											$hovertext = '';
											$thumbnail = wp_get_attachment_image_src( $post_image_id, 'medium', false);
											if (is_array($thumbnail)) $thumbnail = reset($thumbnail);
										}
									echo '<img src="'.$thumbnail.'" '.$hw.' class="ryucover" /><br/>';
								}
							}
						}
						echo get_the_title();
						echo "</a></li>\r\n";
						$wp_query->is_single = $temp_query;
					}
				}
				echo "</ul>";
				echo $after_widget;
				if ($instance['showrack']) {
					$racktitle = empty($instance['racktitle']) ? '' : apply_filters('widget_title', $instance['racktitle']);
				if ($racktitle == '') { $racktitle = 'Ryuzine Rack'; }
					echo $before_widget;
					echo "<h2 class=\"widget-title\"><a href=\"".get_post_type_archive_link( 'ryuzine' )."\">".$racktitle."</a></h2>";
					echo $after_widget;
				}
				wp_reset_query();
			};

	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['hidecontent'] = (bool)( $new_instance['hidecontent'] == 1 ? true : false );
		$instance['onlyhome'] = (bool)( $new_instance['onlyhome'] == 1 ? true : false );
		$instance['listsize'] = strip_tags($new_instance['listsize']);
		$instance['ascending'] = (bool)( $new_instance['ascending'] == 1 ? true : false );
		$instance['coverthumbs'] = $new_instance['coverthumbs'];
		$instance['restrict'] = (bool)( $new_instance['restrict'] == 1 ? true : false );
		$instance['coversize'] = strip_tags($new_instance['coversize']);
		$instance['coverhw'] = (bool)( $new_instance['coverhw'] == 1 ? true : false );
		$instance['racktitle'] = strip_tags($new_instance['racktitle']);
		$instance['showrack'] = (bool)($new_instance['showrack'] == 1 ? true : false );
		return $instance;
	}
	
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		$title = strip_tags($instance['title']);
		$hidecontent = $instance['hidecontent'];
		$onlyhome = $instance['onlyhome'];
		$listsize = strip_tags($instance['listsize']);
		$showasc = $instance['ascending'];
		$coverthumbs = $instance['coverthumbs'];
		$restrict = $instance['restrict'];
		$coversize = $instance['coversize'];
		$coverhw = $instance['coverhw'];
		$racktitle = $instance['racktitle'];
		$showrack = $instance['showrack'];
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Heading:'); ?><br /><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('hidecontent'); ?>"><input id="<?php echo $this->get_field_id('hidecontent'); ?>" name="<?php echo $this->get_field_name('hidecontent'); ?>" type="checkbox" value="1" <?php checked(true, $hidecontent); ?> /> <?php _e('Hide widget if there is no content?'); ?></label></p>
		<p><label for="<?php echo $this->get_field_id('onlyhome'); ?>"><input id="<?php echo $this->get_field_id('onlyhome'); ?>" name="<?php echo $this->get_field_name('onlyhome'); ?>" type="checkbox" value="1" <?php checked(true, $onlyhome); ?> /> <?php _e('Display only on the home page?'); ?></label></p>

		<p><label for="<?php echo $this->get_field_id('listsize'); ?>"><?php _e('Show');?> <input class="widefat" style="width:25%" id="<?php echo $this->get_field_id('listsize'); ?>" name="<?php echo $this->get_field_name('listsize'); ?>" type="text" value="<?php echo esc_attr($listsize); ?>" /> <?php _e('Editions (-1 = all)'); ?></label></p>
		<p><label for="<?php echo $this->get_field_id('ascending'); ?>"><input id="<?php echo $this->get_field_id('ascending'); ?>" name="<?php echo $this->get_field_name('ascending'); ?>" type="checkbox" value="1" <?php checked(true, $showasc); ?> /> <?php _e('Order Oldest to Newest?'); ?></label></p>

		<p><label><?php _e('Cover Art Thumbnails'); ?><br/>
			<input id="<?php echo $this->get_field_id('coverthumbs'); ?>-0" name="<?php echo $this->get_field_name('coverthumbs'); ?>" type="radio" value="0" <?php echo $coverthumbs == 0 ? 'checked' : ''; ?> /> <?php _e('Newest Only'); ?><br/>
		   	<input id="<?php echo $this->get_field_id('coverthumbs'); ?>-1" name="<?php echo $this->get_field_name('coverthumbs'); ?>" type="radio" value="1" <?php echo $coverthumbs == 1 ? 'checked' : ''; ?> /> <?php _e('All Cover Art'); ?><br/>
			<input id="<?php echo $this->get_field_id('coverthumbs'); ?>-2" name="<?php echo $this->get_field_name('coverthumbs'); ?>" type="radio" value="2" <?php echo $coverthumbs == 2 ? 'checked' : ''; ?> /> <?php _e('No Cover Art'); ?></label></p>
			
		<p><label for="<?php echo $this->get_field_id('restrict'); ?>"><input id="<?php echo $this->get_field_id('restrict'); ?>" name="<?php echo $this->get_field_name('restrict'); ?>" type="checkbox" value="1" <?php checked(true, $restrict); ?> /> <?php _e('Restrict Cover Thumbnail Size?'); ?></label></p>
		<p><input class="widefat" style="width:25%" id="<?php echo $this->get_field_id('coversize'); ?>" name="<?php echo $this->get_field_name('coversize'); ?>" type="text" value="<?php echo esc_attr($coversize); ?>" /> <?php _e('px'); ?>  (
			<input id="<?php echo $this->get_field_id('coverhw'); ?>-0" name="<?php echo $this->get_field_name('coverhw'); ?>" type="radio" value="0" <?php echo $coverhw == 0 ? 'checked' : ''; ?> /> <?php _e('High'); ?>
		   	<input id="<?php echo $this->get_field_id('coverhw'); ?>-1" name="<?php echo $this->get_field_name('coverhw'); ?>" type="radio" value="1" <?php echo $coverhw == 1 ? 'checked' : ''; ?> /> <?php _e('Wide'); ?> )</p>


<?php 
		$rack_status = get_option('ryuzine_opt_rack');
			if ($rack_status['install']=='1') { // Only show this option if Ryuzine Rack is installed to the current theme
?>
			<p><label for="<?php echo $this->get_field_id('racktitle'); ?>"><?php _e('Ryuzine Rack Title:'); ?><br /><input class="widefat" id="<?php echo $this->get_field_id('racktitle'); ?>" name="<?php echo $this->get_field_name('racktitle'); ?>" type="text" value="<?php echo esc_attr($racktitle); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('showrack'); ?>"><input id="<?php echo $this->get_field_id('showrack'); ?>" name="<?php echo $this->get_field_name('showrack'); ?>" type="checkbox" value="1" <?php checked(true, $showrack); ?> /> <?php _e('Show link to Ryuzine Rack Newsstand?'); ?></label></p>
<?php	
		}
   }
}


function ryuzine_list_widget_register() {
	register_widget('ryuzine_list_widget');
}

add_action( 'widgets_init', 'ryuzine_list_widget_register');

?>