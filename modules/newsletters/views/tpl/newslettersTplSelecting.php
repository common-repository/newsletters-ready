<?php echo htmlSub::hidden('id')?>
<?php /*?><div>
	<label>
		<?php _e('Subject')?>: 
		<?php echo htmlSub::text('subject', array('attrs' => 'id="subAdminNewslettersTplSelectionSubject"'))?>
		<?php echo htmlSub::hidden('id')?>
	</label>
</div><?php */?>
<ul class="subTemplatesList subAdminNewslettersTplSelectionElement">
	<?php foreach($this->templates as $tpl) { ?>
	<li class="subTemplatePrevShell subTemplatePrevShell-existing subTemplatePrevShell-<?php echo $tpl['id']?>" onclick="newslettersSelectTplSub(this); return false;" stpl_id="<?php echo $tpl['id']?>">
		<h2 style="text-align: center; color: #454545"><?php echo $tpl['label']?></h2><hr />
		<?php echo htmlSub::img( $tpl['full_preview_img'], false, array('attrs' => 'class="subAdminTemplateImgPrev"'));?><hr />
		<?php echo htmlSub::text('subject', array('attrs' => 'class="subAdminNewslettersTplSelectionSubject" placeholder="'. __('Subject'). '" onclick="event.stopPropagation();"'))?>
		<input type="submit" onclick="return false;/*it will trigger click on parent element - and it will trigger select template, no need to make this twice*/" class="button button-primary button-large subTemplateSelectButt" value="<?php _e('Apply')?>">
		<div class="subAdminNewslettersTplSelectionMsg"><div class="subAdminNewslettersTplSelectionMsgTxt"></div></div>
	</li>
	<?php } ?>
</ul>
<div style="clear: both;"></div>
<?php /*foreach($this->templates as $t) {?>
<div class="subAdminNewslettersTplSelectionElement">
	<a href="#" onclick="newslettersSelectTplSub(this); return false;" stpl_id="<?php echo $tId?>">
		<?php echo $t['label']?>
	</a><br />
	<a href="#" onclick="newslettersSelectTplSub(this); return false;" stpl_id="<?php echo $tId?>">
		<img src="<?php echo $this->getModule()->getModPath(). 'tpl_img/'. $tId. '.png'?>" />
	</a><br />
	<div class="subAdminNewslettersTplSelectionMsg"></div>
</div>
<?php }*/?>