<?php

namespace App\Repositories\RedshiftRepositories;

use App\Models\RedshiftModels\Client;
use App\Repositories\RepoInterfaces\IRedshiftRepo;
use DB;

class ClientRepo implements IRedshiftRepo {
    
    private $model;

    public function __construct(Client $model) {
        $this->model = $model;
    }

    public function loadEntity($entity) {
        $sql = <<<SQL
copy clients
from 's3://mt2-listprofile-export/{$entity}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '"' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);
    }

    public function clearAndReloadEntity($entity) {
        DB::connection('redshift')->table('clients')->truncate();
        
        $sql = <<<SQL
copy clients
from 's3://mt2-listprofile-export/{$entity}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '"' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);
    }
}