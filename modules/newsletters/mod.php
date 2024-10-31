<?php
class newslettersSub extends moduleSub {
	private $_statuses = array();
	private $_sentStatuses = array();
	private $_dayNumbers = array(
		'monday'	=> 32,
		'tuesday'	=> 33,
		'wednesday' => 34,
		'thursday'	=> 35,
		'friday'	=> 36,
		'saturday'	=> 37,
		'sunday'	=> 38,
	);
	public function init() {
		dispatcherSub::addFilter('adminOptionsTabs', array($this, 'addOptionsTab'));
		
		add_filter('cron_schedules', array($this, 'filterAddCronSchedules' ) );
		
		add_action(SUB_SCHEDULE_FILTER, array($this, 'makeScheduleSend'));
		
		if(wp_next_scheduled(SUB_SCHEDULE_FILTER) === false) {
			$endOfHour = mktime(date('H'), 0, 0) + 3600;
			// Schedule event check - each hour
			wp_schedule_event($endOfHour, SUB_S_MIN, SUB_SCHEDULE_FILTER);
		}
		$postStatusesSendCheck = array('new',
			'pending',
			'draft',
			'auto-draft',
			'future',
			'private',
			'inherit',
			'trash');
		foreach($postStatusesSendCheck as $status) {
			add_action($status. '_to_publish', array($this, 'checkNewPostSend'));
		}
		
		$this->_statuses = array(
			0 => array('key' => 'new',			'label' => __('New')),
			1 => array('key' => 'tpl_selected', 'label' => __('Template Selected')),
			2 => array('key' => 'waiting',		'label' => __('Waiting')),
			3 => array('key' => 'sent',			'label' => __('Sent')),
			10=> array('key' => 'first_step',	'label' => __('First Step')),
		);
		$this->_sentStatuses = array(
			-1 => array('key' => 'waiting'),
			0 => array('key' => 'failed'),
			1 => array('key' => 'success'),
		);
	}
   public function checkNewPostSend($post) {
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if(!$post || !is_object($post)) return;
		if(wp_is_post_revision($post->ID)) return;
		// Increase content counter
		$newContentCounter = $this->getNewContentCounter() + 1;
		$newsletters = frameSub::_()->getModule('newsletters')->getModel()->getListForNewPost($post);
		if(!empty($newsletters)) {
			foreach($newsletters as $newsletter) {
				if($newsletter 
					&& $newsletter['active'] // It's active
					&& isset($newsletter['send_params']['new_content']['more_then'])
					&& $newsletter['send_params']['new_content']['more_then']
					&& ($newContentCounter % $newsletter['send_params']['new_content']['more_then'] === 0)	// And it's time for current newsletter
				) {
					$this->getModel()->send($newsletter);
				}
			}
		}
		$this->setNewContentCounter( $newContentCounter );
	}
	public function filterAddCronSchedules($schedules) {
		return array_merge($schedules, array(
			SUB_S_MIN => array(
				'interval' => 60,
				'display' => __('Each minute')
			),
		));
	}
	public function makeScheduleSend() {
		$newsletters = frameSub::_()->getModule('newsletters')->getModel()->getListForSchedule(toeCurrentTimeSub());
		if(!empty($newsletters)) {
			foreach($newsletters as $newsletter) {
				if($newsletter && $newsletter['active']) {
					if($newsletter['send_type'] == SUB_TYPE_NOW && $newsletter['status_msg'] == 'sent') {
						// We showld send this only once - just return
						continue;
					}
					if(SUB_TEST_MODE) {
						/*SOME LOG*/
						$eol = "\n\r";
						file_put_contents('letter_'. $newsletter['id']. '.txt', $newsletter['id']. $eol. date('d-m-Y H:i:s'). $eol. utilsSub::serialize($newsletter));
						/*****/
					}
					$this->getModel()->send($newsletter);
				}
			}
		}
		
	}
	public function addOptionsTab($tabs) {
		frameSub::_()->addScript('adminNewslettersOptions', $this->getModPath(). 'js/admin.newsletters.options.js');
		$tabs['subnNewslettersOptions'] = array(
			'title'		=> 'Newsletters', 
			'content'	=> $this->getController()->getView()->getAdminTab(),
		);
		return $tabs;
	}
	public function getStatuses() {
		return $this->_statuses;
	}
	public function getStatusesByKeys() {
		static $statusesByKeys = array();
		if(empty($statusesByKeys)) {
			foreach($this->_statuses as $id => $status) {
				$statusesByKeys[ $status['key'] ] = $id;
			}
		}
		return $statusesByKeys;
	}
	public function getStatusByKey($key) {
		$statuses = $this->getStatusesByKeys();
		return isset($statuses[ $key ]) ? $statuses[ $key ] : false;
	}
	public function getStatusById($id, $what = 'key') {
		return isset($this->_statuses[ $id ]) ? $this->_statuses[ $id ][ $what ] : false;
	}
	public function getSentStatusById($id, $what = 'key') {
		return isset($this->_sentStatuses[ $id ]) ? $this->_sentStatuses[ $id ][ $what ] : false;
	}
	public function getDayNum($dayname) {
		$dayname = strtolower($dayname);
		return isset($this->_dayNumbers[ $dayname ]) ? $this->_dayNumbers[ $dayname ] : false;
	}
	public function getNewContentCounter() {
		return (int) get_option(SUB_CODE. '_new_content_counter');
	}
	public function setNewContentCounter($counter) {
		update_option(SUB_CODE. '_new_content_counter', (int)$counter);
	}
}