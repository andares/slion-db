<?php
namespace Slion\DB\Vo;

use Slion\Http\{Dispatcher, Response};
use Slim\Collection;

/**
 * Description of Autoload
 *
 * @author andares
 */
class Autoload {

    /**
     * 注意这个是全局的
     * @var array
     */
    private static $mask = [];

    /**
     *
     * @var array
     */
    private static $ids = [];

    /**
     *
     * @var array
     */
    private static $loaded_ids = [];

    public static function add(string $class, int $method, ...$ids) {
        !isset(self::$ids[$class][$method]) && self::$ids[$class][$method] = [];
        self::$ids[$class][$method] = array_unique(
            array_merge(self::$ids[$class][$method],
                count($ids) > 1 ? [$ids] : $ids),
            \SORT_REGULAR);
    }

    public static function setMask(string $class, $mask = null) {
        if (is_array($mask)) {
            self::$mask[$class] = $mask;
        } else {
            unset(self::$mask[$class]);
        }
    }

    public static function getMask(string $class) {
        return self::$mask[$class] ?? null;
    }

    public static function cleanMask() {
        self::$mask = [];
    }

    public function __invoke(\Slion\Run $run, Response $response, ...$args) {
        $collection = new Collection;

        do {
            list($class, $method, $ids) = $this->fetchIds();
            if ($class && $ids) {
                $name = $class::getName();
                if (isset($collection[$name])) {
                    $appender = function(array $list) use ($name) {
                        foreach ($list as $id => $row) {
                            if (isset($this->data[$name][$id])) {
                                continue;
                            }
                            $this->data[$name][$id] = $row;
                        }
                    };
                    $appender->call($collection,
                        $class::autoloadHandler($ids, $method));
                } else {
                    $collection[$name] = $class::autoloadHandler($ids, $method);
                }
            }
        } while($class);

        $response->setChannelData('autoload', $collection);
        self::cleanMask();
        self::$loaded_ids = [];
    }

    /**
     * 防重复
     * @param string $class
     * @param array $ids
     * @return array
     */
    private function getIdsWithoutLoaded(string $class,
        int $method, array $ids): array {

        if (isset(self::$loaded_ids[$class][$method])) {
            $ids = array_diff($ids, self::$loaded_ids[$class][$method]);
            self::$loaded_ids[$class][$method] = array_merge(
                self::$loaded_ids[$class][$method], $ids);
            return $ids;
        }

        self::$loaded_ids[$class][$method] = $ids;
        return $ids;
    }

    private function fetchIds(): array {
        $class = key(self::$ids);
        if (!$class) {
            return [null, null, null];
        }

        // 这里默认method层与class同步
        $method = key(self::$ids[$class]);
        $ids    = $this->getIdsWithoutLoaded($class, $method,
            self::$ids[$class][$method]);
        unset(self::$ids[$class][$method]);
        if (!self::$ids[$class]) {
            unset(self::$ids[$class]);
        }

        return [$class, $method, $ids];
    }
}
