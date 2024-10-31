<?php
class tableSubscribersSub extends tableSub {
    public function __construct() {
        $this->_table = '@__subscribers';
        $this->_id = 'id';
        $this->_alias = 'toe_subscr';
        $this->_addField('user_id', 'text', 'int', '', __('User Id'), 11, '', '', __('User Id'))
            ->_addField('email', 'text', 'varchar', '', __('User E-mail'), 255, '', '', __('Subscriber E-mail'))
            ->_addField('name', 'text', 'varchar', 0, __('User Name'),255,'','', __('User Name If User Is Registered'))
            ->_addField('created', 'text', 'datetime', '', __('Subscription Date'), '', '','', __('Date Of Subscription'))
            ->_addField('active', 'checkbox', 'tinyint', '', __('Active Subscription'), 4, '','', __('If Is Not Checked user will not get any newsletters'))
            ->_addField('token', 'hidden', 'varchar', '', __('Token'), 255,'','','')
			->_addField('ip', 'hidden', 'varchar', '', __('IP address'), 64,'','','');
    }
}
