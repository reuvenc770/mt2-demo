<?php
/*/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/29/16
 * Time: 2:38 PM
 */

namespace App\Services;


use App\Events\NewDeployWasCreated;
use App\Repositories\DeployRepo;
use App\Repositories\MT1Repositories\EspAdvertiserJoinRepo;
use App\Services\ServiceTraits\PaginateList;
use League\Csv\Writer;
use Log;
use Event;
use Illuminate\Support\Facades\Artisan;
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

    public function getCakeAffiliates()
    {
        return $this->espAdvertiser->getCakeAffiliates();
    }

    public function getModel($searchType, $searchData)
    {

        return $this->deployRepo->getModel($searchType, $searchData);

    }

    public function insertDeploy($data)
    {
        return $this->deployRepo->insert($data);
    }

    public function getDeploy($deployId)
    {
        $deploy = $this->deployRepo->getDeploy($deployId);
        $deploy->offer_id = ['id' => $deploy->offer_id, "name" => $deploy->offer_name];
        unset($deploy->offer_name);
        return $deploy;
    }

    public function updateDeploy($data, $id)
    {
        $this->deployRepo->update($data, $id);
    }

    public function exportCsv($rows)
    {
        $rows = $this->deployRepo->retrieveRowsForCsv($rows);
        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $schema = $this->deployRepo->returnCsvHeader();

        $writer->insertOne($schema);

        foreach ($rows as $row) {
            $writer->insertOne($row->toArray());
        }
        return $writer->__toString();
    }

    public function validateDeploy($deploy)
    {
        return $this->deployRepo->validateOldDeploy($deploy);
    }

    public function massUpload($data)
    {
        return $this->deployRepo->massInsert($data);
    }

    public function deployPackages($data)
    {
        $filename = false;
        if (count($data) == 1) {
            $filename = "some name"; // CALL direct service call to create package and return file path.
        } else {
            foreach ($data as $id) {
                Event::fire(new NewDeployWasCreated($id));
            }
            Artisan::call('deploys:sendtoops', ['deploysCommaList' => join(",",$data)]);
        }

        $this->deployRepo->deployPackages($data);
        return $filename;
    }



    public function getPaginatedJson($page, $count, $params = null)
    {
        $searchType = null;
        $searchData = null;
        if ($this->hasCache($page, $count, $params)) {
            return $this->getCachedJson($page, $count, $params);
        } else {
            try {
                if (isset($params['type'])) {
                    $searchType = $params['type'];
                    $searchData = $params['data'];
                }
                $eloquentObj = $this->getModel($searchType, $searchData);

                $paginationJSON = $eloquentObj->paginate($count)->toJSON();

                $this->cachePagination(
                    $paginationJSON,
                    $page,
                    $count, $params
                );

                return $paginationJSON;
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return false;
            }
        }
    }

    public function getPendingDeploys()
    {
        return $this->deployRepo->getPendingDeploys();
    }

    public function getdeployTextDetailsForDeploys($deployIds)
    {

        $records = $this->deployRepo->getDeployDetailsByIds($deployIds)->toArray();
        return $records;
    }

    public function getHeaderRow()
    {
        return ['Send Date', 'Deploy ID', 'ESP Account', "Mailing Template", "Mailing Domain",
            "Content Domain", "Subject", "From", "Creative"];
    }
}
