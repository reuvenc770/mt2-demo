<?php

namespace App\Repositories\RedshiftRepositories;

use App\Models\RedshiftModels\SuppressionGlobalOrange;
use App\Repositories\RepoInterfaces\IRedshiftRepo;
use DB;

class SuppressionGlobalOrangeRepo implements IRedshiftRepo {
    
    private $model;

    public function __construct(SuppressionGlobalOrange $model) {
        $this->model = $model;
    }

    public function loadEntity($entity) {
        $sql = <<<SQL
copy suppression_global_orange
from 's3://mt2-listprofile-export/{$entity}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '\'' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);
    }

    public function clearAndReloadEntity($entity) {
        DB::connection('redshift')->statement("TRUNCATE suppression_global_orange_staging");

        $sql = <<<SQL
copy suppression_global_orange_staging
from 's3://mt2-listprofile-export/{$entity}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '\'' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);

        DB::connection('redshift')->transaction(function() {
            DB::connection('redshift')->statement("DELETE FROM
                suppression_global_orange
            USING
                suppression_global_orange_staging
            WHERE
                suppression_global_orange.email_address = suppression_global_orange_staging.email_address");

            DB::connection('redshift')->statement("INSERT INTO suppression_global_orange 
                SELECT * FROM suppression_global_orange_staging");
        });

        DB::connection('redshift')->statement("TRUNCATE suppression_global_orange_staging");

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