<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 3/17/16
 * Time: 4:17 PM
 */

namespace App\Library\AWeber;


/**
 * Thrown when attempting to use a resource that is not implemented.
 *
 * @uses AWeberException
 * @package
 * @version $id$
 */
class AWeberResourceNotImplemented extends AWeberException {

    public function __construct($object, $value) {
        $this->object = $object;
        $this->value = $value;
        parent::__construct("Resource \"{$value}\" is not implemented on this resource.");
    }
}