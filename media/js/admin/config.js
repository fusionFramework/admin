$(document).ready( function() {
	var file = '';
	$('.cfg-edit').click(function(e){
		e.preventDefault();
		var self = $(this);

		$('body').modalmanager('loading');
		file = $(this).data('file');
		$('#cfg-form').html('');

		self.on('req.success', function(e, response){
			$('#cfg-form').html(response[0].data.form);

			if(response[0].data.img != false)
			{
				$.each(response[0].data.img, function(ind, def){
					El = $('input[name="'+def.input+'"]');
					def.onUploadSuccess = function(file, data, repsonse) {
						// Add the uploaded image's name as a hidden field
						El.uploadify('destroy');
						var data = data.split('*/*');

						El.after('<input type="hidden" name="'+El.attr('name')+'" value="'+data[0]+'" /><span class="form-control-static text-success">Upload successful</span>');
						El.parent('.field').find('img').attr('src', data[1]);
						El.remove();
					};

					El.uploadify(response[0].data.img);
				});

			}
			$('.form-tooltip').tooltip();

			$('#modal-config').modal('show');
			$('body').modalmanager('removeLoading');
		})
		.req({'url': '../admin/config/load/'+file, type: 'GET'});
	});

	$('#modal-save').click(function(e){
		e.preventDefault();

		var form = $('#cfg-form').find('form').serialize();
		
		$(this)
			.on('req.success', function(e, response){
				$.fn.req.defaultRequestHandlers.success(response);
				$('#modal-config').modal('hide');
			})
			.req({'url': '../admin/config/save/'+file, type: 'POST', data: form});
	});
});