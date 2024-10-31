<form id="subAdminNewslettersSaveTplForm">
	<div id="subAdminNewslettersStplShell"></div>
	<div style="float: right;">
		<?php echo htmlSub::hidden('id')?>
		<?php echo htmlSub::hidden('stpl_id')?>
		<?php echo htmlSub::hidden('subject')?>
		<?php echo htmlSub::hidden('page', array('value' => 'newsletters'))?>
		<?php echo htmlSub::hidden('action', array('value' => 'saveTemplate'))?>
		<?php echo htmlSub::hidden('reqType', array('value' => 'ajax'))?>
		<?php echo htmlSub::submit('back', array('value' => __('Back'), 'attrs' => 'class="button button-primary"'))?>
		<?php echo htmlSub::submit('save', array('value' => __('Save'), 'attrs' => 'class="button button-primary"'))?>
		<?php echo htmlSub::submit('next', array('value' => __('Next'), 'attrs' => 'class="button button-primary"'))?>
		<div id="subAdminNewslettersSaveTplMsg"></div>
	</div>
	<div style="clear: both;"></div>
</form>