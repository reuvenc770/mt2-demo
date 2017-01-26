<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BrontoIdMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('reporting_data')->create('bronto_id_mappings', function (Blueprint $table) {
            $table->string("primary_id");
            $table->integer("generated_id");
            $table->integer("esp_account_id");
            $table->primary("primary_id");
            $table->index("esp_account_id","generated_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('reporting_data')->drop('bronto_id_mappings');
    }
}
