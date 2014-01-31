<div class="modal fade" id="modal-<?=$resource;?>">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&nbsp;&nbsp; &times;</button>
				<div class="btn-group btn-group-sm pull-right" style="margin-right: 5px" id="modal-error-button">
					<button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" title="Errors">
						<i class="fa fa-exclamation"></i>
						<span class="sr-only">Toggle Dropdown</span></button>
					<ul class="dropdown-menu" role="menu" id="modal-errors">
					</ul>
				</div>
				<?php if($history == true): ?>
				 <button type="button" class="btn btn-xs btn-info pull-right" title="history" id="modal-history-button"><i class="fa fa-level-up"></i></button>&nbsp;&nbsp;
				<?php endif; ?>

				<h4 class="modal-title"><span id="modal-header-action"></span> <?=strtolower($entity);?> <span id="modal-header-id"></span></h4>
			</div>
			<div class="modal-body">
				<?=$body;?>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-success" id="modal-save">Save</button>
			</div>
</div><!-- /.modal -->