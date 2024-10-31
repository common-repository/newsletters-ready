<?php
class subscribeSub extends moduleSub {
	public function init() {
		dispatcherSub::addFilter('adminOptionsTabs', array($this, 'addOptionsTab'));
		add_action('widgets_init', array($this, 'registerWidget'));
		if(!is_admin()) {
			frameSub::_()->addScript('adminSubscribeOptions', $this->getModPath(). 'js/frontend.subscribe.js');
		}
		add_action('user_register',		array($this, 'createWpUser'));
		add_action('profile_update',	array($this, 'updateWpUser'));
		add_action('delete_user',		array($this, 'deleteWpUser'));
	}
	public function deleteWpUser($userId) {
		$subscriber = $this->getModel()->getByWpUserId($userId);
		if($userId && !empty($subscriber)) {
			$this->getModel()->remove(array('id' => $subscriber['id']));
		}
	}
	public function createWpUser($userId) {
		$userData = get_userdata($userId);
		if($userId 
			&& $userData 
			&& is_object($userData) 
			&& isset($userData->data)
			&& isset($userData->roles)
			&& is_array($userData->roles)
			&& in_array('subscriber', $userData->roles)
		) {
			$this->getModel()->create(array(
				'user_id' => $userId,
				'email' => $userData->data->user_email,
				'name' => $userData->data->display_name,	// Unused for now
				'list' => SUB_WP_LIST_ID,
				'withoutConfirm' => true,
			));
		}
	}
	public function updateWpUser($userId) {
		$userData = get_userdata($userId);
		if($userId 
			&& $userData 
			&& is_object($userData) 
			&& isset($userData->data)
			&& isset($userData->roles)
			&& is_array($userData->roles)
			&& in_array('subscriber', $userData->roles)
		) {
			$subscriber = $this->getModel()->getByWpUserId($userId);
			if(empty($subscriber)) {	// Just create it if it does not exist
				$this->createWpUser($userId);
			} else {
				$this->getModel()->update(array(
					'id' => $subscriber['id'],
					'user_id' => $userId,
					'email' => $userData->data->user_email,
					'name' => $userData->data->display_name,	// Unused for now
					'list' => $subscriber['list'],
				));
			}
		}
	}
	public function addOptionsTab($tabs) {
		frameSub::_()->addScript('adminSubscribeOptions', $this->getModPath(). 'js/admin.subscribe.options.js');
		$tabs['subSubscribeOptions'] = array(
			'title' => 'Subscribers', 'content' => $this->getController()->getView()->getAdminSubscribersOptions(),
		);
		$tabs['subSubscribeListsOptions'] = array(
			'title' => 'Subscribers Lists', 'content' => $this->getController()->getView()->getAdminListsOptions(),
		);
		return $tabs;
	}
	public function registerWidget() {
        return register_widget('subscribeWidgetSub');
    }
}

/**
 * Recent Products Widget Class
 */
class subscribeWidgetSub extends toeWordpressWidgetSub {
    public function __construct() {
        $widgetOps = array( 
            'classname' => 'subscribeWidgetSub', 
            'description' => __('Displays Subscribe Form')
        );
        $control_ops = array(
            'id_base' => 'subscribeWidgetSub'
        );
		parent::__construct( 'subscribeWidgetSub', __('Ready! Subscribe'), $widgetOps );
    }
    public function widget($args, $instance) {
		$this->preWidget($args, $instance);
        frameSub::_()->getModule('subscribe')->getView()->displaySubscribeWidget($instance);
		$this->postWidget($args, $instance);
    }
    public function update($new_instance, $old_instance) {
        return $new_instance;
    }
    public function form($instance) {
        frameSub::_()->getModule('subscribe')->getView()->displaySubscribeSetupForm($instance, $this);
    }
}
