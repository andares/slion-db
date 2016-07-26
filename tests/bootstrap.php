<?php
/**
 * @author andares
 */
define('IN_TEST', 1);

$GLOBALS['settings'] = require __DIR__ . '/../vendor/slion/slion/src/settings.php';
$GLOBALS['settings']['slion_settings']['utils']['config'][0] = __DIR__ . '/../src/config';
require __DIR__ . '/../vendor/autoload.php';

