<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpaReportingListProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('reporting_data')->create('cpa_reporting_list_profile', function (Blueprint $table) {
            $table->integer( 'feed_id' )->unsigned()->default( 0 );
            $table->integer( 'cake_offer_id' )->unsigned()->default( 0 );
            $table->integer( 'offer_id' )->unsigned()->default( 0 );
            $table->integer( 'deploy_id' )->unsigned()->default( 0 );
            $table->integer( 'conversions' )->unsigned()->default( 0 );
            $table->decimal( 'rev' , 11 , 3 )->unsigned()->default( 0.000 );
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->index( 'feed_id' , 'feed_index' );
            $table->index( 'cake_offer_id' , 'offer_index' );
            $table->unique( [ 'feed_id' , 'cake_offer_id' ] , 'feed_offer_unique' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('reporting_data')->drop('cpa_reporting_list_profile');
    }
}
