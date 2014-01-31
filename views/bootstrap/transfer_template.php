<?php
	$options = array('selected' => '', 'available' => '');

	$selected = $field->val();
	$opts = $field->get('opts', array());

	foreach ($opts as $key => $value)
	{
		if(in_array($key, $selected))
		{
			$options['selected'] += '<option value="'.$key.'">'.$value.'</option>';
		}
		else
		{
			$options['available'] += '<option value="'.$key.'">'.$value.'</option>';
		}
	}
?>
<div class="form-group formo-<?=$field->get('driver')?><?php if ($error = $field->error()) echo ' error'; ?>" id="field-container-<?=$field->alias()?>">
	<label class="control-label col-sm-3"><?=$field->label()?></label>
	<div class="col-sm-8">
		<div class="row" id="permissions">
			<div class="row">
				<div class="col-sm-5"><input type="text" id="move-<?=$field->alias()?>-filter-base" class="col-sm-12"/></div>
				<div class="col-sm-2"></div>
				<div class="col-sm-5"><input type="text" class="col-sm-12 pull-right" id="move-<?=$field->alias()?>-filter-container" /></div>
			</div>
			<div class="row">
				<div class="col-sm-5">
					<select multiple="multiple" class="col-sm-12" id="move-<?=$field->alias()?>-base" size="8">
						<?=$options['available']?>
					</select>
				</div>
				<div class="col-sm-2 pagination-centered" style="padding-top: 60px;">
					<a class="btn btn-small btn-primary" id="move-<?=$field->alias()?>-in">>></a><br />
					<a class="btn btn-small btn-primary" id="move-<?=$field->alias()?>-out"><<</a>
				</div>
				<div class="col-sm-5"><select multiple="multiple" class="col-sm-12" id="move-<?=$field->alias()?>-container" size="8" name="<?=$field->name()?>[]">
					<?=$options['selected']?>
				</select></div>
			</div>
			<div class="row">
				<div class="col-sm-5"><a href="#" class="btn btn-mini btn-success" id="move-<?=$field->alias()?>-fill">Move all</a></div>
				<div class="col-sm-2" style="padding-top: 20px; padding-left: 15px"></div>
				<div class="col-sm-5"><a href="#" class="btn btn-mini btn-danger pull-right" id="move-<?=$field->alias()?>-empty">Remove all</a></div>
			</div>
		</div>
	</div>
</div>