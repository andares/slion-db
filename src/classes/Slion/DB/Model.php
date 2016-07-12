<?php
namespace Slion\DB;
use Illuminate\Database\Eloquent\Model as EloquentModel;

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

//    protected $fillable = [
//    ];
//    protected $casts = [
//        'id'    => 'integer',
//    ];

    public function confirm() {
        $this->_confirm();
        return $this;
    }

    protected function _confirm() {}
}
