<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/10/16
 * Time: 1:30 PM
 */

namespace App\Services;


use App\Services\API\MT1Api;
use Cache;
use Log;
class MT1ApiService
{
    protected $api;
    protected $response;
    protected $cache;

    public function __construct(MT1Api $api, Cache $cache)
    {
        $this->api = $api;
        $this->cache = $cache;
    }

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

    public function getPaginatedJson($pageName, $pageNumber, $perPage, $params = null){
        $cacheKey = "{$pageName}.{$pageNumber}.{$perPage}";
        $timeout = env("CACHETIMEOUT",60);

        if(Cache::has($cacheKey)){
            return Cache::tags( $pageName )->get($cacheKey);
        } else {
            $records = collect( json_decode( $this->getJson( $pageName , $params ) , true ) );

            $chunkedList = $records->chunk( $perPage );
            $currentResponse = [
                "pageCount" => count( $chunkedList ) ,
                "records" => []
            ];

            foreach ( $chunkedList as $pageIndex => $chunk ) {
                $currentPageNumber = $pageIndex + 1;

                if ( $currentPageNumber == $pageNumber ) $currentResponse[ 'records' ] = $chunk;
                
                Cache::tags( $pageName )->put( "{$pageName}.{$currentPageNumber}.{$perPage}" , $chunk , $timeout );
            }

            return $currentResponse;
        }
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
