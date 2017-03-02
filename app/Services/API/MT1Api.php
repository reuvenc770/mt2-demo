<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/10/16
 * Time: 12:29 PM
 */

namespace App\Services\API;

use App\Factories\APIFactory;
use URL;

class MT1Api
{
    protected $guzzle;
    protected $baseUrl;
    CONST PATH = 'http://mailingtool.mtroute.com:83/newcgi-bin/';
    CONST LOGIN_URL = 'http://mailingtool.mtroute.com:83/newcgi-bin/login.cgi';
    CONST USERNAME = 'achin';
    CONST PASSWORD = '@$pir3';

    public function __construct()
    {
        $this->guzzle = APIFactory::createSharedCookieGuzzleClient(); 

    }

    public function getMT1Json($page, $params = null)
    {
        $url = $this->constructUrl($page, $params);
        return $this->guzzle->get($url);
    }

    public function postMT1Json($page, $data, $file = null)
    {
        $url = $this->constructUrl($page);
        $fileParam = [];

        if ($file) {
            $fileParam = [ 'multipart' => [
                [
                    "name" => $data[ 'name' ] ,
                    "filename" => $data[ 'filename' ] ,
                    "contents" => ( file_exists( $file ) ? fopen( $file , 'r' ) : $file )
                ]
            ] ];

            foreach ( $data as $key => $value ) {
                if ( in_array( $key , [ 'name' , 'filename' ] ) ) {
                    continue;
                }

                $fileParam[ 'multipart' ][] = [ "name" => $key , "contents" => $value ];
            }

            $params = $fileParam;
        } else {
            $params = array_merge($fileParam, [
                'form_params' => $data
            ]);
        }

        $response = $this->guzzle->post(
            self::LOGIN_URL ,
            [ 'form_params' => [ 'username' => self::USERNAME , 'password' => self::PASSWORD ] ]
        );

        return $this->guzzle->post($url,$params);
    }

    private function constructUrl($page, $params = null)
    {
        $queryString = false;
        if ($params) {
            $queryString = http_build_query($params);
        }
        $base = self::PATH;
        return "{$base}/{$page}" . ($queryString ? "?{$queryString}" : "");

    }


}
