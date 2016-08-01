<?php

namespace Slion\DB;
use Illuminate\Support\Collection;
use Slion\Meta;

/**
 * Description of Vo
 *
 * @author andares
 */
abstract class Vo implements \IteratorAggregate, \ArrayAccess, \Serializable, \JsonSerializable {
    use Meta\Base, Meta\Access, Meta\Serializable, Meta\Json;

    protected static $fields_mapping = [];

    public function fill($data) {
        if (!is_array($data) && !is_object($data)) {
            throw new \InvalidArgumentException("fill data error");
        }

        foreach ($this->getDefault() as $name => $default) {
            $field = isset(static::$fields_mapping[$name]) ? static::$fields_mapping[$name] : $name;
            isset($data[$field]) && $this->$name = $data[$field];
        }
        return $this;
    }

    /**
     *
     * @param Collection $collection
     * @return array
     */
    public static function makeArray(Collection $collection): array {
        $result = [];
        foreach ($collection as $row) {
            $vo = new static($row->toArray());
            /* @var $vo self */
            $result[] = $vo->confirm()->toArray();
        }
        return $result;
    }
}
