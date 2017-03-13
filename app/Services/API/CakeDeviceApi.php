<?php

namespace App\Services\API;

use App\Services\API\CakeApi;

class CakeDeviceApi extends CakeApi {
    const ENDPOINT = "http://caridan.ampxl.net/app/websvc/cake/mt2/device.php?";

    const API_KEY = 'kf#kdlk!feI@EF';

    public function __construct() {}

    protected function constructApiUrl($data = null) {
        $data = ['api_key' => self::API_KEY];
        return self::ENDPOINT . http_build_query($data);
    }
}