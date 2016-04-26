<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 4/18/16
 * Time: 3:32 PM
 */

namespace App\Services\API;


class RelevantToolsApi extends EspBaseAPI
{
    CONST ESP_NAME = "RelevantTools";
    public function __construct($espAccountId)
    {
        parent::__construct(self::ESP_NAME, $espAccountId);
    }

    public function sendApiRequest()
    {
       throw new \Exception("RT does not use the API");
    }


}