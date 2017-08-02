<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpmReportingListProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('reporting_data')->create('cpm_reporting_listprofile', function (Blueprint $table) {
            $table->integer( 'feed_id' )->unsigned()->default( 0 );
            $table->integer( 'cake_offer_id' )->unsigned()->default( 0 );
            $table->decimal( 'payout' , 11 , 3 )->unsigned()->default( 0.000 );
            $table->integer( 'delivered' )->unsigned()->default( 0 );
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
        Schema::connection('reporting_data')->drop('cpm_reporting_listprofile');
    }
}
