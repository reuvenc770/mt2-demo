<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributionRecordTruthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribution_record_truths', function (Blueprint $table) {
            $table->integer( 'email_id' )->unsigned();
            $table->boolean( 'expired' )->default( false );
            $table->boolean( 'active' )->default( false );
            $table->timestamps();

            $table->primary( 'email_id' );
            $table->index( 'expired' );
            $table->index( 'active' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('attribution_record_truths');
    }
}
