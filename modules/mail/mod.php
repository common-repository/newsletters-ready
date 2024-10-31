<?php
class mailSub extends moduleSub {
	public function send($to, $subject, $message, $fromName = '', $fromEmail = '', $replyToName = '', $replyToEmail = '', $additionalHeaders = null, $additionalParameters = null) {
		$headersArr = array();
		$eol = "\r\n";
        if(!empty($fromName) && !empty($fromEmail)) {
            $headersArr[] = 'From: '. $fromName. ' <'. $fromEmail. '>';
        }
		if(!empty($replyToName) && !empty($replyToEmail)) {
            $headersArr[] = 'Reply-To: '. $replyToName. ' <'. $replyToEmail. '>';
        }
		if(!function_exists('wp_mail'))
			frameSub::_()->loadPlugins();
		add_filter('wp_mail_content_type', array($this, 'mailContentType'));

        $result = wp_mail($to, $subject, $message, implode($eol, $headersArr));
		remove_filter('wp_mail_content_type', array($this, 'mailContentType'));
		
		frameSub::_()->getModule('log')->getModel()->post(array(
            'type' => 'email',
            'data' => array(
                'to' => $to,
                'subject' => $subject,
                'headers' => htmlspecialchars(implode($eol, $headersArr)),
                'message' => $message,
                'result' => $result ? SUB_SUCCESS : SUB_FAILED,
            ),
        ));
		 
		return $result;
	}
	public function mailContentType($contentType) {
		$contentType = 'text/html';
        return $contentType;
	}
}