<?php
class stplViewSub extends viewSub {
	private $_elementsAddStyles = array(
		'.alignleft' => array(
			'float' => 'left',
			'margin-right' => '10px',
		),
		'.alignright' => array(
			'float' => 'right',
			'margin-left' => '10px',
		),
		'.aligncenter' => array(
			'margin-bottom' => '7px',
			'margin-top' => '7px',
			'display' => 'block',
			'margin-left' => 'auto',
			'margin-right' => 'auto',
		),
	);
	public function load($d = array()) {
		$this->assign('titleStyles', array(
			'h1' => __('Heading 1'),
			'h2' => __('Heading 2'),
			'h3' => __('Heading 3'),
			'h4' => __('Heading 4'),
			'h5' => __('Heading 5'),
			'h6' => __('Heading 6'),
			'div' => __('Text Block'),
		));
		$this->assign('aligns', array(
			'left'		=> __('Left'),
			'center'	=> __('Center'),
			'right'		=> __('Right'),
		));
		$this->assign('showContent', array(
			'excerpt'	=> __('Excerpt'),
			'full' => __('Full Post'),
		));
		$this->assign('fonts', array(
			'Arial'				=> 'Arial',
			'Arial Black'		=> 'Arial Black',
			'Comic Sans MS'		=> 'Comic Sans MS',
			'Courier New'		=> 'Courier New',
			'Georgia'			=> 'Georgia',
			'Impact'			=> 'Impact',
			'Tahoma'			=> 'Tahoma',
			'Times New Roman'	=> 'Times New Roman',
			'Trebuchet MS'		=> 'Trebuchet MS',
			'Verdana'			=> 'Verdana',
		));
		$this->assign('styleElements', array(
			'text'	=> array('label' => __('Text'),		'selector' => '*', 'defaults' => array('font-family' => 'Trebuchet MS', 'font-size' => '13px', 'color' => '#000000')),
			'links' => array('label' => __('Links'),	'selector' => 'a', 'defaults' => array('font-family' => 'Trebuchet MS', 'font-size' => '13px', 'color' => '#0000EE')),
			'h1'	=> array('label' => __('Heading 1'), 'selector' => 'h1', 'defaults' => array('font-family' => 'Trebuchet MS', 'font-size' => '22px', 'color' => '#000000')),
			'h2'	=> array('label' => __('Heading 2'), 'selector' => 'h2', 'defaults' => array('font-family' => 'Trebuchet MS', 'font-size' => '18px', 'color' => '#000000')),
			'h3'	=> array('label' => __('Heading 3'), 'selector' => 'h3', 'defaults' => array('font-family' => 'Trebuchet MS', 'font-size' => '16px', 'color' => '#000000')),
			'h4'	=> array('label' => __('Heading 4'), 'selector' => 'h4', 'defaults' => array('font-family' => 'Trebuchet MS', 'font-size' => '14px', 'color' => '#000000')),
			'h5'	=> array('label' => __('Heading 5'), 'selector' => 'h5', 'defaults' => array('font-family' => 'Trebuchet MS', 'font-size' => '13px', 'color' => '#000000')),
			'h6'	=> array('label' => __('Heading 6'), 'selector' => 'h6', 'defaults' => array('font-family' => 'Trebuchet MS', 'font-size' => '12px', 'color' => '#000000')),
		));
		$postsNum = array();
		for($i = 1; $i <= 10; $i++) {
			$postsNum[ $i ] = $i;
		}
		$this->assign('postsNum', $postsNum);
		$fontSizesList = array(8, 9, 10, 11, 12, 13, 14, 15, 16, 18, 20, 22, 24, 26, 28, 30, 32, 34, 36, 38, 40, 44, 48, 52, 56, 60, 66, 72);
		$fontSizes = array();
		foreach($fontSizesList as $f) {
			$fontSizes[ $f. 'px' ] = $f. 'px';
		}
		$this->assign('fontSizes', $fontSizes);
		return parent::getContent('stplEditor');
	}
	public function generateContent($idOrContent, $options = array()) {
		$stpl = array();
		if(is_numeric($idOrContent)) {
			$stpl = $this->getModel()->getById($idOrContent);
		} elseif(is_array($idOrContent)) {
			$stpl = $idOrContent;
		}
		if($stpl) {
			$this->assign('options', $options);
			$this->assign('stpl', $stpl);
			$stplContent = parent::getContent('stplContent');
			$this->getModule()->loadLib('simple_html_dom');
			$stplContentObj = str_get_html( $stplContent );
			foreach($this->_elementsAddStyles as $selector => $selectorStyles) {
				$elements = $stplContentObj->find( $selector );
				if(!empty($elements)) {
					foreach($elements as $element) {
						$stylesArray = $element->getStyleArray();
						foreach($selectorStyles as $styleKey => $styleVal) {
							if(isset($stylesArray[ $styleKey ])) continue;
							$stylesArray[ $styleKey ] = $styleVal;
						}
						$element->setStyleFromArray( $stylesArray );
					}
				}
			}
			
			// Set all user selected style params
			if(isset($stpl['style_params']) && isset($stpl['style_params']['font_style'])) {
				$fontStyleKeys = array('font-family', 'font-size', 'color');
				foreach($stpl['style_params']['font_style'] as $key => $style) {
					$elements = $stplContentObj->find($style['selector']);
					if(!empty($elements)) {
						foreach($elements as $element) {
							$stylesArray = $element->getStyleArray();
							foreach($fontStyleKeys as $styleKey) {
								if(isset($stylesArray[ $styleKey ]) && $key === 'text') continue;
								$stylesArray[ $styleKey ] = $style[ $styleKey ];
							}
							$element->setStyleFromArray( $stylesArray );
						}
					}
				}
			}
			$stplContent = $stplContentObj;
			return $stplContent;
		} else
			$this->pushError(__('Can not find template for to generate'));
		return false;
	}
	public function showTextEditor() {
		parent::display('stplTextEditor');
	}
	public function showPostsContent($posts, $params) {
		$this->assign('posts', $posts);
		$this->assign('params', $params);
		return parent::getContent('postsContent');
	}
}
