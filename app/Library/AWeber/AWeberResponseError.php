<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 3/17/16
 * Time: 4:18 PM
 */

namespace App\Library\AWeber;


/**
 * AWeberResponseError
 *
 * This is raised when the server returns a non-JSON response. This
 * should only occur when there is a server or some type of connectivity
 * issue.
 *
 * @uses AWeberException
 * @package
 * @version $id$
 */
class AWeberResponseError extends AWeberException {

    public function __construct($uri) {
        $this->uri = $uri;
        parent::__construct("Request for {$uri} did not respond properly.");
    }

}