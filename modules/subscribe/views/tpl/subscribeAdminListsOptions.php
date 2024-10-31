<div class="wrap">
	<div class="metabox-holder">
		<div class="postbox-container" style="width: 100%;">
			<div class="meta-box-sortables ui-sortable">
				<div id="idSubSubscribersLists" class="postbox subAdminTemplateOptRow" style="display: block">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><?php _e( 'Subscribers Lists' )?></h3>
					<div class="inside">
						<?php echo htmlSub::button(array('value' => __('Add New'), 'attrs' => 'id="subSubscribersListsAddButt" class="button"'))?>
						<table id="subAdminSubersListsTable" width="100%">
							<thead>
								<tr class="subTblHeader">
									<td><?php _e('Label')?></td>
									<td><?php _e('Remove')?></td>
								</tr>
							</thead>
							<tbody>
								<tr class="subExample subTblRow" style="display: none;">
									<td class="label" onclick="subSubscrbShowEditListForm(this); return false;"></td>
									<td>
										<a href="#" onclick="subSubscrbListRemove(this); return false;"><?php echo htmlSub::img('cross.gif')?></a>
										<?php echo htmlSub::hidden('id', array('attrs' => 'class="id" valueTo="value"'))?>
									</td>
								</tr>
							</tbody>
						</table>
						<div id="subAdminSubersListsPaging"></div>
						<div id="subAdminSubersListsMsg"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<form id="subAdminSubersListsForm" style="display: none;">
	<table>
		<tr>
			<td>
				<label for="subAdminSubersListsFormLabel"><?php _e('Label')?></label>
			</td>
			<td>
				<?php echo htmlSub::text('label', array('attrs' => 'id="subAdminSubersListsFormLabel"'))?>
			</td>
		</tr>
		<tr>
			<td>
				<label for="subAdminSubersListsFormDescription"><?php _e('Description')?></label>
			</td>
			<td>
				<?php echo htmlSub::textarea('description', array('attrs' => 'id="subAdminSubersListsFormDescription"'))?>
			</td>
		</tr>
	</table>
	<?php echo htmlSub::hidden('id')?>
	<?php echo htmlSub::hidden('page', array('value' => 'subscribe'))?>
	<?php echo htmlSub::hidden('action', array('value' => 'saveList'))?>
	<?php echo htmlSub::hidden('reqType', array('value' => 'ajax'))?>
	<?php echo htmlSub::submit('save', array('value' => __('Save'), 'attrs' => 'class="button button-primary"'))?>
	<?php echo htmlSub::button(array('value' => __('Cancel'), 'attrs' => 'class="button" onclick="subSubscrbCloseAddForm(this); return false;"'))?>
	<div class="subAdminSubersListsFormMsg"></div>
</form>
<div style="clear: both;"></div>