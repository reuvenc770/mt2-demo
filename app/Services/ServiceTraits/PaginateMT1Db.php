<?php

namespace App\Services\ServiceTraits;

use Log;
use Cache;
use App\Services\ServiceTraits\PaginationCache;
use Illuminate\Pagination\LengthAwarePaginator;

trait PaginateMT1Db {
    use PaginationCache;

    public $repo;

    public function getJSON ( $repoMethod ) {
        try {
            $records = collect( $this->repo->$repoMethod() );
        } catch ( \Exception $e ) {
            Log::error( $e->getMessage() );

            return false;
        }

        return $records->toJSON();
    }

    public function getPaginatedJson ( $repoMethod , $pageNumber, $perPage ) {
        $params = [ 'method' => $repoMethod ];

        if ( $this->hasCache( $pageNumber , $perPage, $params ) ) {
            return $this->getCachedJson( $pageNumber , $perPage, $params );
        } else {
            try {
                return $this->paginateRecords( $repoMethod , $pageNumber , $perPage );
            } catch ( \Exception $e ) {
                Log::error( $e->getMessage() );
                return false;
            }
        }
    }

    public function paginateRecords ( $repoMethod , $pageNumber , $perPage ) {
        $records = collect( json_decode( $this->getJson( $repoMethod ) , true ) );
        
        $totalRecordCount = count( $records );
        $chunkedList = $records->chunk( $perPage );
        $pageCount = count( $chunkedList );

        $responsePaginator = null;
        $currentPaginator = null;
        foreach ( $chunkedList as $pageIndex => $chunk ) {
            $currentPageNumber = $pageIndex + 1;

            $currentPaginator = ( new LengthAwarePaginator(
                $chunk ,
                $totalRecordCount ,
                $perPage ,
                $currentPageNumber
            ) )->toJSON();

            if ( $currentPageNumber == $pageNumber ) {
                $responsePaginator = $currentPaginator;
            }

            $this->cachePagination( $currentPaginator , $currentPageNumber , $perPage , [ 'method' => $repoMethod ] );
        }

        return $responsePaginator;
    }

    public function getType () { return $this->repo->getType(); }
}
