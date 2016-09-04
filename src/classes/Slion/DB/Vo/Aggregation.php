<?php
namespace Slion\DB\Vo;

use Slim\Collection;

/**
 * Description of Autoload
 *
 * @author andares
 */
trait Aggregation {
    abstract protected static function bind(Vo\Autoload $autoload);
    abstract protected function pull(Collection $loads): self;

    protected static function unionData($collection, ...$more) {
        $autoload = array_pop($more);
        if (!($autoload instanceof Vo\Autoload)) {
            $autoload && $more[] = $autoload; // 防null
            $autoload = new Autoload();
            $more[] = $autoload;
        } else {
            $autoload && $more[] = $autoload; // 防null
        }
        static::bind($autoload);

        $temp = [];
        foreach (parent::unionData($collection, ...$more) as $vo) {
            $temp[] = $vo;
        }

        $loads = $autoload();
        foreach ($temp as $vo) {
            $vo->pull($loads);
            yield $vo;
        }
    }

}
