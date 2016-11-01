<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameAttributionListOwnerReportsToAttributionClientReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('attribution')->rename( 'attribution_list_owner_reports' , 'attribution_client_reports' );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('attribution')->rename( 'attribution_client_reports' , 'attribution_list_owner_reports' );
    }
}
