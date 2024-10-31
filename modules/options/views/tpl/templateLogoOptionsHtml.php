<div class="subLeftCol">
<?php echo htmlSub::ajaxfile('logo_image', array(
	'url' => uriSub::_(array('baseUrl' => admin_url('admin-ajax.php'), 'page' => 'options', 'action' => 'saveLogoImg', 'reqType' => 'ajax')), 
	'buttonName' => 'Select Logo image', 
	'responseType' => 'json',
	'onSubmit' => 'toeOptLogoImgOnSubmitNewFile',
	'onComplete' => 'toeOptLogoImgCompleteSubmitNewFile',
))?>
<div id="subOptLogoImgkMsg"></div>
<br />
<img id="subOptLogoImgPrev" 
		src="<?php echo $this->optModel->isEmpty('logo_image') 
		? '' 
		: frameSub::_()->getModule('options')->getLogoImgFullPath()?>" 
style="max-width: 200px;" />
</div>
<div class="subRightCol">
    <div class="subTip subTipArrowLeft nomargin">
        <?php _e('Choose your logo, you can use png, jpg or gif image file.')?>
        <span class="subTipCorner"></span>
    </div>
    <br />
    <div class="subTip subTipArrowDown nomargin">
        <?php _e('You can use default logo, your own or disable it. To disable logo on Coming Soon page click "Remove image" button bellow.')?>
        <span class="subTipCorner"></span>
    </div> <br /> 
    
    <?php echo htmlSub::button(array('value' => __('Remove image'), 'attrs' => 'id="subLogoRemove" class="button button-large" style="width:100%;"'))?>
    <?php echo htmlSub::button(array('value' => __('Set default'), 'attrs' => 'id="subLogoSetDefault" class="button button-large" style="width:100%;"'))?>
    <div id="subAdminOptLogoDefaultMsg"></div>
</div>
<div class="clearfix"></div>