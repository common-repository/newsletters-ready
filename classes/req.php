<?php
class reqSub {
    static protected $_requestData;
    static protected $_requestMethod;
    public function __construct() {

    }
    static public function init() {
        session_start();
		//session_write_close();
    }
/**
 * @param string $name key in variables array
 * @param string $from from where get result = "all", "input", "get"
 * @param mixed $default default value - will be returned if $name wasn't found
 * @return mixed value of a variable, if didn't found - $default (NULL by default)
 */
    static public function getVar($name, $from = 'all', $default = NULL) {
        $from = strtolower($from);
        if($from == 'all') {
			if(isset($_GET[$name])) {
                $from = 'get';
            } elseif(isset($_POST[$name])) {
                $from = 'post';
            }
        }
        
        switch($from) {
            case 'get':
                if(isset($_GET[$name]))
                    return $_GET[$name];
            break;
            case 'post':
                if(isset($_POST[$name]))
                    return $_POST[$name];
            break;
            case 'file':
            case 'files':
                if(isset($_FILES[$name]))
                    return $_FILES[$name];
                break;
            case 'session':
				//session_start();
                if(isset($_SESSION[$name])) {
					//session_write_close();
                    return $_SESSION[$name];
				}
				//session_write_close();
				break;
            case 'server':
                if(isset($_SERVER[$name]))
                    return $_SERVER[$name];
				break;
        }
        return $default;
    }
	static public function isEmpty($name, $from = 'all') {
		$val = self::getVar($name, $from);
		return empty($val);
	}
    static public function setVar($name, $val, $in = 'input') {
        $in = strtolower($in);
        switch($in) {
            case 'get':
                $_GET[$name] = $val;
            break;
            case 'post':
                $_POST[$name] = $val;
            break;
            case 'session':
				//session_start();
                $_SESSION[$name] = $val;
				//session_write_close();
            break;
        }
    }
    static public function clearVar($name, $in = 'input') {
        $in = strtolower($in);
        switch($in) {
            case 'get':
                if(isset($_GET[$name]))
                    unset($_GET[$name]);
            break;
            case 'post':
                if(isset($_POST[$name]))
                    unset($_POST[$name]);
            break;
            case 'session':
                if(isset($_SESSION[$name]))
                    unset($_SESSION[$name]);
            break;
        }
    }
    static public function get($what) {
        $what = strtolower($what);
        switch($what) {
            case 'get':
                return $_GET;
                break;
            case 'post':
                return $_POST;
                break;
            case 'session':
                return $_SESSION;
                break;
            case 'files':
				return $_FILES;
				break;
        }
        return NULL;
    }
    static public function getMethod() {
        if(!self::$_requestMethod) {
            self::$_requestMethod = strtoupper( self::getVar('method', 'all', $_SERVER['REQUEST_METHOD']) );
        }
        return self::$_requestMethod;
    }
    static public function getAdminPage() {
        $pagePath = self::getVar('page');
        if(!empty($pagePath) && strpos($pagePath, '/') !== false) {
            $pagePath = explode('/', $pagePath);
            return str_replace('.php', '', $pagePath[count($pagePath) - 1]);
        }
        return false;
    }
    static public function getRequestUri() {
        return $_SERVER['REQUEST_URI'];
    }
    static public function getMode() {
		$mod = '';
        if(!($mod = self::getVar('page')))  //Admin usage
            $mod = self::getVar('mod');     //Frontend usage
        return $mod;
    }
}
