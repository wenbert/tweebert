<?php
date_default_timezone_set('Asia/Manila');
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Define application environment INI
defined('APPLICATION_INI')
    || define('APPLICATION_INI', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_INI') : 'You Must Define A APPLICATION_INI in .htaccess'));


// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

if(APPLICATION_ENV == 'testing' OR APPLICATION_ENV == 'development') {
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    ini_set('xdebug.collect_vars', 'on');
    ini_set('xdebug.var_display_max_data', 50000);
    ini_set('xdebug.var_display_max_depth',10);
    ini_set('xdebug.collect_params', '4');
    ini_set('xdebug.dump_globals', 'on');
    ini_set('xdebug.dump.SERVER', 'REQUEST_URI');
    ini_set('xdebug.show_local_vars', 'on');

    /* Enable XDebug stack traces */
    ini_set('xdebug.auto_trace', 1);
    ini_set('xdebug.var_display_max_depth', 100);
    ini_set('xdebug.var_display_max_children', 500);
}

/** Zend_Application */
require_once 'Zend/Application.php';  

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();