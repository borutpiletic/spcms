<?php
error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


// Bootstraping SimplCMS application
// and include necessary resources
include_once 'core/Autoloader.php';
include_once 'core/Application.php';
include_once 'core/SimplCMS.php';

// Register core class autoloading
spcms\core\Autoloader::register();

// Run CMS application
SimplCMS::app()->run();