<strong>
	<a href="#" class="subject row-title" onclick="subSubscrbShowEditForm(this); return false;"><?php echo $this->subscriber['email']?></a>
</strong>
<div class="row-actions">
	<span class="edit">
		<a class="submitedit" href="#" onclick="subSubscrbShowEditForm(this); return false;"><?php _e('Edit')?></a>
	</span> | 
	<span class="change_status">
		<a class="submitchange_status <?php echo ($this->subscriber['active'] ? 'active' : 'disabled')?>" href="#" onclick="subSubscrbChangeStatus(this); return false;">
			<?php $this->subscriber['active'] ? _e('Deactivate') : _e('Activate')?>
		</a>
	</span> | 
	<span class="delete">
		<a class="submitdelete" href="#" onclick="subSubscrbRemove(this); return false;"><?php _e('Delete')?></a>
	</span>
</div>
<?php echo htmlSub::hidden('id', array('value' => $this->subscriber['id'], 'attrs' => 'class="id"'))?>