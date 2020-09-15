(function($){
	$(document).ready(function(){
		$('.notice_file').click(function(e){
			e.preventDefault();
			var media = wp.media({
		      title: 'Choose or upload notice file',
		      button: {
		        text: 'Choose notice file'
		      },
		      multiple: false
		    });
		    media.open();

		    media.on('select', function(){
		    	var attachment = media.state().get('selection').first().toJSON();
		    	$('.notice_file_link').val(attachment.url);
		    });
		    
		});
	});
}(jQuery))