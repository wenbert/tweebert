<?php 
error_reporting( E_ALL | E_STRICT );
date_default_timezone_set('Asia/Manila');

define('BASE_PATH', realpath(dirname(__FILE__) . '/../../'));
define('APPLICATION_PATH', BASE_PATH . '/application');


// Include path
set_include_path(
    '.'
    . PATH_SEPARATOR . BASE_PATH . '/library'
    . PATH_SEPARATOR . get_include_path()
);

// Define application environment
define('APPLICATION_ENV', 'testing');

// Define application environment INI
define('APPLICATION_INI','/htdocs/twitter/application/configs/application.ini');


/** Zend_Application */
require_once 'Zend/Application.php';  

// Create application, bootstrap, and run
            
require_once 'controllers/ControllerTestCase.php';
