<?php

namespace App\Repositories\RedshiftRepositories;

use App\Models\RedshiftModels\RecordData;
use App\Repositories\RepoInterfaces\IRedshiftRepo;
use DB;

class FirstPartyRecordDataRepo implements IRedshiftRepo {
    
    private $model;

    public function __construct(RecordData $model) {
        $this->model = $model;
    }

    public function loadEntity($entity) {
        // We are overwriting the entire table -- if updated_at is keyed we could use that as well
        DB::connection('redshift')->statement("TRUNCATE first_party_record_data_staging");

        $sql = <<<SQL
copy first_party_record_data_staging
from 's3://mt2-listprofile-export-cmpte/{$entity}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '\'' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);

        DB::connection('redshift')->transaction(function() {
            DB::connection('redshift')->statement("DELETE FROM
                first_party_record_data
            USING
                first_party_record_data_staging
            WHERE
                first_party_record_data.email_id = first_party_record_data_staging.email_id");

            DB::connection('redshift')->statement("INSERT INTO first_party_record_data SELECT * FROM first_party_record_data_staging");
        });

        DB::connection('redshift')->statement("TRUNCATE first_party_record_data_staging");
    }

    public function clearAndReloadEntity($entity) {
        DB::connection('redshift')->statement("TRUNCATE first_party_record_data");

        $sql = <<<SQL
copy first_party_record_data
from 's3://mt2-listprofile-export-cmpte/{$entity}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '\'' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);
    }
}