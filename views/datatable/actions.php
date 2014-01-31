var modalSubmitData = {};

$(document).ready( function() {
	<?php if($history != false): ?>
	$('#modal-history-button').click(function(e){
		e.preventDefault();
		$('body').modalmanager('loading');

		var id = $('#modal-header-id').text().replace('# ', '');
		$('#modal-history-id').html(id);

		var load_url = "<?=$url_load;?>";
		var history_url = load_url.replace('0', id).replace('load', 'history');

		$(this).on('req.success', function(e, resp, s, x) {
			var history = resp[0].data.history;
			var html = '';
			var total = history.length;

			if(total > 0) {
			$.each(history, function(i, v){
				if(v.concat != null)
				{
					var changes = v.concat.split('$*/*$');
					html += '<div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#history-panels" href="#collapse-'+i+'">';
					html += '<span class="pull-right">('+changes.length+')</span>';
					var num = total - i;
					html += '#'+ num +' - '+v.date_and_time;
					html += '</a></h4></div><div id="collapse-'+i+'" class="panel-collapse collapse"><div class="panel-body">';

					$.each(changes, function(ind, val) {
						var def = val.split('|');
						html += '<div class="row"><div class="col-lg-4"><strong>'+def[2]+'</strong></div>';
						html += '<div class="col-lg-8"><small class="text-muted">'+def[0]+' -> '+v.new_value+'</small></div></div>';
						html += '<div class="row"><div class="col-lg-10"><p class="text-success">'+def[1]+'</p></div></div>';
					});
					html += '</div></div></div>';
				}
			});
			}
			else
			{
				html = '<p class="text-center text-danger">There\'s no history for this record.</p>';
			}
			$('#history-panels').html(html);
			$('#modal-history').modal('show');
			$('body').modalmanager('removeLoading');
		})
		.on('req.error', function(e, errors){
			$('body').modalmanager('removeLoading');
			$.each(errors, function(i, v){
				$('.notifications').notify({
					message: { text: v.value },
					type: "danger"
				}).show();
			});
		})
		.req({url: history_url, type: "GET"});
	});
	<?php endif; ?>

	// remove button
	var remove_url = "<?=$url_remove;?>";
	$('#dataTable-<?=$id;?> tbody').on('click', '.btn-action-remove', function(e){
		var El = $(this);
		var id = El.data('id');

		// Set request event handlers
		El.on('req.success', function(e, response, status, jqXHR){
			$.each(response, function(i, v){
				$('.notifications').notify({
					message: { text: v.value }
				}).show();
			});

			// remove the row
			var rowIndex = <?=$datatable;?>.fnGetPosition(El.closest('tr')[0]);
			<?=$datatable;?>.fnDeleteRow(rowIndex,null,true);
		})
		.on('req.error', function(e, errors, status, jqXHR){
			$.each(errors, function(i, v){
				$('.notifications').notify({
					message: { text: v.value },
					type: "danger"
				}).show();
			});
		});

		bootbox.confirm("Are you sure you want to delete <?=Inflector::singular($entity);?> #"+id+"?", function(r) {
			if(r == true)
			{
				var url = remove_url.replace('0', id);
				El.req({url: url, type: "GET"});
			}
		});
	});

	// edit button
	$('#dataTable-<?=$id;?> tbody').on('click', '.btn-action-edit', function(e){
		e.preventDefault();
		var El = $(this);
		var id = El.data('id');

		var modal_url = "<?=$url_load;?>";

		// Set request event handlers
		El.on('req.success', function(e, response, status, jqXHR){
			openModal('Edit', response[0].data);
			$('body').modalmanager('removeLoading');
			modalSubmitData.id = id;
		})
		.on('req.error', function(e, errors, status, jqXHR){
			$.each(errors, function(i, v){
				$('.notifications').notify({
					message: { text: v.value },
					type: "danger"
				}).show();
			});
		});

		var url = modal_url.replace('0', id);

		El.req({url: url, type: "GET"});
	});

	$('.btn-create').click(function(e){
		e.preventDefault();
		openModal('Create');
		$('body').modalmanager('removeLoading');
		modalSubmitData.id = null;
	});

	$('#modal-save')
		.on('req.success', function(e, response, status, jqXHR)
		{
			//Close modal
			$('#modal-<?=$resource;?>').modal('hide').on('hidden.bs.modal', function(e){
				//Show notifications
				$.each(response, function(i, v){
					$('.notifications').notify({
						message: { text: v.value }
					}).show();
				});
			});
		})
		.on('req.error', function(e, errors, status, jqXHR)
		{
			var error_list = '';
			//Show errors
			for(i=0;i<errors.length;i++)
			{
				$.each(errors[i].data.errors, function(i, string){
					$('#input-'+i).parents('.form-group').addClass('has-error');
					error_list += '<li><a href="#" class="nolink">'+string+'</a></li>';
				});
			}
			$('#modal-errors').html(error_list);
			$('#modal-error-button').removeClass('hide');
		})
		.on('click', function(e){
			e.preventDefault();
			El = $(this);

			$('#modal-errors').html('');
			$('#modal-error-button').addClass('hide');
			$('#modal-<?=$resource;?> form').each(function(){
				var data = $(this).serializeArray();
				$.each(data, function(i, v) {
					modalSubmitData[v.name] = v.value;
				});

				// Unchecked checkboxes should be added as 0
				$('input[type="checkbox"]', this).not(':checked').each(function(){
					modalSubmitData[$(this).attr('name')] = 0;
				});
			});

			$('#modal-<?=$resource;?> .wysiwyg').each(function(){
				modalSubmitData[$(this).attr('name')] = $(this).code();
			});

			// Handle some last minute changes to data that's sent through
			// (modify modalSubmitData, which is a global var)
			$('#modal-<?=$resource;?>').trigger('save');

			El.req({url: '<?=$url_save;?>', type: "POST", CSRF: "<?=$csrf;?>"}, modalSubmitData);
		});

	$('.form-tooltip').tooltip({placement: 'auto left'});
});

function openModal(action, data)
{
	$('body').modalmanager('loading');

	// Clean modal
	$('#modal-<?=$resource;?> form').each(function(){
		$('input:checkbox', this).removeAttr('checked');
		$('input:radio', this).removeAttr('checked');
		this.reset();
	});
	$('#modal-history-button').addClass('hide');
	$('#modal-<?=$resource;?> .wysiwyg').code('');
	$('.form-group').removeClass('has-error');
	$('#modal-header-id').text('');
	// Reset tabs located in the modal
	$('.nav', $('#modal-<?=$resource;?>')).each(function(){
		$('a:first', this).tab('show');
	});
	$('#modal-error-button').addClass('hide');
	modalSubmitData = {};

<?php if($images != false): ?>

	$('.image-upload').each(function(){
		var El = $(this);
		var parent = $(this).parent();

		parent.find('input').removeClass('hide');
		$('.form-control-static', parent).remove();
		$('input[type="hidden"]', parent).remove();
		$('#img-'+El.attr('id')).attr('src', 'http://demo.onokumus.com/metis/assets/img/user.gif');

		El.uploadify({
		'fileTypeDesc'    : 'Image Files',
		'fileTypeExts'    : '*.png',
		'multi'           : false,
		'formData'        : {csrf: '<?=$csrf;?>', 'type': El.attr('name')},
		'swf'             : '<?=$uploadify;?>',
		'uploader'        : '<?=$upload_url;?>',
		'onUploadSuccess' : function(file, data, response) {
			// Add the uploaded image's name as a hidden field
			El.uploadify('destroy');
			var data = data.split('*/*');
			parent.find('input').addClass('hide');
			parent.append('<input type="hidden" name="'+El.attr('name')+'" value="'+data[0]+'" /><span class="form-control-static text-success">Upload successful</span>');
			$('#img-'+El.attr('id')).attr('src', data[1]);
			}
		});
	});
<?php endif; ?>

	// Trigger clean event on the modal
	$('#modal-<?=$resource;?>').trigger('clean');

	// Set the modal's action
	$('#modal-header-action').html(action);

	// Load the data
	if (typeof data != 'undefined')
	{
		$('#modal-history-button').removeClass('hide');
		$('#modal-header-id').text('# '+data.id);
		$.each(data, function(field, val){
			var type = $('#input-'+field).attr('type');

			if(type == 'checkbox')
			{
				$('#input-'+field).prop('checked', (val == 1));
			}
			else if(type == 'radio')
			{
				$('#input-'+field+'[value="'+val+'"]').prop('checked', true);
			}
			else if($('#input-'+field).hasClass('wysiwyg'))
			{
				$('#input-'+field).code(val);
			}
			else if($('div#input-'+field).length > 0)
			{
				$('div#input-'+field).parents('.formo-image').find('div.col-sm-3').find('img').attr('src', val);
			}
			else
			{
				$('#input-'+field).val(val);
			}
		});
		$('#modal-<?=$resource;?>').trigger('load', [data]);
	}

	// Show the modal
	$('#modal-<?=$resource;?>').modal(<?=$modal;?>);
}