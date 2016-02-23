<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/23/16
 * Time: 10:15 AM
 */

namespace App\Services\ServiceTraits;

use Log;
use Cache;
use App\Services\ServiceTraits\PaginationCache;
use Illuminate\Pagination\LengthAwarePaginator;

trait PaginateMT1
{
    use PaginationCache;

    public $api;
    public $response;
    public $pageName;
    public function getJson($page, $params = null)
    {
        $page = $page . ".cgi";
        try {
            $this->response = $this->api->getMT1Json($page, $params);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
        return $this->processGuzzle($page, $params);

    }

    public function getPaginatedJson ( $pageNumber, $perPage , $params = null ) {
        if ( $this->hasCache( $pageNumber , $perPage ) ) {
            return $this->getCachedJson( $pageNumber , $perPage );
        } else {
            try {
                return $this->paginateRecords( $pageNumber , $perPage , $params );
            } catch ( \Exception $e ) {
                Log::error( $e->getMessage() );
                return false;
            }
        }
    }

    public function paginateRecords ( $pageNumber , $perPage , $params ) {
            $records = collect( json_decode( $this->getJson( $this->pageName , $params ) , true ) );
            
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

                $this->cachePagination( $currentPaginator , $currentPageNumber , $perPage );
            }

            return $responsePaginator;
    }

    public function postForm($page, $data)
    {
        $page = $page . ".cgi";
        try {
            $this->response = $this->api->postMT1Json($page, $data);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
        return $this->processGuzzle($page, $data);

    }

    private function processGuzzle($page, $params = null)
    {
        if ($this->response->getStatusCode() != 200) {
            Log::error("MT1 RETURNED {$this->response->getStatusCode()} for {$page} with {$params} params");
            return false;
        }
        return $this->response->getBody()->getContents();
    }
}
