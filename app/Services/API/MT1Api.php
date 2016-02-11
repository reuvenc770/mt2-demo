<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/10/16
 * Time: 12:29 PM
 */

namespace App\Services\API;
use GuzzleHttp\Client;
use URL;
class MT1Api
{
    protected $guzzle;
    protected $baseUrl;
    CONST PATH = 'http://mt1bin.mtroute.com/newcgi-bin/';

    public function __construct(Client $guzzle)
    {
        $this->guzzle = $guzzle;

    }

    public function getMT1Json($page,$params = null)
    {
        $url = $this->constructUrl($page, $params);
        return $this->guzzle->get($url);
    }

    public function postMT1Json($page,$data){
        $url = $this->constructUrl($page);
        return $this->guzzle->post($url,['form_params' => $data]);
    }

    private function constructUrl($page, $params = null) {
    $queryString = false;
        if($params) {
            $queryString = http_build_query($params);
        }
        $base = self::PATH;
        return "{$base}/{$page}".($queryString ? "?{$queryString}" : "");

    }


}