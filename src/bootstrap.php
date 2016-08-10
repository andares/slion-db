<?php
namespace Slion;
use Slim\{App, Container};

$run = $GLOBALS['run'];
/* @var $run \Slion\Run */
// setup自身
$run->add('slion-db', __DIR__)

    ->setup(60, function(string $root, Run $run) {
        $app        = $run->app();
        $container  = $run->container();
        $settings   = $run->settings();

        require "$root/dependencies.php";
        require "$root/helpers.php";
        require "$root/hooks.php";
    }, 'load dependencies')
;