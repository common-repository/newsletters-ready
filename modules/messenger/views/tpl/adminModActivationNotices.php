<?php foreach($this->notices as $note) {?>
	<div class="info_box" style="width: 100%; margin: -1px 15px 0 5px;">
		<?php echo $note;?>
		<a href="#" class="toeRemovePlugActivationNoticeSub"><?php echo htmlSub::img('cross.gif')?></a>
	</div>
<?php }?>