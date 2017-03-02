<?php
/**
 * @author Adam Chin <achin@zetaglobal.net>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLegacySuppressionReasonFormValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'suppression_reasons' , function ( $table ) {
            $table->string( 'legacy_form_value' )->after( 'legacy_status' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'suppression_reasons' , function ( $table ) {
            $table->dropColumn( 'legacy_form_value' );
        } );
    }
}
