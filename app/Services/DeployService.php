<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/29/16
 * Time: 2:38 PM
 */

namespace App\Services;


use App\Repositories\DeployRepo;
use App\Repositories\MT1Repositories\EspAdvertiserJoinRepo;
use App\Services\ServiceTraits\PaginateList;
use League\Csv\Writer;
class DeployService
{
    protected $deployRepo;
    protected $espAdvertiser;
    use PaginateList;
    public function __construct(DeployRepo $deployRepo, EspAdvertiserJoinRepo $repo)
    {
        $this->deployRepo = $deployRepo;
        $this->espAdvertiser = $repo;
    }

    public function getCakeAffiliates(){
        return $this->espAdvertiser->getCakeAffiliates();
    }

    public function getModel(){
        return $this->deployRepo->getModel();
    }

    public function insertDeploy($data){
        return $this->deployRepo->insert($data);
    }

    public function getDeploy($deployId){
        $deploy = $this->deployRepo->getDeploy($deployId);
        $deploy->offer_id = ['id'=> $deploy->offer_id, "name"=> $deploy->offer_name];
        unset($deploy->offer_name);
        return $deploy;
    }

    public function updateDeploy($data, $id){
        $this->deployRepo->update($data, $id);
    }

    public function exportCsv($rows){
        $rows = $this->deployRepo->retrieveRowsForCsv($rows);
        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $schema = $this->deployRepo->returnCsvHeader();

        $writer->insertOne($schema);

        foreach ($rows as $row){
            $writer->insertOne($row->toArray());
        }
        return $writer->__toString();
    }

}