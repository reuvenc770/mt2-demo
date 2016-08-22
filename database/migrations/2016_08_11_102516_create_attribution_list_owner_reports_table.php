<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributionListOwnerReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'attribution' )->create('attribution_list_owner_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer( 'client_stats_grouping_id' )->unsigned();
            $table->decimal( 'standard_revenue' , 11 , 3 )->unsigned()->default( 0.00 );
            $table->decimal( 'cpm_revenue' , 11 , 3 )->unsigned()->default( 0.00 );
            $table->integer( 'mt1_uniques' )->unsigned()->default( 0 );
            $table->integer( 'mt2_uniques' )->unsigned()->default( 0 );
            $table->date( 'date' );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( 'attribution' )->drop('attribution_list_owner_reports');
    }
}
