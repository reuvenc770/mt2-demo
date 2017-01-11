<?php

namespace App\Repositories\RedshiftRepositories;

use App\Models\RedshiftModels\EmailFeedAssignment;
use App\Repositories\RepoInterfaces\IRedshiftRepo;
use DB;

class DomainGroupRepo implements IRedshiftRepo {
    
    private $model;

    public function __construct(EmailFeedAssignment $model) {
        $this->model = $model;
    }

    public function loadEntity($fileName) {
        // this one needs a truncate first
        
        $sql = <<<SQL
copy domain_groups
from 's3://mt2-listprofile-export/{$fileName}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '"' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);
    }
}