<?php
class subscribeModelSub extends modelSub {
	public function create($d = array()) {
		if(isset($d['email']) && !empty($d['email'])) {
			if(is_email($d['email'])) {
				$d['email'] = trim($d['email']);
				$d['user_id'] = isset($d['user_id']) ? (int) $d['user_id'] : 0;
				if(!frameSub::_()->getTable('subscribers')->exists($d['email'], 'email')) {
					$withoutConfirm = isset($d['withoutConfirm']) ? $d['withoutConfirm'] : false;
					if(($subscriberId = frameSub::_()->getTable('subscribers')->insert(array(
						'email'		=> $d['email'],
						'created'	=> dbSub::timeToDate(),
						'ip'		=> utilsSub::getIP(),
						'active'	=> $withoutConfirm ? 1 : 0,
						'token'		=> md5($d['email']. AUTH_KEY),
						'user_id'	=> $d['user_id'],
					)))) {
						if(!$withoutConfirm)
							$this->sendConfirmEmail($d['email']);
						$listIds = isset($d['list']) ? $d['list'] : array();
						$this->bindUserToLists($subscriberId, $listIds);
						return $subscriberId;
					} else {
						$this->pushError( __('Error insert email to database') );
					}
				} else
					$this->pushError (__('You are already subscribed'));
			} else
				$this->pushError (__('Invalid email'));
		} else
			$this->pushError (__('Please enter email'));
		return false;
	}
	public function update($d = array()) {
		$id = isset($d['id']) ? (int) $d['id'] : 0;
		if($id) {
			if(frameSub::_()->getTable('subscribers')->update(array(
				'email'		=> $d['email'],
			), array(
				'id' => $id
			))) {
				$listIds = isset($d['list']) ? $d['list'] : array();
				$this->bindUserToLists($id, $listIds);
				return $id;
			} else
				$this->pushError(frameSub::_()->getTable('subscribers')->getErrors());
		} else
			$this->pushError (__('Empty or invalid ID'));
		return false;
	}
	public function bindUserToLists($userId, $listId) {
		$this->unbindUserFromLists($userId);
		if(!empty($listId)) {
			if(!is_array($listId))
				$listId = array( $listId );
			$listId = array_map('intval', $listId);
			$insertValuesArr = array();
			foreach($listId as $lid) {
				if($lid)
					$insertValuesArr[] = '('. $userId. ', '. $lid. ')';
			}
			if(!empty($insertValuesArr))
				dbSub::query('INSERT INTO @__subscribers_to_lists (subscriber_id, subscriber_list_id) VALUES '. implode(', ', $insertValuesArr));
		}
	}
	public function unbindUserFromLists($userId) {
		frameSub::_()->getTable('subscribers_to_lists')->delete(array('subscriber_id' => $userId));
	}
	public function save($d = array()) {
		$id = isset($d['id']) ? (int) $d['id'] : 0;
		if($id) {
			return $this->update($d);
		} else {
			return $this->create($d);
		}
	}
	public function sendConfirmEmail($email) {
		return frameSub::_()->getModule('messenger')->send(
					$email, 
					get_bloginfo('name'), 
					get_bloginfo('name'), 
					'subscribe', 
					'sub_confirm', 
					array(
						'site_name' => get_bloginfo('name'),
						'link' => $this->getConfirmLink($email),
					));
	}
	public function getConfirmLink($email) {
		$token = frameSub::_()->getTable('subscribers')->get('token', array('email' => $email), '', 'one');
		return uriSub::_(array(
			'pl'		=> SUB_CODE,
			'mod'		=> 'subscribe',
			'action'	=> 'confirmLead',
			'email'		=> $email,
			'token'		=> $token,
		));
	}
	public function confirm($d = array()) {
		if(isset($d['email']) 
			&& !empty($d['email']) 
			&& isset($d['token']) 
			&& !empty($d['token'])
		) {
			$subscriber = $this->getSuscriberByEmailToken($d['email'], $d['token'], false);
			$subId = $subscriber ? (int) $subscriber['id'] : 0;
			if(!empty($subId)) {
				frameSub::_()->getTable('subscribers')->update(array('active' => 1), array('id' => $subId));
				if(!frameSub::_()->getModule('options')->isEmpty('sub_admin_email')) {
					$this->sendAdminNotification($d['email']);
				}
				dispatcherSub::doAction('subscribeConfirm', $subId);
				return $subId;
			} else
				$this->pushError (__('No record for such email or token'));
		} else
			$this->pushError (__('Invalid confirm data'));
		return false;
	}
	public function unsubscribe($d = array()) {
		if(isset($d['email']) 
			&& !empty($d['email']) 
			&& isset($d['token']) 
			&& !empty($d['token'])
		) {
			$subscriber = $this->getSuscriberByEmailToken($d['email'], $d['token'], true);
			$subId = $subscriber ? (int) $subscriber['id'] : 0;
			if(!empty($subId)) {
				// Just deactivate subscriber for now
				frameSub::_()->getTable('subscribers')->update(array('active' => 0), array('id' => $subId));
				/*if(!frameSub::_()->getModule('options')->isEmpty('sub_admin_email')) {
					$this->sendAdminNotification($d['email']);
				}*/
				dispatcherSub::doAction('unsubscribeConfirm', $subId);
				return $subId;
			} else
				$this->pushError (__('No record for such email or token'));
		} else
			$this->pushError (__('Invalid unsubscribe data'));
		return false;
	}
	public function getSuscriberByEmailToken($email, $token, $active = null) {
		$getParams = array('email' => $email, 'token' => $token);
		if(!is_null($active)) {
			$getParams['active'] = $active;
		}
		return frameSub::_()->getTable('subscribers')->get('*', $getParams, '', 'row');
	}
	public function sendAdminNotification($email) {
		return frameSub::_()->getModule('messenger')->send(
					frameSub::_()->getModule('options')->get('sub_admin_email'), 
					get_bloginfo('name'), 
					get_bloginfo('name'), 
					'subscribe', 
					'sub_admin_notify', 
					array(
						'site_name' => get_bloginfo('name'),
						'email' => $email,
					));
	}
	/*public function prepareData($data) {
		$tmpData = $data;
		$data = array();
		$idToIter = array();
		$i = 0;
		foreach($tmpData as $d) {
			if(!isset($idToIter)) {
				$idToIter[ $d['id'] ] = $i;
				$data[ $idToIter[ $d['id'] ] ] = $d;
				$data[ $idToIter[ $d['id'] ] ]['lists'] = array();
				$i++;
			}
			$data[ $idToIter[ $d['id'] ] ]['lists'][] = $d['list_id'];
		}
		return $data;
	}*/
	public function getList($d = array()) {
		if(isset($d['limitFrom']) && isset($d['limitTo']))
			frameSub::_()->getTable('subscribers')->limitFrom($d['limitFrom'])->limitTo($d['limitTo']);
		if(isset($d['filterListId']) && !empty($d['filterListId'])) {
			if(!is_array($d['filterListId']))
				$d['filterListId'] = array( $d['filterListId'] );
			$d['filterListId'] = array_map('intval', $d['filterListId']);
		} else
			$d['filterListId'] = 0;
		if(!empty($d['filterListId'])) {
			$d['additionalCondition'] = 'EXISTS(SELECT subscriber_id FROM @__subscribers_to_lists WHERE subscriber_id = id AND subscriber_list_id IN ('. implode(', ', $d['filterListId']). ') )';
		}
		if(isset($d['orderBy']) && !empty($d['orderBy'])) {
			frameSub::_()->getTable('subscribers')->orderBy( $d['orderBy'] );
		}
		$fromDb = frameSub::_()->getTable('subscribers')
				->get('*, id AS subscr_id,
					(SELECT GROUP_CONCAT(subscriber_list_id SEPARATOR ",") FROM @__subscribers_to_lists WHERE subscriber_id = subscr_id) AS list_str,
					(SELECT GROUP_CONCAT(label SEPARATOR ", ") FROM @__subscribers_lists WHERE id IN (
						SELECT subscriber_list_id FROM @__subscribers_to_lists WHERE subscriber_id = subscr_id
					)) AS list_label_str', $d);
		//var_dump(dbSub::$query);
		foreach($fromDb as $i => $val) {
			$fromDb[ $i ] = $this->prepareItemData($fromDb[ $i ]);
		}
		return $fromDb;
	}
	public function getById($id) {
		$fromDb = frameSub::_()->getTable('subscribers')->get('*', array('id' => $id), '', 'row');
		if($fromDb && !empty($fromDb)) {
			$fromDb = $this->prepareItemData($fromDb);
		}
		return $fromDb;
	}
	public function getByWpUserId($userId) {
		$fromDb = frameSub::_()->getTable('subscribers')->get('*', array('user_id' => $userId), '', 'row');
		if($fromDb && !empty($fromDb)) {
			$fromDb = $this->prepareItemData($fromDb);
		}
		return $fromDb;
	}
	public function prepareItemData($data) {
		$data['status'] = (int)$data['active'] ? 'active' : 'disabled';
		$data['list'] = empty($data['list_str']) ? array() : array_map('intval', explode(',', $data['list_str']));
		return $data;
	}
	public function getCount($d = array()) {
		if(isset($d['filterListId']) && !empty($d['filterListId'])) {
			if(!is_array($d['filterListId']))
				$d['filterListId'] = array( $d['filterListId'] );
			$d['filterListId'] = array_map('intval', $d['filterListId']);
		} else
			$d['filterListId'] = 0;
		if(!empty($d['filterListId'])) {
			$d['additionalCondition'] = 'EXISTS(SELECT subscriber_id FROM @__subscribers_to_lists WHERE subscriber_id = id AND subscriber_list_id IN ('. implode(', ', $d['filterListId']). ') )';
		}
		return frameSub::_()->getTable('subscribers')->get('COUNT(*)', $d, '', 'one');
	}
	public function getCountLists($d = array()) {
		return frameSub::_()->getTable('subscribers_lists')->get('COUNT(*)', $d, '', 'one');
	}
	public function getListLists($d = array()) {
		if(isset($d['limitFrom']) && isset($d['limitTo']))
			frameSub::_()->getTable('subscribers_lists')->limitFrom($d['limitFrom'])->limitTo($d['limitTo']);
		$fromDb = frameSub::_()->getTable('subscribers_lists')->get('*, (SELECT COUNT(*) FROM @__subscribers_to_lists WHERE subscriber_list_id = id) AS subscribers_count', $d);
		return $fromDb;
	}
	public function saveList($d = array()) {
		$dbData = array(
			'label'			=> isset($d['label']) ? trim($d['label']) : '',
			'description'	=> isset($d['description']) ? trim($d['description']) : '',
		);
		if(!empty($dbData['label'])) {
			$id = isset($d['id']) ? (int) $d['id'] : 0;
			if($id)
				frameSub::_()->getTable('subscribers_lists')->update($dbData, array('id' => $id));
			else
				$id = frameSub::_()->getTable('subscribers_lists')->insert($dbData);
			return $id;
		} else
			$this->pushError (__('Empty Label'));
		return false;
	}
	public function getListById($id) {
		return frameSub::_()->getTable('subscribers_lists')->get('*', array('id' => $id), '', 'row');
	}
	public function unbindListFromUsers($listId) {
		frameSub::_()->getTable('subscribers_to_lists')->delete(array('subscriber_list_id' => $listId));
	}
	public function removeList($d = array()) {
		$id = isset($d['id']) ? (int) $d['id'] : 0;
		if($id) {
			frameSub::_()->getTable('subscribers_lists')->delete(array('id' => $id));
			$this->unbindListFromUsers( $id );
			return true;
		} else
			$this->pushError (__('Empty or invalid ID'));
		return false;
	}

	public function changeStatus($d = array()) {
		$d['id'] = isset($d['id']) ? (int)$d['id'] : 0;
		if($d['id']) {
			if(dbSub::query('UPDATE @__subscribers SET active = IF(active, 0, 1) WHERE id = "'. $d['id']. '"')) {
				return true;
			} else
				$this->pushError (__('Database error were occured'));
			return true;
		} else
			$this->pushError (__('Invalid ID'));
		return false;
	}
	public function remove($d = array()) {
		$d['id'] = isset($d['id']) ? (int)$d['id'] : 0;
		if($d['id']) {
			if(frameSub::_()->getTable('subscribers')->delete($d['id'])) {
				$this->unbindUserFromLists( $d['id'] );
				return true;
			} else
				$this->pushError (__('Database error were occured'));
			return true;
		} else
			$this->pushError (__('Invalid ID'));
		return false;
	}
	public function sendNewPostNotif($d = array()) {
		// All active subscribers
		$subscribers = $this->getList(array('active' => 1));
		if(!empty($subscribers)) {
			foreach($subscribers as $s) {
				$data = $s;
				$data['post_id'] = $d['post_id'];
				$this->sendNewPostNotifOne($data);
			}
		}
	}
	public function sendNewPostNotifOne($d = array()) {
		if(!empty($d['email'])) {
			return frameSub::_()->getModule('messenger')->send(
					$d['email'], 
					get_bloginfo('name'), 
					get_bloginfo('name'), 
					'subscribe', 
					'sub_new_post', 
					array(
						'site_name' => get_bloginfo('name'),
						'post_link' => get_permalink($d['post_id']),
						'post_title' => get_the_title($d['post_id']),
					));
		}
		return false;
	}
	/**
	 * Provide subscribers by lists IDs
	 */
	public function getSubscribersFromLists($list = array()) {
		return $this->getList(array(
			'filterListId' => $list,
		));
	}
}
