<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/14/16
 * Time: 1:32 PM
 */

namespace App\Services\API;

use SimpleXMLElement;
use Guzzle;

/**
 * Class BlueHornet
 * @package App\Services\API
 */
class BlueHornet
{
    public function __construct()
    {
        $this->apiKey = "ced21d9cfb0655eccf3946585d6b0fde";
        $this->sharedSecret = "bdc925fe6cbd7596dc2a5e71bc211caa";

    }

    CONST API_URL = "https://echo.bluehornet.com/api/xmlrpc/index.php";

    /**
     * @param $methodName
     * @param null $dataPoints
     * @return mixed
     */
    protected function buildRequest($methodName, $dataPoints = null)
    {
        $xml = new SimpleXMLElement('<api></api>');
        $auth = $xml->addChild('authentication');
        $auth->addChild('api_key', $this->apiKey);
        $auth->addChild('shared_secret', $this->sharedSecret);
        $auth->addChild('response_type', "xml");
        $data = $xml->addChild('data');
        $methodCall = $data->addChild('methodCall');
        $methodCall->addChild('methodName', $methodName);
        if (!empty($dataPoints)) {
            foreach ($dataPoints as $name => $value) {
                $methodCall->addChild($name, $value);
            }
        }
        return $xml->asXML();
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function sendAPIRequest($data)
    {
        return Guzzle::request('POST', self::API_URL, [
            'form_params' => [
                'data' => $data,
            ]
        ]);
    }
}