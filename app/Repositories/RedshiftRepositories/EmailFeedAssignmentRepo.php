<?php

namespace App\Repositories\RedshiftRepositories;

use App\Models\RedshiftModels\EmailFeedAssignment;
use App\Repositories\RepoInterfaces\IRedshiftRepo;
use DB;

class EmailFeedAssignmentRepo implements IRedshiftRepo {
    
    private $model;

    public function __construct(EmailFeedAssignment $model) {
        $this->model = $model;
    }

    public function loadEntity($entity) {
        DB::connection('redshift')->statement("TRUNCATE email_feed_assignments_staging");
        
        $sql = <<<SQL
copy email_feed_assignments_staging
from 's3://mt2-listprofile-export/{$entity}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '\'' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);

        DB::connection('redshift')->transaction(function() {
            DB::connection('redshift')->statement("DELETE FROM
                email_feed_assignments
            USING
                email_feed_assignments_staging
            WHERE
                email_feed_assignments.email_id = email_feed_assignments_staging.email_id");

            DB::connection('redshift')->statement("INSERT INTO email_feed_assignments 
                SELECT * FROM email_feed_assignments_staging");
        });

        DB::connection('redshift')->statement("TRUNCATE email_feed_assignments_staging");
    }

    public function clearAndReloadEntity($entity) {
        $this->loadEntity($entity);
    }

    public function getAttributionDist() {
        $output = [];

        $result = $this->model
                    ->selectRaw('feed_id, COUNT(*) as total')
                    ->groupBy('feed_id')
                    ->get();

        foreach($result as $row) {
            $output[$row->feed_id] = $row->total;
        }

        return $output;
    }

    public function getRandomSample($number) {
        // So this is actually quite efficient and fast in redshift
        return $this->model->inRandomOrder()->take($number)->get();
    }
}