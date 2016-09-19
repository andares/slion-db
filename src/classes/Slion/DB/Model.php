<?php
namespace Slion\DB;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Collection;

/**
 * Description of Model
 *
 * @author andares
 */
abstract class Model extends EloquentModel {
//    protected $dateFormat = 'Y-m-d H:i:s';
//    protected $connection = '';
//    protected $table      = '';
//    protected $primaryKey = 'id';
//    protected $dates      = ['created_at', 'updated_at'];
//    public $timestamps    = false;
//    public $incrementing  = false;

//    protected $hidden   = [];
//    protected $visible  = [];
//    protected $appends  = [];

//    protected $fillable = [
//    ];
//    protected $casts = [
//        'id'    => 'integer',
//    ];

    /**
     * @todo 暂时只支持单id字段
     * @param array $ids
     * @param string $field
     * @param callable $mod
     */
    public static function in(array $ids, string $field = null,
        callable $mod = null): Collection {

        $builder = static::query();
        $mod && $buider = $mod($builder);
        return $buider->whereIn(
            $field ?: (new static())->primaryKey, $ids)->get();
    }

    public function confirm() {
        $this->_confirm();
        return $this;
    }

    protected function _confirm() {}
}
