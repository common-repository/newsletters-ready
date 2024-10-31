<table width="100%">
	<tr class="subHeadCells">
		<td width="50%">
			<?php echo htmlSub::radiobutton('opt_values[bg_type]', array('value' => 'color', 'attrs' => 'id="subBgTypeColor"', 'checked' => ($this->optModel->get('bg_type') == 'color')))?>
			<label for="subBgTypeColor" class="button button-large"><?php _e('Color')?></label>            
		</td>
		<td width="50%">
			<?php echo htmlSub::radiobutton('opt_values[bg_type]', array('value' => 'image', 'attrs' => 'id="subBgTypeImage"', 'checked' => ($this->optModel->get('bg_type') == 'image')))?>
			<label for="subBgTypeImage" class="button button-large"><?php _e('Image')?></label>
		</td>
	</tr>
	<tr class="subBodyCells">
		<td id="subBgTypeColor-selection" colspan="2">
            <?php _e('Select Color:')?>
			<?php echo htmlSub::colorpicker('opt_values[bg_color]', array('value' => $this->optModel->get('bg_color')))?>
			<br />
			<?php echo htmlSub::button(array('value' => __('Set default'), 'attrs' => 'id="subColorBgSetDefault"'))?>
			<div id="subAdminOptColorDefaultMsg"></div>
		</td>
		<td id="subBgTypeImage-selection" colspan="2">
            <div class="subLeftCol">
                <?php echo htmlSub::ajaxfile('bg_image', array(
                    'url' => uriSub::_(array('baseUrl' => admin_url('admin-ajax.php'), 'page' => 'options', 'action' => 'saveBgImg', 'reqType' => 'ajax')), 
                    'buttonName' => 'Select Background image', 
                    'responseType' => 'json',
                    'attrs' => 'class="button button-large"',
                    'onSubmit' => 'toeOptImgOnSubmitNewFile',
                    'onComplete' => 'toeOptImgCompleteSubmitNewFile',
                ))?>
                <div id="subOptImgkMsg"></div>            
                <br />
                <img id="subOptBgImgPrev" src="<?php echo $this->optModel->isEmpty('bg_image') ? '' : frameSub::_()->getModule('options')->getBgImgFullPath()?>" style="max-width: 200px;" />
			</div>
            <div class="subRightCol">
                <div class="subBgImgShowTypeWrapper">
                    <?php echo htmlSub::radiobutton('opt_values[bg_img_show_type]', array('value' => 'stretch', 'attrs' => 'id="subBgImgShowType-stretch"', 'checked' => ($this->optModel->get('bg_img_show_type') == 'stretch')))?>
                    <label for="subBgImgShowType-stretch" class="button button-large"><?php _e('Stretch')?></label>
                    <?php echo htmlSub::radiobutton('opt_values[bg_img_show_type]', array('value' => 'center', 'attrs' => 'id="subBgImgShowType-center"', 'checked' => ($this->optModel->get('bg_img_show_type') == 'center')))?>
                    <label for="subBgImgShowType-center" class="button button-large"><?php _e('Center')?></label>
                    <?php echo htmlSub::radiobutton('opt_values[bg_img_show_type]', array('value' => 'tile', 'attrs' => 'id="subBgImgShowType-tile"', 'checked' => ($this->optModel->get('bg_img_show_type') == 'tile')))?>
                    <label for="subBgImgShowType-tile" class="button button-large"><?php _e('Tile')?></label>
                </div>
                <div class="subTip subTipArrowUp">
                    <?php _e('Choose a one of way how to display the site background.')?>
                </div>
                <?php echo htmlSub::button(array('value' => __('Remove image'), 'attrs' => 'id="subImgBgRemove" class="button button-large" style="width:100%;"'))?>
				<?php echo htmlSub::button(array('value' => __('Set default'), 'attrs' => 'id="subImgBgSetDefault" class="button button-large" style="width:100%;"'))?>
				<div id="subAdminOptImgBgDefaultMsg"></div>
            </div>
		</td>
	</tr>
</table>