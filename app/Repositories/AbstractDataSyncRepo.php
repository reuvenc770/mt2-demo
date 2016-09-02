<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 9/2/16
 * Time: 2:37 PM
 */

namespace App\Repositories;

use PDO;
abstract class AbstractDataSyncRepo
{
    private $bulkRecords = array();

    public function clearBulkRecords(){
        $this->bulkRecords = [];
    }

    public function addToBulkRecords($record){
        $prepped= [];
        foreach($record as $property){
            $prepped[] = "\"".addslashes($property)."\"";
        }
        $preppedData = "(".join(',',$prepped).")";
        $this->bulkRecords[] = $preppedData;
    }

    public function isReadyToSave(){
        return (count($this->bulkRecords) == 5000) ? true : false;
    }

    public function recordsStillLeft(){
        return (count($this->bulkRecords) > 0) ? true : false;
    }

    public function getBulkRecords(){
        return $this->bulkRecords;
    }

    abstract function updateOrCreate($data);
    abstract function bulkInsert();

}