<h4 class="subTitle"><?php _e('Title')?>:</h4>
<?php echo htmlSub::text('opt_values[msg_title]', array('value' => $this->optModel->get('msg_title')))?>
<div class="subLeftCol">
    <?php _e('Select color')?>:
    <?php echo htmlSub::colorpicker('opt_values[msg_title_color]', array('value' => $this->optModel->get('msg_title_color')))?>
</div>
<div class="subRightCol">
    <?php _e('Select font')?>:
    <?php echo htmlSub::fontsList('opt_values[msg_title_font]', array('value' => $this->optModel->get('msg_title_font')));?>
</div>
<div class="clearfix"></div>
<div class="clearfix">
	<?php echo htmlSub::button(array('value' => __('Set default'), 'attrs' => 'id="subMsgTitleSetDefault"'))?>
	<div id="subAdminOptMsgTitleDefaultMsg"></div>
</div>
<div class="clearfix"></div>
<br />
<h4 class="subTitle"><?php _e('Text')?>:</h4>
<?php echo htmlSub::textarea('opt_values[msg_text]', array('value' => $this->optModel->get('msg_text')))?>
<div class="subLeftCol">
    <?php _e('Select color')?>:
    <?php echo htmlSub::colorpicker('opt_values[msg_text_color]', array('value' => $this->optModel->get('msg_text_color')))?>
</div>
<div class="subRightCol">
    <?php _e('Select font')?>:
    <?php echo htmlSub::fontsList('opt_values[msg_text_font]', array('value' => $this->optModel->get('msg_text_font')));?>
</div>
<div class="clearfix"></div>
<div class="clearfix">
	<?php echo htmlSub::button(array('value' => __('Set default'), 'attrs' => 'id="subMsgTextSetDefault"'))?>
	<div id="subAdminOptMsgTextDefaultMsg"></div>
</div>
<div class="clearfix"></div>