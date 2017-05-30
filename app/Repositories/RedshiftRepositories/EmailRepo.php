<?php

namespace App\Repositories\RedshiftRepositories;

use App\Models\RedshiftModels\Email;
use App\Repositories\RepoInterfaces\IRedshiftRepo;
use DB;

class EmailRepo implements IRedshiftRepo {
    
    private $model;

    public function __construct(Email $model) {
        $this->model = $model;
    }

    public function loadEntity($entity) {
        $sql = <<<SQL
copy emails
from 's3://mt2-listprofile-export/cmpte/{$entity}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '\'' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);
    }

    public function clearAndReloadEntity($entity) {
        DB::connection('redshift')->statement("TRUNCATE emails_staging");

        $sql = <<<SQL
copy emails_staging
from 's3://mt2-listprofile-export/cmpte/{$entity}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '\'' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);

        DB::connection('redshift')->transaction(function() {
            DB::connection('redshift')->statement("DELETE FROM
                emails
            USING
                emails_staging
            WHERE
                emails.id = emails_staging.id");

            DB::connection('redshift')->statement("INSERT INTO emails 
                SELECT * FROM emails_staging");
        });

        DB::connection('redshift')->statement("TRUNCATE emails_staging");

    }

    public function getDistribution() {
        $output = [];

        $result = $this->model
                    ->selectRaw("round(id / 1000000) as million, COUNT(*) as total")
                    ->groupBy(DB::raw('round(id / 1000000)'))
                    ->get();

        foreach($result as $row) {
            $output[$row->million] = $row->total;
        }

        return $output;
    }

    public function getRandomSample($number) {
        // So this is actually quite efficient and fast in redshift
        return $this->model->inRandomOrder()->take($number)->get();
    }
}