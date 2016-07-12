<?php
/**
 * @author andares
 */
define('IN_TEST', 1);

$GLOBALS['settings'] = require __DIR__ . '/../src/settings.php';
require __DIR__ . '/../vendor/autoload.php';
$app = $GLOBALS['app'];
/* @var $app \Slim\App */
require __DIR__ . '/../src/dependencies.php';

//Slion\Test::init();
