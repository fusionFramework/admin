
	<?php foreach ($opts as $key => $opt): ?>

	<label class="radio-inline">
		<input type="radio" id="input-<?=$field->alias()?>" name="<?=$field->name()?>" value="<?=$key?>"<?php if ($key == $field->val()) echo ' checked="checked"'; ?> />
		<?=$opt?>
	</label>

	<?php endforeach; ?>