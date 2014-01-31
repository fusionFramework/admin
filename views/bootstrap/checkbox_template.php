<div class="field control-group col-sm-offset-2 formo-<?=$field->get('driver')?><?php if ($error = $field->error()) echo ' error'; ?>" id="field-container-<?=$field->alias()?>">
	<?php if ($msg = $field->get('message')): ?>
		<a href="#" class="btn btn-info btn-xs pull-right form-tooltip" data-toggle="tooltip" title="<?=$msg?>"><i class="fa fa-question-circle"></i></a>
	<?php endif; ?>

	<label class="checkbox"><?=$field->open().$field->html().$field->render_opts().$field->close()?> <span class="checkbox-label"><?=$field->label()?></span></label>

	<?php if ($msg = $field->error()): ?>
		<span class="help-block"><?=$msg?></span>
	<?php endif; ?>
</div>
