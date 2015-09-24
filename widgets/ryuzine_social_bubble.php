<?php
/*
	Ryuzine Press Plugin
	This creates a customizable "sidebar" that is 
	used by the "Share" button within Ryuzine and
	is intended for a social network / sharing
	widget to be dropped into it.
*/

function ryuzine_sidebar() {
	if ( function_exists('register_sidebar') ){
		register_sidebar(array(
			'name' => 'Ryuzine Share Bubble',
			'id' => 'ryuzine-share',
			'description' => 'Place the social networking / sharing widget here and it will show in the Ryuzine UI share bubble',
			'before_widget' => '',
			'after_widget' => '',
			'before_title' => '',
			'after_title' => '',
		));
	}
}
// Register Sidebar late to avoid re-ordering existing theme sidebars //
add_action( 'wp_loaded', 'ryuzine_sidebar' );
?>