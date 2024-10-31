<form class="subNiceStyle" id="subAdminNewslettersEditForm">
	<table width="100%">
		<tr>
			<td><?php _e('Subject line')?></td>
			<td><?php echo htmlSub::text('subject')?></td>
		</tr>
		<tr class="subNormalInputs">
			<td><?php _e('From Name and Email')?></td>
			<td nowrap>
				<label><?php echo htmlSub::text('from_name')?></label>
				<label><<?php echo htmlSub::text('from_email')?>></label>
			</td>
		</tr>
		<tr class="subNormalInputs">
			<td><?php _e('Reply to Name and Email')?></td>
			<td nowrap>
				<label><?php echo htmlSub::text('reply_name')?></label>
				<label><span style="font-size: 15px;"><</span><?php echo htmlSub::text('reply_email')?>></label>
			</td>
		</tr>
		<tr>
			<td id="subAdminNewslettersSendParamsShell" colspan="2">
				<div class="subAdminNewslettersSendParamShell"><?php _e('Send newsletter when')?>:</div>
				<div class="subAdminNewslettersSendParamShell">
					<label>
						<?php echo htmlSub::radiobutton('send_type', array('value' => 'now', 'checked' => 1))?>
						<?php _e('Send it right now')?>
					</label>
				</div>
				<div class="subAdminNewslettersSendParamShell">
					<label>
						<?php echo htmlSub::radiobutton('send_type', array('value' => 'new_content'))?>
						<?php _e('You have more then')?>
					</label>
					<?php echo htmlSub::text('send_params[new_content][more_then]', array( 'attrs' => 'style="width: 40px"'))?>
					<?php _e('new content with')?>
					<?php echo htmlSub::text('send_params[new_content][tags]', array('attrs' => 'style="width: 80px"'))?>
					<?php _e('tags from')?>
					<?php echo htmlSub::selectlist('send_params[new_content][categories]', array('options' => $this->categoriesList)) // !!! MAKE IT WORK NORMAL !!!?>
					<?php _e('category(es)')?>
				</div>
				<div class="subAdminNewslettersSendParamShell">
					<label>
						<?php echo htmlSub::radiobutton('send_type', array('value' => 'schedule'))?>
						<?php _e('I just want to send my content at exact time')?>
					</label>
					<?php echo htmlSub::selectbox('send_params[schedule][month]', array('options' => $this->monthes))?>
					<?php echo htmlSub::selectbox('send_params[schedule][day]', array('options' => $this->days))?>
					<?php echo htmlSub::selectbox('send_params[schedule][hour]', array('options' => $this->hours))?>
				</div>
			</td>
		</tr>
		<tr class="subNormalInputs" id="subAdminNewslettersSendingTimeShell">
			<td><?php _e('Sending Time')?></td>
			<td>
				<?php echo htmlSub::selectbox('send_params[sending_time][type]', array('options' => $this->sendingTimeOptions, 'attrs' => 'id="subAdminNewslettersSendingTimeSelect"'))?>
				<span id="subAdminNewslettersSendingTimeParams" style="display: none;">
					<?php echo htmlSub::datepicker('send_params[sending_time][date]', array('attrs' => 'style="width: 100px;"'))?>
					<?php echo htmlSub::timepicker('send_params[sending_time][time]', array('attrs' => 'style="width: 100px;"', 'step' => 60, 'timeFormat' => 'H:i'))?>
					<?php _e('Local Time')?>
					<code><?php echo toeDateSub(SUB_TIME_FORMAT)?></code>
				</span>
			</td>
		</tr>
		<tr>
			<td><?php _e('Send To')?></td>
			<td id="subAdminNewslettersListsShell"></td>
		</tr>
		<tr>
			<td colspan="2">
				<label>
					<?php echo htmlSub::checkbox('send_params[send_only_new_users]', array('value' => 1))?>
					<?php _e('Send emails only to users, who never receive this email')?>
				</label>
				<?php _e('Send To')?>
			</td>
		</tr>
		<tr>
			<td><?php _e('Send Test')?></td>
			<td>
				<?php echo htmlSub::text('test_email', array('value' => get_bloginfo('admin_email'), 'attrs' => 'id="subAdminNewslettersSendPreviewEmail" style="width: auto; display: inline;"'))?>
				<?php echo htmlSub::button(array('value' => __('Send'), 'attrs' => 'id="subAdminNewslettersSendPreviewButt" class="button"'))?>
				<div id="subAdminNewslettersSendPreviewMsg"></div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div style="float: right;">
					<?php echo htmlSub::hidden('active', array('value' => 1)) // It will be always active for now?>
					<?php echo htmlSub::hidden('id')?>
					<?php echo htmlSub::hidden('reqType', array('value' => 'ajax'))?>
					<?php echo htmlSub::hidden('page', array('value' => 'newsletters'))?>
					<?php echo htmlSub::hidden('action', array('value' => 'save'))?>
					<?php echo htmlSub::submit('back', array('value' => __('Back'), 'attrs' => 'class="button button-primary button-large"'))?>
					<?php echo htmlSub::submit('save', array('value' => __('Save'), 'attrs' => 'class="button button-primary button-large"'))?>
					<?php echo htmlSub::submit('send', array('value' => __('Send'), 'attrs' => 'class="button button-primary button-large"'))?>
					<div id="subAdminNewslettersEditMsg"></div>
				</div>
			</td>
		</tr>
	</table>
</form>

