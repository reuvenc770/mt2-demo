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
trait PaginateMT1
{
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

    public function getPaginatedJson($pageNumber, $perPage, $params = null){
        $recordsCacheKey = "{$this->pageName}.{$pageNumber}.{$perPage}";
        $pageCountCacheKey = "{$this->pageName}.pageCount.{$perPage}";
        $timeout = env("CACHETIMEOUT",60);

        if(Cache::tags( $this->pageName )->has($recordsCacheKey)){
            return [
                "pageCount" => Cache::tags( $this->pageName )->get($pageCountCacheKey) ,
                "records" => Cache::tags( $this->pageName )->get($recordsCacheKey)
            ];
        } else {
            $records = collect( json_decode( $this->getJson( $this->pageName , $params ) , true ) );

            $chunkedList = $records->chunk( $perPage );
            $pageCount = count( $chunkedList );
            $currentResponse = [
                "pageCount" => $pageCount ,
                "records" => []
            ];

            Cache::tags( $this->pageName )->put( $pageCountCacheKey , $pageCount , $timeout );

            foreach ( $chunkedList as $pageIndex => $chunk ) {
                $currentPageNumber = $pageIndex + 1;

                if ( $currentPageNumber == $pageNumber ) $currentResponse[ 'records' ] = $chunk;

                Cache::tags( $this->pageName )->put( "{$this->pageName}.{$currentPageNumber}.{$perPage}" , $chunk , $timeout );
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