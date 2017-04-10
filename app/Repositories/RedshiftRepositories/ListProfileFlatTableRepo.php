<?php

namespace App\Repositories\RedshiftRepositories;

use App\Models\RedshiftModels\ListProfileFlatTable;
use App\Models\ListProfileFlatTable as CmpListProfileFlatTable;
use App\Repositories\RepoInterfaces\IRedshiftRepo;
use DB;

class ListProfileFlatTableRepo implements IRedshiftRepo {
    
    private $model;

    public function __construct(ListProfileFlatTable $model) {
        $this->model = $model;
    }

    public function loadEntity($entity) {
        $clear = "TRUNCATE list_profile_flat_table_staging";
        DB::connection('redshift')->statement($clear);

        $sql = <<<SQL
copy list_profile_flat_table_staging
from 's3://mt2-listprofile-export/{$entity}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '\'' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);

        DB::connection('redshift')->transaction(function() {
            DB::connection('redshift')->statement("DELETE FROM
                list_profile_flat_table
            USING
                list_profile_flat_table_staging
            WHERE
                list_profile_flat_table.email_id = list_profile_flat_table_staging.email_id 
                AND list_profile_flat_table.deploy_id = list_profile_flat_table_staging.deploy_id 
                AND list_profile_flat_table.date = list_profile_flat_table_staging.date");

            DB::connection('redshift')->statement("INSERT INTO list_profile_flat_table 
                SELECT * FROM list_profile_flat_table_staging");
        });

        DB::connection('redshift')->statement($clear);
    }

    public function clearAndReloadEntity($entity) {
        $this->loadEntity($entity);
    }

    public function optimizeDb() {
        // Re-sort and partition the tables based off of their keys
        DB::connection('redshift')->statement('VACUUM SORT ONLY');
    }

    public function getRandomSample($lookback, $size) {
        // So this is actually quite efficient and fast in redshift
        return $this->model
                    ->selectRaw("deploy_id, sum(has_click) as clicks, sum(has_open) as opens, sum(has_conversion) as conversions")
                    ->whereRaw("date >= current_date - interval '$lookback day'")
                    ->inRandomOrder()
                    ->take($size)
                    ->get();
    }
}