<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->increments('id');
            $table->enum("domain_type", ['mailing','content']);
            $table->string("domain_name",100);
            $table->string("main_site",100);
            $table->integer('registrar_id');
            $table->integer("proxy_id");
            $table->integer("esp_account_id");
            $table->date("created_at");
            $table->date("expires_at");
            $table->integer("doing_business_as");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('domains');
    }
}
