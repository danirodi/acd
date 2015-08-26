<input type="hidden" name="field[<?=$id?>][id]" value="<?=htmlspecialchars($fieldId)?>"/>
<input type="hidden" name="field[<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=htmlspecialchars($id)?>"><?=htmlspecialchars($fieldName)?></label>
<div class="select-wrap">
<?php
	 $itemsInOut = $fieldOptions->detachItems($fieldValue);
?>
	<select name="field[<?=$id?>][value]" id="field_<?=$id?>" class="field select">
		<option value="">Select option…</option>
		<?php
			foreach ($itemsInOut['in'] as $key => $value) {
		?>
			<option value="<?=htmlspecialchars($key)?>" selected="selected"><?=htmlspecialchars($value)?></option>
		<?php
			}
			foreach ($itemsInOut['out'] as $key => $value) {
		?>
			<option value="<?=htmlspecialchars($key)?>"><?=htmlspecialchars($value)?></option>
		<?php

			}
		?>
	</select>

</div>