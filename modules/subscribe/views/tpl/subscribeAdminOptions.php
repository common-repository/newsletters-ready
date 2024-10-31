<div class="wrap">
	<div class="metabox-holder">
		<div class="postbox-container" style="width: 100%;">
			<div class="meta-box-sortables ui-sortable">
				<div id="idSubMainSubOpts" class="postbox subAdminTemplateOptRow subAvoidJqueryUiStyle" style="display: block">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><?php _e( 'Main Subscribe Settings' )?></h3>
					<div class="inside">
						<form id="subSubAdminOptsForm" action="" class="subNiceStyle">
							<table width="100%">
								<?php foreach($this->subOptions as $opt) { ?>
								<tr class="subAdminSUbscribeOptionRow-<?php echo $opt['code']?> subTblRow">
									<td><?php _e($opt['label'])?></td>
									<td>
									<?php
										$htmltype = $opt['htmltype'];
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
									?>
									</td>
								<?php }?>
							</table>
							<?php  if(!empty($this->emailEditTpls)) { ?>
							<div class="wrap">
								<div class="metabox-holder">
									<div class="postbox-container" style="width: 100%;">
										<div class="meta-box-sortables ui-sortable">
										<?php foreach($this->emailEditTpls as $tpl) { ?>
											<div id="idSubMainSubOpts" class="postbox subAdminTemplateOptRow" style="display: block">
												<div class="handlediv" title="Click to toggle"><br></div>
												<h3 class="hndle"><?php _e( $tpl['label'] )?></h3>
												<div class="inside"><?php echo $tpl['content'];?></div>
											</div>
										<?php }?>
										</div>
									</div>
								</div>
							</div>
							<?php }?>
							<div>
								<?php echo htmlSub::hidden('reqType', array('value' => 'ajax'))?>
								<?php echo htmlSub::hidden('page', array('value' => 'options'))?>
								<?php echo htmlSub::hidden('action', array('value' => 'saveSubscriptionGroup'))?>
								<?php echo htmlSub::submit('saveAll', array('value' => __('Save All Changes'), 'attrs' => 'class="button button-primary button-large"'))?>
							</div>
							<div id="subAdminSubOptionsMsg"></div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div style="clear: both;"></div>