<?php
/**
 * Created by PhpStorm.
 * User: codedestroyer
 * Date: 2/27/17
 * Time: 3:02 PM
 */

namespace App\Repositories\RedshiftRepositories;


use App\Models\SuppressionListSuppression;
use App\Repositories\RepoInterfaces\IRedshiftRepo;

class SuppressionListSuppressionRepo implements IRedshiftRepo
{
    private $model;

    public function __construct(SuppressionListSuppression $model) {
        $this->model = $model;
    }

    public function loadEntity($entity) {
        $sql = <<<SQL
copy suppression_global_orange
from 's3://mt2-listprofile-export/{$entity}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '\'' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);
    }

    public function clearAndReloadEntity($entity) {
        DB::connection('redshift')->statement("TRUNCATE suppression_list_suppression");

        $sql = <<<SQL
copy suppression_global_orange
from 's3://mt2-listprofile-export/{$entity}.csv'
credentials 'aws_iam_role=arn:aws:iam::286457008090:role/redshift-s3-stg'
format as csv quote as '\'' delimiter as ',';
SQL;
        DB::connection('redshift')->statement($sql);

    }
}