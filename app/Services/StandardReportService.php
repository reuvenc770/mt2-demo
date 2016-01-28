<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/14/16
 * Time: 11:11 PM
 */

namespace App\Services;

use App\Repositories\ReportRepo;
use App\Services\Interfaces\IReportService;

class StandardReportService implements IReportService
{
    protected $repo;
    private $apiName;
    private $espAccountId;

    public function __construct(ReportRepo $reportRepo, $apiName, $espAccountId){
       $this->repo = $reportRepo;
       $this->apiName = $apiName;
       $this->accountNumber = $espAccountId;
    }

    public function insertStandardStats($standardReport){
        $this->repo->insertStats($this->accountNumber,$standardReport);
    }

    /**
     * @return mixed
     */
    public function getEspAccountId()
    {
        return $this->getEspAccountId();
    }

    /**
     * @return mixed
     */
    public function getApiName()
    {
        return $this->apiName;
    }

}