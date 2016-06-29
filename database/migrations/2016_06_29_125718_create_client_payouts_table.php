<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientPayoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_payouts', function (Blueprint $table) {
            $table->integer( 'client_id' )->unsigned();
            $table->integer( 'client_payout_type_id' )->unsigned();
            $table->decimal( 'amount' , 11 , 3 );
            $table->timestamps();

            $table->primary( 'client_id' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('client_payouts');
    }
}
