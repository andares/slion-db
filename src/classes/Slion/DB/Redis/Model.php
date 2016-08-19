<?php

namespace Slion\DB\Redis;

/**
 * Description of Model
 *
 * @author andares
 */
abstract class Model extends Meta\Base implements \ArrayAccess, \Serializable, \JsonSerializable {
    use Meta\Access, Meta\Serializable, Meta\Json;
    //put your code here
}
