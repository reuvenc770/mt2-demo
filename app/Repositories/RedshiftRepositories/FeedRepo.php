<?php

namespace App\Repositories\RedshiftRepositories;

use App\Models\RedshiftModels\Feed;
use App\Repositories\RepoInterfaces\IRedshiftRepo;
use DB;

class FeedRepo implements IRedshiftRepo {
    
    private $model;

    public function __construct(Feed $model) {
        $this->model = $model;
    }

    public function loadEntity($entity) {
        $sql = <<<SQL
copy feeds
from 's3://mt2-listprofile-export-cmpte/{$entity}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '\'' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);
    }

    public function clearAndReloadEntity($entity) {
        DB::connection('redshift')->statement("TRUNCATE feeds");
        
        $sql = <<<SQL
copy feeds
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