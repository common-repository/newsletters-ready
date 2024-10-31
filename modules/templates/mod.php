<?php
class templatesSub extends moduleSub {
    /**
     * Returns the available tabs
     * 
     * @return array of tab 
     */
    protected $_styles = array();
	
    public function init() {
        $this->_styles = array(
            'styleSub'				=> array('path' => SUB_CSS_PATH. 'style.css'), 
			'adminStylesSub'		=> array('path' => SUB_CSS_PATH. 'adminStyles.css'), 
			'jquery-tabsSub'			=> array('path' => SUB_CSS_PATH. 'jquery-tabs.css'),
			'jquery-buttonsSub'		=> array('path' => SUB_CSS_PATH. 'jquery-buttons.css'),
			'wp-jquery-ui-dialogSub'	=> array(),
			'farbtastic'			=> array(),
			// Our corrections for ui dialog
			'jquery-dialog'			=> array('path' => SUB_CSS_PATH. 'jquery-dialog.css'),
			'jquery-timepicker'		=> array('path' => SUB_CSS_PATH. 'jquery-timepicker.css'),
			'jquery.slideInput'		=> array('path' => SUB_CSS_PATH. 'jquery.slideInput.css'),
			'jquery-dataTables'		=> array('path' => SUB_CSS_PATH. 'jquery.dataTables.css'),
			'jquery-ui-datepicker'	=> array('path' => SUB_CSS_PATH. 'jquery-datepicker.css'),
			'jquery-ui-resizable'	=> array('path' => SUB_CSS_PATH. 'jquery-resizable.css'),
        );
		$ajaxurl = admin_url('admin-ajax.php');
        $jsData = array(
            'siteUrl'					=> SUB_SITE_URL,
            'imgPath'					=> SUB_IMG_PATH,
			'cssPath'					=> SUB_CSS_PATH,
            'loader'					=> SUB_LOADER_IMG, 
            'close'						=> SUB_IMG_PATH. 'cross.gif', 
            'ajaxurl'					=> $ajaxurl,
            'animationSpeed'			=> frameSub::_()->getModule('options')->get('js_animation_speed'),
			'SUB_CODE'					=> SUB_CODE,
			'ball_loader'				=> SUB_IMG_PATH. 'ajax-loader-ball.gif',
			'ok_icon'					=> SUB_IMG_PATH. 'ok-icon.png',
			'options'					=> frameSub::_()->getModule('options')->getAllowedPublicOptions(),
			'SUB_ANY'					=> SUB_ANY,
			'SUB_TIME_IMMEDIATELY'		=> SUB_TIME_IMMEDIATELY,
			
			'SUB_TYPE_NOW'				=> SUB_TYPE_NOW,
			'SUB_TYPE_NEW_CONTENT'		=> SUB_TYPE_NEW_CONTENT,
			'SUB_TYPE_SCHEDULE'			=> SUB_TYPE_SCHEDULE,
        );
        
		frameSub::_()->addScript('jquery');

		frameSub::_()->addScript('commonSub', SUB_JS_PATH. 'common.js');
		frameSub::_()->addScript('coreSub', SUB_JS_PATH. 'core.js');
		$loadStyles = false;
        if (is_admin()) {
			if(reqSub::getVar('reqType') != 'ajax' && frameSub::_()->isAdminPlugPage()) {
				frameSub::_()->addScript('jquery-ui-tabs', '', array('jquery'));
				frameSub::_()->addScript('jquery-ui-dialog', '', array('jquery'));
				frameSub::_()->addScript('jquery-ui-button', '', array('jquery'));
				frameSub::_()->addScript('jquery-ui-resizable', '', array('jquery'));
				//frameSub::_()->addStyle('jquery-resizable', SUB_CSS_PATH. 'jquery-resizable.css', array('jquery'));
				
				frameSub::_()->addScript('jquery-ui-sortable', '', array('jquery'));
				frameSub::_()->addScript('jquery-ui-accordion', '', array('jquery'));
				frameSub::_()->addScript('jquery-ui-datepicker', '', array('jquery'));
				frameSub::_()->addScript('jquery-ui-timepicker-sub', SUB_JS_PATH. 'jquery.timepicker.min.js', array('jquery'));
				frameSub::_()->addScript('jquery-slideInput-sub', SUB_JS_PATH. 'jquery.slideInput.js', array('jquery'));
				frameSub::_()->addScript('jquery-dataTables-sub', SUB_JS_PATH. 'jquery.dataTables.js', array('jquery'));
				frameSub::_()->addScript('jquery-dataTables-columnFilter-sub', SUB_JS_PATH. 'jquery.dataTables.columnFilter.js', array('jquery'));
				
				frameSub::_()->addScript('farbtastic');
				frameSub::_()->addScript('adminOptionsSub', SUB_JS_PATH. 'admin.options.js');
				frameSub::_()->addScript('ajaxupload', SUB_JS_PATH. 'ajaxupload.js');
				frameSub::_()->addScript('postbox', get_bloginfo('wpurl'). '/wp-admin/js/postbox.js');

				/*$baseurl = includes_url('js/tinymce');
				$version = 'ver=' . $tinymce_version;
				frameSub::_()->addScript('tinymce', $baseurl. '/wp-tinymce.php?c=1&amp;'. $version);

				add_action('init', array($this, 'mediaInit'));*/
				
				add_thickbox();
				$jsData['allCheckRegPlugs']	= modInstallerSub::getCheckRegPlugs();
				
				frameSub::_()->addScript('wp-color-picker');
				frameSub::_()->addStyle('wp-color-picker');
				$loadStyles = true;
			}
		} else {
			
        }
		$jsData = dispatcherSub::applyFilters('jsInitVariables', $jsData);
        frameSub::_()->addJSVar('coreSub', 'SUB_DATA', $jsData);
        if($loadStyles) {
			foreach($this->_styles as $s => $sInfo) {
				if(isset($sInfo['for'])) {
					if(($sInfo['for'] == 'frontend' && is_admin()) || ($sInfo['for'] == 'admin' && !is_admin()))
						continue;
				}
				$canBeSubstituted = true;
				if(isset($sInfo['substituteFor'])) {
					switch($sInfo['substituteFor']) {
						case 'frontend':
							$canBeSubstituted = !is_admin();
							break;
						case 'admin':
							$canBeSubstituted = is_admin();
							break;
					}
				}
				if(!empty($sInfo['path'])) {
					frameSub::_()->addStyle($s, $sInfo['path']);
				} else {
					frameSub::_()->addStyle($s);
				}
			}
		}
		//$compress_scripts = false;
        parent::init();
    }
	public function loadFrontendAssets() {
		
	}
}
