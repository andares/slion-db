<?php
namespace Slion;

$hook = $container->get('hook');
/* @var $hook \Slion\Hook */
$hook->attach(HOOK_BEFORE_RESPONSE, new DB\Vo\Autoload());
