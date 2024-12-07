<?php
/*	
	Ryuzine Press Plugin
	This file customizes:
		All Posts management page
		All Editions management page
		All Comics (Comic Easel) management page
		All Comics (MangaPress) management page
*/

//add_filter('manage_taxonomies_for_post_columns', 'ryuzine_issues_taxonomies' );
//add_filter('manage_taxonomies_for_ryuzine_columns', 'ryuzine_issues_taxonomies');

// Add "Ryuzine Issues" column to Comic Easel / ComicPost management
if ( post_type_exists('comic') ) {
	add_filter('manage_edit-comic_columns','ryuzine_add_issues_column', 99, 1);
	add_action('manage_comic_posts_custom_column', 'ryuzine_issues_column_data',10,2);

	function ryuzine_add_issues_column( $columns ) {
		$columns['issues'] = 'Ryuzine Issues';
		return $columns;
	}

	function ryuzine_issues_column_data( $column, $post_id ) {
		global $post;
		switch($column) {
			case 'issues' :
				$issues = wp_get_post_terms( $post_id, 'issues');
				if ($issues) {
					$i = 1;
					foreach($issues as $issue) {
						echo '<a href="edit.php?post_type=comic&issues='.$issue->slug.'">'.$issue->name.'</a>';
						if (count($issues) > 1 && $i < count($issues) ) {
						echo ', ';
						}
						$i++;
					}
				}
			break;
		}
	}
}

// Manga Press?
if (defined('MP_FOLDER')) { 
	add_filter('manage_edit-mangapress_comic_columns','ryuzine_add_issues_column', 99, 1);
	add_action('manage_mangapress_comic_posts_custom_column', 'ryuzine_issues_column_data',10,2);

	function ryuzine_add_issues_column( $columns ) {
		$columns['issues'] = 'Ryuzine Issues';
		return $columns;
	}

	function ryuzine_issues_column_data( $column, $post_id ) {
		global $post;
		switch($column) {
			case 'issues' :
				$issues = wp_get_post_terms( $post_id, 'issues');
				if ($issues) {
					$i = 1;
					foreach($issues as $issue) {
						echo '<a href="edit.php?post_type=mangapress_comic&issues='.$issue->slug.'">'.$issue->name.'</a>';
						if (count($issues) > 1 && $i < count($issues) ) {
						echo ', ';
						}
						$i++;
					}
				}
			break;
		}
	}
}


function ryuzine_issues_taxonomies( $columns ) {
	global $typenow;
	$columns[] = 'issues';
	if ($typenow == 'ryuzine') {
		$columns[] = 'rackcats';
	}
	return $columns;
}

// Remove useless "Comic" column from Ryuzine Edition list
function my_columns_filter( $columns ) {
    unset($columns['comic']);
    return $columns;
}
add_filter( 'manage_edit-ryuzine_columns', 'my_columns_filter', 10, 1 );

// Add the drop-downs for filtering
// Thanks to: http://pippinsplugins.com/post-list-filters-for-custom-taxonomies-in-manage-posts/

function add_ryuzine_taxonomy_filters() {
	global $typenow;
 
	// an array of all the taxonomyies you want to display. Use the taxonomy name or slug
	$taxonomies = array('issues');
	if ($typenow == 'ryuzine') {
	$taxonomies[] = 'rackcats'; // Only show this for Ryuzine post-type!
	}
	// NOTE: Checks for Comic Easel and Comic Easel version number removed RP 1.2
 
	// must set this to the post type you want the filter(s) displayed on
	if( $typenow == 'post' || $typenow == 'ryuzine' || $typenow == 'comic' || $typenow == 'mangapress_comic'){

		foreach ($taxonomies as $tax_slug) {
			$tax_obj = get_taxonomy($tax_slug);
			$tax_name = $tax_obj->labels->name;
			$terms = get_terms($tax_slug);
			if(count($terms) > 0) {
				echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
				echo "<option value=''>Show All $tax_name</option>";
				foreach ($terms as $term) {
					echo '<option value='. $term->slug, isset($_GET[$tax_slug]) && $_GET[$tax_slug] == $term->slug ? ' selected="selected"' : '','>' . $term->name .' (' . $term->count .')</option>'; 
				}
				echo "</select>";
			}
		}
	}
}
add_action( 'restrict_manage_posts', 'add_ryuzine_taxonomy_filters' );


?>