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
        $data   = static::redis()->get(static::makeIdForAccess($id));
        if (!$data) {
            return null;
        }
        $model = unserialize($data);
        $model->setId($id);
        return $model;
    }

    public function save() {
        $id = $this->getId();
        if (!$id) {
            throw abort(new \RuntimeException('redis model need id to save'));
        }
        $key    = static::makeIdForAccess($id);
        $data   = serialize($this);

        if (static::$_expire) {
            return static::redis()->setEx($key, static::$_expire, $data);
        }
        return static::redis()->set($key, $data);
    }

    public function delete(): int {
        $id = $this->getId();
        if (!$id) {
            throw abort(new \RuntimeException('redis model need id to delete'));
        }
        return static::deleteByIds($id);
    }

    public static function deleteByIds(...$ids): int {
        $keys = [];
        foreach ($ids as $id) {
            $keys[] = static::makeIdForAccess($id);
        }
        return static::redis()->del($keys);
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

    protected static function makeIdForAccess($id): string {
        return static::$_prefix . $id;
    }

    protected static function redis(): \Slion\DB\Redis\Client {
        return Redis::instance(static::$_connection);
    }
}
