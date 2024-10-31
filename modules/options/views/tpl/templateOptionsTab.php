<script type="text/javascript">
// <!--
jQuery(document).ready(function(){
	postboxes.add_postbox_toggles(pagenow);
});
// -->
</script>
<form id="subAdminTemplateOptionsForm">
	<div>
		<?php echo htmlSub::inputButton(array('value' => __('Сhoose Preset template'), 'attrs' => 'class="subSetTemplateOptionButton button button-primary button-large"')); ?>
	</div>
	<div class="wrap">
		<div id="dashboard-widgets" class="metabox-holder">
			<div id="postbox-container-1" class="postbox-container" style="width: 100%;">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
				<?php if(!empty($this->tplOptsData)) { ?>
					<?php $i = 1;?>
					<?php foreach($this->tplOptsData as $optData) { ?>
                          <div id="id<?php echo $i;?>" class="postbox subAdminTemplateOptRow" style="display: block;">
                              <div class="handlediv" title="<?php _e( 'Click to toggle' )?>"><br></div>
                              <h3 class="hndle"><?php _e( $optData['title'] )?></h3>
                              <div class="inside">
                                  <?php echo $optData['content']?>
                              </div>
                          </div>
						<?php $i++;?>
					<?php }?>
				<?php }?>
				</div>
			</div>
			<div>
				<?php echo htmlSub::hidden('reqType', array('value' => 'ajax'))?>
				<?php echo htmlSub::hidden('page', array('value' => 'options'))?>
				<?php echo htmlSub::hidden('action', array('value' => 'saveGroup'))?>
				<?php echo htmlSub::submit('saveAll', array('value' => __('Save All Changes'), 'attrs' => 'class="button button-primary button-large"'))?>
			</div>
			<div id="subAdminTemplateOptionsMsg"></div>
		</div>
	</div>
</form>