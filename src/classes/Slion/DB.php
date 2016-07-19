<?php

namespace Slion;
use \Illuminate\Database\Capsule\Manager;

/**
 * Description of DB
 *
 * @author andares
 *
 * @method void beginTransaction()
 * @method void rollBack()
 * @method void commit()
 */
class DB {
    /**
     *
     * @var \Slim\Container
     */
    private $container;

    /**
     *
     * @var Manager
     */
    private $capsule;

    public function __construct(\Slim\Container $container, array $connections_conf = []) {
        $this->container = $container;

        //创建Eloquent
        $capsule = new Manager;
        foreach ($connections_conf as $name => $conf) {
            $capsule->addConnection($conf, $name);
        }
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        $this->capsule = $capsule;

        // 注册hook
        $hook = $container->get('hook');
        /* @var $hook \Slion\Hook */
        $hook->attach(\Slion\HOOK_BEFORE_RESPONSE, new DB\Vo\Autoload());
    }

    public function __call(string $name, $arguments) {
        $this->capsule->$name(...$arguments);
    }

    public static function __callStatic($method, $parameters) {
        return Manager::$method(...$parameters);
    }
}
