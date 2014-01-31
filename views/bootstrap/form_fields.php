<div class="formo-<?=$field->get('driver')?>" id="form-container-<?=$field->alias()?>">
	<?php foreach ($field->as_array() as $_field): ?>
		<?$_field->add_class('form-control');?>
	<?=$_field->render()?>
	<?php endforeach; ?>
</div>