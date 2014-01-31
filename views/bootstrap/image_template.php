<div class="field form-group formo-<?=$field->get('driver')?><?php if ($error = $field->error()) echo ' error'; ?>" id="field-container-<?=$field->alias()?>">
	<?php if ($title): ?>
		<label class="col-sm-3 control-label"><?=$title?></label>
	<?php elseif ($label = $field->label()): ?>
	<?php $class = ($field->get('label_class') != '') ? $field->get('label_class') : 'col-sm-3'; ?>
		<label for="<?=$field->attr('id')?>" class="<?=$class;?> control-label"><?=$label?></label>
	<?php endif; ?>
	<?php if ($msg = $field->get('message')): ?>
		<a href="#" class="btn btn-info btn-sm pull-right form-tooltip" data-toggle="tooltip" title="<?=$msg?>"><i class="fa fa-question-circle"></i></a>
		<?php endif; ?>
	<div class="col-sm-6">
		<?=$field->open().$field->html().$field->render_opts().$field->close()?>

		<?php if ($msg = $field->error()): ?>
		<span class="help-block"><?=$msg?></span>
		<?php endif; ?>
	</div>

	<div class="col-sm-3">
		<img src="http://demo.onokumus.com/metis/assets/img/user.gif" id="img-<?=$field->get('attr.id');?>" width="<?=$field->get('dim.width', 64);?>" height="<?=$field->get('dim.height', 64);?>" />
	</div>
</div>