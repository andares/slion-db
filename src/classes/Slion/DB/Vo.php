<?php

namespace Slion\DB;
use Illuminate\Support\Collection;
use Slion\Meta;

/**
 * Description of Vo
 *
 * @author andares
 */
abstract class Vo extends Meta\Base implements \ArrayAccess, \Serializable, \JsonSerializable {
    use Meta\Access, Meta\Serializable, Meta\Json;

    protected static $_name = '';

    protected static $_index_field = '';

    protected static $_fields_mapping = [];

    protected static $_autoload = [
//        'vo_class' => ['id1', 'id2']
    ];

    protected static $_autoload_method = [];

    public function __construct($data = null) {
        $data && $this->fill((is_object($data) && method_exists($data, 'toArray')) ?
            $data->toArray() : $data);
    }

    public function fill($data, $excludes = []) {
        if (!is_array($data) && !is_object($data)) {
            throw new \InvalidArgumentException("fill data error");
        }

        $fields = $this->getDefault();
        if ($excludes) {
            foreach ($excludes as $name) {
                unset($fields[$name]);
            }
        }
        foreach ($fields as $name => $default) {
            $field = isset(static::$_fields_mapping[$name]) ? static::$_fields_mapping[$name] : $name;
            isset($data[$field]) && $this->$name = $data[$field];
        }
        return $this;
    }

    public static function getName(): string {
        return static::$_name ?: static::class;
    }

    /**
     * 设置autoload mask。
     *
     * 设置为数组则表示只处理哪些自动载入。如果给个空数组则不进行autoload。
     * 如果设置null则表示使用全量的autoload配置。
     *
     * mask信息会在一次完整的autoload后被清除。
     *
     * @param array|null $mask
     */
    public static function setAutoloadMask($mask = null) {
        Vo\Autoload::setMask(static::class, $mask);
    }

    /**
     * 自动载入持扩展功能
     * @param array $ids
     * @return array
     */
    public static function autoloadHandler(array $ids, int $method = 0): array {}

    public static function makeBlock(int $offset, int $limit): Vo\Block {
        return new Vo\Block($offset, $limit, function($collection, ...$more) {
            return static::makeArray($collection, ...$more);
        });
    }

    /**
     *
     * @param Collection|array $collection
     * @return array
     */
    public static function makeArray($collection, ...$more): array {
        $result = [];
        foreach (static::unionData($collection, ...$more) as $vo) {
            $result[] = $vo->toArray();
        }
        return $result;
    }

    public static function makeCollection($collection, ...$more) {
        $result = [];
        foreach (static::unionData($collection, ...$more) as $vo) {
            $row = $vo->toArray();
            if (static::$_index_field) {
                $result[$row[static::$_index_field]] = $row;
            } else {
                $result[] = $row;
            }
        }
        return $result;
    }

    protected static function unionData($collection, ...$more) {
        $more_data  = [];
        foreach ($collection as $key => $row) {
            // 多维填充
            if ($more) {
                $more_data = [];
                foreach ($more as $more_collection) {
                    $more_data[] = $more_collection[$key] ?? null;
                }
            }
            $vo = new static($row, ...$more_data);
            /* @var $vo self */
            $vo->confirm();
            static::addAutoloadIds($vo);
            yield $vo;
        }
    }

    protected static function addAutoloadIds(self $vo) {
        // 取autoload列表
        $mask = Vo\Autoload::getMask(static::class);
        if (is_array($mask)) {
            $autoload_vo_list = [];
            foreach ($mask as $class) {
                isset(static::$_autoload[$class]) &&
                    $autoload_vo_list[$class] = static::$_autoload[$class];
            }
        } else {
            $autoload_vo_list = static::$_autoload;
        }

        foreach ($autoload_vo_list as $class => $id_field) {
            $method = static::$_autoload_method[$class] ?? 0;

            if (is_array($id_field)) {
                // 支持二层数组
                // 二层数组时表示该vo中某个autoload vo class要载入多个
                if (is_array($id_field[0])) {
                    foreach ($id_field as $id_field_more) {
                        $ids = [];
                        foreach ($id_field_more as $field) {
                            if ($vo->$field === null) {
                                continue 2; // 防止填null
                            }
                            $ids[] = $vo->$field;
                        }
                        Vo\Autoload::add($class, $method, ...$ids);
                    }
                } else {
                    $ids = [];
                    foreach ($id_field as $field) {
                        $ids[] = $vo->$field;
                    }
                    Vo\Autoload::add($class, $method, ...$ids);
                }
                continue;
            }
            Vo\Autoload::add($class, $method, $vo->$id_field);
        }
    }
}
