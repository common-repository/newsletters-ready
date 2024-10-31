<?php echo htmlSub::button(array('value' => __('Force Re-send now'), 'attrs' => 'class="button" id="subAdminNewslettersResendButt" style="float: right;"'))?>
<span id="subAdminNewslettersResendMsg"></span>
<div class="list">
	<br />
	<table id="subAdminNewslettersSendStatTbl" width="100%" class="widefat subAdminTable" style="clear: none;">
		<thead>
			<tr class="subTblHeader">
				<th><?php _e('Email')?></th>
				<th><?php _e('Send Status')?></th>
				<th><?php _e('Sent On')?></th>
				<th><?php _e('Errors')?></th>
			</tr>
		</thead>
		<tbody>
			<tr class="subExample" onmouseover="showTableRowActionsSub(this);" onmouseout="hideTableRowActionsSub(this);">
				<td class="email">
					<strong class="email"></strong>
					<?php echo htmlSub::hidden('id', array('attrs' => 'class="id" valueTo="value"'))?>
				</td>
				<td class="status_msg"></td>
				<td class="date_sent_conv"></td>
				<td class="error_msg" style="color: red;"></td>
			</tr>
		</tbody>
	</table>
</div>
<div id="subAdminNewslettersSendStatPaging"></div>
<div id="subAdminNewslettersSendStatMsg"></div>
<?php echo htmlSub::button(array('value' => __('Back to Edit'), 'attrs' => 'class="button button-primary button-large" id="subAdminNewslettersBackToEditButt" style=""'))?>