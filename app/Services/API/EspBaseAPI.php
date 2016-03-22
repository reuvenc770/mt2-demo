<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/14/16
 * Time: 9:03 PM
 */

namespace App\Services\API;


use App\Services\Interfaces\IReportService;
use App\Services\Interfaces\IApi;
use App\Events\RawReportDataWasInserted;
use Illuminate\Support\Facades\Event;
/**
 * Class EspBaseAPI
 * @package App\Services\API
 */
abstract class EspBaseAPI implements  IReportService, IApi
{

    private $apiName;
    protected $espAccountId;
    protected $reportRepo;

    public function __construct($name, $espAccountId)
    {
        $this->apiName = $name;
        $this->espAccountId = $espAccountId;
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
    public function getEspAccountId()
    {
        return $this->espAccountId;
    }

    /**
     * @param mixed $accountName
     */
    public function setEspAccountId($espAccountId)
    {
        $this->espAccountId = $espAccountId;
    }

    //helper functions
    public function reduce_array($array, $keys) {
        $res=array(); // Our result array
        foreach($array as $v1){ // Loop thru original array
            $t1=array(); // Our new inner array
            foreach($v1 as $k2=>$v2){ // Loop thru original inner array
                if(in_array($k2,$keys)) $t1[$k2]=$v2; //If inside inner array store
            }
            $res[]=$t1; //Add to result array
        }
        return($res);
    }

}