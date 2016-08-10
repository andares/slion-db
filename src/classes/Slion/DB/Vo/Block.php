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
    private $filter = null;
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

    public function setFilter(callable $filter): self {
        $this->filter = $filter;
        return $this;
    }

    /**
     *
     * @param type $collection
     * @param array $more
     * @param \Slion\DB\Vo\callable $filter
     * @return array
     */
    public function __invoke($collection, ...$more): array {
        return $this->fill($collection, ...$more)->toArray();
    }

    /**
     *
     * @param Collection|array $collection
     * @param array $more
     * @return \self
     */
    public function fill($collection, ...$more): self {
        $array_maker = $this->array_maker;
        $this->list = array_merge($this->list, $array_maker($collection, ...$more));
        return $this;
    }

    public function toArray(): array {
        $result = [
            'offset'    => 0,
            'has_more'  => 0,
            'list'      => [],
        ];

        $count  = 0;
        $lastid = 0;
        foreach ($this->list as $row) {
            // 自定义过滤器
            if ($this->filter && !$this->filter($row)) {
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
