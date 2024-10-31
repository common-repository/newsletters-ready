<?php

class email_templatesModelSub extends modelSub {
    /**
     * Template object from database
     * 
     * @var object 
     */
    var $template;
    /**
     * template subject
     * @var string 
     */
    var $subject = '';
    /**
     * Template Message
     * @var string 
     */
    var $message = '';
    /**
     * Return the object with fields
     * @param array $d
     * @return array 
     */
    public function get($d = array()) {
        parent::get($d);
        $fields = NULL;
        if(isset($d['id']) && is_numeric($d['id'])) {
            if($d['id']) {
                frameSub::_()->getTable('email_templates')->fillFromDB($d['id']);
            }
            $fields = frameSub::_()->getTable('email_templates')->getFields();
            $fields['active']->addHtmlParam('checked', (bool)$fields['active']->value);
        } elseif(!empty($d) && is_array($d)) {
            $fields = frameSub::_()->getTable('email_templates')->get('*', $d);
        } else {
            $fields = frameSub::_()->getTable('email_templates')->getAll();
            if(!empty($fields)) {
                for($i = 0; $i < count($fields); $i++) {
                    $fields[$i]['data'] = $fields[$i]['data'];
                }
            }
        }
        return $fields;
    }
    /**
     * Get all templates
     * 
     * @return array
     */
    public function getAll() {
        $templates = frameSub::_()->getTable('email_templates')->get('*');
        return $templates;
    }
    /**
     * Get the template by ID
     * @param int $id
     * @return object 
     */
    public function getById($id) {
        if (is_numeric($id)) {
            $conditions = array('id' => $id);
        } else {
            return false;
        }
        $templates = frameSub::_()->getTable('email_templates')->get('*',$conditions);
        return $templates[0];
    }
    /**
     * Update the template
     * 
     * @param array $d 
     */
    public function save($d=array()) {
        if (!empty($d)) {
            if (is_numeric($d['id'])) {
                $id = (int) $d['id'];
                if (frameSub::_()->getTable('email_templates')->update($d, array('id' => $id))){
                    return true;
                }
            } else
                $this->pushError (__('Invalid ID'));
        } else
			$this->pushError (__('Empty data'));
        return $res;
    }
    /**
     * Get the template from database
     * @param string $module
     * @param string $template
     * @return object Email Template 
     */
    public function getTemplate($module, $template) {
        $conditions = array('module' => $module, 'name' => $template);
        $template = frameSub::_()->getTable('email_templates')->get('*',$conditions);
        $this->template = $template[0];
        return $this;
    }
    /**
     * Render content
     * 
     * @param array $variables 
     */
    public function renderContent($variables) {
       //$replacements = utilsSub::jsonDecode($this->template['variables']);
       $this->message = utilsSub::makeVariablesReplacement($this->template['body'], $variables);
       $this->subject = utilsSub::makeVariablesReplacement($this->template['subject'], $variables);
       /*foreach($replacements as $k => $v) {
            $message = str_replace(":$v", $variables[$v], $message);
            $subject = str_replace(":$v", $variables[$v], $subject);
       } // foreach
       $this->subject = $subject;
       $this->message = $message;*/
    }
    
    public function getSubject() {
        return $this->subject;
    }
    
    public function getMessage() {
        return $this->message;
    }
}
?>
