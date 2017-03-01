<?php

namespace App\Services;

use App\Models\ListProfileBaseTable;
use App\Repositories\ListProfileBaseTableRepo;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ListProfileBaseTableCreationService {

    const BASE_NAME = 'list_profile_export_';
    private $requiredFields = ['email_id', 'email_address', 'lower_case_md5', 'upper_case_md5', 'globally_suppressed'];
    private $repo;

    public function __construct() {}

    public function createTable($id, $columns) {

        $tableName = self::BASE_NAME . $id;

        if (Schema::connection('list_profile_export_tables')->hasTable($tableName)) {
            // Need to drop in case columns have changed.
            Schema::connection('list_profile_export_tables')->drop($tableName);
        }

        // Note REQUIRED_PROFILE_FIELDS in ListProfileQueryBuilder

        Schema::connection('list_profile_export_tables')->create($tableName, function (Blueprint $table) use ($columns) {
            $table->bigInteger('email_id')->unsigned()->default(0);
            $table->string('email_address')->default('');
            $table->boolean('globally_suppressed')->default(0);
            $table->string('lower_case_md5')->default('');
            $table->string('upper_case_md5')->default('');

            foreach ($columns as $column) {
                if (!in_array($column, $this->requiredFields)) {
                    $table->string($column)->default('');
                }
            }

            $table->primary('email_id');
            $table->index('email_address', 'email_address');
            $table->index('lower_case_md5', 'lower_case_md5');
            $table->index('upper_case_md5', 'upper_case_md5');
        });

        $this->repo = new ListProfileBaseTableRepo(new ListProfileBaseTable($tableName));
    }

    public function insert($row) {

    }

    public function massInsert($rows) {
        $this->repo->insert($rows);
    }
}