<?
// Uninstall Script for Ryuzine Press //

if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
    exit();
 
	delete_option('ryu_default_options_db');
	delete_option('ryu_admin_hide_text');
	delete_option('ryuzine_opt_covers');
	delete_option('ryuzine_opt_pages');
	delete_option('ryuzine_opt_addons');
	delete_option('ryuzine_opt_ads');
	delete_option('ryuzine_opt_rack');
	delete_option('ryuzine_rack_cat');
	delete_option('ryuzine_opt_lightbox');
	delete_option('ryuzine_press_installed');
	delete_option('ryuzine_app_installed');
	delete_option('ryuzine_press_installed');
	$ryuFile = STYLESHEETPATH.'/single-ryuzine.php';
	unlink($ryuFile);
	$rakFile = STYLESHEETPATH.'/archive-ryuzine.php';
	unlink($rakFile);
	
function uninstallMsg()
{
echo '<div class="error">
       <p>Ryuzine Press can only uninstall from the currently active theme.  If you used the plugin with other themes there may be a "single-ryuzine.php" you will need to manually delete.</p>
    </div>';
}  

add_action('admin_notices', 'uninstallMsg');

?>
