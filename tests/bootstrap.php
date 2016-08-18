<?php
use Slim\{App, Container};
use Slion\{Run};

/**
 * @author andares
 */
define('IN_TEST', 1);

$GLOBALS['settings'] = require __DIR__ . '/../vendor/slion/slion/src/settings.php';
$GLOBALS['settings']['slion_settings']['utils']['config'][0] = __DIR__ . '/../src/config';
require __DIR__ . '/../vendor/autoload.php';

$run = $GLOBALS['run'];
$run->select('slion-db')

    ->setup(305, function(string $root, Run $run) {
        $app        = $run->app();
        $container  = $run->container();
        $settings   = $run->settings();

        // 配置
        $container->get('config')->addScene('default', "$root/config");
    }, 'utils setting for unit test');

$run();
