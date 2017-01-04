<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveAllReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $from = config('database.connections.mysql.database');
        $to = config('database.connections.reporting_data.database');

        DB::statement("alter table {$from}.blue_hornet_reports rename {$to}.blue_hornet_reports");
        DB::statement("alter table {$from}.standard_reports rename {$to}.standard_reports");
        DB::statement("alter table {$from}.cake_aggregated_data rename {$to}.cake_aggregated_data");
        DB::statement("alter table {$from}.email_direct_reports rename {$to}.email_direct_reports");
        DB::statement("alter table {$from}.maro_reports rename {$to}.maro_reports");
        DB::statement("alter table {$from}.campaigner_reports rename {$to}.campaigner_reports");



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $to = config('database.connections.mysql.database');
        $from = config('database.connections.reporting_data.database');

        DB::statement("alter table {$from}.blue_hornet_reports rename {$to}.blue_hornet_reports");
        DB::statement("alter table {$from}.standard_reports rename {$to}.standard_reports");
        DB::statement("alter table {$from}.cake_aggregated_data rename {$to}.cake_aggregated_data");
        DB::statement("alter table {$from}.email_direct_reports rename {$to}.email_direct_reports");
        DB::statement("alter table {$from}.maro_reports rename {$to}.maro_reports");
        DB::statement("alter table {$from}.campaigner_reports rename {$to}.campaigner_reports");
    }
}
