<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvertiserSuppressionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('suppression')->create('suppression_list_suppressions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('suppression_list_id')->default(0);
            $table->string('email_address')->default('');
            $table->string('lower_case_md5')->default('');
            $table->string('upper_case_md5')->default('');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        
            $table->index(['suppression_list_id', 'email_address'], 'list_email');
            $table->index('email_address', 'email_address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('suppression')->drop('suppression_list_suppressions');
    }
}
