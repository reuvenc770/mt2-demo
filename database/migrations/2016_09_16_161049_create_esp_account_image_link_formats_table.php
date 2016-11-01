<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEspAccountImageLinkFormatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('esp_account_image_link_formats', function (Blueprint $table) {
            $table->integer('esp_account_id')->default(0);
            $table->boolean('remove_file_extension')->default(0);
            $table->string('url_format')->default("/images/{{FILE_NAME}}");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('esp_account_image_link_formats');
    }
}
