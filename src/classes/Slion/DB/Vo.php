<?php

namespace Slion\DB;
use Illuminate\Support\Collection;

/**
 * Description of Vo
 *
 * @author andares
 */
abstract class Vo extends \Slion\Meta {
    /**
     *
     * @param Collection $collection
     * @return array
     */
    public static function makeArray(Collection $collection): array {
        $result = [];
        $class  = get_called_class();
        foreach ($collection as $row) {
            $vo = new $class($row->toArray());
            /* @var $vo self */
            $result[] = $vo->confirm();
        }
        return $result;
    }
}
