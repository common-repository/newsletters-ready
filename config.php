<?php
    global $wpdb;
    if (WPLANG == '') {
        define('SUB_WPLANG', 'en_GB');
    } else {
        define('SUB_WPLANG', WPLANG);
    }
    if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

    define('SUB_PLUG_NAME', basename(dirname(__FILE__)));
    define('SUB_DIR', WP_PLUGIN_DIR. DS. SUB_PLUG_NAME. DS);
    define('SUB_TPL_DIR', SUB_DIR. 'tpl'. DS);
    define('SUB_CLASSES_DIR', SUB_DIR. 'classes'. DS);
    define('SUB_TABLES_DIR', SUB_CLASSES_DIR. 'tables'. DS);
	define('SUB_HELPERS_DIR', SUB_CLASSES_DIR. 'helpers'. DS);
    define('SUB_LANG_DIR', SUB_DIR. 'lang'. DS);
    define('SUB_IMG_DIR', SUB_DIR. 'img'. DS);
    define('SUB_TEMPLATES_DIR', SUB_DIR. 'templates'. DS);
    define('SUB_MODULES_DIR', SUB_DIR. 'modules'. DS);
    define('SUB_FILES_DIR', SUB_DIR. 'files'. DS);
    define('SUB_ADMIN_DIR', ABSPATH. 'wp-admin'. DS);

    define('SUB_SITE_URL', get_bloginfo('wpurl'). '/');
    define('SUB_JS_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/js/');
    define('SUB_CSS_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/css/');
    define('SUB_IMG_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/img/');
    define('SUB_MODULES_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/modules/');
    define('SUB_TEMPLATES_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/templates/');
    define('SUB_IMG_POSTS_PATH', SUB_IMG_PATH. 'posts/');
    define('SUB_JS_DIR', SUB_DIR. 'js/');

    define('SUB_URL', SUB_SITE_URL);

    define('SUB_LOADER_IMG', SUB_IMG_PATH. 'loading-cube.gif');
	define('SUB_TIME_FORMAT', 'H:i:s');
    define('SUB_DATE_DL', '/');
    define('SUB_DATE_FORMAT', 'm/d/Y');
    define('SUB_DATE_FORMAT_HIS', 'm/d/Y ('. SUB_TIME_FORMAT. ')');
    define('SUB_DATE_FORMAT_JS', 'mm/dd/yy');
    define('SUB_DATE_FORMAT_CONVERT', '%m/%d/%Y');
    define('SUB_WPDB_PREF', $wpdb->prefix);
    define('SUB_DB_PREF', 'sub_');    /*TheOneEcommerce*/
    define('SUB_MAIN_FILE', 'sub.php');

    define('SUB_DEFAULT', 'default');
    define('SUB_CURRENT', 'current');
    
    
    define('SUB_PLUGIN_INSTALLED', true);
    define('SUB_VERSION', '0.3.8');
    define('SUB_USER', 'user');
    
    
    define('SUB_CLASS_PREFIX', 'subc');        
    define('SUB_FREE_VERSION', false);
    
    define('SUB_API_UPDATE_URL', 'http://somereadyapiupdatedomain.com');
    
    define('SUB_SUCCESS', 'Success');
    define('SUB_FAILED', 'Failed');
	define('SUB_ERRORS', 'subErrors');
	
	define('SUB_THEME_MODULES', 'theme_modules');
	
	
	define('SUB_ADMIN',	'admin');
	define('SUB_LOGGED','logged');
	define('SUB_GUEST',	'guest');
	
	define('SUB_ALL',		'all');
	
	define('SUB_METHODS',		'methods');
	define('SUB_USERLEVELS',	'userlevels');
	/**
	 * Framework instance code, unused for now
	 */
	define('SUB_CODE', 'sub');
	/**
	 * Plugin name
	 */
	define('SUB_WP_PLUGIN_NAME', 'Newsletters Ready!');
	/**
	 * Build-in Subscribers lists IDs
	 */
	define('SUB_WP_LIST_ID', 1);
	/**
	 * Newsletters send types
	 */
	define('SUB_TYPE_NOW', 'now');
	define('SUB_TYPE_NEW_CONTENT', 'new_content');
	define('SUB_TYPE_SCHEDULE', 'schedule');
	define('SUB_ANY', 'any');
	/**
	 * Newsletters send time types
	 */
	define('SUB_TIME_IMMEDIATELY', 'immediately');
	define('SUB_TIME_APPOINTED', 'appointed');
	/**
	 * Newsletters schedules new types
	 */
	define('SUB_S_MIN', 'one_min');
	/**
	 * Newsletters cron main filter name
	 */
	define('SUB_SCHEDULE_FILTER', SUB_CODE. '_schedule_send');
	/**
	 * Test mode - create logs in files, etc.
	 */
	define('SUB_TEST_MODE', false);
