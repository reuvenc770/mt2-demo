<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Services;

use App\Services\ServiceTraits\PaginateList;
use App\Repositories\ClientRepo;

class ClientService {
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

    public function get () {
        return $this->clientRepo->get();
    }

    public function getAll () {
        return $this->clientRepo->getAll();
    }

    public function getAllClientsArray() {
        return $this->clientRepo->getAllClientsArray();
    }

    public function getClientFeedMap () {
        return $this->clientRepo->getClientFeedMap();
    }

    public function getAccount ( $id ) {
        return $this->clientRepo->getAccount( $id );
    }

    public function getFeeds ( $id ) {
        return $this->clientRepo->getFeeds( $id );
    }
}
