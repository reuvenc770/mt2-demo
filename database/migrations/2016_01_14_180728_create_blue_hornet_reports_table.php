<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlueHornetReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blue_hornet_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->string("account_name");
            $table->integer("internal_id");
            $table->string("message_subject")->nullable();
            $table->string("message_name", 100)->nullable();
            $table->string("date_sent")->nullable();
            $table->string("message_notes")->nullable();
            $table->integer("withheld_total")->nullable();
            $table->integer("globally_suppressed")->nullable();
            $table->integer("suppressed_total")->nullable();
            $table->integer("bill_codes")->nullable();
            $table->integer("sent_total")->nullable();
            $table->integer("sent_total_html")->nullable();
            $table->integer("sent_total_plain")->nullable();
            $table->double("sent_rate_total")->nullable();
            $table->double("sent_rate_html")->nullable();
            $table->integer("sent_rate_plain")->nullable();
            $table->integer("delivered_total")->nullable();
            $table->integer("delivered_html")->nullable();
            $table->integer("delivered_plain")->nullable();
            $table->double("delivered_rate_total")->nullable();
            $table->double("delivered_rate_html")->nullable();
            $table->double("delivered_rate_plain")->nullable();
            $table->integer("bounced_total")->nullable();
            $table->integer("bounced_html")->nullable();
            $table->integer("bounced_plain")->nullable();
            $table->double("bounced_rate_total")->nullable();
            $table->double("bounced_rate_html")->nullable();
            $table->double("bounced_rate_plain")->nullable();
            $table->integer("invalid_total")->nullable();
            $table->double("invalid_rate_total")->nullable();
            $table->boolean("has_dynamic_content")->nullable();
            $table->boolean("has_delivery_report")->nullable();
            $table->string("link_append_statement")->nullable();
            $table->string("timezone", 50)->nullable();
            $table->string("ftf_forwarded")->nullable();
            $table->string("ftf_signups")->nullable();
            $table->string("ftf_conversion_rate")->nullable();
            $table->integer("optout_total")->nullable();
            $table->double("optout_rate_total")->nullable();
            $table->integer("opened_total")->nullable();
            $table->integer("opened_unique")->nullable();
            $table->double("opened_rate_unique")->nullable();
            $table->double("opened_rate_aps")->nullable();
            $table->integer("clicked_total")->nullable();
            $table->integer("clicked_unique")->nullable();
            $table->double("clicked_rate_unique")->nullable();
            $table->double("clicked_rate_aps")->nullable();
            $table->string("campaign_name", 100)->nullable();
            $table->integer("campaign_id")->nullable();
             $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('blue_hornet_reports');
    }
}
