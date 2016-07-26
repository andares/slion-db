<?php

namespace Slion;

/**
 * Description of Redis
 *
 * @author andares
 */
class Redis {
    /**
     *
     * @var DB\Redis\Client[]
     */
    private static $clients = [];

    private static $default_client = 'default';


    public static function instance(string $name = null): DB\Redis\Client {
        !$name && $name = self::$default_client;

        if (!isset(self::$clients[$name])) {
            $config = cf('database/redis')[$name];
            if (!$config) {
                throw new \UnexpectedValueException("redis conf lost");
            }
            self::$clients[$name] = new DB\Redis\Client($config);
        }
        return self::$clients[$name];
    }

    public static function selectClient(string $name = null): string {
        $name && self::$default_client = $name;
        return self::$default_client;
    }

    public static function __callStatic(string $name, array $arguments) {
        return self::instance()->$name(...$arguments);
    }
}
