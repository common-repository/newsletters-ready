<?php
class tableFilesSub extends tableSub {
    public function __construct() {
        $this->_table = '@__files';
        $this->_id = 'id';
        $this->_alias = 'toe_f';
        $this->_addField('pid', 'hidden', 'int', '', __('Product ID'))
                ->_addField('name', 'text', 'varchar', '255', __('File name'))
                ->_addField('path', 'hidden', 'text', '', __('Real Path To File'))
                ->_addField('mime_type', 'text', 'varchar', '32', __('Mime Type'))
                ->_addField('size', 'text', 'int', 0, __('File Size'))
                ->_addField('active', 'checkbox', 'tinyint', 0, __('Active Download'))
                ->_addField('date','text','datetime','',__('Upload Date'))
                ->_addField('download_limit','text','int','',__('Download Limit'))
                ->_addField('period_limit','text','int','',__('Period Limit'))
                ->_addField('description', 'textarea', 'text', 0, __('Descritpion'))
                ->_addField('type_id','text','int','',__('Type ID'));
    }
}
