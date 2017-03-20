<?php

namespace App\Repositories\RedshiftRepositories;

use App\Models\RedshiftModels\RecordData;
use App\Repositories\RepoInterfaces\IRedshiftRepo;
use DB;

class RecordDataRepo implements IRedshiftRepo {
    
    private $model;

    public function __construct(RecordData $model) {
        $this->model = $model;
    }

    public function loadEntity($entity) {
        // We are overwriting the entire table -- if updated_at is keyed we could use that as well
        DB::connection('redshift')->statement("TRUNCATE record_data_staging");

        $sql = <<<SQL
copy record_data_staging
from 's3://mt2-listprofile-export/{$entity}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '\'' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);

        DB::connection('redshift')->transaction(function() {
            DB::connection('redshift')->statement("DELETE FROM
                record_data
            USING
                record_data_staging
            WHERE
                record_data.email_id = record_data_staging.email_id");

            DB::connection('redshift')->statement("INSERT INTO record_data SELECT * FROM record_data_staging");
        });

        DB::connection('redshift')->statement("TRUNCATE record_data_staging");
    }

    public function clearAndReloadEntity($entity) {
        $this->loadEntity($entity);
    }

    public function matches($obj) {
        $result = $this->model->find($obj->email_id);

        if ($result) {
            return ($result->is_deliverable === $obj->is_deliverable)
                && ($result->subscribe_date === $obj->subscribe_date);
        }
        else {
            return false;
        }
    }

    public function getActionDateDistribution() {
        $output = [];
        // 15 days is the attribution import shield
        $data = $this->model
                    ->selectRaw("date(updated_at) as day, sum(is_deliverable) as deliverable_count")
                    ->whereRaw("updated_at >= current_date - interval '3 DAY'")
                    ->groupBy(DB::raw('date(updated_at)'))
                    ->get();

        foreach($data as $row) {
            $output[$row->day] = $row->deliverable_count;
        }

        return $output;
    }
}