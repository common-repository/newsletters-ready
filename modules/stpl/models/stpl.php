<?php
class stplModelSub extends modelSub {
	public function save($d = array()) {
		$stplId = isset($d['id']) ? (int) $d['id'] : 0;
		$update = $stplId ? true : false;
		$rows = isset($d['rows']) ? $d['rows'] : array();
		if(!empty($rows)) {
			$styleParams = $this->_prepareTplParams( (isset($d['style_params']) ? $d['style_params'] : array()), $stplId );
			$styleParamsCoded = dbsub::escape( utilsSub::serialize($styleParams) );
			$stplDbData = array(
				//'id' => $stplId,
				'style_params' => $styleParamsCoded,
			);	// Empty for now
			if(isset($d['label']))
				$stplDbData['label'] = $d['label'];
			if(isset($d['parent_id']))
				$stplDbData['parent_id'] = $d['parent_id'];
			if($update) {
				$stplDbData['id'] = $stplId;
				frameSub::_()->getTable('stpl')->update($stplDbData, $stplId);
			} else {
				frameSub::_()->getTable('stpl')->insert($stplDbData);
				//dbSub::query('INSERT INTO @__stpl (`style_params`) values ("'. $styleParamsCoded. '")');
				$stplId = dbSub::insertID();
			}
			if($stplId) {
				$this->clearDataForTpl( $stplId );
				foreach($rows as $rowIter => $row) {
					$rowId = frameSub::_()->getTable('stpl_rows')->insert(array(
						'stpl_id'	=> $stplId,
						'height'	=> $row['height'],
						'background_color'	=> $row['background_color'],
					));
					if($rowId) {
						if(isset($row['cols']) && !empty($row['cols'])) {
							foreach($row['cols'] as $colIter => $col) {
								$colId = frameSub::_()->getTable('stpl_cols')->insert(array(
									'stpl_row_id'	=> $rowId,
									'width'			=> $col['width'],
									'content'		=> $this->_prepareColContent($col['content'], $stplId),
									'element_class'	=> $col['element_class'],
								));
								if(!$colId) {
									$this->pushError(sprintf(__('Can not insert col number %1$s in row number %2$s'), $colIter, $rowIter));
									$this->pushError( frameSub::_()->getTable('stpl_cols')->getErrors() );
									return false;
								}
							}
						}
					} else {
						$this->pushError(sprintf(__('Can not insert row number %1$s'), $rowIter));
						$this->pushError( frameSub::_()->getTable('stpl_rows')->getErrors() );
						return false;
					}
				}
				return $stplId;
			} else {
				$this->pushError (__('Can not insert stpl object'));
				$this->pushError ( frameSub::_()->getTable('stpl')->getErrors() );
			}
		} else
			$this->pushError (__('Empty template content'));
		
		return false;
	}
	public function getList($d = array()) {
		$data = frameSub::_()->getTable('stpl')->get('*', $d);
		if(!empty($data)) {
			foreach($data as $i => $tpl) {
				$data[ $i ]['full_preview_img'] = $this->getModule()->getModPath(). 'img/previews/'. $data[ $i ]['preview_img'];
			}
		}
		return $data;
	}
	public function duplicateTpl($id) {
		$stplData = $this->getById( $id );
		if($stplData) {
			$stplData['parent_id'] = $stplData['id'];
			unset($stplData['id']);	// Let's create new template from selected
			$stplData = $this->_escapeStplColsData($stplData);
			return $this->save( $stplData );
		}
		return false;
	}
	private function _escapeStplColsData($stplData) {
		if(isset($stplData['rows'])) {
			foreach($stplData['rows'] as $i => $row) {
				if(isset($row['cols'])) {
					foreach($row['cols'] as $j => $col) {
						if(isset($col['content']) && !empty($col['content'])) {
							$stplData['rows'][$i]['cols'][$j]['content'] = dbSub::escape( $col['content'] );
						}
					}
				}
			}
		}
		return $stplData;
	}
	public function getMaxId() {
		return (int) frameSub::_()->getTable('stpl')->get('MAX(id) as max_id', array(), '', 'one');
	}
	private function _prepareTplParams($params, $stplId) {
		if(is_array($params) && !empty($params)) {
			foreach($params as $i => $p) {
				if(is_string($params[ $i ]))
					$params[ $i ] = $this->_replaceBeforeDatabase($params[ $i ], $stplId);
			}
		}
		return $params;
	}
	private function _afterGetTplParams($params) {
		if(is_array($params) && !empty($params)) {
			foreach($params as $i => $p) {
				if(is_string($params[ $i ]))
					$params[ $i ] = $this->_replaceAfterDatabase($params[ $i ]);
			}
		}
		return $params;
	}
	private function _replaceBeforeDatabase($content, $stplId = 0) {
		if(frameSub::_()->getModule('stpl_additions')) {
			$content = str_replace(frameSub::_()->getModule('stpl_additions')->getModPath(), 'SUB_STPL_ADDITIONS', $content);
		}
		$content = 
				str_replace(get_bloginfo('admin_email'), 'SUB_STPL_ADMIN_EMAIL', 
				str_replace(SUB_SITE_URL, 'SUB_STPL_SITE_URL', 
				str_replace($this->getModule()->getModPath(), 'SUB_STPL_MOD_URL', 
				str_replace(frameSub::_()->getModule('templates')->getModPath(), 'SUB_STPL_TPL_MOD', 
				str_replace('SUB_STPL_ID', $stplId, $content)))));
		return $content;
	}
	private function _replaceAfterDatabase($content) {
		if(frameSub::_()->getModule('stpl_additions')) {
			$content = str_replace('SUB_STPL_ADDITIONS', frameSub::_()->getModule('stpl_additions')->getModPath(), $content);
		}
		$content = 
			str_replace('SUB_STPL_ADMIN_EMAIL', get_bloginfo('admin_email'),
			str_replace('SUB_STPL_SITE_URL', SUB_SITE_URL, 
			str_replace('SUB_STPL_MOD_URL', $this->getModule()->getModPath(), 
			str_replace('SUB_STPL_TPL_MOD', frameSub::_()->getModule('templates')->getModPath(), $content))));
		if(strpos($content, 'SUB_STPL_RAND_POST_ID') !== false) {
			$content = str_replace('SUB_STPL_RAND_POST_ID', $this->_getRandPostId(), $content);
		}
		return $content;
	}
	/**
	 * Internal method to replace rand post IDs from database protected templates
	 */
	private function _getRandPostId() {
		static $posts, $usedIds = array();
		$randId = 0;
		if(empty($posts)) {
			$posts = get_posts(array(
				'posts_per_page' => -1,
			));
		}
		if(!empty($posts)) {
			return $this->_getRandNotUsedId($posts, $usedIds);
		}
		return $randId;
	}
	private function _getRandNotUsedId(&$posts, &$usedIds) {
		$randKey = array_rand( $posts );
		$randId = $posts[ $randKey ]->ID;
		if(!in_array($randId, $usedIds)) {
			$usedIds[] = $randId;
			return $randId;
		} elseif(count($usedIds) >= count($posts)) {
			return $randId;
		}
		return $this->_getRandNotUsedId($posts, $usedIds);
	}
	private function _prepareColContent($content, $stplId) {
		return $this->_replaceBeforeDatabase($content, $stplId);
	}
	private function _afterGetCol($col) {
		$col['content'] = $this->_replaceAfterDatabase($col['content']);
		return $col;
	}
	public function clearDataForTpl($stplId) {
		$rows = $this->getRowsForTpl($stplId);
		if(!empty($rows)) {
			$rowsIds = array();
			foreach($rows as $row) {
				$rowsIds[] = $row['id'];
			}
			frameSub::_()->getTable('stpl_rows')->delete(array('stpl_id' => $stplId));
			frameSub::_()->getTable('stpl_cols')->delete(array('additionalCondition' => 'stpl_row_id IN ('. implode(', ', $rowsIds). ')'));
		}
	}
	public function getRowsForTpl($stplId) {
		return frameSub::_()->getTable('stpl_rows')->get('*', array('stpl_id' => $stplId));
	}
	public function getColsForRow($rowId) {
		$cols = frameSub::_()->getTable('stpl_cols')->get('*', array('stpl_row_id' => $rowId));
		if(!empty($cols)) {
			foreach($cols as $i => $col) {
				$cols[ $i ] = $this->_afterGetCol($cols[ $i ]);
			}
		}
		return $cols;
	}
	public function getById($stplId) {
		$stplData = frameSub::_()->getTable('stpl')->get('*', array('id' => $stplId), '', 'row');
		if(!empty($stplData)) {
			$stplData['rows'] = array();
			$stplRows = $this->getRowsForTpl($stplId);
			if(!empty($stplRows)) {
				$i = 0;
				foreach($stplRows as $row) {
					$stplData['rows'][ $i ] = array(
						'height'	=> $row['height'],
						'cols'		=> array(),
						'background_color' => $row['background_color'],
					);
					$cols = $this->getColsForRow( $row['id'] );
					if(!empty($cols)) {
						foreach($cols as $col) {
							$stplData['rows'][ $i ]['cols'][] = $col;
						}
					}
					$i++;
				}
			}
			$stplData = $this->prepareItem($stplData);
			return $stplData;
		}
		return false;
	}
	public function prepareItem($item) {
		$item['style_params'] = utilsSub::unserialize($item['style_params']);
		if(empty($item['style_params']))
			$item['style_params'] = array();
		$item['style_params'] = $this->_afterGetTplParams($item['style_params']);
		return $item;
	}
	public function getPostsList($d = array()) {
		return get_posts(array_merge(array(
			'posts_per_page' => -1,
			'post_status' => 'any',
			'orderby' => 'title',
			'order' => 'ASC',
		), $d));
	}
	public function getPostsCategoriesList($d = array()) {
		return get_categories(array_merge(array(
			'hide_empty' => 0,
		), $d));
	}
	public function getShortcodeHtml($d = array()) {
		$shortcode = isset($d['shortcode']) ? $d['shortcode'] : '';
		if(!empty($shortcode)) {
			return do_shortcode(stripslashes($shortcode));
		} else
			$this->pushError (__('Empty shortcode'));
		return false;
	}
	public function getIdByParent($parentId) {
		return frameSub::_()->getTable('stpl')->get('id', array('parent_id' => $parentId), '', 'one');
}
	public function getParentById($id) {
		return frameSub::_()->getTable('stpl')->get('parent_id', array('id' => $id), '', 'one');
	}
}
