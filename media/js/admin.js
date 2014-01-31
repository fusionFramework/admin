$(document).ready( function() {
	$('ul.collapse li.active').parent().addClass('in');

	$('body').on('click', '.nolink', function(e){
		e.preventDefault();
	});

	$('.tt').tooltip();

	$('.wysiwyg').summernote({
		height: 200,
		toolbar: [
			//['style', ['style']], // no style button
			['style', ['bold', 'italic', 'underline', 'clear']],
			['fontsize', ['fontsize']],
			['para', ['ul', 'ol', 'paragraph']],
			['insert', ['picture', 'link']] // no insert buttons
			//['table', ['table']], // no table button
			//['help', ['help']] //no help button
		]
	});
});