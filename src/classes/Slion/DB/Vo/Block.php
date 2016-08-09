<?php

namespace Slion\DB\Vo;
use Illuminate\Support\Collection;

/**
 * Description of Block
 *
 * @author andares
 */
class Block {
    const OFFSETMODE_ID    = 1;
    const OFFSETMODE_COUNT = 2;

    private $id_field       = 'id';
    private $offset_mode    = self::OFFSETMODE_COUNT;

    private $offset;
    private $limit;
    private $array_maker;
    private $list = [];

    public function __construct(int $offset, int $limit, callable $array_maker) {
        $this->offset = $offset;
        $this->limit  = $limit;
        $this->array_maker = $array_maker;
    }

    public function setIdField(string $id_field): self {
        $this->id_field = $id_field;
        return $this;
    }

    public function offset(): int {
        return $this->offset;
    }

    public function limit(): int {
        return $this->limit + 1;
    }

    /**
     *
     * @param type $collection
     * @param \Slion\DB\Vo\callable $filter
     * @return array
     */
    public function __invoke($collection, callable $filter = null): array {
        return $this->fill($collection)->toArray($filter);
    }

    /**
     *
     * @param Collection|array $collection
     * @return \self
     */
    public function fill($collection): self {
        $array_maker = $this->array_maker;
        $this->list = array_merge($this->list, $array_maker($collection));
        return $this;
    }

    public function toArray(callable $filter = null) {
        $result = [
            'offset'    => 0,
            'has_more'  => 0,
            'list'      => [],
        ];

        $count  = 0;
        $lastid = 0;
        foreach ($this->list as $row) {
            // 自定义过滤器
            if ($filter && !$filter($row)) {
                continue;
            }

            $result['list'][] = $row;
            $count++;
            $lastid = $row[$this->id_field];

            if ($count == $this->limit) {
                break;
            }
        }

        $result['offset']   = $this->offset_mode == self::OFFSETMODE_COUNT ? $count : $lastid;
        $result['has_more'] = isset($this->list[$count]) ? 1 : 0;
        return $result;
    }
}
