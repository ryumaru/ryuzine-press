<?php
/*
	Ryuzine Press Plugin
	This creates a customizable "sidebar" that is 
	used by the "Share" button within Ryuzine and
	Ryuzine Rack intended for a social media sharing
	widget to be dropped into it.  Because some only
	work on posts and pages Rack now has a separate
	one since it is an archive.
*/

function ryuzine_sidebar() {
	if ( function_exists('register_sidebar') ){
		register_sidebar(array(
			'name' => 'Ryuzine Share Bubble',
			'id' => 'ryuzine-share',
			'description' => 'Place the social networking/sharing widget here and it will show in the Ryuzine share bubble',
			'before_widget' => '',
			'after_widget' => '',
			'before_title' => '',
			'after_title' => '',
		));
	}
}
// Register Sidebar late to avoid re-ordering existing theme sidebars //
add_action( 'wp_loaded', 'ryuzine_sidebar' );

function ryuzinerack_sidebar() {
	if ( function_exists('register_sidebar') ){
		register_sidebar(array(
			'name' => 'Ryuzine Rack Share Bubble',
			'id'   => 'ryuzinerack-share',
			'description' => 'Place the social networking/sharing widget here and it will show in the Ryuzine Rack share bubble.  IMPORTANT: Widgets that require $post will not work, Ryuzine Rack is an ARCHIVE page.',
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '',
			'after_title'   => '',
		));
	}
}
add_action( 'wp_loaded', 'ryuzinerack_sidebar' );
?>