<?php

namespace App\Repositories\RedshiftRepositories;

use App\Models\RedshiftModels\Email;
use App\Repositories\RepoInterfaces\IRedshiftRepo;

class EmailRepo implements IRedshiftRepo {
    
    private $model;

    public function __construct(Email $model) {
        $this->model = $model;
    }

    public function loadEntity($fileName) {
        $sql = <<<SQL
copy emails
from 's3://mt2-listprofile-export/{$fileName}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '"' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);
    }
}