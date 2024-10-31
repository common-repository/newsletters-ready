<?php
class tableEmail_templatesSub extends tableSub {
    public function __construct() {
        $this->_table = '@__email_templates';
        $this->_id = 'id';
        $this->_alias = 'toe_etpl';
        $this->_addField('label', 'text', 'varchar', '', __('Label'), 128, '','',__('Template label'))
               ->_addField('subject', 'textarea', 'varchar','', __('Subject'),255,'','',__('E-mail Subject'))
               ->_addField('body', 'textarea', 'text','', __('Body'),'','','',__('E-mail Body'))
               ->_addField('variables', 'block', 'text','', __('Variables'),'','','',__('Template variables. They can be used in the body and subject'))
               ->_addField('active', 'checkbox', 'tinyint',0, __('Active'),'','','',__('If checked the notifications will be sent to receiver'))
               ->_addField('name', 'hidden', 'varchar','','',128)
               ->_addField('moduleSub', 'hidden', 'varchar','','', 128);
    }
}
?>
