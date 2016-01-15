<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/14/16
 * Time: 9:03 PM
 */

namespace App\Services\API;


use App\Services\Interfaces\IReportService;

/**
 * Class BaseAPI
 * @package App\Services\API
 */
class BaseAPI implements  IReportService
{

    private $apiName;
    private $accountName;

    public function __construct($name, $accountName)
    {
        $this->apiName = $name;
        $this->accountName = $accountName;
    }

    /**
     * @return mixed
     */
    public function getApiName()
    {
        return $this->apiName;
    }

    /**
     * @param mixed $apiName
     */
    public function setApiName($apiName)
    {
        $this->apiName = $apiName;
    }

    /**
     * @return mixed
     */
    public function getAccountName()
    {
        return $this->accountName;
    }

    /**
     * @param mixed $accountNumber
     */
    public function setAccountName($accountName)
    {
        $this->accountName = $accountName;
    }

}