<?php
namespace Slion;

$this->hook->attach(HOOK_BEFORE_RESPONSE, new DB\Vo\Autoload());
