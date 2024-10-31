<div id="toeModActivationPopupShellSub" style="display: none;">
	<center>
		<form id="toeModActivationPopupFormSub">
			<label>
				<?php _e('Enter your activation key here')?>:
				<?php echo htmlSub::text('activation_key')?>
			</label>
			<?php echo htmlSub::hidden('page', array('value' => 'options'))?>
			<?php echo htmlSub::hidden('action', array('value' => 'activatePlugin'))?>
			<?php echo htmlSub::hidden('reqType', array('value' => 'ajax'))?>
			<?php echo htmlSub::hidden('plugName')?>
			<?php echo htmlSub::hidden('goto')?>
			<?php echo htmlSub::submit('activate', array('value' => __('Activate')))?>
			<br />
			<div id="toeModActivationPopupMsgSub"></div>
		</form>
	</center>
	<i><?php _e('To get your keys - go to')?>
		<a target="_blank" href="http://readyshoppingcart.com/my-account/my-orders/">http://readyshoppingcart.com/my-account/my-orders/</a>
		<?php _e('and copy & paste key from your ordered extension here.')?>
	</i>
</div>