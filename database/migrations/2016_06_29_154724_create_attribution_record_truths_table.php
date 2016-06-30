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
        Schema::connection( 'attribution' )->create('attribution_record_truths', function (Blueprint $table) {
            $table->bigInteger( 'email_id' )->unsigned();
            $table->boolean( 'recent_import' )->default( false );
            $table->boolean( 'has_action' )->default( false );
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
        Schema::connection( 'attribution' )->drop('attribution_record_truths');
    }
}
