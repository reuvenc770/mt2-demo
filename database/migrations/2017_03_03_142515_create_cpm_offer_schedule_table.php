<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpmOfferScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'cpm_offer_schedules' , function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->integer( 'cake_offer_id' );
            $table->date( 'start_date' );
            $table->date( 'end_date' );
            $table->timestamps();

            $table->index( 'cake_offer_id' , 'offer_index' );
            $table->index( 'start_date' , 'start_date_index' );
            $table->index( 'end_date' , 'end_date_index' );
            $table->unique( [ 'cake_offer_id' , 'start_date' , 'end_date' ] , 'date_offer_unique' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop( 'cpm_offer_schedules' );
    }
}
