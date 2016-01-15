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
    /**
     * @var
     */
    private $apiName;
    /**
     * @var
     */
    private $accountNumber;
    /**
     * BaseAPI constructor.
     * @param $name
     * @param $accountNumber
     */
    public function __construct($name, $accountNumber)
    {
        $this->apiName = $name;
        $this->accountNumber;
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
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * @param mixed $accountNumber
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;
    }

}