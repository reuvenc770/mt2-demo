<?php

namespace App\Repositories;

use App\Models\SuppressionListSuppression;
use App\Repositories\RepoInterfaces\IAwsRepo;
use DB;

class SuppressionListSuppressionRepo implements IAwsRepo {

    private $model;

    public function __construct ( SuppressionListSuppression $model ) {
        $this->model = $model;
    }

    public function insert($row) {
        return $this->model->insert($row);
    }

    public function updateOrCreate($data) {
        $this->model->updateOrCreate(['id' => $data['id']], $data);
    }

    public function returnSuppressedWithFeedIds($emails, $listIds) {
        return $this->model
                    ->whereIn('suppression_list_id', $listIds)
                    ->whereIn('email_address', $emails)
                    ->selectRaw('email_address, GROUP_CONCAT(DISTINCT suppression_list_id SEPARATOR ",") as suppression_lists')
                    ->groupBy('email_address')
                    ->get();
    }

    public function isSuppressedInLists($emailAddress, array $listIds) {
        if (count($listIds) > 0) {
            return $this->model->where('email_address', $emailAddress)->whereIn('suppression_list_id', $listIds)->count() > 0;
        }
        else {
            return false;
        }
    }

    public function addToSuppressionList($emailAddress, $listId) {
        $emailAddress = strtolower($emailAddress);
        $lowerMd5 = md5($emailAddress);
        $upperMd5 = md5(strtoupper($emailAddress));

        $this->model->insert([
            'suppression_list_id' => $listId,
            'email_address' => $emailAddress,
            'lower_case_md5' => $lowerMd5,
            'upper_case_md5' => $upperMd5
        ]);
    }

    public function extractForS3Upload($stopPoint)
    {
        return $this->model->whereRaw("id > $stopPoint");
    }

    public function specialExtract($data) {}

    public function mapForS3Upload($row)
    {
        $pdo = DB::connection('redshift')->getPdo();
        return $pdo->quote($row->id) . ','
        . $pdo->quote($row->suppression_list_id) . ','
        . $pdo->quote($row->email_address) . ','
        . $pdo->quote($row->lower_case_md5) . ','
        . $pdo->quote($row->upper_case_md5) . ','
        . $pdo->quote($row->created_at) . ','
        . $pdo->quote($row->updated_at);
    }

    public function extractAllForS3()
    {
        return $this->model;
    }

    public function getConnection()
    {
        return $this->model->getConnection();
    }

    public function getAllQuery($lookback) {
        return $this->model->whereRaw("created_at <= CURDATE() - INTERVAL $lookback DAY")->toSql();
    }

    public function getCount($lookback) {
        return $this->model
                    ->whereRaw("created_at <= CURDATE() - INTERVAL $lookback DAY")
                    ->count();
    }

}