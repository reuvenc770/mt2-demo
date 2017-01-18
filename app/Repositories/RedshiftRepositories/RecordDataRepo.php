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
        DB::connection('redshift')->table('record_data')->truncate();

        $sql = <<<SQL
copy record_data
from 's3://mt2-listprofile-export/{$entity}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '"' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);
    }

    public function clearAndReloadEntity($entity) {
        DB::connection('redshift')->table('record_data')->truncate();

        $sql = <<<SQL
copy record_data
from 's3://mt2-listprofile-export/{$entity}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '"' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);
    }
}