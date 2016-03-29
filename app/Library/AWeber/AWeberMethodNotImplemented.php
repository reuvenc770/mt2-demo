<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 3/17/16
 * Time: 4:18 PM
 */

namespace App\Library\AWeber;


/**
 * AWeberMethodNotImplemented
 *
 * Thrown when attempting to call a method that is not implemented for a resource
 * / collection.  Differs from standard method not defined errors, as this will
 * be thrown when the method is infact implemented on the base class, but the
 * current resource type does not provide access to that method (ie calling
 * getByMessageNumber on a web_forms collection).
 *
 * @uses AWeberException
 * @package
 * @version $id$
 */
class AWeberMethodNotImplemented extends AWeberException {

    public function __construct($object) {
        $this->object = $object;
        parent::__construct("This method is not implemented by the current resource.");

    }
}