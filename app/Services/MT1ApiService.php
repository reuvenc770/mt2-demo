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

    public function getPaginatedJson($pageName, $pageNumber, $perPage, $params){
        $cacheKey = "{$pageName}.{$pageNumber}.{$perPage}";
        $timeout = env("CACHETIMEOUT",60);

        if($this->cache->has($cacheKey)){
            return $this->cache->get($cacheKey);
        } else {
            $params['currentPage'] = $pageNumber;
            $params['perPage'] = $perPage;
            $returnJson = $this->getJson($pageName,$params);
            $this->cache->tags($pageName)->add($cacheKey,$returnJson,$timeout);
            return $returnJson;
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