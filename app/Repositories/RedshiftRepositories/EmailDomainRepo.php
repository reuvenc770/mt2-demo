<?php

namespace App\Repositories\RedshiftRepositories;

use App\Models\RedshiftModels\EmailDomain;
use App\Repositories\RepoInterfaces\IRedshiftRepo;
use DB;

class EmailDomainRepo implements IRedshiftRepo {
    
    private $model;

    public function __construct(EmailDomain $model) {
        $this->model = $model;
    }

    public function loadEntity($entity) {
        $sql = <<<SQL
copy email_domains
from 's3://mt2-listprofile-export-cmpte/{$entity}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '\'' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);
    }

    public function clearAndReloadEntity($entity) {
        DB::connection('redshift')->statement("TRUNCATE TABLE email_domains");
        
        $sql = <<<SQL
copy email_domains
from 's3://mt2-listprofile-export-cmpte/{$entity}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '\'' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);
    }

    public function getCount() {
        return $this->model->count();
    }
}