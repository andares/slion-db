<?php
$run = $GLOBALS['run'];
/* @var $run \Slion\Run */
$run->setup('slion-db', new class(__DIR__) extends Slion\Init {
    public function head(\Slim\App $app, \Slim\Container $container, array $settings) {
        require "$this->root/dependencies.php";
        require "$this->root/helpers.php";
        require "$this->root/hooks.php";
    }
});
