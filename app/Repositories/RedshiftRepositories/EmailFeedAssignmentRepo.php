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

    public function loadEntity($fileName) {
        // this one needs a truncate first -- if updated_at is keyed we could use that as well
        DB::connection('redshift')->table('record_data')->truncate();
        
        $sql = <<<SQL
copy email_feed_assignments
from 's3://mt2-listprofile-export/{$fileName}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '"' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);
    }
}