<?php
    defined('C5_EXECUTE') or die("Access Denied.");
?>
<div class="form-group">
	<?php echo $form->label('formname', t('Form Name'))?>
	<?php echo $form->text('formname', $controller->formname)?>
</div>
<div class="form-group">
	<label class="control-label"><?php echo t('Return mail')?></label>
	<div class="radio">
	<label>
		<?php echo $form->radio('rtmail', 1, (int) $controller->rtmail)?>
		<span><?php echo t('Yes')?></span>
	</label>
	</div>
	<div class="radio">
	<label>
		<?php echo $form->radio('rtmail', 0, (int) $controller->rtmail)?>
		<span><?php echo t('No')?></span>
	</label>
	</div>
</div>