<?php
/**
 * Plugin Name: Newsletters Ready!
 * Plugin URI: http://readyshoppingcart.com/product/newsletters
 * Description: Newsletters, post notifications and autoresponders in a simple and easy way.
 * Version: 0.3.8
 * Author: NewsletterReady
 * Author URI: http://readyshoppingcart.com
 **/
    require_once(dirname(__FILE__). DIRECTORY_SEPARATOR. 'config.php');
    require_once(dirname(__FILE__). DIRECTORY_SEPARATOR. 'functions.php');
    importClassSub('dbSub');
    importClassSub('installerSub');
    importClassSub('baseObjectSub');
    importClassSub('moduleSub');
    importClassSub('modelSub');
    importClassSub('viewSub');
    importClassSub('controllerSub');
    importClassSub('helperSub');
    importClassSub('dispatcherSub');
    importClassSub('fieldSub');
    importClassSub('tableSub');
    importClassSub('frameSub');
    importClassSub('reqSub');
    importClassSub('uriSub');
    importClassSub('htmlSub');
    importClassSub('responseSub');
    importClassSub('fieldAdapterSub');
    importClassSub('validatorSub');
    importClassSub('errorsSub');
    importClassSub('utilsSub');
    importClassSub('modInstallerSub');
    importClassSub('wpUpdater');
	importClassSub('toeWordpressWidgetSub');
	importClassSub('installerDbUpdaterSub');
	importClassSub('fileuploaderSub');
	importClassSub('dateSub');

    installerSub::update();
    errorsSub::init();
    
    dispatcherSub::doAction('onBeforeRoute');
    frameSub::_()->parseRoute();
    dispatcherSub::doAction('onAfterRoute');

    dispatcherSub::doAction('onBeforeInit');
    frameSub::_()->init();
    dispatcherSub::doAction('onAfterInit');

    dispatcherSub::doAction('onBeforeExec');
    frameSub::_()->exec();
    dispatcherSub::doAction('onAfterExec');
