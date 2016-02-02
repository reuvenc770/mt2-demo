<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/14/16
 * Time: 1:32 PM
 */

namespace App\Services\API;

use App\Facades\EspAccount;
use SimpleXMLElement;
use App\Facades\Guzzle;
/**
 * Class BlueHornet
 * @package App\Services\API
 */
class BlueHornetApi extends EspBaseAPI
{
    CONST API_URL = "https://echo.bluehornet.com/api/xmlrpc/index.php";
    private  $apiKey;
    private  $sharedSecret;
    private $xml;

    public function __construct($name, $espAccountId)
    {
        parent::__construct($name, $espAccountId);
        $creds = EspAccount::grabApiKeyWithSecret($espAccountId);
        $this->apiKey = $creds['apiKey'];
        $this->sharedSecret = $creds['sharedSecret'];

    }

    /**
     * @param $methodName
     * @param null $dataPoints
     * @return mixed
     */
    public function buildRequest($methodName, $dataPoints = null)
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
        $this->xml = $xml->asXML();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function sendApiRequest()
    {
        return Guzzle::request('POST', self::API_URL, [
            'form_params' => [
                'data' => $this->xml,
            ]
        ]);

    }
}