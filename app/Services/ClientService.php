<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Services;

use App\Services\ServiceTraits\PaginateList;
use App\Repositories\ClientRepo;

class CilentService {
    use PaginateList;

    protected $clientRepo;

    public function __construct ( ClientRepo $clientRepo ) {
        $this->clientRepo = $clientRepo;
    }
    
    public function getModel () {
        return $this->clientRepo->getModel();
    }

    public function updateOrCreate ( $data ) {
        $this->clientRepo->updateOrCreate( $data );
    }

    public function getAll () {
        return $this->clientRepo->getAll();
    }
}
