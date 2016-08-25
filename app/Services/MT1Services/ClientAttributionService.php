<?php

namespace App\Services\MT1Services;

use App\Repositories\MT1Repositories\ClientAttributionRepo;
use App\Services\ServiceTraits\PaginationCache;
use Cache;
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

    public function getFeedList ( $page , $count ) {
        if ( $this->hasCache( $page , $count ) ) {
            return $this->getCachedJson( $page , $count );
        } else {
            try {
                $feeds = $this->clientAttributionRepo->getFeedsByAttribution( $count );

                $this->cachePagination(
                    $feeds ,
                    $page ,
                    $count
                );

                return $feeds;
            } catch ( \Exception $e ) {
                Log::error( $e->getMessage() );
                return false;
            }
        }
    }

    public function flushCache () {
        Cache::tags( $this->getType() )->flush();
    }
}
