<?php

namespace Slion\DB\Redis;
use Slion\{
    Redis,
    Meta
};
use Slion\Utils\{
    IdGenerator
};

/**
 * Description of Model
 *
 * @author andares
 */
abstract class Model extends Meta\Base implements \ArrayAccess, \Serializable, \JsonSerializable {
    use Meta\Access, Meta\Serializable, Meta\Json;
    protected static $_connection   = 'default';
    protected static $_prefix       = ':';
    protected static $_expire       = 0;

    protected $_id = null;

    public static function load($id) {
        $data   = static::redis()->get($this->makeIdForAccess($id));
        if (!$data) {
            return null;
        }
        return unserialize($data);
    }

    public function save() {
        if (static::$_expire) {
            return static::redis()->setEx(
                $this->makeIdForAccess($this->getId()),
                static::$_expire, serialize($this));
        }
        return static::redis()->set(
            $this->makeIdForAccess($this->getId()), serialize($this));
    }

    public function setId($id) {
        $this->_id = $id;
    }

    public function getId() {
        return $this->_id;
    }

    public function genId(): string {
        $generator = new IdGenerator();
        return $generator->uuid()->gmp_strval()->get();
    }

    protected function makeIdForAccess($id): string {
        return static::$_prefix . $id;
    }

    protected static function redis(): \Slion\DB\Redis\Client {
        return Redis::instance(static::$_connection);
    }
}
