<?php
	// This was for ajax load
	/*frameSub::_()->loadAdminEditor();
	// We need here stripslashes() as $this->currentContent is html text, delivered via http
	wp_editor(stripslashes($this->currentContent), $this->elementId, array(
		'dfw' => true,
		'tabfocus_elements' => 'insert-media-button,save-post',
	));*/
	// Now we will try to do this without ajax
?>
<style type="text/css">
	#stplCanvasTextEditor {
		height: auto !important;
	}
</style>
<div id="stplCanvasTextEditorShell" style="display: none;">
	<?php
		wp_editor('', 'stplCanvasTextEditor', array(
			'dfw' => true,
			'tabfocus_elements' => 'insert-media-button,save-post',
		));
	?>
</div>
<script type="text/javascript">
// <!--
jQuery(function(){
	//jQuery('#stplCanvasTextEditor-tmce').click();
});
// -->
</script>