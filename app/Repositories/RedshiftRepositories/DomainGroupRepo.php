<?php

namespace App\Repositories\RedshiftRepositories;

use App\Models\RedshiftModels\DomainGroup;
use App\Repositories\RepoInterfaces\IRedshiftRepo;
use DB;

class DomainGroupRepo implements IRedshiftRepo {
    
    private $model;

    public function __construct(DomainGroup $model) {
        $this->model = $model;
    }

    public function loadEntity($entity) {        
        $sql = <<<SQL
copy domain_groups
from 's3://mt2-listprofile-export/cmpte/{$entity}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '\'' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);
    }

    public function clearAndReloadEntity($entity) {
        DB::connection('redshift')->statement("TRUNCATE domain_groups");

        $sql = <<<SQL
copy domain_groups
from 's3://mt2-listprofile-export/cmpte/{$entity}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '\'' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);
    }

    public function getCount() {
        return $this->model->count();
    }
}