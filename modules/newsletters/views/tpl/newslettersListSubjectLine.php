<strong>
	<a href="#" class="subject row-title" onclick="newslettersEditLinkSub(this); return false;"><?php echo $this->newsletter['subject']?></a>
</strong>
<div class="row-actions">
	<span class="viewnl">
		<a title="Preview in new tab" class="viewnews" href="#" onclick="newslettersPreviewLinkSub(this); return false;"><?php _e('Preview')?></a>
	</span> |
	<span class="edit">
		<a class="submitedit" href="#" onclick="newslettersEditLinkSub(this); return false;"><?php _e('Edit')?></a>
	</span> | 
	<span class="duplicate">
		<a class="submitedit" href="#" onclick="newslettersDuplicateLinkSub(this); return false;"><?php _e('Duplicate')?></a>
	</span> | 
	<span class="delete">
		<a class="submitdelete" href="#" onclick="newslettersDeleteLinkSub(this); return false;"><?php _e('Delete')?></a>
	</span>
</div>
<?php echo htmlSub::hidden('id', array('value' => $this->newsletter['id'], 'attrs' => 'class="id"'))?>