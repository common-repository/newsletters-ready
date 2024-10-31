<div class="wrap">
	<div class="metabox-holder">
		<div class="postbox-container" style="width: 100%;">
			<div class="meta-box-sortables ui-sortable">
				<div id="idMainSubOpts" class="postbox subAdminTemplateOptRow subAvoidJqueryUiStyle" style="display: block">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><?php _e( 'Main Settings' )?></h3>
					<div class="inside">
						<form class="subNiceStyle" id="subAdminOptionsForm">
							<table width="100%">
								<?php foreach($this->allOptions as $opt) { ?>
								<tr class="subAdminOptionRow-<?php echo $opt['code']?> subTblRow">
									<td><?php _e($opt['label'])?></td>
									<td>
									<?php
										$htmltype = $opt['htmltype'];
										if($opt['code'] != 'template') {	// For template will be unique option editor
											$htmlOptions = array('value' => $opt['value'], 'attrs' => 'class="subGeneralOptInput"');
											switch($htmltype) {
												case 'checkbox': case 'checkboxHiddenVal':
													$htmlOptions['checked'] = (bool)$opt['value'];
													break;
											}
											if(!empty($opt['params']) && is_array($opt['params'])) {
												$htmlOptions = array_merge($htmlOptions, $opt['params']);
											}
											echo htmlSub::$htmltype('opt_values['. $opt['code']. ']', $htmlOptions);
										}
									?>
									</td>
								</tr>
								<?php }?>
								<tr>
									<td>
										<?php echo htmlSub::hidden('reqType', array('value' => 'ajax'))?>
										<?php echo htmlSub::hidden('page', array('value' => 'options'))?>
										<?php echo htmlSub::hidden('action', array('value' => 'saveMainGroup'))?>
										<?php echo htmlSub::submit('saveAll', array('value' => __('Save All Changes'), 'attrs' => 'class="button button-primary button-large"'))?>
									</td>
									<td id="subAdminMainOptsMsg"></td>
								</tr>
							</table>
						</form>
					</div>
				</div>
				<div id="idSubMainSubOpts" class="postbox subAdminTemplateOptRow subAvoidJqueryUiStyle" style="display: block">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><?php _e( 'Subscribe Settings' )?></h3>
					<div class="inside"><?php echo $this->subscribeSettings?></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div style="clear: both;"></div>


