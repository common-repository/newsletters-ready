<?php
class installerSub {
	static public $update_to_version_method = '';
	static public function init() {
		global $wpdb;
		$wpPrefix = $wpdb->prefix;
		//$start = microtime(true);					// Speed debug info
		//$queriesCountStart = $wpdb->num_queries;	// Speed debug info
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$current_version = get_option($wpPrefix. SUB_DB_PREF. 'db_version', 0);
		$installed = (int) get_option($wpPrefix. SUB_DB_PREF. 'db_installed', 0);
		/**
		 * htmltype 
		 */
		if (!dbSub::exist($wpPrefix.SUB_DB_PREF."htmltype")) {
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.SUB_DB_PREF."htmltype` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `label` varchar(32) NOT NULL,
			  `description` varchar(255) NOT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE INDEX `label` (`label`)
			) DEFAULT CHARSET=utf8");
		}
		dbSub::query("INSERT INTO `".$wpPrefix.SUB_DB_PREF."htmltype` VALUES
			(1, 'text', 'Text'),
			(2, 'password', 'Password'),
			(3, 'hidden', 'Hidden'),
			(4, 'checkbox', 'Checkbox'),
			(5, 'checkboxlist', 'Checkboxes'),
			(6, 'datepicker', 'Date Picker'),
			(7, 'submit', 'Button'),
			(8, 'img', 'Image'),
			(9, 'selectbox', 'Drop Down'),
			(10, 'radiobuttons', 'Radio Buttons'),
			(11, 'countryList', 'Countries List'),
			(12, 'selectlist', 'List'),
			(13, 'countryListMultiple', 'Country List with posibility to select multiple countries'),
			(14, 'block', 'Will show only value as text'),
			(15, 'statesList', 'States List'),
			(16, 'textFieldsDynamicTable', 'Dynamic table - multiple text options set'),
			(17, 'textarea', 'Textarea'),
			(18, 'checkboxHiddenVal', 'Checkbox with Hidden field')");
		/**
		 * modules 
		 */
		if (!dbSub::exist($wpPrefix.SUB_DB_PREF."modules")) {
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.SUB_DB_PREF."modules` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `code` varchar(64) NOT NULL,
			  `active` tinyint(1) NOT NULL DEFAULT '0',
			  `type_id` smallint(3) NOT NULL DEFAULT '0',
			  `params` text,
			  `has_tab` tinyint(1) NOT NULL DEFAULT '0',
			  `label` varchar(128) DEFAULT NULL,
			  `description` text,
			  `ex_plug_dir` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE INDEX `code` (`code`)
			) DEFAULT CHARSET=utf8;");
		}
		dbSub::query("INSERT INTO `".$wpPrefix.SUB_DB_PREF."modules` (id, code, active, type_id, params, has_tab, label, description) VALUES
		  (NULL, 'adminmenu',1,1,'',0,'Admin Menu',''),
		  (NULL, 'options',1,1,'',1,'Options',''),
		  (NULL, 'user',1,1,'',1,'Users',''),
		  (NULL, 'pages',1,1,'". json_encode(array()). "',0,'Pages',''),
		  (NULL, 'templates',1,1,'',1,'Templates for Plugin',''),
		  (NULL, 'messenger', 1, 1, '', 1, 'Notifications', 'Module provides the ability to create templates for user notifications and for mass mailing.'),
		  (NULL, 'shortcodes', 1, 3, '', 0, 'Shortcodes', 'Shortcodes data'),
		  (NULL, 'log', 1, 1, '', 0, 'Log', 'Internal system module to log some actions on server'),
		  (NULL, 'subscribe', 1, 1, '', 0, 'Subscribe', 'Subscribe'),
		  (NULL, 'newsletters', 1, 1, '', 0, 'Newsletters', 'Newsletters'),
		  (NULL, 'stpl', 1, 1, '', 0, 'Super Template', 'Super Template'),
		  (NULL, 'mail', 1, 1, '', 0, 'mail', 'mail'),
		  (NULL, 'promo_ready', 1, 1, '', 0, 'promo_ready', 'promo_ready');");

		/**
		 *  modules_type 
		 */
		if(!dbSub::exist($wpPrefix.SUB_DB_PREF."modules_type")) {
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.SUB_DB_PREF."modules_type` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `label` varchar(64) NOT NULL,
			  PRIMARY KEY (`id`)
			) AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;");
		}
		dbSub::query("INSERT INTO `".$wpPrefix.SUB_DB_PREF."modules_type` VALUES
		  (1,'system'),
		  (2,'widget'),
		  (3,'addons');");
		/**
		 * options 
		 */
		if(!dbSub::exist($wpPrefix.SUB_DB_PREF."options")) {
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.SUB_DB_PREF."options` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `code` varchar(64) CHARACTER SET latin1 NOT NULL,
			  `value` text NULL,
			  `label` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
			  `description` text CHARACTER SET latin1,
			  `htmltype_id` smallint(2) NOT NULL DEFAULT '1',
			  `params` text NULL,
			  `cat_id` mediumint(3) DEFAULT '0',
			  `sort_order` mediumint(3) DEFAULT '0',
			  `value_type` varchar(16) CHARACTER SET latin1 DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `id` (`id`),
			  UNIQUE INDEX `code` (`code`)
			) DEFAULT CHARSET=utf8");
		}
		$eol = "\n";
		dbSub::query("INSERT INTO `".$wpPrefix.SUB_DB_PREF."options` (`id`,`code`,`value`,`label`,`description`,`htmltype_id`,`params`,`cat_id`,`sort_order`) VALUES
			(NULL,'default_from_name','". get_bloginfo('name'). "','Default From Name','Default From name in emails from your site',1,'',1,0),
			(NULL,'default_from_email','". get_bloginfo('admin_email'). "','Default From Email','Default From email in emails from your site',1,'',1,0),
			(NULL,'default_reply_name','". get_bloginfo('name'). "','Default Reply To Name','Default Reply to name in emails from your site',1,'',1,0),
			(NULL,'default_reply_email','". get_bloginfo('admin_email'). "','Default Reply To Email','Default Reply to email in emails from your site',1,'',1,0),
			
			(NULL,'sub_admin_email','". get_bloginfo('admin_email'). "','Email notification about new subscriber','You you don\'t want to get such notifications - just clear this field',1,'',3,0),
			(NULL,'sub_enter_email_msg','". __('Please enter your email'). "','\"Enter Email\" message','Default \"Enter Email\" message for your subscribe form',1,'',3,0),
			(NULL,'sub_success_msg','". __('Thank you for subscription!'). "','Subscribe success message','Message that user will see after subscribe',1,'',3,0),
			(NULL,'sub_activation_required','1','Subscribe activation required','If this checked - after subscription user will get notification message with subscribe activation link',18,'',3,0),
			(NULL,'sub_form_title','Subscribe','Default subscribe form title','Default subscribe form title',1,'',3,0);");

		/* options categories */
		if(!dbSub::exist($wpPrefix.SUB_DB_PREF."options_categories")) {
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.SUB_DB_PREF."options_categories` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `label` varchar(128) NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `id` (`id`)
			) DEFAULT CHARSET=utf8");
		}
		dbSub::query("INSERT INTO `".$wpPrefix.SUB_DB_PREF."options_categories` VALUES
			(1, 'General'),
			(2, 'Template'),
			(3, 'Subscribe'),
			(4, 'Social');");
		/**
		 * Email Templates
		 */
		if(!dbSub::exist($wpPrefix.SUB_DB_PREF."email_templates")) {
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.SUB_DB_PREF."email_templates` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `label` varchar(128) NOT NULL,
				  `subject` varchar(255) NOT NULL,
				  `body` text NOT NULL,
				  `variables` text NOT NULL,
				  `active` tinyint(1) NOT NULL,
				  `name` varchar(128) NOT NULL,
				  `module` varchar(128) NOT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE INDEX `name` (`name`)
				) DEFAULT CHARSET=utf8");
		}
		$eol = "\n\r";
		$emailTemplates = array(
			'sub_confirm' => array(
				'body' => 'Hello!'. $eol. 'Thank you for subscribing for :site_name!'. $eol. 'To complete your subscription please follow the link bellow:'. $eol. '<a href=":link">:link</a>'. $eol. 'Regards,'. $eol. ':site_name team.',
				'variables' => array('site_name', 'link'),
			),
			'sub_admin_notify' => array(
				'body' => 'Hello!'. $eol. 'New user activated subscription on your site :site_name for email :email.',
				'variables' => array('site_name', 'email'),
			),
			'sub_new_post' => array(
				'body' => 'Hello!'. $eol. 'New entry was published on :site_name.'. $eol. 'Visit it by following next link:'. $eol. '<a href=":post_link">:post_title</a>'. $eol. 'Regards,'. $eol. ':site_name team.',
				'variables' => array('site_name', 'post_link', 'post_title'),
			),
		);
		dbSub::query("INSERT INTO `".$wpPrefix.SUB_DB_PREF."email_templates` (`id`, `label`, `subject`, `body`, `variables`, `active`, `name`, `module`) VALUES 
			(NULL, 'Subscribe Confirm', 'Subscribe Confirmation', '". $emailTemplates['sub_confirm']['body']. "', '[\"". implode('","', $emailTemplates['sub_confirm']['variables'])."\"]', 1, 'sub_confirm', 'subscribe'),
			(NULL, 'Subscribe Admin Notify', 'New subscriber', '". $emailTemplates['sub_admin_notify']['body']. "', '[\"". implode('","', $emailTemplates['sub_admin_notify']['variables'])."\"]', 1, 'sub_admin_notify', 'subscribe'),
			(NULL, 'Subscribe New Entry', ':site_name - New Entry!', '". $emailTemplates['sub_new_post']['body']. "', '[\"". implode('","', $emailTemplates['sub_new_post']['variables'])."\"]', 1, 'sub_new_post', 'subscribe');");
		/**
		 * Subscribers
		 */
		// Will do this always for now
		$insertWpSubscribers = true;
		if(!dbSub::exist($wpPrefix.SUB_DB_PREF."subscribers")) {
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.SUB_DB_PREF."subscribers` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `user_id` int(11) NOT NULL DEFAULT '0',
				  `email` varchar(255) NOT NULL,
				  `name` varchar(255) DEFAULT NULL,
				  `created` datetime NOT NULL,
				  `unsubscribe_date` datetime DEFAULT NULL,
				  `active` tinyint(4) NOT NULL DEFAULT '1',
				  `token` varchar(255) DEFAULT NULL,
				  `ip` varchar(64) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8");
			$insertWpSubscribers = true;
		}
		/**
		 * Subscribers Lists
		 */
		if(!dbSub::exist($wpPrefix.SUB_DB_PREF."subscribers_lists")) {
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.SUB_DB_PREF."subscribers_lists` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `label` varchar(255) NOT NULL,
				  `description` text,
				  `protected` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8");
			dbSub::query("INSERT INTO `".$wpPrefix.SUB_DB_PREF."subscribers_lists` (`id`, `label`, `description`, `protected`) VALUES 
			(". SUB_WP_LIST_ID. ", '". __('Wordpress Users'). "', '". __('Wordpress Users list'). "', 1),
			(2, '". __('First Subscribers List'). "', '". sprintf(__('Default list, created automaticaly on first install of %1$s'), SUB_WP_PLUGIN_NAME). "', 0);");
			
			// Reserve 100 first IDs for future use
			dbSub::query("ALTER TABLE `".$wpPrefix.SUB_DB_PREF."subscribers_lists` AUTO_INCREMENT = 100;");
		}
		/**
		 * Subscribers Lists to Subscribers Connection
		 */
		if(!dbSub::exist($wpPrefix.SUB_DB_PREF."subscribers_to_lists")) {
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.SUB_DB_PREF."subscribers_to_lists` (
				  `subscriber_id` int(11) NOT NULL,
				  `subscriber_list_id` int(11) NOT NULL,
				  KEY `subscriber_id` (`subscriber_id`),
				  KEY `subscriber_list_id` (`subscriber_list_id`),
				  UNIQUE KEY `subscriber_to_list` (`subscriber_id`, `subscriber_list_id`)
				) DEFAULT CHARSET=utf8");
			$insertWpSubscribers = true;
		}
		/**
		 * Copy wp subscribers to oursubscribers list
		 */
		if($insertWpSubscribers) {
			$wpSubscribers = get_users(array('role' => 'subscriber'));
			if(!empty($wpSubscribers)) {
				$dbDateCreated = dbSub::timeToDate();
				foreach($wpSubscribers as $sub) {
					if(dbSub::query('INSERT INTO @__subscribers (user_id, email, name, created, active, token) 
						VALUES ('. $sub->data->ID. ', "'. $sub->data->user_email. '", "'. $sub->data->display_name. '", "'. $dbDateCreated. '", 1, "'. md5($sub->data->user_email. AUTH_KEY). '");')
					&& ($newSubId = dbSub::lastID())
					) {
						dbSub::query('INSERT INTO @__subscribers_to_lists (subscriber_id, subscriber_list_id) 
							VALUES ('. $newSubId. ', '. SUB_WP_LIST_ID. ')');
					}
				}
			}
		}
		/**
		 * Log table - all logs in project
		 */
		if(!dbSub::exist($wpPrefix.SUB_DB_PREF."log")) {
			dbDelta("CREATE TABLE `".$wpPrefix.SUB_DB_PREF."log` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `type` varchar(64) NOT NULL,
			  `data` text,
			  `date_created` int(11) NOT NULL DEFAULT '0',
			  `uid` int(11) NOT NULL DEFAULT 0,
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8");
		}
		/**
		* Files
		*/
	   if(!dbSub::exist($wpPrefix.SUB_DB_PREF."files")) {
		   dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.SUB_DB_PREF."files` (
			 `id` int(11) NOT NULL AUTO_INCREMENT,
			 `pid` int(11) NOT NULL,
			 `name` varchar(255) NOT NULL,
			 `path` varchar(255) NOT NULL,
			 `mime_type` varchar(255) DEFAULT NULL,
			 `size` int(11) NOT NULL DEFAULT '0',
			 `active` tinyint(1) NOT NULL,
			 `date` datetime DEFAULT NULL,
			 `download_limit` int(11) NOT NULL DEFAULT '0',
			 `period_limit` int(11) NOT NULL DEFAULT '0',
			 `description` text NOT NULL,
			 `type_id` SMALLINT(5) NOT NULL DEFAULT 1,
			 PRIMARY KEY (`id`)
		   ) DEFAULT CHARSET=utf8");
	   }
	   
	   /**
		* Newsletters
		*/
	   if(!dbSub::exist($wpPrefix.SUB_DB_PREF."newsletters")) {
		   dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.SUB_DB_PREF."newsletters` (
			 `id` int(11) NOT NULL AUTO_INCREMENT,
			 `subject` varchar(255) NOT NULL,			 
			 `active` tinyint(1) NOT NULL DEFAULT '1',
			 `status` tinyint(1) NOT NULL DEFAULT '0',
			 `stpl_id` int(11) NOT NULL DEFAULT '0',
			 `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			 `send_type` enum('now','new_content','schedule'),
			 `send_params` text,
			 `from_name` varchar(128) DEFAULT NULL,
			 `from_email` varchar(128) DEFAULT NULL,
			 `reply_name` varchar(128) DEFAULT NULL,
			 `reply_email` varchar(128) DEFAULT NULL,
			 `date_sent` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
			 PRIMARY KEY (`id`)
		   ) DEFAULT CHARSET=utf8");
	   }
	   /**
		* Newsletters Lists
		*/
	   if(!dbSub::exist($wpPrefix.SUB_DB_PREF."newsletters_to_lists")) {
		   dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.SUB_DB_PREF."newsletters_to_lists` (
			 `newsletter_id` int(11) NOT NULL,
			 `subscriber_list_id` int(11) NOT NULL,
			  KEY `newsletter_id` (`newsletter_id`),
			  KEY `subscriber_list_id` (`subscriber_list_id`)
		   ) DEFAULT CHARSET=utf8");
	   }
	   /**
		* Newsletters to Tags
		*/
	   if(!dbSub::exist($wpPrefix.SUB_DB_PREF."newsletters_to_tags")) {
		   dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.SUB_DB_PREF."newsletters_to_tags` (
			 `newsletter_id` int(11) NOT NULL,
			 `tag` varchar(128) NOT NULL,
			  KEY `newsletter_id` (`newsletter_id`)
		   ) DEFAULT CHARSET=utf8");
	   }
	   /**
		* Newsletters to Post Categories
		*/
	   if(!dbSub::exist($wpPrefix.SUB_DB_PREF."newsletters_to_posts_categories")) {
		   dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.SUB_DB_PREF."newsletters_to_posts_categories` (
			 `newsletter_id` int(11) NOT NULL,
			 `cat_id` int(11) NOT NULL,
			  KEY `newsletter_id` (`newsletter_id`),
			  KEY `cat_id` (`cat_id`)
		   ) DEFAULT CHARSET=utf8");
	   }
	   
	   /**
		* Super Templates
		*/
	   // We will do this only one first time
	   $initStplDatabase = false;
	   if(!dbSub::exist($wpPrefix.SUB_DB_PREF."stpl")) {
		   dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.SUB_DB_PREF."stpl` (
			 `id` int(11) NOT NULL AUTO_INCREMENT,
			 `content` text,			 
			 `protected` tinyint(1) NOT NULL DEFAULT '0',
			 `category_id` tinyint(2) NOT NULL DEFAULT '0',
			 `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			 `style_params` text,
			 `preview_img` varchar(64),
			 `label` VARCHAR(64) NULL DEFAULT NULL,
			 `parent_id` INT(11) NULL DEFAULT '0',
			 PRIMARY KEY (`id`)
		   ) DEFAULT CHARSET=utf8");
		   $initStplDatabase = true;
	   }
	   if(!dbSub::exist($wpPrefix.SUB_DB_PREF."stpl_rows")) {
		   dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.SUB_DB_PREF."stpl_rows` (
			 `id` int(11) NOT NULL AUTO_INCREMENT,
			 `stpl_id` int(11) NOT NULL,
			 `height` int(11) NOT NULL DEFAULT '0',
			 `background_color` varchar(24),
			 PRIMARY KEY (`id`)
		   ) DEFAULT CHARSET=utf8");
	   }
	   if(!dbSub::exist($wpPrefix.SUB_DB_PREF."stpl_cols")) {
		   dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.SUB_DB_PREF."stpl_cols` (
			 `id` int(11) NOT NULL AUTO_INCREMENT,
			 `stpl_row_id` int(11) NOT NULL,
			 `width` int(11) NOT NULL DEFAULT '0',
			 `content` text,
			 `element_class` varchar(64),
			 PRIMARY KEY (`id`)
		   ) DEFAULT CHARSET=utf8");
	   }
	   if($initStplDatabase) {
		   self::installDefaultStpls();
		   // Reserve 100 first IDs for future use
			dbSub::query("ALTER TABLE `@__stpl` AUTO_INCREMENT = 100;");
	   }
	   /*****/
	   /**
	    * List of emails in sending quie
	    */
	   if(!dbSub::exist($wpPrefix.SUB_DB_PREF."email_sent")) {
		   dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.SUB_DB_PREF."email_sent` (
			 `id` int(11) NOT NULL AUTO_INCREMENT,
			 `subscriber_id` int(11) NOT NULL,
			 `newsletter_id` int(11) NOT NULL,
			 `date_sent` int(11) NOT NULL DEFAULT '0',
			 `date_opened` int(11) NOT NULL DEFAULT '0',
			 `status` int(5) NOT NULL DEFAULT 0,
			 `error_msg` varchar(255) DEFAULT NULL,
			 PRIMARY KEY (`id`)
		   ) DEFAULT CHARSET=utf8");
	   }
	   /**
	    * Scheduled Newsletters
	    */
	   if(!dbSub::exist($wpPrefix.SUB_DB_PREF."newsletters_schedule")) {
		   dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.SUB_DB_PREF."newsletters_schedule` (
			 `id` int(11) NOT NULL AUTO_INCREMENT,
			 `newsletter_id` int(11) NOT NULL,
			 `year` smallint(4) NOT NULL DEFAULT '0',
			 `month` tinyint(2) NOT NULL DEFAULT '0',
			 `day` tinyint(2) NOT NULL DEFAULT '0',
			 `hour` tinyint(2) NOT NULL DEFAULT '0',
			 `one_time` tinyint(1) NOT NULL DEFAULT '0',
			 PRIMARY KEY (`id`),
			 KEY `year` (`year`),
			 KEY `month` (`month`),
			 KEY `day` (`day`),
			 KEY `hour` (`hour`)
		   ) DEFAULT CHARSET=utf8");
	   }

		installerDbUpdaterSub::runUpdate();

		update_option($wpPrefix. SUB_DB_PREF. 'db_version', SUB_VERSION);
		add_option($wpPrefix. SUB_DB_PREF. 'db_installed', 1);
		dbSub::query("UPDATE `".$wpPrefix.SUB_DB_PREF."options` SET value = '". SUB_VERSION. "' WHERE code = 'version' LIMIT 1");

		//$time = microtime(true) - $start;	// Speed debug info
	}
	static public function delete() {
		global $wpdb;
		$wpPrefix = $wpdb->prefix; /* add to 0.0.3 Versiom */
		$deleteOptions = reqSub::getVar('deleteOptions');
		if(frameSub::_()->getModule('pages')) {
			if(is_null($deleteOptions)) {
				frameSub::_()->getModule('pages')->getView()->displayDeactivatePage();
				exit();
			}
		}
		if((bool) $deleteOptions) {
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.SUB_DB_PREF."modules`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.SUB_DB_PREF."modules_type`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.SUB_DB_PREF."options`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.SUB_DB_PREF."htmltype`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.SUB_DB_PREF."templates`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.SUB_DB_PREF."email_templates`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.SUB_DB_PREF."files`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.SUB_DB_PREF."log`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.SUB_DB_PREF."options_categories`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.SUB_DB_PREF."subscribers`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.SUB_DB_PREF."subscribers_lists`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.SUB_DB_PREF."subscribers_to_lists`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.SUB_DB_PREF."newsletters`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.SUB_DB_PREF."newsletters_to_lists`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.SUB_DB_PREF."stpl`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.SUB_DB_PREF."stpl_rows`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.SUB_DB_PREF."stpl_cols`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.SUB_DB_PREF."email_sent`");
			// Clear all scheduled events
			wp_clear_scheduled_hook(SUB_SCHEDULE_FILTER);
		}
		delete_option($wpPrefix. SUB_DB_PREF. 'db_version');
		delete_option($wpPrefix. SUB_DB_PREF. 'db_installed');
	}
	static public function update() {
		global $wpdb;
		$wpPrefix = $wpdb->prefix;
		$currentVersion = get_option($wpPrefix. SUB_DB_PREF. 'db_version', 0);
		if(!$currentVersion || version_compare(SUB_VERSION, $currentVersion, '>')) {
			self::init();
			update_option($wpPrefix. SUB_DB_PREF. 'db_version', SUB_VERSION);
		}
	}
	// Install default (pre-set) Super Tempates
	static public function installDefaultStpls() {
		/*dbSub::query("INSERT INTO `@__stpl` (`id`, `protected`, `category_id`, `date_created`, `style_params`) VALUES 
			('1', '1', '0', '2014-03-10 17:52:11', 'a:3:{s:16:\"background_color\";s:0:\"\";s:9:\"undefined\";s:5:\"Clear\";s:16:\"background_image\";s:0:\"\";}'),
			('2', '1', '0', '2014-03-10 17:55:25', 'a:3:{s:16:\"background_color\";s:0:\"\";s:9:\"undefined\";s:5:\"Clear\";s:16:\"background_image\";s:0:\"\";}'),
			('3', '1', '0', '2014-03-10 17:56:33', 'a:3:{s:16:\"background_color\";s:0:\"\";s:9:\"undefined\";s:5:\"Clear\";s:16:\"background_image\";s:0:\"\";}');");
		dbSub::query("INSERT INTO `@__stpl_rows` (`id`, `stpl_id`, `height`, `background_color`) VALUES 
			('1', '1', '58', ''),
			('2', '1', '112', ''),
			('3', '1', '523', ''),
			('4', '1', '112', ''),
			('5', '1', '49', ''),
			('6', '2', '58', ''),
			('7', '2', '112', ''),
			('8', '2', '523', ''),
			('9', '2', '112', ''),
			('10', '2', '49', ''),
			('11', '3', '58', ''),
			('12', '3', '112', ''),
			('13', '3', '523', ''),
			('14', '3', '112', ''),
			('15', '3', '49', '');");
		dbSub::query("INSERT INTO `@__stpl_cols` (`id`, `stpl_row_id`, `width`, `content`, `element_class`) VALUES 
			('1', '1', '564', '<p style=\"text-align: center;\"><em>Display problems? <a title=\"View this newsletter in your browser\" href=\"[site_url]?mod=stpl&amp;action=preview&amp;pl=sub&amp;stpl_id=SUB_STPL_ID&amp;[stpl_preview_code]\" target=\"_blank\">View this newsletter in your browser</a>.</em></p>', 'stplCanvasElementText'),
			('2', '2', '564', '', 'stplCanvasElementText'),
			('3', '3', '564', '', 'stplCanvasElementText'),
			('4', '4', '564', '', 'stplCanvasElementText'),
			('5', '5', '564', '<p style=\"text-align: center;\"><em>If you don\'t want to receive such newsletter anymore - you can just <a title=\"unsubscribe\" href=\"[site_url]?mod=subscribe&amp;action=unsubscribeLead&amp;pl=sub&amp;[unsubscribe_link_params]\" target=\"_blank\">unsubscribe</a>.</em></p>', 'stplCanvasElementText'),
			('6', '6', '564', '<p style=\"text-align: center;\"><em>Display problems? <a title=\"View this newsletter in your browser\" href=\"[site_url]?mod=stpl&amp;action=preview&amp;pl=sub&amp;stpl_id=SUB_STPL_ID&amp;[stpl_preview_code]\" target=\"_blank\">View this newsletter in your browser</a>.</em></p>', 'stplCanvasElementText'),
			('7', '7', '564', '', 'stplCanvasElementText'),
			('8', '8', '279', '', 'stplCanvasElementText'),
			('9', '8', '279', '', 'stplCanvasElementText'),
			('10', '9', '564', '', 'stplCanvasElementText'),
			('11', '10', '564', '<p style=\"text-align: center;\"><em>If you don\'t want to receive such newsletter anymore - you can just <a title=\"unsubscribe\" href=\"[site_url]?mod=subscribe&amp;action=unsubscribeLead&amp;pl=sub&amp;[unsubscribe_link_params]\" target=\"_blank\">unsubscribe</a>.</em></p>', 'stplCanvasElementText'),
			('12', '11', '564', '<p style=\"text-align: center;\"><em>Display problems? <a title=\"View this newsletter in your browser\" href=\"[site_url]?mod=stpl&amp;action=preview&amp;pl=sub&amp;stpl_id=SUB_STPL_ID&amp;[stpl_preview_code]\" target=\"_blank\">View this newsletter in your browser</a>.</em></p>', 'stplCanvasElementText'),
			('13', '12', '564', '', 'stplCanvasElementText'),
			('14', '13', '184', '', 'stplCanvasElementText'),
			('15', '13', '184', '', 'stplCanvasElementText'),
			('16', '13', '184', '', 'stplCanvasElementText'),
			('17', '14', '564', '', 'stplCanvasElementText'),
			('18', '15', '564', '<p style=\"text-align: center;\"><em>If you don\'t want to receive such newsletter anymore - you can just <a title=\"unsubscribe\" href=\"[site_url]?mod=subscribe&amp;action=unsubscribeLead&amp;pl=sub&amp;[unsubscribe_link_params]\" target=\"_blank\">unsubscribe</a>.</em></p>', 'stplCanvasElementText');");*/
		
				/*dbSub::query("INSERT INTO `@__stpl` (`id`, `content`, `protected`, `category_id`, `date_created`, `style_params`, `preview_img`, `label`, `parent_id`) VALUES 
			('1', '', '1', '0', '2014-03-30 17:47:54', 'a:5:{s:15:\"background_type\";s:4:\"none\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:7:\"stretch\";s:16:\"background_image\";s:0:\"\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', 'FreshNewsletter-small.png', 'Fresh Newsletter', '0'),
			('2', '', '1', '0', '2014-04-02 02:41:35', 'a:5:{s:15:\"background_type\";s:4:\"none\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:7:\"stretch\";s:16:\"background_image\";s:0:\"\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', 'BlauMail-small.png', 'Blau Mail', '0'),
			('3', '', '1', '0', '2014-04-10 18:20:26', 'a:5:{s:15:\"background_type\";s:4:\"none\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:7:\"stretch\";s:16:\"background_image\";s:0:\"\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', 'AirMail-small.png', 'AirMail', '0'),
			('4', '', '1', '0', '2014-04-15 17:29:11', 'a:5:{s:15:\"background_type\";s:4:\"none\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:7:\"stretch\";s:16:\"background_image\";s:0:\"\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', 'Orion-small.png', 'Orion', '0'),
			('5', '', '1', '0', '2014-04-16 11:08:28', 'a:5:{s:15:\"background_type\";s:4:\"none\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:7:\"stretch\";s:16:\"background_image\";s:0:\"\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:15:\"Times New Roman\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', 'EmbedMail-small.png', 'EmbedMail', '0'),
			('6', '', '1', '0', '2014-04-16 22:23:43', 'a:5:{s:15:\"background_type\";s:4:\"none\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:7:\"stretch\";s:16:\"background_image\";s:0:\"\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', 'Rocketway-small.png', 'Rocket way', '0'),
			('7', '', '1', '0', '2014-04-22 19:37:41', 'a:5:{s:15:\"background_type\";s:4:\"none\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:7:\"stretch\";s:16:\"background_image\";s:0:\"\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', 'FirstEmail-small.png', 'First Email', '0'),
			('8', '', '1', '0', '2014-04-26 12:05:04', 'a:5:{s:15:\"background_type\";s:4:\"none\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:7:\"stretch\";s:16:\"background_image\";s:0:\"\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', 'Cuzto-small.png', 'Cuzto', '0'),
			('9', '', '1', '0', '2014-03-10 17:52:11', 'a:3:{s:16:\"background_color\";s:0:\"\";s:9:\"undefined\";s:5:\"Clear\";s:16:\"background_image\";s:0:\"\";}', 'Simple1-small.png', 'Simple 1 Column', '0'),
			('10', '', '1', '0', '2014-03-10 17:55:25', 'a:3:{s:16:\"background_color\";s:0:\"\";s:9:\"undefined\";s:5:\"Clear\";s:16:\"background_image\";s:0:\"\";}', 'Simple2-small.png', 'Simple 2 Column', '0'),
			('11', '', '1', '0', '2014-03-10 17:56:33', 'a:3:{s:16:\"background_color\";s:0:\"\";s:9:\"undefined\";s:5:\"Clear\";s:16:\"background_image\";s:0:\"\";}', 'Simple3-small.png', 'Simple 3 Column', '0');");*/
		dbSub::query("INSERT INTO `@__stpl` (`id`, `content`, `protected`, `category_id`, `date_created`, `style_params`, `preview_img`, `label`, `parent_id`) VALUES 
			('1', '', '1', '0', '2014-03-30 17:47:54', 'a:5:{s:15:\"background_type\";s:4:\"none\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:7:\"stretch\";s:16:\"background_image\";s:0:\"\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', 'FreshNewsletter-small.png', 'Fresh Newsletter', '0'),
			('2', '', '1', '0', '2014-04-02 02:41:35', 'a:5:{s:15:\"background_type\";s:4:\"none\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:7:\"stretch\";s:16:\"background_image\";s:0:\"\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', 'BlauMail-small.png', 'Blau Mail', '0'),
			('3', '', '1', '0', '2014-04-10 18:20:26', 'a:5:{s:15:\"background_type\";s:4:\"none\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:7:\"stretch\";s:16:\"background_image\";s:0:\"\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', 'AirMail-small.png', 'AirMail', '0'),
			('4', '', '1', '0', '2014-04-15 17:29:11', 'a:5:{s:15:\"background_type\";s:4:\"none\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:7:\"stretch\";s:16:\"background_image\";s:0:\"\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', 'Orion-small.png', 'Orion', '0'),
			('5', '', '1', '0', '2014-04-16 11:08:28', 'a:5:{s:15:\"background_type\";s:4:\"none\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:7:\"stretch\";s:16:\"background_image\";s:0:\"\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:15:\"Times New Roman\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', 'EmbedMail-small.png', 'EmbedMail', '0'),
			('6', '', '1', '0', '2014-04-16 22:23:43', 'a:5:{s:15:\"background_type\";s:4:\"none\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:7:\"stretch\";s:16:\"background_image\";s:0:\"\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', 'Rocketway-small.png', 'Rocket way', '0'),
			('7', '', '1', '0', '2014-04-22 19:37:41', 'a:5:{s:15:\"background_type\";s:4:\"none\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:7:\"stretch\";s:16:\"background_image\";s:0:\"\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', 'FirstEmail-small.png', 'First Email', '0'),
			('8', '', '1', '0', '2014-04-26 12:05:04', 'a:5:{s:15:\"background_type\";s:4:\"none\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:7:\"stretch\";s:16:\"background_image\";s:0:\"\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', 'Cuzto-small.png', 'Cuzto', '0'),
			('9', '', '1', '0', '2014-03-10 17:52:11', 'a:3:{s:16:\"background_color\";s:0:\"\";s:9:\"undefined\";s:5:\"Clear\";s:16:\"background_image\";s:0:\"\";}', 'Simple1-small.png', 'Simple 1 Column', '0'),
			('10', '', '1', '0', '2014-03-10 17:55:25', 'a:3:{s:16:\"background_color\";s:0:\"\";s:9:\"undefined\";s:5:\"Clear\";s:16:\"background_image\";s:0:\"\";}', 'Simple2-small.png', 'Simple 2 Column', '0'),
			('11', '', '1', '0', '2014-03-10 17:56:33', 'a:3:{s:16:\"background_color\";s:0:\"\";s:9:\"undefined\";s:5:\"Clear\";s:16:\"background_image\";s:0:\"\";}', 'Simple3-small.png', 'Simple 3 Column', '0');");
		dbSub::query("INSERT INTO `@__stpl_rows` (`id`, `stpl_id`, `height`, `background_color`) VALUES 
			('1', '9', '58', ''),
			('2', '9', '112', ''),
			('3', '9', '523', ''),
			('4', '9', '112', ''),
			('5', '9', '49', ''),
			('6', '10', '58', ''),
			('7', '10', '112', ''),
			('8', '10', '523', ''),
			('9', '10', '112', ''),
			('10', '10', '49', ''),
			('11', '11', '58', ''),
			('12', '11', '112', ''),
			('13', '11', '523', ''),
			('14', '11', '112', ''),
			('15', '11', '49', ''),
			('16', '4', '53', 'rgb(255, 255, 255)'),
			('17', '4', '240', 'rgb(38, 38, 38)'),
			('18', '4', '68', 'rgb(255, 255, 255)'),
			('19', '4', '201', 'rgb(255, 255, 255)'),
			('20', '4', '271', 'rgb(252, 252, 252)'),
			('21', '4', '280', 'rgb(249, 249, 249)'),
			('22', '4', '115', 'rgb(37, 37, 37)'),
			('23', '4', '224', 'rgba(0, 0, 0, 0)'),
			('24', '4', '203', 'rgb(249, 249, 249)'),
			('25', '4', '150', 'rgb(249, 249, 249)'),
			('26', '4', '73', 'rgb(249, 249, 249)'),
			('27', '1', '59', 'rgb(51, 51, 51)'),
			('28', '1', '153', 'rgb(255, 255, 255)'),
			('29', '1', '244', 'rgb(244, 244, 244)'),
			('30', '1', '210', 'rgb(252, 252, 252)'),
			('31', '1', '45', 'rgba(0, 0, 0, 0)'),
			('32', '1', '207', 'rgb(252, 252, 252)'),
			('33', '1', '38', 'rgba(0, 0, 0, 0)'),
			('34', '1', '204', 'rgb(247, 247, 247)'),
			('35', '1', '48', 'rgba(0, 0, 0, 0)'),
			('36', '1', '250', 'rgb(252, 252, 252)'),
			('37', '1', '47', 'rgba(0, 0, 0, 0)'),
			('38', '1', '145', 'rgb(51, 51, 51)'),
			('39', '1', '124', 'rgb(51, 51, 51)'),
			('40', '1', '101', 'rgb(51, 51, 51)'),
			('41', '2', '53', 'rgb(229, 229, 229)'),
			('42', '2', '91', 'rgb(255, 255, 255)'),
			('43', '2', '113', 'rgb(244, 137, 36)'),
			('44', '2', '403', 'rgb(255, 255, 255)'),
			('45', '2', '219', 'rgb(247, 247, 247)'),
			('46', '2', '165', 'rgb(244, 244, 244)'),
			('47', '6', '64', 'rgb(117, 190, 192)'),
			('48', '6', '282', 'rgb(117, 190, 192)'),
			('49', '6', '60', 'rgb(255, 255, 255)'),
			('50', '6', '65', 'rgb(255, 255, 255)'),
			('51', '6', '78', 'rgb(255, 255, 255)'),
			('52', '6', '228', 'rgb(255, 255, 255)'),
			('53', '6', '284', 'rgb(255, 255, 255)'),
			('54', '6', '304', 'rgb(255, 255, 255)'),
			('55', '6', '44', 'rgb(117, 190, 192)'),
			('56', '6', '285', 'rgb(117, 190, 192)'),
			('57', '6', '276', 'rgb(255, 255, 255)'),
			('58', '6', '313', 'rgb(255, 255, 255)'),
			('59', '6', '78', 'rgb(74, 83, 98)'),
			('60', '6', '53', 'rgb(53, 59, 70)'),
			('61', '7', '101', 'rgb(255, 255, 255)'),
			('62', '7', '184', 'rgb(255, 255, 255)'),
			('63', '7', '61', 'rgb(255, 255, 255)'),
			('64', '7', '545', 'rgb(255, 255, 255)'),
			('65', '7', '308', 'rgb(255, 255, 255)'),
			('66', '7', '93', 'rgb(255, 255, 255)'),
			('67', '7', '60', 'rgba(0, 0, 0, 0)'),
			('68', '7', '122', 'rgb(255, 255, 255)'),
			('69', '5', '56', 'rgb(35, 157, 170)'),
			('70', '5', '57', 'rgb(255, 255, 255)'),
			('71', '5', '73', 'rgb(37, 157, 170)'),
			('72', '5', '58', 'rgb(255, 255, 255)'),
			('73', '5', '318', 'rgb(249, 249, 249)'),
			('74', '5', '159', 'rgb(252, 252, 252)'),
			('75', '5', '54', 'rgb(255, 255, 255)'),
			('76', '5', '63', 'rgb(252, 252, 252)'),
			('77', '5', '218', 'rgb(255, 255, 255)'),
			('78', '5', '162', 'rgb(247, 247, 247)'),
			('79', '5', '52', 'rgb(255, 255, 255)'),
			('80', '5', '70', 'rgb(255, 255, 255)'),
			('81', '5', '195', 'rgba(0, 0, 0, 0)'),
			('82', '5', '180', 'rgb(252, 252, 252)'),
			('83', '5', '45', 'rgb(255, 255, 255)'),
			('84', '5', '69', 'rgb(255, 255, 255)'),
			('85', '5', '211', 'rgb(255, 255, 255)'),
			('86', '5', '174', 'rgb(255, 255, 255)'),
			('87', '5', '230', 'rgb(255, 255, 255)'),
			('88', '5', '234', 'rgb(252, 252, 252)'),
			('89', '5', '266', 'rgb(252, 252, 252)'),
			('90', '5', '180', 'rgb(255, 255, 255)'),
			('91', '5', '117', 'rgb(255, 255, 255)'),
			('92', '5', '169', 'rgb(255, 255, 255)'),
			('93', '5', '202', 'rgb(255, 255, 255)'),
			('94', '5', '191', 'rgb(255, 255, 255)'),
			('95', '5', '121', 'rgb(252, 252, 252)'),
			('96', '5', '97', 'rgb(255, 255, 255)'),
			('97', '5', '178', 'rgb(255, 255, 255)'),
			('98', '5', '61', 'rgb(255, 255, 255)'),
			('99', '5', '61', 'rgb(255, 255, 255)'),
			('100', '5', '68', 'rgb(74, 181, 172)'),
			('101', '8', '53', 'rgb(28, 204, 169)'),
			('102', '8', '103', 'rgb(255, 255, 255)'),
			('103', '8', '319', 'rgba(0, 0, 0, 0)'),
			('104', '8', '187', 'rgb(255, 255, 255)'),
			('105', '8', '282', 'rgb(255, 255, 255)'),
			('106', '8', '299', 'rgb(255, 255, 255)'),
			('107', '8', '446', 'rgb(255, 255, 255)'),
			('108', '8', '69', 'rgb(255, 255, 255)'),
			('109', '8', '351', 'rgb(255, 255, 255)'),
			('110', '8', '69', 'rgb(255, 255, 255)'),
			('111', '8', '260', 'rgb(255, 255, 255)'),
			('112', '8', '69', 'rgb(255, 255, 255)'),
			('113', '8', '408', 'rgb(255, 255, 255)'),
			('114', '8', '69', 'rgb(255, 255, 255)'),
			('115', '8', '303', 'rgb(255, 255, 255)'),
			('116', '8', '97', 'rgb(28, 204, 169)'),
			('117', '8', '34', 'rgb(255, 255, 255)'),
			('118', '8', '114', 'rgb(28, 204, 169)'),
			('119', '8', '64', 'rgba(0, 0, 0, 0)'),
			('120', '3', '117', 'rgb(255, 255, 255)'),
			('121', '3', '191', 'rgb(249, 249, 249)'),
			('122', '3', '55', 'rgb(255, 255, 255)'),
			('123', '3', '885', 'rgb(244, 244, 244)'),
			('124', '3', '138', 'rgb(255, 255, 255)'),
			('125', '3', '146', 'rgb(219, 50, 50)');");
		dbSub::query("INSERT INTO `@__stpl_cols` (`id`, `stpl_row_id`, `width`, `content`, `element_class`) VALUES 
			('1', '1', '564', '<p style=\"text-align: center;\"><em>Display problems? <a title=\"View this newsletter in your browser\" href=\"[site_url]?mod=stpl&amp;action=preview&amp;pl=sub&amp;id=SUB_STPL_ID&amp;[stpl_preview_code]\" target=\"_blank\">View this newsletter in your browser</a>.</em></p>', 'stplCanvasElementText'),
			('2', '2', '564', '', 'stplCanvasElementText'),
			('3', '3', '564', '', 'stplCanvasElementText'),
			('4', '4', '564', '', 'stplCanvasElementText'),
			('5', '5', '564', '<p style=\"text-align: center;\"><em>If you don\'t want to receive such newsletter anymore - you can just <a title=\"unsubscribe\" href=\"[site_url]?mod=subscribe&amp;action=unsubscribeLead&amp;pl=sub&amp;[unsubscribe_link_params]\" target=\"_blank\">unsubscribe</a>.</em></p>', 'stplCanvasElementText'),
			('6', '6', '564', '<p style=\"text-align: center;\"><em>Display problems? <a title=\"View this newsletter in your browser\" href=\"[site_url]?mod=stpl&amp;action=preview&amp;pl=sub&amp;id=SUB_STPL_ID&amp;[stpl_preview_code]\" target=\"_blank\">View this newsletter in your browser</a>.</em></p>', 'stplCanvasElementText'),
			('7', '7', '564', '', 'stplCanvasElementText'),
			('8', '8', '279', '', 'stplCanvasElementText'),
			('9', '8', '279', '', 'stplCanvasElementText'),
			('10', '9', '564', '', 'stplCanvasElementText'),
			('11', '10', '564', '<p style=\"text-align: center;\"><em>If you don\'t want to receive such newsletter anymore - you can just <a title=\"unsubscribe\" href=\"[site_url]?mod=subscribe&amp;action=unsubscribeLead&amp;pl=sub&amp;[unsubscribe_link_params]\" target=\"_blank\">unsubscribe</a>.</em></p>', 'stplCanvasElementText'),
			('12', '11', '564', '<p style=\"text-align: center;\"><em>Display problems? <a title=\"View this newsletter in your browser\" href=\"[site_url]?mod=stpl&amp;action=preview&amp;pl=sub&amp;id=SUB_STPL_ID&amp;[stpl_preview_code]\" target=\"_blank\">View this newsletter in your browser</a>.</em></p>', 'stplCanvasElementText'),
			('13', '12', '564', '', 'stplCanvasElementText'),
			('14', '13', '184', '', 'stplCanvasElementText'),
			('15', '13', '184', '', 'stplCanvasElementText'),
			('16', '13', '184', '', 'stplCanvasElementText'),
			('17', '14', '564', '', 'stplCanvasElementText'),
			('18', '15', '564', '<p style=\"text-align: center;\"><em>If you don\'t want to receive such newsletter anymore - you can just <a title=\"unsubscribe\" href=\"[site_url]?mod=subscribe&amp;action=unsubscribeLead&amp;pl=sub&amp;[unsubscribe_link_params]\" target=\"_blank\">unsubscribe</a>.</em></p>', 'stplCanvasElementText'),
			('19', '16', '302', '<p style=\"text-align: center;\"><em><a href=\"SUB_STPL_SITE_URL\"><img class=\"alignleft size-full wp-image-2555\" alt=\"logo\" src=\"SUB_STPL_MOD_URLimg/common/orion_logo.png\" title=\"logo\" width=\"76\" height=\"14\" style=\"display: inline;\"></a>&nbsp;</em></p>', 'stplCanvasElementText'),
			('20', '16', '256', '<pre><span style=\"text-decoration: none; color: #888888;\"><a href=\"SUB_STPL_SITE_URL\">Home</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;<a href=\"SUB_STPL_SITE_URL\">About</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"SUB_STPL_SITE_URL\">Subscribe</a></span></pre>', 'stplCanvasElementText'),
			('21', '17', '158', '', 'stplCanvasElementText'),
			('22', '17', '209', '<p><a href=\"\"><img class=\"aligncenter size-full wp-image-2554\" alt=\"header\" src=\"SUB_STPL_MOD_URLimg/common/orion_header.png\" title=\"header image\" width=\"235\" height=\"188\" style=\"display: inline;\"></a></p>', 'stplCanvasElementText'),
			('23', '17', '185', '', 'stplCanvasElementText'),
			('24', '18', '106', '', 'stplCanvasElementText'),
			('25', '18', '450', '<pre><a href=\"\"><img class=\"alignleft\" alt=\"introduction_icon\" src=\"SUB_STPL_MOD_URLimg/common/introduction_icon.png\" title=\"Introdaction image\" width=\"24\" height=\"27\" style=\"display: inline;\"></a>The&nbsp;<span style=\"color: #ff0000;\">Kickstarter&nbsp;</span>template for all your email campaigns</pre>', 'stplCanvasElementText'),
			('26', '19', '564', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h1\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"left\" read_more_text=\"Activate Now\" read_more_styles=\"color: #fff; text-decoration: none; user-select: none; background: #f65f5f; padding: .6em 1.5em; float: left; border-radius: 4px;\"]', 'stplCanvasElementStaticContent'),
			('27', '20', '564', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h1\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"right\" read_more_text=\"Activate Now\" read_more_styles=\"color: #fff; text-decoration: none; user-select: none; background: #f65f5f; padding: .6em 1.5em; float: left; border-radius: 4px;\"]', 'stplCanvasElementStaticContent'),
			('28', '21', '558', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h1\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"left\" read_more_text=\"Activate Now\" read_more_styles=\"color: #fff; text-decoration: none; user-select: none; background: #f65f5f; padding: .6em 1.5em; float: left; border-radius: 4px;\"]', 'stplCanvasElementStaticContent'),
			('29', '22', '558', '<pre style=\"text-align: center;\"><span style=\"color: #ffffff;\"><br>\nNEXT GENERATION EDITING.</span></pre>', 'stplCanvasElementText'),
			('30', '23', '251', '<a href=\"\"><img src=\"SUB_STPL_MOD_URLimg/common/column_image_3.png\" title=\"column image 1\" alt=\"\" style=\"display: block; margin-left: auto; margin-right: auto;\"></a>', 'stplCanvasElementImage'),
			('31', '23', '296', '<a href=\"\"><img src=\"SUB_STPL_MOD_URLimg/common/column_image_4.png\" title=\"column image 2\" alt=\"\" style=\"display: block; margin-left: auto; margin-right: auto;\"></a>', 'stplCanvasElementImage'),
			('32', '24', '279', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h1\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"left\" read_more_text=\"Activate Now\" read_more_styles=\"color: #fff; text-decoration: none; user-select: none; background: #f65f5f; padding: .6em 1.5em; float: left; border-radius: 4px;\"]', 'stplCanvasElementStaticContent'),
			('33', '24', '279', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h1\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"left\" read_more_text=\"Activate Now\" read_more_styles=\"color: #fff; text-decoration: none; user-select: none; background: #f65f5f; padding: .6em 1.5em; float: left; border-radius: 4px;\"]', 'stplCanvasElementStaticContent'),
			('34', '25', '558', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h1\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"left\" read_more_text=\"Activate Now\" read_more_styles=\"color: #fff; text-decoration: none; user-select: none; background: #f65f5f; padding: .6em 1.5em; float: left; border-radius: 4px;\"]', 'stplCanvasElementStaticContent'),
			('35', '26', '558', '<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\"><tbody><tr><td height=\"20\">&nbsp;</td></tr><tr><td><table width=\"175\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"left\"><tbody><tr><td height=\"50\"><a href=\"SUB_STPL_SITE_URL\"><img style=\"display: inline;\" alt=\"\" src=\"SUB_STPL_MOD_URLimg/common/orion_logo.png\"></a></td></tr></tbody></table><table width=\"375\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"right\"><tbody><tr><td height=\"50\"><pre><span style=\"color: #888888;\"><a href=\"[site_url]?mod=subscribe&amp;amp;action=unsubscribeLead&amp;amp;pl=sub&amp;amp;[unsubscribe_link_params]\">Unsubscribe</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"SUB_STPL_SITE_URL\">Register</a>&nbsp;&nbsp;</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"https://twitter.com/ReadyEcommerceW\"><img style=\"display: inline;\" alt=\"\" src=\"SUB_STPL_MOD_URLimg/common/twitter.png\"></a></pre></td></tr></tbody></table></td></tr><tr><td height=\"20\">&nbsp;</td></tr></tbody></table>', 'stplCanvasElementText'),
			('36', '27', '306', '<p style=\"text-align: center;\"><span style=\"color: #3366ff;\"><a href=\"mailto:\"><span style=\"color: #3366ff;\">Forward to a friend</span></a> &nbsp; | &nbsp; <a href=\"SUB_STPL_SITE_URL\"><span style=\"color: #3366ff;\">Visit our website</span></a></span></p>', 'stplCanvasElementText'),
			('37', '27', '249', '<p style=\"text-align: right;\"><span style=\"color: #ffffff;\">Sales and Support: +1 (555) 555-5555</span></p>', 'stplCanvasElementText'),
			('38', '28', '275', '<p><a href=\"SUB_STPL_SITE_URL\"><img class=\"alignnone size-full wp-image-2538\" alt=\"logo\" src=\"SUB_STPL_MOD_URLimg/common/logo.png\" title=\"Logo\" style=\"display: inline;\" height=\"110\" width=\"290\"></a></p>', 'stplCanvasElementText'),
			('39', '28', '275', '<div><div style=\"text-align: center;\">&nbsp;</div><h3 style=\"text-align: center;\"><span style=\"color: #3366ff;\">Having trouble viewing this email? <a href=\"[site_url]?mod=stpl&amp;action=preview&amp;pl=sub&amp;id=103&amp;[stpl_preview_code]\">Click here</a> to view the hosted version.</span></h3></div><h3 style=\"text-align: center;\">&nbsp;</h3><h3 style=\"text-align: center;\">&nbsp;</h3>', 'stplCanvasElementText'),
			('40', '29', '563', '<p><a href=\"\"><img class=\"alignnone size-full wp-image-2540\" alt=\"image\" src=\"SUB_STPL_MOD_URLimg/common/image1.png\" style=\"display: inline;\" height=\"200\" width=\"558\"></a></p>', 'stplCanvasElementDivider'),
			('41', '30', '563', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h1\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"left\" read_more_text=\"Read More...\" read_more_styles=\"\"]', 'stplCanvasElementStaticContent'),
			('42', '31', '557', '', 'stplCanvasElementText'),
			('43', '32', '557', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h1\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"left\" read_more_text=\"Read More...\" read_more_styles=\"\"]', 'stplCanvasElementStaticContent'),
			('44', '33', '557', '', 'stplCanvasElementText'),
			('45', '34', '557', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h1\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"left\" read_more_text=\"Read More...\" read_more_styles=\"\"]', 'stplCanvasElementStaticContent'),
			('46', '35', '557', '', 'stplCanvasElementText'),
			('47', '36', '274', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h1\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"left\" read_more_text=\"Read More...\" read_more_styles=\"\"]', 'stplCanvasElementStaticContent'),
			('48', '36', '278', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h1\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"left\" read_more_text=\"Read More...\" read_more_styles=\"\"]', 'stplCanvasElementStaticContent'),
			('49', '37', '557', '', 'stplCanvasElementText'),
			('50', '38', '557', '<p><a href=\"SUB_STPL_SITE_URL\"><img class=\"alignnone size-full wp-image-2546\" alt=\"logo2\" src=\"SUB_STPL_MOD_URLimg/common/logo2.png\" title=\"footer logo\" style=\"display: inline;\" height=\"110\" width=\"290\"></a></p>', 'stplCanvasElementText'),
			('51', '39', '370', '<p><span style=\"color: #ffffff;\">301 some street name, city name, state, country </span></p><p><span style=\"color: #ffffff;\"><a href=\"SUB_STPL_SITE_URL\"><span style=\"color: #ffffff;\"> SUB_STPL_SITE_URL</span></a>&nbsp;&nbsp;| &nbsp;<a href=\"mailto:SUB_STPL_ADMIN_EMAIL\"><span style=\"color: #ffffff;\"> SUB_STPL_ADMIN_EMAIL</span></a></span></p><p><span style=\"color: #ffffff;\">Sales and Support: +1 (555) 555-5555</span></p>', 'stplCanvasElementText'),
			('52', '39', '182', '<div class=\"stplCanvasSocSet\" style=\"text-align: center;\"><a href=\"https://www.facebook.com/ReadyECommerce\" target=\"_blank\" title=\"facebook\"><img style=\"padding: 10px 10px 10px 0px; display: inline;\" src=\"SUB_STPL_MOD_URLimg/soc_icons/facebook-1.png\"></a><a href=\"https://twitter.com/ReadyEcommerceW\" target=\"_blank\" title=\"twitter\"><img style=\"padding: 10px 10px 10px 0px; display: inline;\" src=\"SUB_STPL_MOD_URLimg/soc_icons/twitter-1.png\"></a><a href=\"https://plus.google.com/105222308619741800340/about\" target=\"_blank\" title=\"google_plus\"><img style=\"padding: 10px 10px 10px 0px; display: inline;\" src=\"SUB_STPL_MOD_URLimg/soc_icons/google_plus-1.png\"></a><input type=\"hidden\" class=\"stplCanvasSocSetId\" value=\"1\"></div>', 'stplCanvasElementSocial'),
			('53', '40', '563', '<p><span style=\"color: #ffffff;\">You are cirrently signed up to Company\'s newsletters as: <a href=\"mailto:SUB_STPL_ADMIN_EMAIL\"><span style=\"color: #ffffff;\"> SUB_STPL_ADMIN_EMAIL</span></a>. &nbsp;<a href=\"[site_url]?mod=subscribe&amp;action=unsubscribeLead&amp;pl=sub&amp;[unsubscribe_link_params]\"><span style=\"color: #ffffff;\">To unscribe click here</span></a></span><br><span style=\"color: #ffffff;\">Copyright 2010 Your company Name.</span></p>', 'stplCanvasElementText'),
			('54', '41', '564', '<p style=\"text-align: center;\"><a href=\"mailto:\">Forward to a Friend</a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<a href=\"[site_url]?mod=stpl&amp;amp;action=preview&amp;amp;pl=sub&amp;amp;id=104&amp;amp;[stpl_preview_code]\">Online Version</a>&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<a href=\"[site_url]?mod=stpl&amp;amp;action=preview&amp;amp;pl=sub&amp;amp;id=104&amp;amp;[stpl_preview_code]\">Mobile Version</a>&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<a href=\"[site_url]?mod=subscribe&amp;amp;action=unsubscribeLead&amp;amp;pl=sub&amp;amp;[unsubscribe_link_params]\">Unsubscribe</a></p>', 'stplCanvasElementText'),
			('55', '42', '184', '<p><a href=\"SUB_STPL_SITE_URL\"><img class=\"alignnone size-full wp-image-2517\" alt=\"logo\" src=\"SUB_STPL_MOD_URLimg/common/BlauMail/logo.gif\" title=\"logo\" width=\"170\" height=\"42\" style=\"display: inline;\"></a></p>', 'stplCanvasElementText'),
			('56', '42', '184', '', 'stplCanvasElementText'),
			('57', '42', '184', '<p><a href=\"\"><img class=\"alignnone size-full wp-image-2518\" alt=\"issue_date\" src=\"SUB_STPL_MOD_URLimg/common/BlauMail/issue_date.gif\" width=\"174\" height=\"24\" style=\"display: inline;\"></a></p>', 'stplCanvasElementText'),
			('58', '43', '382', '<h1><span style=\"color: #ffffff;\">Get Inspired&nbsp;</span></h1><address><span style=\"color: #ffffff;\">Quisque vitae quis sagittis sollictudin elementum ipsum malesuada mi sagittis hendrerit</span></address><address>&nbsp;</address>', 'stplCanvasElementText'),
			('59', '43', '176', '<p><a href=\"\"><img class=\"size-full wp-image-2525 alignright\" alt=\"top-header-image-button\" src=\"SUB_STPL_MOD_URLimg/common/BlauMail/top-header-image-button1.gif\" width=\"173\" height=\"53\" style=\"display: inline;\"></a></p>', 'stplCanvasElementText'),
			('60', '44', '402', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h1\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"center\" read_more_text=\"READ MORE\" read_more_styles=\"color: #fff; text-decoration: none; user-select: none; background: #f78d1e; padding: .6em 1.5em; float: left; border: 4px;\"]', 'stplCanvasElementStaticContent'),
			('61', '44', '155', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h1\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"center\" read_more_text=\"READ MORE\" read_more_styles=\"color: #fff; text-decoration: none; user-select: none; background: #bababa; padding: .6em 1.5em; float: left; border: 4px;\"]', 'stplCanvasElementStaticContent'),
			('62', '45', '199', '<p style=\"text-align: center;\"><em><a href=\"\"><img class=\"alignnone size-full wp-image-2520\" alt=\"dummy-image_170x80_0111111\" src=\"SUB_STPL_MOD_URLimg/common/BlauMail/dummy-image_170x80_0111111.jpg\" width=\"170\" height=\"80\" style=\"display: inline;\"></a></em></p><p style=\"text-align: center;\"><a href=\"\"><img class=\"alignnone size-full wp-image-2521\" alt=\"dummy-image_170x80_022222222\" src=\"SUB_STPL_MOD_URLimg/common/BlauMail/dummy-image_170x80_022222222.jpg\" width=\"170\" height=\"80\" style=\"display: inline;\"></a></p>', 'stplCanvasElementText'),
			('63', '45', '359', '<h2><span style=\"color: #ff6600;\">Vivamus viverra facilisis nisl eget</span></h2><p>Fusce amet ligula ornare tempus vulputate ipsum semper. Praesent non lorem..</p><h2><span style=\"color: #ff6600;\">Vivamus viverra facilisis nisl eget malesuada </span></h2><p>Cras mauris enim, feugiat porttitor consectetur sed, fermentum ut dolor...</p>', 'stplCanvasElementText'),
			('64', '46', '320', '<h3>BLAUMAIL</h3><p>1600 Amphitheatre Parkway Mountain View, CA 94043</p><p>Help &amp; Support Center: 0000 000 000</p><p>Website: SUB_STPL_SITE_URL</p>', 'stplCanvasElementText'),
			('65', '46', '238', '<div class=\"stplCanvasSocSet\" style=\"text-align: center;\"><a href=\"https://www.facebook.com/ReadyECommerce\" target=\"_blank\" title=\"facebook\"><img style=\"padding: 10px 10px 10px 0px; display: inline;\" src=\"SUB_STPL_MOD_URLimg/soc_icons/facebook-1.png\"></a><a href=\"https://twitter.com/ReadyEcommerceW\" target=\"_blank\" title=\"twitter\"><img style=\"padding: 10px 10px 10px 0px; display: inline;\" src=\"SUB_STPL_MOD_URLimg/soc_icons/twitter-1.png\"></a><a href=\"https://plus.google.com/105222308619741800340/about\" target=\"_blank\" title=\"google_plus\"><img style=\"padding: 10px 10px 10px 0px; display: inline;\" src=\"SUB_STPL_MOD_URLimg/soc_icons/google_plus-1.png\"></a><input type=\"hidden\" class=\"stplCanvasSocSetId\" value=\"1\"></div>', 'stplCanvasElementSocial'),
			('66', '47', '346', '<h2 style=\"text-align: left;\"><span style=\"color: #ffffff;\">&nbsp; &nbsp; &nbsp; CORP</span></h2>', 'stplCanvasElementText'),
			('67', '47', '175', '<p><span style=\"color: #ffffff;\">Issue #24. 12th Jan 2014</span></p>', 'stplCanvasElementText'),
			('68', '48', '564', '<p><a href=\"\"><img class=\"aligncenter size-full wp-image-2583\" alt=\"header\" src=\"SUB_STPL_MOD_URLimg/common/RocketWay/header1.png\" title=\"header image\" width=\"600\" height=\"316\" style=\"display: inline;\"></a></p>', 'stplCanvasElementText'),
			('69', '49', '564', '', 'stplCanvasElementText'),
			('70', '50', '564', '<h2 style=\"text-align: center;\">How much does an estate agency website cost?</h2>', 'stplCanvasElementText'),
			('71', '51', '564', '<p style=\"text-align: center;\"><span style=\"color: #888888;\">There are many variations of passages vailable, but the majority have suffered alteration in some form, by injected humour andwords which don\'t look slightly...</span></p>', 'stplCanvasElementText'),
			('72', '52', '151', '<h2 style=\"text-align: center;\"><a href=\"\"><img class=\"aligncenter size-full wp-image-2585\" alt=\"one\" src=\"SUB_STPL_MOD_URLimg/common/RocketWay/one.png\" title=\"imag 1\" width=\"114\" height=\"119\" style=\"display: inline;\"></a></h2><h2 style=\"text-align: center;\">WEBSITES</h2>', 'stplCanvasElementText'),
			('73', '52', '259', '<p style=\"text-align: center;\"><a href=\"\"><img class=\"aligncenter size-full wp-image-2586\" alt=\"two\" src=\"SUB_STPL_MOD_URLimg/common/RocketWay/two.png\" title=\"image 2\" width=\"114\" height=\"119\" style=\"display: inline;\"></a></p><h2 style=\"text-align: center;\">SERVICES</h2>', 'stplCanvasElementText'),
			('74', '52', '139', '<h2><a href=\"\"><img class=\"aligncenter size-full wp-image-2587\" alt=\"three\" src=\"SUB_STPL_MOD_URLimg/common/RocketWay/three.png\" title=\"image 3\" width=\"114\" height=\"119\" style=\"display: inline;\"></a></h2><h2 style=\"text-align: left;\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;SEO</h2>', 'stplCanvasElementText'),
			('75', '53', '558', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h1\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"left\" read_more_text=\"READ MORE\" read_more_styles=\"color: #fff; text-decoration: none; user-select: none; background: #83c4c6; padding: .6em 1.5em; float: left; border: 4px;\"]', 'stplCanvasElementStaticContent'),
			('76', '54', '558', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h2\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"right\" read_more_text=\"READ MORE\" read_more_styles=\"color: #fff; text-decoration: none; user-select: none; background: #83c4c6; padding: .6em 1.5em; float: left; border: 4px;\"]', 'stplCanvasElementStaticContent'),
			('77', '55', '558', '', 'stplCanvasElementText'),
			('78', '56', '182', '<a href=\"\"><img src=\"SUB_STPL_MOD_URLimg/common/RocketWay/11.png\" alt=\"\" title=\"11\" style=\"display: block; margin-left: auto; margin-right: auto;\"></a>', 'stplCanvasElementImage'),
			('79', '56', '182', '<a href=\"\"><img src=\"SUB_STPL_MOD_URLimg/common/RocketWay/22.png\" alt=\"\" title=\"22\" style=\"display: block; margin-left: auto; margin-right: auto;\"></a>', 'stplCanvasElementImage'),
			('80', '56', '182', '<a href=\"\"><img src=\"SUB_STPL_MOD_URLimg/common/RocketWay/33.png\" alt=\"\" title=\"33\" style=\"display: block; margin-left: auto; margin-right: auto;\"></a>', 'stplCanvasElementImage'),
			('81', '57', '277', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h1\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"left\" read_more_text=\"READ MORE\" read_more_styles=\"color: #fff; text-decoration: none; user-select: none; background: #83c4c6; padding: .6em 1.5em; float: left; border: 4px;\"]', 'stplCanvasElementStaticContent'),
			('82', '57', '275', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h1\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"left\" read_more_text=\"READ MORE\" read_more_styles=\"color: #fff; text-decoration: none; user-select: none; background: #83c4c6; padding: .6em 1.5em; float: left; border: 4px;\"]', 'stplCanvasElementStaticContent'),
			('83', '58', '182', '<h2 style=\"text-align: center;\"><a href=\"\"><img class=\"aligncenter size-full wp-image-2595\" alt=\"9\" src=\"SUB_STPL_MOD_URLimg/common/RocketWay/9.png\" width=\"100\" height=\"100\" style=\"display: inline;\"></a></h2><h2 style=\"text-align: center;\">BASIC</h2><p style=\"text-align: center;\">unlimited data 100MB per day 10 projects</p><p style=\"text-align: center;\"><a href=\"\"><img class=\"aligncenter size-full wp-image-2598\" alt=\"SINGUP\" src=\"SUB_STPL_MOD_URLimg/common/RocketWay/SINGUP.png\" width=\"101\" height=\"28\" style=\"display: inline;\"></a></p>', 'stplCanvasElementText'),
			('84', '58', '182', '<h2 style=\"text-align: center;\"><a href=\"\"><img class=\"aligncenter size-full wp-image-2596\" alt=\"19\" src=\"SUB_STPL_MOD_URLimg/common/RocketWay/19.png\" width=\"100\" height=\"100\" style=\"display: inline;\"></a></h2><h2 style=\"text-align: center;\">STANDART</h2><p style=\"text-align: center;\">unlimited data 200MB per day 50 projects</p><p style=\"text-align: center;\"><a href=\"\"><img class=\"aligncenter size-full wp-image-2598\" alt=\"SINGUP\" src=\"SUB_STPL_MOD_URLimg/common/RocketWay/SINGUP.png\" width=\"101\" height=\"28\" style=\"display: inline;\"></a></p>', 'stplCanvasElementText'),
			('85', '58', '182', '<h2 style=\"text-align: center;\"><a href=\"\"><img class=\"aligncenter size-full wp-image-2597\" alt=\"29\" src=\"SUB_STPL_MOD_URLimg/common/RocketWay/29.png\" width=\"102\" height=\"105\" style=\"display: inline;\"></a></h2><h2 style=\"text-align: center;\">CORPORATE</h2><p style=\"text-align: center;\">unlimited data 500MB per day 100 projects</p><p style=\"text-align: center;\"><a href=\"\"><img class=\"aligncenter size-full wp-image-2598\" alt=\"SINGUP\" src=\"SUB_STPL_MOD_URLimg/common/RocketWay/SINGUP.png\" width=\"101\" height=\"28\" style=\"display: inline;\"></a></p>', 'stplCanvasElementText'),
			('86', '59', '203', '', 'stplCanvasElementText'),
			('87', '59', '355', '<p><a href=\"https://www.facebook.com/ReadyECommerce\"><img style=\"display: inline;\" alt=\"social_icon1\" src=\"SUB_STPL_MOD_URLimg/common/RocketWay/social_icon1.png\" title=\"facebook\" width=\"30\" height=\"30\"></a>&nbsp;<a href=\"https://twitter.com/ReadyEcommerceW\"><img style=\"display: inline;\" alt=\"social_icon2\" src=\"SUB_STPL_MOD_URLimg/common/RocketWay/social_icon2.png\" title=\"twitter\" width=\"30\" height=\"30\"></a>&nbsp;<a href=\"https://plus.google.com/105222308619741800340/about\"><img style=\"display: inline;\" alt=\"social_icon3\" src=\"SUB_STPL_MOD_URLimg/common/RocketWay/social_icon3.png\" title=\"google+\" width=\"30\" height=\"30\"></a>&nbsp;<img style=\"display: inline;\" alt=\"social_icon4\" src=\"SUB_STPL_MOD_URLimg/common/RocketWay/social_icon4.png\" title=\"P\" width=\"30\" height=\"30\"></p>', 'stplCanvasElementText'),
			('88', '60', '558', '<p style=\"text-align: center;\"><span style=\"color: #ffffff;\"><a href=\"[site_url]?mod=subscribe&amp;amp;action=unsubscribeLead&amp;amp;pl=sub&amp;amp;[unsubscribe_link_params]\"><span style=\"color: #ffffff;\">Unsubscribe</span></a></span><span style=\"color: #888888;\"> &nbsp; &nbsp; |&nbsp;&nbsp; &nbsp;&nbsp;<a href=\"[site_url]?mod=stpl&amp;amp;action=preview&amp;amp;pl=sub&amp;amp;id=124&amp;amp;[stpl_preview_code]\"><span style=\"color: #ffffff;\"><span style=\"color: #ffffff;\">Visit online</span></span>&nbsp;</a> &nbsp; &nbsp;|&nbsp;&nbsp; &nbsp;<span style=\"color: #ffffff;\">&nbsp;<a href=\"mailto:\"><span style=\"color: #ffffff;\">Send to a friend</span></a></span></span></p>', 'stplCanvasElementText'),
			('89', '61', '279', '<a href=\"SUB_STPL_SITE_URL\"><img src=\"SUB_STPL_MOD_URLimg/common/1.jpg\" alt=\"\" title=\"Your Site\" style=\"display: block; margin-left: auto; margin-right: auto;\"></a>', 'stplCanvasElementImage'),
			('90', '61', '279', '<h2 style=\"text-align: right; \">&nbsp;</h2><h2 style=\"text-align: right; user-select: none;  font-size: 13px; text-decoration: none;\"><a href=\"SUB_STPL_SITE_URL\">Visit Our Site</a></h2>', 'stplCanvasElementText'),
			('91', '62', '564', '<a href=\"\"><img src=\"SUB_STPL_MOD_URLimg/common/2.jpg\" alt=\"\" title=\"2\" style=\"display: block; margin-left: auto; margin-right: auto;\"></a>', 'stplCanvasElementImage'),
			('92', '63', '441', '<p>Monday 02 August | Published by Your Company. ........................................................................................</p>', 'stplCanvasElementText'),
			('93', '63', '117', '<p>Custom Sidebar ...................</p>', 'stplCanvasElementText'),
			('94', '64', '432', '[new_content_ready title_style=\"h1\" title_align=\"left\" show_content=\"excerpt\" posts_num=\"3\" category=\"0\" content_styles=\"\" image_align=\"center\" read_more_text=\"Read More...\" read_more_styles=\"\"]', 'stplCanvasElementNewContent'),
			('95', '64', '124', '<p><a href=\"\"><img class=\"aligncenter size-full wp-image-2534\" alt=\"6\" src=\"SUB_STPL_MOD_URLimg/common/6.jpg\" title=\"image 6\" width=\"125\" height=\"125\" style=\"display: inline;\"></a> <a href=\"\"><img class=\"aligncenter size-productpreview wp-image-2533\" alt=\"5\" src=\"SUB_STPL_MOD_URLimg/common/5.jpg\" title=\"image 5\" width=\"125\" height=\"125\" style=\"display: inline;\"></a> <a href=\"\"><img class=\"aligncenter size-productpreview wp-image-2532\" alt=\"4\" src=\"SUB_STPL_MOD_URLimg/common/4.jpg\" title=\"image 4\" width=\"125\" height=\"125\" style=\"display: inline;\"></a> <a href=\"\"><img class=\"aligncenter size-full wp-image-2531\" alt=\"3\" src=\"SUB_STPL_MOD_URLimg/common/3.jpg\" title=\"image 3\" width=\"125\" height=\"125\" style=\"display: inline;\"></a></p>', 'stplCanvasElementText'),
			('96', '65', '436', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h1\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"left\" read_more_text=\"Read More...\" read_more_styles=\"\"]', 'stplCanvasElementStaticContent'),
			('97', '65', '116', '', 'stplCanvasElementText'),
			('98', '66', '558', '<p style=\"text-align: center;\">Your Company is Copyright by Your Corp.</p><p style=\"text-align: center;\">Your Company - 123 Some Street, City, ST 99999. Ph +1 4 1477 89 745</p>', 'stplCanvasElementText'),
			('99', '67', '558', '<a href=\"\"><img src=\"SUB_STPL_MOD_URLimg/common/9.jpg\" title=\"image 9\" alt=\"\" style=\"display: block; margin-left: auto; margin-right: auto;\"></a>', 'stplCanvasElementImage'),
			('100', '68', '558', '<p style=\"text-align: center;\">You\'re receiving this newsletter because you asked to recieve updates.</p><p style=\"text-align: center;\">Having trouble reading this email?&nbsp;<a href=\"[site_url]?mod=stpl&amp;amp;action=preview&amp;amp;pl=sub&amp;amp;id=126&amp;amp;[stpl_preview_code]\">Click here</a> to view the hosted version.</p><p style=\"text-align: center;\">Want to be taken off this list? <a href=\"[site_url]?mod=subscribe&amp;amp;action=unsubscribeLead&amp;amp;pl=sub&amp;amp;[unsubscribe_link_params]\">Unsubscribe Instantly</a></p>', 'stplCanvasElementText'),
			('101', '69', '396', '<p><a href=\"[site_url]?mod=stpl&amp;amp;action=preview&amp;amp;pl=sub&amp;amp;id=123&amp;amp;[stpl_preview_code]\"><span style=\"color: #ffffff;\">VIEW ONLINE</span></a></p>', 'stplCanvasElementText'),
			('102', '69', '162', '<p><a href=\"http://www.youtube.com/user/readyshoppingcart\"><img class=\"aligncenter size-full wp-image-2646\" style=\"display: inline;\" alt=\"icon-instagram\" src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/icon-instagram.png\" title=\"Youtube\" width=\"30\" height=\"30\"></a> <a href=\"https://plus.google.com/105222308619741800340/about\"><img class=\"aligncenter size-full wp-image-2645\" style=\"display: inline;\" alt=\"icon-googleplus\" src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/icon-googleplus.png\" title=\"google+\" width=\"30\" height=\"30\"></a> <a href=\"https://twitter.com/ReadyEcommerceW\"><img class=\"aligncenter size-productpreview wp-image-2644\" style=\"display: inline;\" alt=\"icon-twitter\" src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/icon-twitter.png\" title=\"twitter\" width=\"30\" height=\"30\"></a> <a href=\"https://www.facebook.com/ReadyECommerce\"><img class=\"aligncenter size-full wp-image-2643\" style=\"display: inline;\" alt=\"icon-facebook\" src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/icon-facebook.png\" title=\"facebook\" width=\"30\" height=\"30\"></a></p>', 'stplCanvasElementText'),
			('103', '70', '558', '<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"left\"><tbody><tr><td align=\"center\" valign=\"top\"><img style=\"display: inline;\" title=\"top-logo\" alt=\"Logo\" src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/top-logo.png\" width=\"114\" border=\"0\" hspace=\"0\" vspace=\"0\"></td></tr><tr><td valign=\"top\">&nbsp;</td></tr></tbody></table><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"right\"><tbody><tr><td align=\"center\" valign=\"middle\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\"><tbody><tr><td><a href=\"SUB_STPL_SITE_URL\">ABOUT</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"SUB_STPL_SITE_URL\">SERVICE</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"SUB_STPL_SITE_URL\">CONTACT</a></td></tr></tbody></table></td></tr></tbody></table>', 'stplCanvasElementText'),
			('104', '71', '564', '', 'stplCanvasElementText'),
			('105', '72', '564', '<h2 style=\"text-align: center;\"><span style=\"color: #259daa;\">[Ready!Newsletter]</span></h2>', 'stplCanvasElementText'),
			('106', '73', '564', '<p><a href=\"\"><img class=\"alignleft size-full wp-image-2563\" alt=\"header-image\" src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/header-image.jpg\" title=\"header image\" width=\"564\" height=\"253\" style=\"display: block;\"></a></p>', 'stplCanvasElementText'),
			('107', '74', '558', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h1\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"left\" read_more_text=\"Read More\" read_more_styles=\"color: #fff; text-decoration: none; user-select: none; background: #009dac; padding: .6em 1.5em; float: left; border: 4px; \"]', 'stplCanvasElementStaticContent'),
			('108', '75', '558', '', 'stplCanvasElementText'),
			('109', '76', '276', '<p><span style=\"color: #259daa;\">TITLE</span> <span style=\"color: #888888;\">PRODUCTS</span></p>', 'stplCanvasElementText'),
			('110', '76', '276', '<p style=\"text-align: right;\"><span style=\"color: #259daa;\">2 JUN 2014</span></p>', 'stplCanvasElementText'),
			('111', '77', '276', '<a href=\"\"><img src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/image1.jpg\" title=\"image 1\" alt=\"\" style=\"display: block; margin-left: auto; margin-right: auto;\"></a>', 'stplCanvasElementImage'),
			('112', '77', '276', '<a href=\"\"><img src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/image2.jpg\" title=\"image 2\" alt=\"\" style=\"display: block; margin-left: auto; margin-right: auto;\"></a>', 'stplCanvasElementImage'),
			('113', '78', '281', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h4\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"left\" read_more_text=\"READ MORE &gt;&gt;\" read_more_styles=\"color: #009dac; text-decoration: none; user-select: none;  padding: .6em 1.5em; float: left; border: 4px; \"]', 'stplCanvasElementStaticContent'),
			('114', '78', '277', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h4\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"left\" read_more_text=\"READ MORE &gt;&gt;\" read_more_styles=\"color: #009dac; text-decoration: none; user-select: none;  padding: .6em 1.5em; float: left; border: 4px; \"]', 'stplCanvasElementStaticContent'),
			('115', '79', '558', '', 'stplCanvasElementText'),
			('116', '80', '276', '<p><span style=\"color: #259daa;\">PRODUCTS </span><span style=\"color: #666699;\">POPULAR</span></p>', 'stplCanvasElementText'),
			('117', '80', '276', '', 'stplCanvasElementText'),
			('118', '81', '179', '<a href=\"\"><img src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/image3.jpg\" title=\"image 3\" alt=\"\" style=\"display: block; margin-left: auto; margin-right: auto;\"></a>', 'stplCanvasElementImage'),
			('119', '81', '185', '<a href=\"\"><img src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/image4.jpg\" title=\"image 4\" alt=\"\" style=\"display: block; margin-left: auto; margin-right: auto;\"></a>', 'stplCanvasElementImage'),
			('120', '81', '183', '<a href=\"\"><img src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/image5.jpg\" title=\"image 5\" alt=\"\" style=\"display: block; margin-left: auto; margin-right: auto;\"></a>', 'stplCanvasElementImage'),
			('121', '82', '187', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h5\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"left\" read_more_text=\"MORE DETAIL\" read_more_styles=\"color: #009dac; text-decoration: none; user-select: none;  padding: .6em 1.5em; float: left; \"]', 'stplCanvasElementStaticContent'),
			('122', '82', '182', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h5\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"left\" read_more_text=\"MORE DETAIL\" read_more_styles=\"color: #009dac; text-decoration: none; user-select: none;  padding: .6em 1.5em; float: left; \"]', 'stplCanvasElementStaticContent'),
			('123', '82', '183', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h5\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"left\" read_more_text=\"MORE DETAIL\" read_more_styles=\"color: #009dac; text-decoration: none; user-select: none;  padding: .6em 1.5em; float: left; \"]', 'stplCanvasElementStaticContent'),
			('124', '83', '558', '', 'stplCanvasElementText'),
			('125', '84', '558', '<p><span style=\"color: #009dac;\">VIEW POST</span></p>', 'stplCanvasElementText'),
			('126', '85', '276', '<a href=\"\"><img src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/image6.jpg\" title=\"image 6\" alt=\"\" style=\"display: block; margin-left: auto; margin-right: auto;\"></a>', 'stplCanvasElementImage'),
			('127', '85', '276', '<a href=\"\"><img src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/image7.jpg\" title=\"image 7\" alt=\"\" style=\"display: block; margin-left: auto; margin-right: auto;\"></a>', 'stplCanvasElementImage'),
			('128', '86', '276', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h5\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"left\" read_more_text=\"BUY NOW\" read_more_styles=\"color: #fff; text-decoration: none; user-select: none; padding: .6em 1.5em; float: left; border: 4px;\"]', 'stplCanvasElementStaticContent'),
			('129', '86', '276', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h5\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"left\" read_more_text=\"BUY NOW\" read_more_styles=\"color: #fff; text-decoration: none; user-select: none; padding: .6em 1.5em; float: left; border: 4px;\"]', 'stplCanvasElementStaticContent'),
			('130', '87', '558', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h5\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"left\" read_more_text=\"Learn More\" read_more_styles=\"color: #fff; text-decoration: none; user-select: none; background: #009dac; padding: .6em 1.5em; float: left; border: 4px;\"]', 'stplCanvasElementStaticContent'),
			('131', '88', '558', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h5\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"right\" read_more_text=\"Learn More\" read_more_styles=\"color: #fff; text-decoration: none; user-select: none; background: #009dac; padding: .6em 1.5em; float: left; border: 4px;\"]', 'stplCanvasElementStaticContent'),
			('132', '89', '564', '<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tbody><tr><td align=\"center\" valign=\"top\"><table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\"><tbody><tr><td valign=\"top\"><table width=\"560\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\"><tbody><tr><td valign=\"top\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\"><tbody><tr><td align=\"center\" valign=\"top\" width=\"auto\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\"><tbody><tr><td align=\"center\" valign=\"middle\" width=\"auto\" height=\"28\"><span style=\"color: #259daa;\">PRODUCTS ARRIVING</span></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td align=\"center\" valign=\"top\"><table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\"><tbody><tr><td valign=\"top\" height=\"20\"><span style=\"color: #888888;\"><img style=\"display: inline;\" alt=\"space\" src=\"SUB_STPL_MOD_URLimg/common/space.png\" width=\"600\"></span></td></tr></tbody></table></td></tr><tr><td align=\"center\" valign=\"top\"><table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\"><tbody><tr><td valign=\"top\"><table width=\"560\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\"><tbody><tr><td valign=\"top\" width=\"100%\"><table width=\"250\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"left\"><tbody><tr><td valign=\"top\" width=\"0\"><table width=\"123\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"left\"><tbody><tr><td height=\"20\"><span style=\"color: #888888;\">&nbsp;</span></td></tr><tr><td align=\"center\" valign=\"top\"><span style=\"color: #888888;\"><a href=\"\"><span style=\"color: #888888;\"><img style=\"display: inline;\" alt=\"image10_100x100\" src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/image10.png\" width=\"100\" border=\"0\" hspace=\"0\" vspace=\"0\"></span></a></span></td></tr><tr><td height=\"20\"><span style=\"color: #888888;\">&nbsp;</span></td></tr><tr><td valign=\"top\"><table width=\"80%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\"><tbody><tr><td><span style=\"color: #888888;\">Lorem ipsumse</span></td></tr></tbody></table></td></tr><tr><td height=\"20\"><span style=\"color: #888888;\">&nbsp;</span></td></tr></tbody></table></td><td valign=\"top\"><table width=\"20\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"left\"><tbody><tr><td valign=\"top\"><span style=\"color: #888888;\">&nbsp;</span></td></tr></tbody></table></td><td valign=\"top\" width=\"0\"><table width=\"123\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"right\"><tbody><tr><td height=\"20\"><span style=\"color: #888888;\">&nbsp;</span></td></tr><tr><td align=\"center\" valign=\"top\"><span style=\"color: #888888;\"><a href=\"\"><span style=\"color: #888888;\"><img style=\"display: inline;\" alt=\"image11_100x100\" src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/image11.png\" width=\"100\" border=\"0\" hspace=\"0\" vspace=\"0\"></span></a></span></td></tr><tr><td height=\"20\"><span style=\"color: #888888;\">&nbsp;</span></td></tr><tr><td valign=\"top\"><table width=\"80%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\"><tbody><tr><td><span style=\"color: #888888;\">Lorem ipsumse</span></td></tr></tbody></table></td></tr><tr><td height=\"20\"><span style=\"color: #888888;\">&nbsp;</span></td></tr></tbody></table></td></tr><tr><td><span style=\"color: #888888;\">&nbsp;</span></td></tr></tbody></table><table width=\"1\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"left\"><tbody><tr><td width=\"0\" height=\"2\"><span style=\"color: #888888;\">&nbsp;</span></td></tr></tbody></table><table width=\"250\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"right\"><tbody><tr><td valign=\"top\" width=\"0\"><table width=\"123\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"left\"><tbody><tr><td height=\"20\"><span style=\"color: #888888;\">&nbsp;</span></td></tr><tr><td align=\"center\" valign=\"top\"><span style=\"color: #888888;\"><a href=\"\"><span style=\"color: #888888;\"><img style=\"display: inline;\" alt=\"image12_100x100\" src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/image12.png\" width=\"100\" border=\"0\" hspace=\"0\" vspace=\"0\"></span></a></span></td></tr><tr><td height=\"20\"><span style=\"color: #888888;\">&nbsp;</span></td></tr><tr><td valign=\"top\"><table width=\"80%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\"><tbody><tr><td><span style=\"color: #888888;\">Lorem ipsumse</span></td></tr></tbody></table></td></tr><tr><td height=\"20\"><span style=\"color: #888888;\">&nbsp;</span></td></tr></tbody></table></td><td valign=\"top\"><table width=\"20\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"left\"><tbody><tr><td valign=\"top\"><span style=\"color: #888888;\">&nbsp;</span></td></tr></tbody></table></td><td valign=\"top\" width=\"0\"><table width=\"123\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"right\"><tbody><tr><td height=\"20\"><span style=\"color: #888888;\">&nbsp;</span></td></tr><tr><td align=\"center\" valign=\"top\"><span style=\"color: #888888;\"><a href=\"\"><span style=\"color: #888888;\"><img style=\"display: inline;\" alt=\"image13_100x100\" src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/image13.png\" width=\"100\" border=\"0\" hspace=\"0\" vspace=\"0\"></span></a></span></td></tr><tr><td height=\"20\"><span style=\"color: #888888;\">&nbsp;</span></td></tr><tr><td valign=\"top\"><table width=\"80%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\"><tbody><tr><td><span style=\"color: #888888;\">Lorem ipsumse</span></td></tr></tbody></table></td></tr><tr><td height=\"20\"><span style=\"color: #888888;\">&nbsp;</span></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table>', 'stplCanvasElementText'),
			('133', '90', '558', '<h2><span style=\"color: #888888;\"><a href=\"\"><span style=\"color: #888888;\"><img class=\"size-full wp-image-2578 alignleft\" alt=\"fds\" src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/fds.png\" width=\"69\" height=\"133\" style=\"display: block;\"></span></a> Announce even by anynone </span></h2><p><span style=\"color: #888888;\">12/02/2014</span></p><p><span style=\"color: #888888;\"> Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempam et justo duo dolores et ea rebum. Lorem ipsum</span></p>', 'stplCanvasElementText'),
			('134', '91', '182', '<a href=\"\"><img src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/image14.png\" alt=\"\" title=\"image14\" style=\"display: block; margin-left: auto; margin-right: auto;\"></a>', 'stplCanvasElementImage'),
			('135', '91', '182', '<a href=\"\"><img src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/image15.png\" alt=\"\" title=\"image15\" style=\"display: block; margin-left: auto; margin-right: auto;\"></a>', 'stplCanvasElementImage'),
			('136', '91', '182', '<a href=\"\"><img src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/image16.png\" alt=\"\" title=\"image16\" style=\"display: block; margin-left: auto; margin-right: auto;\"></a>', 'stplCanvasElementImage'),
			('137', '92', '182', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h5\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"color:#888888;\" image_align=\"left\" read_more_text=\"\" read_more_styles=\"\"]', 'stplCanvasElementStaticContent'),
			('138', '92', '182', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h5\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"color: #888888;\" image_align=\"left\" read_more_text=\"\" read_more_styles=\"\"]', 'stplCanvasElementStaticContent'),
			('139', '92', '182', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h5\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"color:#888888;\" image_align=\"left\" read_more_text=\"\" read_more_styles=\"\"]', 'stplCanvasElementStaticContent'),
			('140', '93', '182', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h6\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"center\" read_more_text=\"\" read_more_styles=\"\"]', 'stplCanvasElementStaticContent'),
			('141', '93', '182', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h6\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"center\" read_more_text=\"\" read_more_styles=\"\"]', 'stplCanvasElementStaticContent'),
			('142', '93', '182', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h6\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"center\" read_more_text=\"\" read_more_styles=\"\"]', 'stplCanvasElementStaticContent'),
			('143', '94', '276', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h6\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"center\" read_more_text=\"\" read_more_styles=\"\"]', 'stplCanvasElementStaticContent'),
			('144', '94', '276', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h6\" static_title_align=\"left\" static_show_content=\"excerpt\" content_styles=\"\" image_align=\"center\" read_more_text=\"\" read_more_styles=\"\"]', 'stplCanvasElementStaticContent'),
			('145', '95', '264', '<p><a href=\"\"><img class=\"size-full wp-image-2581 alignright\" style=\"display: block;\" alt=\"btn6\" src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/btn6.png\" title=\"Upload\" width=\"206\" height=\"40\"></a></p>', 'stplCanvasElementText'),
			('146', '95', '294', '<p><a href=\"\"><img class=\"aligncenter size-full wp-image-2582\" alt=\"btn7\" src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/btn7.png\" title=\"Download\" width=\"206\" height=\"40\" style=\"display: inline;\"></a></p>', 'stplCanvasElementText'),
			('147', '96', '558', '<p><span style=\"color: #888888;\">\" Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempam et justo duo dolores et ea rebum. Lorem ip g elitr, sed diam nonumy eirmod tempam et just \"</span></p>', 'stplCanvasElementText'),
			('148', '97', '180', '<h2><span style=\"color: #009dac;\">BASIC</span></h2><p>Lorem ipsum dolor sit amet consectetur adipisicing elit, sed do eimodtempor</p>', 'stplCanvasElementText'),
			('149', '97', '182', '<h2><span style=\"color: #009dac;\">ADVANCED</span></h2><p>Lorem ipsum dolor sit amet consectetur adipisicing elit, sed do eimodtempor</p>', 'stplCanvasElementText'),
			('150', '97', '190', '<h2><span style=\"color: #009dac;\">CONTACT US</span></h2><p>&nbsp;Lorem ipsum dolor sit amet consectetur adipisicing elit, sed do eimodtempor</p>', 'stplCanvasElementText'),
			('151', '98', '157', '', 'stplCanvasElementText'),
			('152', '98', '401', '<p><a href=\"https://www.facebook.com/ReadyECommerce\"><img alt=\"icon-facebook-color\" src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/icon-facebook-color.png\" title=\"facebook\" width=\"29\" height=\"29\" style=\"display: inline;\"></a><a href=\"https://twitter.com/ReadyEcommerceW\"><img alt=\"icon-twitter-color\" src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/icon-twitter-color.png\" title=\"twitter\" width=\"29\" height=\"29\" style=\"display: inline;\"></a><a href=\"https://plus.google.com/105222308619741800340/about\"><img alt=\"icon-googleplus-color\" src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/icon-googleplus-color.png\" title=\"google+\" width=\"29\" height=\"29\" style=\"display: inline;\"></a><img alt=\"icon-rss-color\" src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/icon-rss-color.png\" title=\"RSS\" width=\"29\" height=\"29\" style=\"display: inline;\"><img alt=\"icon-vimeo-color\" src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/icon-vimeo-color.png\" title=\"vimeo\" width=\"29\" height=\"29\" style=\"display: inline;\"><img alt=\"icon-pinterest-color\" src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/icon-pinterest-color.png\" title=\"pinterest\" width=\"29\" height=\"29\" style=\"display: inline;\"><img alt=\"icon-linkedIn-color\" src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/icon-linkedIn-color.png\" title=\"linkedIn\" width=\"29\" height=\"29\" style=\"display: inline;\"><a href=\"http://www.youtube.com/user/readyshoppingcart\"><img alt=\"icon-instagram-color\" src=\"SUB_STPL_MOD_URLimg/common/EmbedMail/icon-instagram-color.png\" title=\"instagram\" width=\"29\" height=\"29\" style=\"display: inline;\"></a></p>', 'stplCanvasElementText'),
			('153', '99', '558', '<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\"><tbody><tr><td align=\"center\" valign=\"middle\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"right\"><tbody><tr><td><h2><span style=\"color: #4ab5ac;\"><a href=\"[site_url]?mod=subscribe&amp;action=unsubscribeLead&amp;pl=sub&amp;[unsubscribe_link_params]\"><span style=\"color: #4ab5ac;\">unsubscribe&nbsp;</span></a>&nbsp;|&nbsp;&nbsp;<a href=\"SUB_STPL_SITE_URL\"><span style=\"color: #4ab5ac;\">update preference</span></a></span><span style=\"color: #4ab5ac;\"><a href=\"SUB_STPL_SITE_URL\"><span style=\"color: #4ab5ac;\">&nbsp;</span></a></span><span style=\"color: #4ab5ac;\">&nbsp;|</span><span style=\"color: #4ab5ac;\">&nbsp;&nbsp;</span><span style=\"color: #4ab5ac;\"><a href=\"SUB_STPL_SITE_URL\"><span style=\"color: #4ab5ac;\">visit website</span></a></span></h2></td></tr></tbody></table></td></tr></tbody></table>', 'stplCanvasElementText'),
			('154', '100', '558', '<p style=\"text-align: center;\">Readyshoppingcart , all rights reserved 2014</p>', 'stplCanvasElementText'),
			('155', '101', '190', '<p style=\"text-align: center;\"><a href=\"[site_url]?mod=stpl&amp;amp;action=preview&amp;amp;pl=sub&amp;amp;id=130&amp;amp;[stpl_preview_code]\"><span style=\"color: #ffffff;\">Problem viewing this email?</span></a></p>', 'stplCanvasElementText'),
			('156', '101', '367', '<p><span style=\"color: #ffffff;\"><a href=\"[site_url]?mod=stpl&amp;amp;action=preview&amp;amp;pl=sub&amp;amp;id=130&amp;amp;[stpl_preview_code]\"><span style=\"color: #ffffff;\">Online version</span></a></span></p>', 'stplCanvasElementText'),
			('157', '102', '218', '<p style=\"text-align: center;\"><span style=\"color: #1ccca9;\"><span style=\"color: #00ffff;\"><a href=\"SUB_STPL_SITE_URL\"><span style=\"color: #00ffff;\">HOME </span></a></span>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span style=\"color: #00ffff;\"><a href=\"SUB_STPL_SITE_URL\"><span style=\"color: #00ffff;\">SHOWCASE</span></a></span></span></p>', 'stplCanvasElementText'),
			('158', '102', '127', '<a href=\"SUB_STPL_SITE_URL\"><img src=\"SUB_STPL_MOD_URLimg/common/cuzto/logo.png\" title=\"logo\" alt=\"\" style=\"display: block; margin-left: auto; margin-right: auto;\"></a>', 'stplCanvasElementImage'),
			('159', '102', '207', '<p style=\"text-align: left;\"><span style=\"color: #1ccca9;\"><span style=\"color: #00ffff;\"><a href=\"SUB_STPL_SITE_URL\"><span style=\"color: #00ffff;\">ABOUT</span></a></span>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <span style=\"color: #00ffff;\"><a href=\"SUB_STPL_SITE_URL\"><span style=\"color: #00ffff;\">CONTACT</span></a></span></span></p>', 'stplCanvasElementText'),
			('160', '103', '558', '<a href=\"\" style=\"float: left;\"><img src=\"SUB_STPL_MOD_URLimg/common/cuzto/banner-600-1.jpg\" alt=\"\" title=\"banner-600-1\" style=\"display: inline;\"></a>', 'stplCanvasElementImage'),
			('161', '104', '564', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h2\" static_title_align=\"center\" static_show_content=\"excerpt\" content_styles=\"Color:#1ccca9;\" image_align=\"left\" read_more_text=\"Read More...\" read_more_styles=\"float:left; color: #1ccca9\"]', 'stplCanvasElementStaticContent'),
			('162', '105', '558', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h2\" static_title_align=\"center\" static_show_content=\"excerpt\" content_styles=\"Color:#1ccca9;\" image_align=\"left\" read_more_text=\"Read More...\" read_more_styles=\"float:left; color: #1ccca9\"]', 'stplCanvasElementStaticContent'),
			('163', '106', '564', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h2\" static_title_align=\"center\" static_show_content=\"excerpt\" content_styles=\"Color:#1ccca9;\" image_align=\"right\" read_more_text=\"Read More...\" read_more_styles=\"float:left; color: #1ccca9\"]', 'stplCanvasElementStaticContent'),
			('164', '107', '558', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h2\" static_title_align=\"center\" static_show_content=\"excerpt\" content_styles=\"Color:#1ccca9;\" image_align=\"right\" read_more_text=\"Read More...\" read_more_styles=\"float:left; color: #1ccca9\"]', 'stplCanvasElementStaticContent'),
			('165', '108', '558', '<p><strong><span style=\"color: #00ffff;\">2 COLS</span></strong></p>', 'stplCanvasElementText'),
			('166', '109', '276', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h2\" static_title_align=\"center\" static_show_content=\"excerpt\" content_styles=\"Color:#1ccca9;\" image_align=\"center\" read_more_text=\"Read More...\" read_more_styles=\"float:left; color: #1ccca9\"]', 'stplCanvasElementStaticContent'),
			('167', '109', '276', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h2\" static_title_align=\"center\" static_show_content=\"excerpt\" content_styles=\"Color:#1ccca9;\" image_align=\"center\" read_more_text=\"Read More...\" read_more_styles=\"float:left; color: #1ccca9\"]', 'stplCanvasElementStaticContent'),
			('168', '110', '558', '<p><strong><span style=\"color: #00ffff;\">3 COLS</span></strong></p>', 'stplCanvasElementText'),
			('169', '111', '182', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h2\" static_title_align=\"center\" static_show_content=\"excerpt\" content_styles=\"Color:#1ccca9;\" image_align=\"center\" read_more_text=\"Read More...\" read_more_styles=\"float:left; color: #1ccca9\"]', 'stplCanvasElementStaticContent'),
			('170', '111', '182', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h2\" static_title_align=\"center\" static_show_content=\"excerpt\" content_styles=\"Color:#1ccca9;\" image_align=\"center\" read_more_text=\"Read More...\" read_more_styles=\"float:left; color: #1ccca9\"]', 'stplCanvasElementStaticContent'),
			('171', '111', '182', '[static_content_ready static_content_post=\"SUB_STPL_RAND_POST_ID\" static_content_page=\"0\" static_title_style=\"h2\" static_title_align=\"center\" static_show_content=\"excerpt\" content_styles=\"Color:#1ccca9;\" image_align=\"center\" read_more_text=\"Read More...\" read_more_styles=\"float:left; color: #1ccca9\"]', 'stplCanvasElementStaticContent'),
			('172', '112', '558', '<p><strong><span style=\"color: #00ffff;\">Gallery 2</span></strong></p>', 'stplCanvasElementText'),
			('173', '113', '276', '<p style=\"text-align: right;\"><a href=\"\"><img class=\"aligncenter size-full wp-image-2742\" alt=\"ph1\" src=\"SUB_STPL_MOD_URLimg/common/cuzto/ph1.png\" title=\"photo 1\" width=\"260\" height=\"175\" style=\"display: inline;\"></a> <a href=\"\"><img class=\"aligncenter size-full wp-image-2743\" alt=\"ph2\" src=\"SUB_STPL_MOD_URLimg/common/cuzto/ph2.png\" title=\"photo 2\" width=\"260\" height=\"175\" style=\"display: inline;\"></a></p>', 'stplCanvasElementText'),
			('174', '113', '276', '<p><a href=\"\"><img class=\"aligncenter size-full wp-image-2744\" alt=\"ph3\" src=\"SUB_STPL_MOD_URLimg/common/cuzto/ph3.png\" title=\"photo 3\" width=\"260\" height=\"175\" style=\"display: inline;\"></a> <a href=\"\"><img class=\"aligncenter size-full wp-image-2745\" alt=\"ph4\" src=\"SUB_STPL_MOD_URLimg/common/cuzto/ph4.png\" title=\"photo 4\" width=\"260\" height=\"175\" style=\"display: inline;\"></a></p>', 'stplCanvasElementText'),
			('175', '114', '558', '<p><strong><span style=\"color: #00ffff;\">Gallery 3</span></strong></p>', 'stplCanvasElementText'),
			('176', '115', '186', '<p style=\"text-align: right;\"><a href=\"\"><img class=\"aligncenter size-full wp-image-2746\" alt=\"ph5\" src=\"SUB_STPL_MOD_URLimg/common/cuzto/ph5.png\" title=\"photo 5\" width=\"160\" height=\"108\" style=\"display: inline;\"></a> <a href=\"\"><img class=\"aligncenter size-productpreview wp-image-2747\" alt=\"ph6\" src=\"SUB_STPL_MOD_URLimg/common/cuzto/ph6.png\" title=\"photo 6\" width=\"160\" height=\"108\" style=\"display: inline;\"></a></p>', 'stplCanvasElementText'),
			('177', '115', '184', '<p style=\"text-align: center;\"><a href=\"\"><img class=\"aligncenter size-full wp-image-2747\" alt=\"ph6\" src=\"SUB_STPL_MOD_URLimg/common/cuzto/ph6.png\" title=\"photo 7\" width=\"160\" height=\"108\" style=\"display: inline;\"></a><a href=\"\"><img class=\"aligncenter size-full wp-image-2747\" alt=\"ph6\" src=\"SUB_STPL_MOD_URLimg/common/cuzto/ph6.png\" title=\"photo 8\" width=\"160\" height=\"108\" style=\"display: inline;\"></a></p>', 'stplCanvasElementText'),
			('178', '115', '182', '<p><a href=\"\"><img class=\"aligncenter size-full wp-image-2748\" alt=\"ph7\" src=\"SUB_STPL_MOD_URLimg/common/cuzto/ph7.png\" title=\"photo 9\" width=\"160\" height=\"108\" style=\"display: inline;\"></a><a href=\"\"><img class=\"aligncenter size-full wp-image-2748\" alt=\"ph7\" src=\"SUB_STPL_MOD_URLimg/common/cuzto/ph7.png\" title=\"photo 10\" width=\"160\" height=\"108\" style=\"display: inline;\"></a></p>', 'stplCanvasElementText'),
			('179', '116', '558', '<div class=\"stplCanvasSocSet\" style=\"text-align: center;\"><a href=\"https://www.facebook.com/ReadyECommerce\" target=\"_blank\" title=\"facebook\"><img style=\"padding: 10px 10px 10px 0px; display: inline;\" src=\"SUB_STPL_MOD_URLimg/soc_icons/facebook-1.png\"></a><a href=\"https://twitter.com/ReadyEcommerceW\" target=\"_blank\" title=\"twitter\"><img style=\"padding: 10px 10px 10px 0px; display: inline;\" src=\"SUB_STPL_MOD_URLimg/soc_icons/twitter-1.png\"></a><a href=\"https://plus.google.com/u/0/105222308619741800340/about\" target=\"_blank\" title=\"google_plus\"><img style=\"padding: 10px 10px 10px 0px; display: inline;\" src=\"SUB_STPL_MOD_URLimg/soc_icons/google_plus-1.png\"></a><input type=\"hidden\" class=\"stplCanvasSocSetId\" value=\"1\"></div>', 'stplCanvasElementSocial'),
			('180', '117', '558', '', 'stplCanvasElementText'),
			('181', '118', '558', '<p style=\"text-align: center;\">This message is sent to you by Company Name, Street Address, City, State 12345 USA and delivered to</p><p style=\"text-align: center;\">Copyright В© SUB_STPL_SITE_URL  , All rights reserved</p>', 'stplCanvasElementText'),
			('182', '119', '558', '<p style=\"text-align: center;\"><a href=\"[site_url]?mod=subscribe&amp;amp;action=unsubscribeLead&amp;amp;pl=sub&amp;amp;[unsubscribe_link_params]\">Unsubscribe</a></p>', 'stplCanvasElementText'),
			('183', '120', '278', '<p style=\"text-align: center;\"><em><a href=\"SUB_STPL_SITE_URL\"><img class=\"alignleft size-full wp-image-2530\" alt=\"1\" src=\"SUB_STPL_MOD_URLimg/common/1.jpg\" style=\"display: inline;\" height=\"75\" width=\"265\"></a>&nbsp;</em></p>', 'stplCanvasElementText'),
			('184', '120', '279', '<h2 style=\"text-align: right;\">&nbsp;</h2><h2 style=\"text-align: right;\">Visit Our Site</h2>', 'stplCanvasElementText'),
			('185', '121', '563', '<p><img class=\"alignleft size-full wp-image-2529\" alt=\"2\" src=\"SUB_STPL_MOD_URLimg/common/2.jpg\" style=\"display: inline;\" height=\"159\" width=\"548\"></p>', 'stplCanvasElementText'),
			('186', '122', '445', '<p>Monday 02 August | Published by Your Company. ......................................................................................</p>', 'stplCanvasElementText'),
			('187', '122', '110', '<p>Custom Sidebar ...................</p>', 'stplCanvasElementText'),
			('188', '123', '425', '<h2>This is a Featured Topic Header that you can edit easily.</h2><address>This is your article text. Say whatever you want to your recipients! It\'s easy to use this email template.</address><p>Now &nbsp;follow filler text, nibh sit amet pharetra placerat, tortor pirus condimentum lectus, at&nbsp;<a href=\"SUB_STPL_SITE_URL\">dignissim nibh</a>&nbsp;velit vitae sem. Nunc condimentum blandit tortorphasellus facilisis neque vitae purus...</p><p><a href=\"SUB_STPL_SITE_URL\">Read more</a> ..................................................................................</p><h2>Donce imperiat accumsan felis.</h2><p><a href=\"SUB_STPL_SITE_URL\"><img class=\"alignleft size-full wp-image-2536\" alt=\"7\" src=\"SUB_STPL_MOD_URLimg/common/7.jpg\" style=\"display: inline;\" height=\"195\" width=\"328\"></a></p><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore . &nbsp;</p><p><a href=\"SUB_STPL_SITE_URL\">Read more</a> ..................................................................................</p><h2>Fermentum quam-donec imperde.</h2><p><a href=\"SUB_STPL_SITE_URL\"><img class=\"size-full wp-image-2537 alignleft\" alt=\"8\" src=\"SUB_STPL_MOD_URLimg/common/8.jpg\" style=\"display: inline;\" height=\"164\" width=\"159\"></a>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p><p><a href=\"SUB_STPL_SITE_URL\">Read more</a> ....................................................................................</p>', 'stplCanvasElementText'),
			('189', '123', '130', '<p><a href=\"SUB_STPL_SITE_URL\"><img class=\"alignleft size-full wp-image-2531\" alt=\"3\" src=\"SUB_STPL_MOD_URLimg/common/3.jpg\" style=\"display: inline;\" height=\"125\" width=\"125\"></a> <a href=\"SUB_STPL_SITE_URL\"><img class=\"alignleft size-full wp-image-2532\" alt=\"4\" src=\"SUB_STPL_MOD_URLimg/common/4.jpg\" style=\"display: inline;\" height=\"125\" width=\"125\"></a> <a href=\"SUB_STPL_SITE_URL\"><img class=\"alignleft size-full wp-image-2533\" alt=\"5\" src=\"SUB_STPL_MOD_URLimg/common/5.jpg\" style=\"display: inline;\" height=\"125\" width=\"125\"></a> <a href=\"SUB_STPL_SITE_URL\"><img class=\"alignleft size-full wp-image-2534\" alt=\"6\" src=\"SUB_STPL_MOD_URLimg/common/6.jpg\" style=\"display: inline;\" height=\"125\" width=\"125\"></a></p>', 'stplCanvasElementText'),
			('190', '124', '563', '<p style=\"text-align: center;\">Your Company is Copyright by Your Corp.</p><p style=\"text-align: center;\">Your Company - 123 Some Street, City, ST 99999. Ph +1 4 1477 89 745</p><p style=\"text-align: center;\"><img class=\"alignleft size-full wp-image-2535\" alt=\"9\" src=\"SUB_STPL_MOD_URLimg/common/9.jpg\" style=\"display: inline;\" height=\"58\" width=\"553\"></p>', 'stplCanvasElementText'),
			('191', '125', '557', '<p style=\"text-align: center;\">You\'re receving this newsletter because you asked to recive updates.</p><p style=\"text-align: center;\">Having trouble reading this email? <a href=\"[site_url]?mod=stpl&amp;action=preview&amp;pl=sub&amp;stpl_id=119&amp;[stpl_preview_code]\">View it in your browser</a>.</p><p style=\"text-align: center;\">Want to be taken off the list? <a href=\"[site_url]?mod=subscribe&amp;action=unsubscribeLead&amp;pl=sub&amp;[unsubscribe_link_params]\">Unsubscribe Instantly</a>.</p>', 'stplCanvasElementText');");
	}
	static protected function _addPageToWP($post_title, $content = '', $post_parent = 0) {
		return wp_insert_post(array(
			 'post_title' => __($post_title),
			 'post_content' => $content,
			 'post_status' => 'publish',
			 'post_type' => 'page',
			 'post_parent' => $post_parent,
			 'comment_status' => 'closed'
		));
	}
	// Next 3 functions is now not used
	/**
	 * Create pages for plugin usage
	 */
   static public function createPages($defaultPagesData = array()) {
	   $defaultPagesData = empty($defaultPagesData) ? array(
		   array('page_id' => 0, 'mod' => 'subscribe', 'action' => 'getConfirmSuccessHtml', 'showFor' => 'all', 'title' => 'Subscribed successfully'),
	   ) : $defaultPagesData;
	   $toePages = @json_decode(get_option('sub_toe_pages'));
	   if(empty($toePages) || !is_array($toePages)) {
		   $toePages = array();
		   foreach($defaultPagesData as $p) {
			   $pageData = $p;
			   if(isset($p['parentTitle']) && ($parentPage = self::_getPageByTitle($p['parentTitle'], $toePages)))
				   $pageData['page_id'] = self::_addPageToWP($p['title'], $parentPage->page_id);
			   else
				   $pageData['page_id'] = self::_addPageToWP($p['title']);	
			   $toePages[] = (object) $pageData;
		   }
	   } else {
		   $existsTitles = array();
		   foreach($toePages as $i => $p) {
			   if(!isset($p->page_id)) continue;
			   $existsTitles[] = $p->title;
			   $page = get_page($p->page_id);
			   if(empty($page)) {
				   if(isset($p->parentTitle) && ($parentPage = self::_getPageByTitle($p->parentTitle, $toePages))) {
					   $toePages[ $i ]->page_id = self::_addPageToWP($p->title, $parentPage->page_id);
				   } else
					   $toePages[ $i ]->page_id = self::_addPageToWP($p->title);	
			   }
		   }
		   // Create new added after update pages
		   if(count($existsTitles) != count($defaultPagesData)) {
			   foreach($defaultPagesData as $p) {
				   if(!in_array($p['title'], $existsTitles)) {
					   $pageData = $p;
					   if(isset($p['parentTitle']) && ($parentPage = self::_getPageByTitle($p['parentTitle'], $toePages)))
						   $pageData['page_id'] = self::_addPageToWP($p['title'], $parentPage->page_id);
					   else
						   $pageData['page_id'] = self::_addPageToWP($p['title']);	
					   $toePages[] = (object) $pageData;
				   }
			   }
		   }
	   }
	   db::query("UPDATE `@__modules` SET params = '". json_encode($toePages). "' WHERE code = 'pages' LIMIT 1");
	   update_option('sub_toe_pages', json_encode($toePages));
   }
	/**
	* Return page data from given array, searched by title, used in self::createPages()
	* @return mixed page data object if success, else - false
	*/
	static private function _getPageByTitle($title, $pageArr) {
		foreach($pageArr as $p) {
			if($p->title == $title)
				return $p;
		}
		return false;
	}
	static public function setUsed() {
		update_option(SUB_DB_PREF. 'plug_was_used', 1);
	}
	static public function isUsed() {
		return (int) get_option(SUB_DB_PREF. 'plug_was_used');
	}
}
