jQuery(document).ready(function() {

jQuery('.image_uploader_button').click(function() { // Changed this to CLASS instead of ID //
formfield = jQuery(this).prev().attr('id'); // Changed this to ID instead of NAME //
tb_show('', 'media-upload.php?type=image&TB_iframe=true');
return false;
});

window.send_to_editor = function(html) {
imgurl = jQuery('img',html).attr('src');
jQuery('#'+formfield).val(imgurl);
tb_remove();
}
});
