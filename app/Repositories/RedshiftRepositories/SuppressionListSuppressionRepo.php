<?php

namespace App\Repositories\RedshiftRepositories;

use App\Models\RedshiftModels\SuppressionListSuppression;
use App\Repositories\RepoInterfaces\IRedshiftRepo;
use DB;

class SuppressionListSuppressionRepo implements IRedshiftRepo
{
    private $model;

    public function __construct(SuppressionListSuppression $model) {
        $this->model = $model;
    }

    public function loadEntity($entity) {
        $sql = <<<SQL
copy suppression_global_orange
from 's3://mt2-listprofile-export/cmpte/{$entity}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '\'' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);
    }

    public function clearAndReloadEntity($entity) {
        DB::connection('redshift')->statement("TRUNCATE suppression_list_suppression");

        $sql = <<<SQL
copy suppression_global_orange
from 's3://mt2-listprofile-export/cmpte/{$entity}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '\'' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);

    }

    public function getCount($maxLookback) {
        return $this->model
                    ->whereRaw("created_at <= current_date - interval '$maxLookback DAY'")
                    ->count();
    }

    public function insertIfNotNew(array $row) {
        $this->model->updateOrCreate(['email_address' => $row['email_address']], $row);
    }
}