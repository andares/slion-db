<?php

namespace Slion\DB;
use Illuminate\Support\Collection;

/**
 * Description of Vo
 *
 * @author andares
 */
abstract class Vo extends \Slion\Meta {
    protected static $id_mapping = '';

    public function __construct(array $data = null) {
        static::$id_mapping && isset($data[static::$id_mapping]) &&
            $data['id'] = $data[static::$id_mapping];

        parent::__construct($data);
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
