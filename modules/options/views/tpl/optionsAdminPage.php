<div id="subAdminOptionsTabs">
    <h1>
        <?php echo SUB_WP_PLUGIN_NAME?>&nbsp;<?php _e('plugin')?>
    </h1>
	<ul>
		<?php foreach($this->tabsData as $tId => $tData) { ?>
		<li class="<?php echo $tId?>"><a href="#<?php echo $tId?>"><?php _e($tData['title'])?></a></li>
		<?php }?>
	</ul>
	<?php foreach($this->tabsData as $tId => $tData) { ?>
	<div id="<?php echo $tId?>"><?php echo $tData['content']?></div>
	<?php }?>
</div>
<div id="subAdminTemplatesSelection"><?php echo $this->presetTemplatesHtml?></div>
