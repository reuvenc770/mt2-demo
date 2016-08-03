<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreativesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('creatives', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('offer_id')->default(0);
            $table->string('file_name')->default('');
            $table->longText('creative_html')->default('');
            $table->boolean('approved')->default(0);
            $table->boolean('status')->default(0);

            # Potentially legacy fields
            $table->boolean('is_original')->default(0);
            $table->boolean('trigger_flag')->default(0);
            $table->date('creative_date')->nullable();
            $table->date('inactive_date')->nullable();
            $table->string('unsub_image', 20)->nullable();
            $table->integer('default_subject')->default(0);
            $table->integer('default_from')->default(0);
            $table->string('image_directory', 255)->nullable();
            $table->string('thumbnail', 255)->nullable();
            $table->datetime('date_approved')->nullable();
            $table->string('approved_by', 20)->nullable();
            $table->tinyInteger('content_id')->unsigned()->default(0);
            $table->tinyInteger('header_id')->unsigned()->default(0);
            $table->tinyInteger('body_content_id')->unsigned()->default(0);
            $table->string('style_id', 50)->default('');
            $table->boolean('replace_flag')->default(1);
            $table->boolean('mediactivate_flag')->default(0);
            $table->boolean('hitpath_flag')->default(0);
            $table->string('comm_wizard_c3', 10)->nullable();
            $table->integer('comm_wizard_cid')->unsigned()->nullable();
            $table->integer('comm_wizard_progid')->unsigned()->nullable();
            $table->string('cr', 10)->nullable();
            $table->char('landing_page', 2)->nullable();
            $table->boolean('is_internally_approved')->default(0);
            $table->dateTime('internal_date_approved')->nullable();
            $table->string('internal_approved_by', 20)->nullable();
            $table->boolean('copywriter')->default(0);
            $table->string('copywriter_name', 5)->nullable();
            $table->longText('original_html')->nullable();
            $table->integer('deleted_by')->unsigned()->default(0);
            $table->boolean('host_images')->default(1);
            $table->tinyInteger('needs_processing')->default(0);
            
            $table->index('offer_id', 'offer_id');
            $table->index('needs_processing', 'needs_processing');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('creatives');
    }
}
