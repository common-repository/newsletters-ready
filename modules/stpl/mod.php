<?php
/**
 * Super Template module
 */
class stplSub extends moduleSub {
	public function __construct($d, $params = array()) {
		parent::__construct($d, $params);
		dispatcherSub::addFilter('jsInitVariables', array($this, 'addjsInitVars'));
	}
	public function init() {
		dispatcherSub::addFilter('adminOptionsTabs', array($this, 'addOptionsTab'));
		if(is_admin() && frameSub::_()->isAdminPlugPage()) {
			add_action('in_admin_footer', array($this, 'showTextEditor'));
			frameSub::_()->addStyle('adminStpl', $this->getModPath(). 'css/admin.stpl.css');
		}
		$this->_libs = array(
			'simple_html_dom' => array('file' => 'simple_html_dom.php', 'testFunc' => 'checkSimpleHtmlDomExists'),
		);
		
		add_shortcode('new_content_ready', array($this, 'newContentShortcode'));
		add_shortcode('static_content_ready', array($this, 'staticContentShortcode'));
	}
	public function newContentShortcode($params) {
		$res = '';
		$getPostsParams = array(
			'numberposts' => $params['posts_num'],
			'category' => $params['category'],
		);
		$posts = wp_get_recent_posts($getPostsParams);
		if(!empty($posts)) {
			$res = $this->getView()->showPostsContent($posts, $params);
		}
		return $res;
	}
	public function staticContentShortcode($params) {
		$res = '';
		$postType = isset($params['static_content_post']) && !empty($params['static_content_post']) 
			 ? 'post'
			 : 'page';
		$postId = $postType === 'post' ? (int) $params['static_content_post'] : (int) $params['static_content_page'];
		if($postId) {
			 $posts = $this->getModel()->getPostsList(array(
				 'posts_per_page' => 1,
				 'post_type' => $postType,
				 'include' => $postId,
			 ));
			 if(!empty($posts)) {
				 foreach($posts as $i => $post) {
					 $posts[ $i ] = (array) $post;
				 }
				 $paramsReplaceKeys = array('title_style', 'title_align', 'show_content');
				 foreach($paramsReplaceKeys as $key) {
					 $params[ $key ] = $params[ 'static_'. $key ];
				 }
				 $res = $this->getView()->showPostsContent($posts, $params);
			 }
		}
		return $res;
	}
	public function checkSimpleHtmlDomExists() {
		return !function_exists('file_get_html');
	}
	public function addOptionsTab($tabs) {
		// Just add javascripts to adin tab
		if(function_exists( 'wp_enqueue_media' )){
			wp_enqueue_media();
		} else {
			wp_enqueue_style('thickbox');
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
		}
		frameSub::_()->addScript('adminStplMath', $this->getModPath(). 'js/admin.stpl.math.js');
		frameSub::_()->addScript('adminStplDragAnDrop', $this->getModPath(). 'js/admin.stpl.drag-an-drop.js');
		frameSub::_()->addScript('adminStplElements', $this->getModPath(). 'js/admin.stpl.elements.js');
		frameSub::_()->addScript('adminStplOptions', $this->getModPath(). 'js/admin.stpl.options.js');
		
		frameSub::_()->addScript('jquery-effects-core');
		return $tabs;
	}
	/**
	 * Call model shell - for more comfortable access
	 */
	public function save($d = array()) {
		return $this->getModel()->save($d);
	}
	public function showTextEditor() {
		$this->getView()->showTextEditor();
	}
	public function addjsInitVars($jsData) {
		$jsData['stplModPath'] = $this->getModPath();
		return $jsData;
	}
}

