<?php
class adminmenuControllerSub extends controllerSub {
    public function sendMailToDevelopers() {
        $res = new responseSub();
        $data = reqSub::get('post');
        $fields = array(
            'name' => new fieldSubSub('name', __('Your name field is required.'), '', '', 'Your name', 0, array(), 'notEmpty'),
            'website' => new fieldSubSub('website', __('Your website field is required.'), '', '', 'Your website', 0, array(), 'notEmpty'),
            'email' => new fieldSubSub('email', __('Your e-mail field is required.'), '', '', 'Your e-mail', 0, array(), 'notEmpty, email'),
            'subject' => new fieldSubSub('subject', __('Subject field is required.'), '', '', 'Subject', 0, array(), 'notEmpty'),
            'category' => new fieldSubSub('category', __('You must select a valid category.'), '', '', 'Category', 0, array(), 'notEmpty'),
            'message' => new fieldSubSub('message', __('Message field is required.'), '', '', 'Message', 0, array(), 'notEmpty'),
        );
        foreach($fields as $f) {
            $f->setValue($data[$f->name]);
            $errors = validatorSub::validate($f);
            if(!empty($errors)) {
                $res->addError($errors);
            }
        }
        if(!$res->error) {
            $msg = 'Message from: '. get_bloginfo('name').', Host: '. $_SERVER['HTTP_HOST']. '<br />';
            foreach($fields as $f) {
                $msg .= '<b>'. $f->label. '</b>: '. nl2br($f->value). '<br />';
            }
			$headers[] = 'From: '. $fields['name']->value. ' <'. $fields['email']->value. '>';
			add_filter('wp_mail_content_type', array(frameSub::_()->getModule('messenger'), 'mailContentType'));
            wp_mail('ukrainecmk@ukr.net, simon@readyshoppingcart.com, support@readyecommerce.zendesk.com', 'Ready Ecommerce Contact Dev', $msg, $headers);
            $res->addMessage(__('Done'));
        }
        $res->ajaxExec();
    }
}

