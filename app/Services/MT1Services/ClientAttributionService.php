<?php

namespace App\Services\MT1Services;

use App\Repositories\MT1Repositories\ClientAttributionRepo;
use App\Services\ServiceTraits\PaginationCache;
use Log;

class ClientAttributionService {
    use PaginationCache;

    protected $clientAttributionRepo;

    public function __construct( ClientAttributionRepo $repo ) {
        $this->clientAttributionRepo = $repo;
    }

    public function getType () {
        return 'clientattribution';
    }

    public function getClientList ( $page , $count ) {
        if ( $this->hasCache( $page , $count ) ) {
            return $this->getCachedJson( $page , $count );
        } else {
            try {
                $clients = $this->clientAttributionRepo->getClientsByAttribution( $count );

                $this->cachePagination(
                    $clients ,
                    $page ,
                    $count
                );

                return $clients;
            } catch ( \Exception $e ) {
                Log::error( $e->getMessage() );
                return false;
            }
        }
    }

    public function setAttribution ( $id , $level ) {
        return $this->clientAttributionRepo->setAttribution( $id , $level );
    }

    public function deleteAttribution ( $id ) {
        return $this->clientAttributionRepo->deleteAttribution( $id );
    }
}
