<?php
	$title = (isset($bNew) && $bNew === true)
		? 'New content'
		: 'Edit content <spam class="structure_name">'.htmlspecialchars($structure->getName()).'</spam>';
?>
<main>
	<h2><?=$title?></h2>
	<p class="result"><?=$resultDesc?></p>
	<form action="do_process_content.php" method="post">
		<input type="hidden" name="id" value="<?=htmlspecialchars($content->getId())?>"/>
		<input type="hidden" name="ids" value="<?=htmlspecialchars($content->getIdStructure())?>"/>
		<div>
			<label for="title">Title</label>: <input type="text" name="title" id="title" value="<?=htmlspecialchars($content->getTitle())?>"/>
		</div>
		<div>
			<?php
			$fieldOU = new Acd\View\Field();
			$fields = $structure->getFields();
			$structure_fields = '';
			$n = 0;
			foreach ($fields as $field) {
				$field->setValue($content->getFieldValue($field->getName())); // TODO: enrevesadísimo
				$fieldOU->setField($field);
				$fieldOU->setId($n);
				$structure_fields .= '<li>'.$fieldOU->render().'</li>';

				$n++;
			}
			?>
			<fieldset>
				<legend>Fields</legend>
				<ul><?=$structure_fields?></ul>
			</fieldset>
		</div>
		<input type="submit" name="a" value="save" class="button publish"/>
		<?php
			if($content->getId()) {
		?>
			<input type="submit" name="a" value="clone" class="button clone"/>
			<input type="submit" name="a" value="delete" class="button delete"/>
		<?php
			}
		?>
	</form>
</main>