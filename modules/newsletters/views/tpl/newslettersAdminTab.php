<div id="subAdminNewslettersListShell">
	<?php /*This input will be moved in JS admin.newsletters.options.js - because it need to be inserted after dynamic created inputs of DataTable*/?>
	<?php //echo htmlSub::button(array('value' => __('Create New'), 'attrs' => 'class="button" id="subAdminNewslettersAddButt" style="float: left;"'))?>
	<div id="subAdminNewslettersAddButtMsg"></div>
	<div class="list">
		<table id="subAdminNewslettersListTbl" class="widefat subAdminTable" style="clear: none; width: 50em;">
			<thead>
				<tr class="subTblHeader">
				<?php foreach($this->displayColumns as $colKey => $colData) { ?>
					<th class="<?php echo $colKey?>"><?php echo $colData['label'];?></th>
				<?php }?>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
		<div style="clear: both;"></div>
	</div>
	<?php /*?><div id="subAdminNewslettersListPaging"></div>
	<div id="subAdminNewslettersListMsg"></div><?php */?>
</div>
<?php echo htmlSub::button(array('value' => __('Back to List'), 'attrs' => 'class="button" id="subAdminNewslettersBackButt" style="display: none; float: left;"'))?>
<div id="subAdminNewslettersTplSelectingShell" style="display: none;">
	<?php echo $this->tplSelecting?>
</div>
<div id="subAdminNewslettersSelectTplShell" style="display: none;">
	<?php echo $this->selectTpl?>
</div>
<div id="subAdminNewslettersFormShell" style="display: none;">
	<?php echo $this->editForm?>
</div>
<div id="subAdminNewslettersSendStatShell" style="display: none;">
	<?php echo $this->sendStat?>
</div>
<div id="subAdminNewslettersCreateNavShell" style="display: none;">
	
</div>

<script type="text/javascript">
// <!--
	var subNewslettersStatuses = <?php echo utilsSub::jsonEncode($this->statusesByKeys)?>
	,	subNewslettersColumns = <?php echo utilsSub::jsonEncode($this->displayColumns)?>;
	
// -->
</script>