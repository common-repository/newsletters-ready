<?php
class newslettersModelSub extends modelSub {
	protected $_sendMailErrors = array();
	public function pushSendMailError($email, $error) {
		if(!isset($this->_sendMailErrors[ $email ]))
			$this->_sendMailErrors[ $email ] = array();
		if(is_array($error))
			$this->_sendMailErrors[ $email ] = array_merge($this->_sendMailErrors[ $email ], $error);
		else
			$this->_sendMailErrors[ $email ][] = $error;
	}
	public function getSendMailError($email = '') {
		if(empty($email))
			return $this->_sendMailErrors;
		return isset($this->_sendMailErrors[ $email ]) ? $this->_sendMailErrors[ $email ] : false;
	}
	public function getList($d = array()) {
		if(isset($d['limitFrom']) && isset($d['limitTo']))
			frameSub::_()->getTable('newsletters')->limitFrom($d['limitFrom'])->limitTo($d['limitTo']);
		if(isset($d['orderBy']) && !empty($d['orderBy'])) {
			frameSub::_()->getTable('newsletters')->orderBy( $d['orderBy'] );
		}
		$fromDb = frameSub::_()->getTable('newsletters')->get('*, 
			(SELECT GROUP_CONCAT(subscriber_list_id SEPARATOR ",") FROM @__newsletters_to_lists WHERE newsletter_id = id) AS list_str,
			(SELECT GROUP_CONCAT(label SEPARATOR ",") FROM @__subscribers_lists WHERE id IN (list_str)) AS list_label_str', $d);
		foreach($fromDb as $i => $val) {
			$fromDb[ $i ] = $this->prepareData($fromDb[ $i ]);
		}
		return $fromDb;
	}
	public function getListForSchedule($timestamp) {
		$hour = (int) date('H', $timestamp);
		$day = (int) date('j', $timestamp);
		$dayNameNum = (int) $this->getModule()->getDayNum(date('l', $timestamp));
		$month = (int) date('n', $timestamp);
		$year = (int) date('Y', $timestamp);
		$condition = array(
			"(`year` = $year OR `year` = 0)",
			"(`month` = $month OR `month` = 0)",
			"(`day` = $day OR `day` = $dayNameNum OR `day` = 0)",
			"(`hour` = $hour OR `hour` = 0)",
		);
		$ids = dbSub::get('SELECT newsletter_id FROM @__newsletters_schedule WHERE '. implode(' AND ', $condition), 'col');
		if(!empty($ids)) {
			return $this->getList(array('additionalCondition' => 'id IN ('. implode(', ', $ids). ') and active = "1"'));
		}
		return false;
	}
	/**
	 * @param object $post - new post object
	 */
	public function getListForNewPost($post) {
		$postCategoriesIds = wp_get_post_categories($post->ID);
		$postTags = wp_get_post_tags($post->ID);
		$categories = array(0);	// Any category
		$tags = array(SUB_ANY); // Any tag
		if(!empty($postCategoriesIds)) {
			foreach($postCategoriesIds as $cid) {
				$categories[] = $cid;
			}
		}
		if(!empty($postTags)) {
			foreach($postTags as $tag) {
				$tags[] = dbSub::escape($tag->name);
			}
		}
		$condition = array(
			"@__newsletters_to_posts_categories.cat_id IN (". implode(', ', $categories). ")",
			"@__newsletters_to_tags.tag IN ('". implode("', '", $tags). "')",
			"@__newsletters_to_posts_categories.newsletter_id = @__newsletters_to_tags.newsletter_id",
		);
		$ids = dbSub::get('SELECT @__newsletters_to_posts_categories.newsletter_id FROM @__newsletters_to_posts_categories, @__newsletters_to_tags WHERE '. implode(' AND ', $condition), 'col');
		if(!empty($ids)) {
			return $this->getList(array('additionalCondition' => 'id IN ('. implode(', ', $ids). ') and active = "1" and send_type = "'. SUB_TYPE_NEW_CONTENT. '"'));
		}
		return false;
	}
	public function getById($id) {
		$fromDb = frameSub::_()->getTable('newsletters')->get('*, 
			(SELECT GROUP_CONCAT(subscriber_list_id SEPARATOR ",") FROM @__newsletters_to_lists WHERE newsletter_id = id) AS list_str,
			(SELECT GROUP_CONCAT(label SEPARATOR ",") FROM @__subscribers_lists WHERE id IN (list_str)) AS list_label_str', array('id' => $id), '', 'row');
		if($fromDb && !empty($fromDb)) {
			$fromDb = $this->prepareData($fromDb);
		}
		return $fromDb;
	}
	public function prepareData($data) {
		$data['list'] = false;
		if($data['list_str'] && !empty($data['list_str'])) {
			$data['list'] = array_map('intval', explode(',', $data['list_str']));
		}
		$data['send_params'] = utilsSub::unserialize($data['send_params']);
		$data['status_msg'] = $this->getModule()->getStatusById($data['status']);
		$data['status_label'] = $this->getModule()->getStatusById($data['status'], 'label');
		return $data;
	}
	public function getCount($d = array()) {
		return frameSub::_()->getTable('newsletters')->get('COUNT(*)', $d, '', 'one');
	}
	public function validate($d = array()) {
		$d['id'] = isset($d['id']) ? (int)$d['id'] : 0;
		if(isset($d['subject'])) {
			$d['subject'] = trim($d['subject']);
			if(empty($d['subject'])) {
				$this->pushError(__('Please enter Subject'), 'subject');
			}
		}
		if(isset($d['active'])) {
			$d['active'] = 1;
		}
		if(isset($d['send_params'])) {
			if(!isset($d['send_type']) || empty($d['send_type'])) {
				$this->pushError(__('Please select when you want to send newsletter'), 'send_type');
			}
			
			$d['send_params']['send_only_new_users'] = isset($d['send_params']['send_only_new_users']) ? 1 : 0;
			switch($d['send_type']) {
				case SUB_TYPE_NOW:
					if($d['send_params']['sending_time']['type'] == SUB_TIME_APPOINTED) {
						if(empty($d['send_params']['sending_time']['date'])) {
							$this->pushError(__('Please select day to send your newsletter'));
						}
						if(empty($d['send_params']['sending_time']['time'])) {
							$this->pushError(__('Please select time to send your newsletter'));
						}
					}
					break;
				case SUB_TYPE_NEW_CONTENT:
					$d['send_params']['new_content']['more_then'] = (int) $d['send_params']['new_content']['more_then'];
					break;
				
			}
			$d['send_params_initial'] = $d['send_params'];
			
			$d['send_params'] = utilsSub::serialize($d['send_params']);
		}
		if(isset($d['from_name'])) {
			$d['from_name'] = trim($d['from_name']);
		}
		if(isset($d['from_email'])) {
			$d['from_email'] = trim($d['from_email']);
		}
		if(isset($d['reply_name'])) {
			$d['reply_name'] = trim($d['reply_name']);
		}
		if(isset($d['reply_email'])) {
			$d['reply_email'] = trim($d['reply_email']);
		}
		return $this->haveErrors() ? false : $d;
	}
	public function save($d = array()) {
		$d = $this->validate($d);
		if($d) {
			$id = $d['id'];
			$update = $id ? true : false;
			$saveData = $d;
			if($update)
				frameSub::_()->getTable('newsletters')->update($saveData, array('id' => $id));
			else
				$id = frameSub::_()->getTable('newsletters')->insert($saveData);
			if($id) {
				if(isset($d['stpl_data'])) {
					$stplId = frameSub::_()->getModule('stpl')->save( $d['stpl_data'] );
					if($stplId) {
						frameSub::_()->getTable('newsletters')->update(array(
							'stpl_id' => $stplId,
						), array('id' => $id));
					} else
						$this->pushError ( frameSub::_()->getModule('stpl')->getModel()->getErrors() );
				}
				//var_dump(mysql_error());
				$d['list'] = isset($d['list']) ? $d['list'] : array();
				$this->bindNewsletterToLists($id, $d['list']);
				if(isset($d['send_params_initial']) && isset($d['send_type'])) {
					$this->unbindNewsletterToContentType($id);
					if($d['send_type'] == SUB_TYPE_NEW_CONTENT) {
						$this->bindNewsletterToContentType($id, $d['send_params_initial']['new_content']);
					}
				}
			}
			return $id;
		}
		return false;
	}
	public function bindNewsletterToContentType($id, $typeData) {
		if(!empty($typeData['tags'])) {
			$typeData['tags'] = array_map('trim', explode(',', $typeData['tags']));
			if(!empty($typeData['tags'])) {
				$valuesArr = array();
				foreach($typeData['tags'] as $tag) {
					//if($tag == SUB_ANY) continue;
					if(empty($tag)) continue;
					$valuesArr[] = '('. $id. ', "'. dbSub::escape($tag). '")';
				}
				if(!empty($valuesArr)) {
					dbSub::query('INSERT INTO @__newsletters_to_tags (newsletter_id, tag) VALUES '. implode(',', $valuesArr));
				}
			}
		}
		if(!empty($typeData['categories'])) {
			$valuesArr = array();
			$typeData['categories'] = array_map('intval', $typeData['categories']);
			foreach($typeData['categories'] as $catId) {
				//if(empty($catId)) continue;
				$valuesArr[] = '('. $id. ', '. $catId. ')';
			}
			if(!empty($valuesArr)) {
				dbSub::query('INSERT INTO @__newsletters_to_posts_categories (newsletter_id, cat_id) VALUES '. implode(',', $valuesArr));
			}
		}
	}
	public function unbindNewsletterToContentType($id) {
		frameSub::_()->getTable('newsletters_to_tags')->delete(array('newsletter_id' => $id));
		frameSub::_()->getTable('newsletters_to_posts_categories')->delete(array('newsletter_id' => $id));
	}
	public function addSentEmail($subscriber, $newsletter, $sendRes = -1) {
		// Clear data for prev. sending of this newsletter - ?
		/*frameSub::_()->getTable('email_sent')->delete(array(
			'newsletter_id' => $newsletter['id'],
		));*/
		$res = false;
		$errors = $this->getSendMailError( $subscriber['email'] );
		$sendId = frameSub::_()->getTable('email_sent')->get('id', array(
			'subscriber_id' => $subscriber['id'],
			'newsletter_id' => $newsletter['id'],
		), '', 'one');
		if($sendId) {
			frameSub::_()->getTable('email_sent')->update(array(
				'status' => (int) $sendRes,
				'error_msg' => $errors ? implode('; ', $errors) : '',
			), array(
				'id' => $sendId,
			));
		} else {
			$res = frameSub::_()->getTable('email_sent')->insert(array(
				'subscriber_id' => $subscriber['id'],
				'newsletter_id' => $newsletter['id'],
				'date_sent' => time(),
				'status' => (int) $sendRes,
				'error_msg' => $errors ? implode('; ', $errors) : '',
			));
		}
		return $res; 
	}
	public function getAllUsedSubscribeLists() {
		return frameSub::_()->getTable('newsletters_to_lists')
				->innerJoin( frameSub::_()->getTable('subscribers_lists'), 'subscriber_list_id' )
				->groupBy('id')
				->get('*');
	}
	public function startSend($newsletter = array()) {
		if(isset($newsletter['list']) && !empty($newsletter['list'])) {
			$subscribers = frameSub::_()->getModule('subscribe')->getModel()->getSubscribersFromLists( $newsletter['list'] );
			if(!empty($subscribers)) {
				frameSub::_()->getTable('newsletters')->update(array(
					'status' => $this->getModule()->getStatusByKey('waiting'),
					'date_sent' => dateSub::getDbWithTime(),
				), array(
					'id' => $newsletter['id']
				));
				foreach($subscribers as $subscriber) {
					$this->addSentEmail( $subscriber, $newsletter );
				}
				// Unbind scheduled from this, if we will need - we will bind them next
				$this->unbindNewsletterSchedule($newsletter['id']);
				// If this is one-time send - just send it right now
				if($newsletter['send_type'] == SUB_TYPE_NOW && $newsletter['send_params']['sending_time']['type'] == SUB_TIME_IMMEDIATELY) {
					$this->send($newsletter);
				} else {
					if(($newsletter['send_type'] == SUB_TYPE_NOW && $newsletter['send_params']['sending_time']['type'] == SUB_TIME_APPOINTED)
						|| ($newsletter['send_type'] == SUB_TYPE_SCHEDULE)
					) {
						$this->bindNewsletterSchedule($newsletter);
					}
				}
				return true;
			} else
				$this->pushError(__('Selected Subscribers Lists are Empty'));
		} else
			$this->pushError(__('Select Lists at first'), 'list[]');
		return false;
	}
	public function unbindNewsletterSchedule($id) {
		frameSub::_()->getTable('newsletters_schedule')->delete(array('newsletter_id' => $id));
	}
	public function bindNewsletterSchedule($newsletter) {
		// Additional check - to avoid mass adding to schedule
		if(in_array($newsletter['send_type'], array(SUB_TYPE_NOW, SUB_TYPE_SCHEDULE))) {
			$dbData = array();
			if($newsletter['send_type'] == SUB_TYPE_NOW) {
				$monthDayYear = array();
				if(!empty($newsletter['send_params']['sending_time']['date'])) {
					$monthDayYear = array_map('trim', explode(SUB_DATE_DL, $newsletter['send_params']['sending_time']['date']));
				}
				$dbData = array(
					'year'	=> isset($monthDayYear[2]) ? (int)$monthDayYear[2] : 0,
					'month'	=> isset($monthDayYear[2]) ? (int)$monthDayYear[0] : 0,
					'day'	=> isset($monthDayYear[1]) ? (int)$monthDayYear[1] : 0,
					'hour'	=> (int) $newsletter['send_params']['sending_time']['time'],
					'one_time' => 1,
				);
			} else {
				$dbData = array(
					'year'	=> 0,
					'month'	=> (int) $newsletter['send_params']['schedule']['month'],
					'day'	=> (int) $newsletter['send_params']['schedule']['day'],
					'hour'	=> (int) $newsletter['send_params']['schedule']['hour'],
					'one_time' => 0,
				);
			}
			$dbData['newsletter_id'] = $newsletter['id'];
			frameSub::_()->getTable('newsletters_schedule')->insert($dbData);
		}
		
	}
	public function send($newsletter) {
		$subscribers = frameSub::_()->getModule('subscribe')->getModel()->getSubscribersFromLists( $newsletter['list'] );
		if(!empty($subscribers)) {
			@set_time_limit(0);
			$tplContent = frameSub::_()->getModule('stpl')->getView()->generateContent( $newsletter['stpl_id'] );
			// Clear send errors
			$this->_sendMailErrors = array();
			foreach($subscribers as $subscriber) {
				$sendRes = $this->sendOne( $subscriber['email'], $newsletter, $tplContent, $subscriber );
				$this->addSentEmail( $subscriber, $newsletter, $sendRes );
			}
			if($newsletter['send_type'] == SUB_TYPE_NOW) {
				frameSub::_()->getTable('newsletters')->update(array(
					'status' => $this->getModule()->getStatusByKey('sent')
				), array(
					'id' => $newsletter['id']
				));
			}
			return true;
		}
		return false;
	}
	public function bindNewsletterToLists($id, $listId) {
		$this->unbindNewsletterFromLists($id);
		if(!empty($listId)) {
			if(!is_array($listId))
				$listId = array( $listId );
			$listId = array_map('intval', $listId);
			$insertValuesArr = array();
			foreach($listId as $lid) {
				if($lid)
					$insertValuesArr[] = '('. $id. ', '. $lid. ')';
			}
			if(!empty($insertValuesArr))
				dbSub::query('INSERT INTO @__newsletters_to_lists (newsletter_id, subscriber_list_id) VALUES '. implode(', ', $insertValuesArr));
		}
	}
	public function unbindNewsletterFromLists($id) {
		frameSub::_()->getTable('newsletters_to_lists')->delete(array('newsletter_id' => $id));
	}
	private function _prepareSendContent($tplContent, $subscriber = array(), $newsletter = array()) {
		if(!empty($subscriber)) {
			$tplContent = str_replace(array('[stpl_preview_code]', '[unsubscribe_link_params]'), 'email='. $subscriber['email']. '&token='. $subscriber['token']. '&id='. $newsletter['id'], $tplContent);
		}
		$tplContent = str_replace('[site_url]', SUB_SITE_URL, $tplContent);
		return $tplContent;
	}
	public function sendOne($email, $newsletter, $tplContent, $subscriber = array()) {
		global $ts_mail_errors;
		global $phpmailer;
		// Clear prev. send errors at first
		$ts_mail_errors = array();
		$tplContent = $this->_prepareSendContent($tplContent, $subscriber, $newsletter);
		$sendRes = frameSub::_()->getModule('mail')->send( $email, $newsletter['subject'], $tplContent, $newsletter['from_name'], $newsletter['from_email'], $newsletter['reply_name'], $newsletter['reply_email'] );
		if(!$sendRes) {
			// Let's try to get errors about mail sending from WP
			if (!isset($ts_mail_errors)) $ts_mail_errors = array();
			if (isset($phpmailer)) {
				$ts_mail_errors[] = $phpmailer->ErrorInfo;
			}
			if(empty($ts_mail_errors)) {
				$ts_mail_errors[] = __('Can not send email - problem with send server');
			}
			$this->pushSendMailError( $email, $ts_mail_errors );
		}
		return $sendRes;
	}
	public function sendTest($d = array()) {
		$d['email'] = isset($d['email']) ? trim($d['email']) : 0;
		if($d['email']) {
			$d['subject'] = isset($d['subject']) ? trim($d['subject']) : 0;
			if($d['subject']) {
				$d['id'] = isset($d['id']) ? (int) $d['id'] : 0;
				if($d['id']) {
					$newsletter = $this->getById($d['id']);
					$newsletter['subject'] = $d['subject'];
					$tplContent = frameSub::_()->getModule('stpl')->getView()->generateContent( $newsletter['stpl_id'] );
					if($this->sendOne( $d['email'], $newsletter, $tplContent )) {
						return true;
					} else {
						$this->pushError( $this->getSendMailError($d['email']) );
					}
				} else
					$this->pushError(__('Empty ID'));
			} else
				$this->pushError(__('Please enter subject at first'));
		} else
			$this->pushError(__('Please enter test email at first'));
		return false;
	}
	public function remove($d = array()) {
		$id = isset($d['id']) ? (int)$d['id'] : 0;
		if($id) {
			$this->unbindNewsletterFromLists($id);
			frameSub::_()->getTable('newsletters')->delete(array('id' => $id));
			return true;
		} else
			$this->pushError(__('Invalid ID'));
		return false;
	}
	public function duplicate($d = array()) {
		$id = isset($d['id']) ? (int)$d['id'] : 0;
		if($id) {
			$newsletter = $this->getById($id);
			if($newsletter) {
				$stplData = frameSub::_()->getModule('stpl')->getModel()->getById( $newsletter['stpl_id'] );
				if($stplData)
					unset($stplData['id']);	// Let's create new template too
				return $this->save(array(
					'subject' => $newsletter['subject'],
					'list' => $newsletter['list'],
					'active' => $newsletter['active'],
					'stpl_data' => $stplData,
				));			
			} else
				$this->pushError(__('Can not find newsletter'));
		} else
			$this->pushError(__('Invalid ID'));
		return false;
	}
	public function selectTemplate($d = array()) {
		$d['subject'] = isset($d['subject']) ? trim($d['subject']) : '';
		$d['stpl_id'] = isset($d['stpl_id']) ? (int) $d['stpl_id'] : 0;
		if(!empty($d['subject'])) {
			if($d['stpl_id']) {
				$stplData = frameSub::_()->getModule('stpl')->getModel()->getById( $d['stpl_id'] );
				if($stplData)
					unset($stplData['id']);	// Let's create new template from selected
				$stplData['parent_id'] = $d['stpl_id'];
				return $this->save(array(
					'id' => $d['id'],
					'subject' => $d['subject'],
					'stpl_data' => $this->_escapeStplColsData($stplData),
					'status' => $this->getModule()->getStatusByKey('new'),
				));
			} else
				$this->pushError (__('Please select Template'));
		} else
			$this->pushError (__('Please enter Subject'), 'subject');
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
	public function saveTemplate($d = array()) {
		$d['next_action'] = isset($d['next_action']) ? $d['next_action'] : 'next';
		switch($d['next_action']) {
			case 'save':
				$d['status'] = $this->getModule()->getStatusByKey('new');
				break;
			case 'back':
				$d['status'] = $this->getModule()->getStatusByKey('first_step');
				break;
			case 'next':
			default:
				$d['status'] = $this->getModule()->getStatusByKey('tpl_selected');
				break;
		}
		if(($id = $this->save($d))) {
			return $id;
		}
		return false;
	}
	public function getSendStatCount($newsletterId) {
		return frameSub::_()->getTable('email_sent')->get('COUNT(*)', array('newsletter_id' => $newsletterId), '', 'one');
	}
	public function getSendStatList($d = array()) {
		if(isset($d['limitFrom']) && isset($d['limitTo']))
			frameSub::_()->getTable('email_sent')->limitFrom($d['limitFrom'])->limitTo($d['limitTo']);
		if(isset($d['newsletterId']))
			$d['newsletter_id'] = (int) $d['newsletterId'];	// Convert key to db key
		$fromDb = frameSub::_()->getTable('email_sent')
				->leftJoin( frameSub::_()->getTable('subscribers'), 'subscriber_id' )
				->get(frameSub::_()->getTable('email_sent')->alias(). '.*, '. frameSub::_()->getTable('subscribers')->alias(). '.email', $d);
		foreach($fromDb as $i => $val) {
			$fromDb[ $i ] = $this->prepareSendStatData($fromDb[ $i ]);
		}
		return $fromDb;
	}
	public function prepareSendStatData($item) {
		$item['status'] = (int) $item['status'];
		$item['date_sent_conv'] = date(SUB_DATE_FORMAT_HIS, $item['date_sent']);
		$item['status_msg'] = $this->getModule()->getSentStatusById($item['status']);
		return $item;
	}
	public function resend($d = array()) {
		$d['id'] = isset($d['id']) ? (int) $d['id'] : 0;
		if($d['id']) {
			$newsletter = $this->getById($d['id']);
			if($newsletter) {
				return $this->send($newsletter);
			} else
				$this->pushError (__('Can not find newsletter'));
		} else 
			$this->pushError (__('Invalid ID'));
		return false;
	}
	public function backToEdit($d = array()) {
		$d['id'] = isset($d['id']) ? (int) $d['id'] : 0;
		if($d['id']) {
			frameSub::_()->getTable('newsletters')->update(array(
				'status' => $this->getModule()->getStatusByKey('new')
			), array(
				'id' => $d['id']
			));
			return true;
		} else 
			$this->pushError (__('Invalid ID'));
		return false;
	}
}
