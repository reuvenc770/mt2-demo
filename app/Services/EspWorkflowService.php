<?php

namespace App\Services;

use App\Repositories\EspWorkflowRepo;
use App\Repositories\EspWorkflowStepRepo;
use App\Services\ServiceTraits\PaginateList;

class EspWorkflowService {
    use PaginateList;

    private $repo;
    private $stepRepo;

    public function __construct(EspWorkflowRepo $repo, EspWorkflowStepRepo $stepRepo) {
        $this->repo = $repo;
        $this->stepRepo = $stepRepo;
    }


    public function getPaginatedJson($page, $count, $params = null) {
        $searchData = null;

        if ($this->hasCache($page, $count, $params)) {
            return $this->getCachedJson($page, $count, $params);
        }
        else {
            try {
                $model = $this->getModel();
                $sort = json_decode($params['sort'], true);
                $order = 'asc';

                if (isset($sort['desc']) && true === $sort['desc']) {
                    $order = 'desc';
                }

                if ($count > 0) {
                    $paginationJson = $model->paginate($count)->toJSON();
                }
                else {
                    $recordCount = $model->count();

                    $paginationJson = json_encode( [
                        "current_page" => 1 ,
                        "last_page" => 1 ,
                        "from" => 1 ,
                        "to" => $recordCount ,
                        "total" => $recordCount ,
                        "data" => $model->get()->toArray()
                    ] );
                }

                $this->cachePagination(
                    $paginationJson,
                    $page,
                    $count,
                    $params
                );

                return $paginationJson;
            }
            catch (\Exception $e) {
                \Log::error($e->getMessage());
                return false;
            }
        }
    }

    public function getModel($options = null) {
        return $this->repo->getDisplayModel($options);
    }

    public function setStatus($id, $status) {
        try {
            $this->repo->setStatus($id, $status);
            return $id;
        }
        catch (\Exception $e) {
            return false;
        }
    }

    public function getSteps($id) {
        return $this->stepRepo->getStepsForWorkflow($id);
    }

    public function getWorkflowFeeds($id) {
        $collection = $this->repo->getFeedsForWorkflow($id);
        $output = [];

        foreach($collection as $feed) {
            $output[] = ['id' => $feed->id, 'short_name' => $feed->short_name];
        }

        return $output;
    }

    public function getName($id) {
        return $this->repo->getName($id);
    }
}