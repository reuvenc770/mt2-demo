<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\EspAdvertiserJoin;
use DB;

class EspAdvertiserJoinRepo {
    protected $model;

    public function __construct (EspAdvertiserJoin $model) {
        $this->model = $model;
    }

    public function getByDeployId($deployId) {
        return $this->model
                    ->where('subAffiliateID', $deployId)
                    ->get();
    }

    public function getBySendDate($date) {
        return $this->model
                    ->where('sendDate', $date)
                    ->get();
    }

    public function getUpdatedFrom($date) {
        return $this->model
                    ->select('subAffiliateID as deploy_id', 'creativeID as creative_id', 'subjectID as subject_id', 'fromID as from_id')
                    ->where('lastUpdated', '>=', $date)
                    ->get();
    }

    public function pullForSync($lookback) {
        return $this->model
                    ->join('ESP as e', 'EspAdvertiserJoin.espID', '=', 'e.espID')
                    ->where('lastUpdated', '>=', DB::raw("CURDATE() - INTERVAL $lookback DAY"))
                    ->select(DB::raw("EspAdvertiserJoin.*, espName"));
    }

    public function getCakeAffiliates(){
        return $this->model->distinct()->get(['affiliateID']);

    }
}