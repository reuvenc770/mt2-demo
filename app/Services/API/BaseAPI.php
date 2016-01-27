<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/14/16
 * Time: 9:03 PM
 */

namespace App\Services\API;


use App\Services\Interfaces\IReportService;
use App\Events\RawReportDataWasInserted;
use Illuminate\Support\Facades\Event;
/**
 * Class BaseAPI
 * @package App\Services\API
 */
class BaseAPI implements  IReportService
{

    private $apiName;
    private $accountName;
    protected $reportRepo;

    public function __construct($name, $accountName)
    {
        $this->apiName = $name;
        $this->accountName = $accountName;
    }

    public function insertCsvRawStats($reports){
        $arrayReportList = array();
        foreach ($reports as $report) {

            try {
                $this->reportRepo->insertStats($this->getAccountName(), $report);
            } catch (\Exception $e){
                throw new \Exception($e->getMessage());
            }

            $arrayReportList[] = $report;
        }

        Event::fire(new RawReportDataWasInserted($this->getApiName(),$this->getAccountName(), $arrayReportList));
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
     * @param mixed $accountName
     */
    public function setAccountName($accountName)
    {
        $this->accountName = $accountName;
    }

}