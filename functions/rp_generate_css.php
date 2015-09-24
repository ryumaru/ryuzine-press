<?php
/*
	Ryuzine Press Plugin
	This file generates stylesheets for Ryuzine Press Editions
	if they are being stored within the Ryuzine plugin /css folder
*/

// Create CSS File from within Admin Pages //
function ryu_create_css($content,$id) {
	// Check permissions
	if ( !current_user_can( 'administrator' ) ) {
		echo "<div class='error'><p>Sorry, you do not have the correct priveledges to install the files.</p></div>";
		return ; 
	}
	$cssFileName = "issue_".$id;
	$cssFile = WP_PLUGIN_DIR.'/ryuzine-press/css/'.$cssFileName.'.css';
	// Find out if the directory is writable or not //
	if (is_writable(WP_PLUGIN_DIR.'/ryuzine-press/css')) {
	 	// Strip out the in-page style tags if they are there //
 		$content = strip_tags($content);
		$fh = fopen( $cssFile , 'w') or die("can't create file");
		fwrite($fh, $content);
		fclose($fh);
		// Update css check variable for success! //
		update_option('ryu_css_admin',1);
	} else {
	// If directory is not writable, files are not created  //
	// Update css check variable for failure notice			//
		update_option('ryu_css_admin',2);
	}

}

// This function gets all the issue-specific styles and tries to make stylesheets //
function generate_ryuzine_stylesheets() {
	// verify this came from the our screen and with proper authorization.
	if ( !wp_verify_nonce( $_POST['ryu_regenstyles_noncename'], 'ryuzine-regenstyles_install' ) ) {
		return ;
	}
	// Check permissions
	if ( !current_user_can( 'administrator' ) ) {
		echo "<div class='error'><p>Sorry, you do not have the correct priveledges to install the files.</p></div>";
		return ; 
	}
	$my_query = null;
	$my_query = new WP_Query( array('post_type' => 'ryuzine') );
	if( $my_query->have_posts() ) {
  		while ($my_query->have_posts()) : $my_query->the_post();
  			$stylesheet = "";
			$issuestyles = get_post_meta( get_the_ID(), '_ryustyles', false);
			if( !empty( $issuestyles ) ){
				foreach ( $issuestyles as $appendstyle) {
				// If there are multiple ryustyles append them //
				$stylesheet = $stylesheet.$appendstyle;
				}
			}
		if ($stylesheet != "") {
			ryu_create_css($stylesheet,get_the_ID());
		}
  		endwhile;
	}
	// reset css check //
//	update_option('ryu_css_admin',0);
	wp_reset_query(); 	
	return;
}



?>