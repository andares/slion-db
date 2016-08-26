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

    protected static $fields_mapping = [];

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
            $field = isset(static::$fields_mapping[$name]) ? static::$fields_mapping[$name] : $name;
            isset($data[$field]) && $this->$name = $data[$field];
        }
        return $this;
    }

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
        $result     = [];
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
            $result[] = $vo->confirm()->toArray();
        }
        return $result;
    }
}
