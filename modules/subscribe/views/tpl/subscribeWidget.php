<form actiom="" method="post" id="<?php echo $this->uniqueId?>">
	<div class="subSubscribeFormTitle"><?php _e($this->instance['sub_form_title'])?></div>
	<label>
		<?php _e($this->instance['sub_enter_email_msg'])?>: 
		<?php echo htmlSub::text('email')?>
	</label>
	<?php if(isset($this->instance['list']) && $this->instance['list']) {
		foreach($this->instance['list'] as $listId) {
			echo htmlSub::hidden('list[]', array('value' => $listId));
		}
	}?>
	<?php echo htmlSub::hidden('mod', array('value' => 'subscribe'))?>
	<?php echo htmlSub::hidden('action', array('value' => 'create'))?>
	<?php echo htmlSub::hidden('reqType', array('value' => 'ajax'))?>
	<?php echo htmlSub::submit('subscribe', array('value' => __('Subscribe')))?>
	
	<div class="subSubscribeFormMsg"></div>
	<div class="subSubscribeFormSuccess" style="display: none;"><?php _e($this->instance['sub_success_msg'])?></div>
</form>
<script type="text/javascript">
// <!--
jQuery(document).ready(function(){
	jQuery('#<?php echo $this->uniqueId?>').submit(function(){
		subSubscribeFormSend(this);
		return false;
	});
});
// -->
</script>

