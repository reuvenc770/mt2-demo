<?php

namespace App\Services;
use App\Repositories\EmailActionsRepo;
use App\Repositories\DeployRecordRerunRepo;


class CheckDeployService {

    private $actionsRepo;
    private $rerunRepo;
    private $lookback;
    const THRESHOLD = -0.075;

    public function __construct(EmailActionsRepo $actionsRepo, DeployRecordRerunRepo $rerunRepo) {
        $this->actionsRepo = $actionsRepo;
        $this->rerunRepo = $rerunRepo;
        $this->lookback = $lookback;
    }

    public function run($lookback) {
        $campaigns = $this->actionsRepo->pullIncompleteDeploys($lookback);

        foreach ($campaigns as $campaign) {
            $data = $this->mapToRerunTable($campaign);
            $this->rerunRepo->insert($data);
        }
    }


    private function mapToRerunTable($row) {
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