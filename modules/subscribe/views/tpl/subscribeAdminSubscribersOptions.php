<div class="wrap">
	<div class="metabox-holder">
		<div class="postbox-container" style="width: 100%;">
			<div class="meta-box-sortables ui-sortable">
				<div id="idSubSubscribers" class="postbox subAdminTemplateOptRow" style="display: block">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><?php _e( 'Subscribers' )?></h3>
					<div class="inside">
						<?php //echo htmlSub::button(array('value' => __('Add New'), 'attrs' => 'id="subSubscribersAddButt" class="button"'))?>
						<?php //echo htmlSub::selectbox('sub_select_list_in_table', array('attrs' => 'id="subSubscribersFilterByListSel" style="width: auto;"'))?>
						<table id="subAdminSubersTable" width="100%" class="widefat subAdminTable">
							<thead>
								<tr class="subTblHeader">
								<?php foreach($this->displayColumns as $colKey => $colData) { ?>
									<th class="<?php echo $colKey?>"><?php echo $colData['label'];?></th>
								<?php }?>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
						<?php /*?><table id="subAdminSubersTable" width="100%">
							<thead>
								<tr class="subTblHeader">
									<td><?php _e('Email')?></td>
									<td><?php _e('Status')?></td>
									<td><?php _e('Remove')?></td>
								</tr>
							</thead>
							<tbody>
								<tr class="subExample subTblRow" style="display: none;">
									<td class="email" onclick="subSubscrbShowEditForm(this); return false;"></td>
									<td>
										<a href="#" onclick="subSubscrbChangeStatus(this); return false;" class="status subStatusIndicator" valueTo="class"></a>
									</td>
									<td>
										<a href="#" onclick="subSubscrbRemove(this); return false;"><?php echo htmlSub::img('cross.gif')?></a>
										<?php echo htmlSub::hidden('id', array('attrs' => 'class="id" valueTo="value"'))?>
									</td>
								</tr>
							</tbody>
						</table><?php */?>
						<div id="subAdminSubersPaging"></div>
						<div id="subAdminSubersMsg"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<form id="subAdminSubersForm" style="display: none;">
	<table>
		<tr>
			<td valign="top">
				<label for="subAdminSubersFormEmail"><?php _e('Email')?></label>
			</td>
			<td valign="top">
				<?php echo htmlSub::text('email', array('attrs' => 'id="subAdminSubersFormEmail"'))?>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<label for="subAdminSubersFormLists"><?php _e('Lists')?></label>
			</td>
			<td class="subAdminSubersFormListsShell" valign="top"></td>
		</tr>
	</table>
	<?php echo htmlSub::hidden('id')?>
	<?php echo htmlSub::hidden('page', array('value' => 'subscribe'))?>
	<?php echo htmlSub::hidden('action', array('value' => 'saveAdmin'))?>
	<?php echo htmlSub::hidden('reqType', array('value' => 'ajax'))?>
	<?php echo htmlSub::submit('save', array('value' => __('Save'), 'attrs' => 'class="button button-primary"'))?>
	<?php echo htmlSub::button(array('value' => __('Cancel'), 'attrs' => 'class="button" onclick="subSubscrbCloseAddForm(this); return false;"'))?>
	<div class="subAdminSubersFormMsg"></div>
</form>
<script type="text/javascript">
// <!--
jQuery(document).ready(function(){
	subSubersAllLists = <?php echo utilsSub::jsonEncode($this->allLists)?>;
	subSubersTotalSubscribers = <?php echo $this->totalSubscribers?>;
	subSubersTblColumns = <?php echo utilsSub::jsonEncode($this->displayColumns)?>;
});
// -->
</script>
<div style="clear: both;"></div>
