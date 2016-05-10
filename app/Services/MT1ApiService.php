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
        $recordsCacheKey = "{$pageName}.{$pageNumber}.{$perPage}";
        $pageCountCacheKey = "{$pageName}.pageCount.{$perPage}";
        $timeout = env("CACHETIMEOUT",60);

        if(Cache::tags( $pageName )->has($recordsCacheKey)){
            return [
                "pageCount" => Cache::tags( $pageName )->get($pageCountCacheKey) ,
                "records" => Cache::tags( $pageName )->get($recordsCacheKey)
            ];
        } else {
            $records = collect( json_decode( $this->getJson( $pageName , $params ) , true ) );

            $chunkedList = $records->chunk( $perPage );
            $pageCount = count( $chunkedList );
            $currentResponse = [
                "pageCount" => $pageCount ,
                "records" => []
            ];

            Cache::tags( $pageName )->put( $pageCountCacheKey , $pageCount , $timeout );

            foreach ( $chunkedList as $pageIndex => $chunk ) {
                $currentPageNumber = $pageIndex + 1;

                if ( $currentPageNumber == $pageNumber ) $currentResponse[ 'records' ] = $chunk;
                
                Cache::tags( $pageName )->put( "{$pageName}.{$currentPageNumber}.{$perPage}" , $chunk , $timeout );
            }

            return $currentResponse;
        }
    }

    public function flushPaginatedCache ( $pageName ) {
        Cache::tags( $pageName )->flush();
    }

    public function postForm($page, $data, $file = null)
    {
        $page = $page . ".cgi";
        try {
            $this->response = $this->api->postMT1Json($page, $data, $file);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $e->getMessage();
            return false;
        }
        return $this->processGuzzle($page, $data);

    }

    public function postFormWithFile($page, $data,$file)
    {
       return $this->postForm($page,$data,$file);
    }

    private function processGuzzle($page, $params = null)
    {
        if ($this->response->getStatusCode() != 200) {
            Log::error("MT1 RETURNED {$this->response->getStatusCode()} for {$page} with {$params} params");
            return $this->response->getBody()->getContents();
        }
        return $this->response->getBody()->getContents();
    }

}
