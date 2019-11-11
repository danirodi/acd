<?php
if ($fieldValue['rgb']) {
  $colorvalue = $fieldValue['rgb'];
  $cssState = 'fill';
}
else {
  $colorvalue = 'unset';
  $cssState = 'unset';
}
?><input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][id]"
  value="<?=htmlspecialchars($fieldId)?>" />
<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=htmlspecialchars($id.'_'.$idParent)?>"><?=htmlspecialchars($fieldName)?></label>
<input type="color" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][value][rgb]" value="<?=htmlspecialchars($fieldValue['rgb'])?>" id="field_<?=htmlspecialchars($id.'_'.$idParent)?>" class="field colorrgb componentrgb"/>
<label class="action empty <?=$cssState?>">
  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
  <input type="checkbox"
    name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][value][empty]" value="1" title="Clear value" class="field clear" />
</label>
<span class="field colorvalue <?=$cssState?>">rgb: <span class="value"><?=$colorvalue?></span></span>
