<?php

namespace App\Services;
use App\Repositories\EmailActionsRepo;
use App\Repositories\DeployRecordRerunRepo;
use App\Services\AbstractEtlService;


class CheckDeployService extends AbstractEtlService {

    const THRESHOLD = -0.075;

    public function __construct(EmailActionsRepo $actionsRepo, DeployRecordRerunRepo $rerunRepo) {
        parent::__construct($actionsRepo, $rerunRepo);
    }

    public function extract($lookback) {
        $this->data = $this->sourceRepo->pullIncompleteDeploys($lookback);
    }

    protected function transform($row) {
        return [
            'deploy_id' => $row->deploy_id,
            'delivers' => $this->mark($row->delivers_diff),
            'opens' => $this->mark($row->opens_diff),
            'clicks' => $this->mark($row->clicks_diff),
            /*
                Currently unused
            'unsubs' = $this->mark($row['unsubs']),
            'complaints' = $this->mark($row['complaints']),
            'bounces' = $this->mark($row['bounces']),
            */
        ];
    }

    private function mark($value) {
        // Returns 1 if below threshold, otherwise returns 0
        return (int)($value < self::THRESHOLD);
    }
}