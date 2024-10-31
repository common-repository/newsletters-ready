<table>
	<tr>
		<td><?php _e('Subject')?>:</td>
		<td><?php echo htmlSub::text('email_tpl['. $this->tplData['id']. '][subject]', array('value' => $this->tplData['subject']))?></td>
	</tr>
	<tr>
		<td><?php _e('Body')?>:</td>
		<td><?php echo htmlSub::textarea('email_tpl['. $this->tplData['id']. '][body]', array('value' => $this->tplData['body']))?></td>
	</tr>
	<tr>
		<td><?php _e('Available veriables')?>:</td>
		<td><?php echo $this->tplData['variables']?></td>
	</tr>
</table>