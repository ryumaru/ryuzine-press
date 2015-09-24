// Media Uploader for WordPress 3.5 that ports URL into a text field //

jQuery(document).ready(function($){
	var _custom_media = false,
		_orig_send_attachment = wp.media.editor.send.attachment;
		id = '';	// need to grab this

	wp.media.editor.send.attachment = function(props, attachment){
		$("#"+id).val(attachment.url); // port into textbox
	}
    $('.uploader .button').click(function(e) {
		var send_attachment_bkp = wp.media.editor.send.attachment;
		var button = $(this);
		id = button.attr('id').replace('_button', '');
		wp.media.editor.open(button);
		return false;
	});
	$('.add_media').on('click', function(){
		_custom_media = false;
	})
	
	// Modify Media Uploader labels
	_wpMediaViewsL10n.insertMediaTitle	= 'Select Image';
	_wpMediaViewsL10n.insertIntoPost 	= 'Insert URL';


});