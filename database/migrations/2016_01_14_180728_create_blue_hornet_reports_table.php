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
            $table->integer("message");
            $table->string("message_subject");
            $table->string("message_name", 100);
            $table->string("date_sent");
            $table->string("message_notes");
            $table->integer("withheld_total");
            $table->integer("globally_suppressed");
            $table->integer("suppressed_total");
            $table->integer("bill_codes");
            $table->integer("sent_total");
            $table->integer("sent_total_html");
            $table->integer("sent_total_plain");
            $table->double("sent_rate_total");
            $table->double("sent_rate_html");
            $table->integer("sent_rate_plain");
            $table->integer("delivered_total");
            $table->integer("delivered_html");
            $table->integer("delivered_plain");
            $table->double("delivered_rate_total");
            $table->double("delivered_rate_html");
            $table->double("delivered_rate_plain");
            $table->integer("bounced_total");
            $table->integer("bounced_html");
            $table->integer("bounced_plain");
            $table->double("bounced_rate_total");
            $table->double("bounced_rate_html");
            $table->double("bounced_rate_plain");
            $table->integer("invalid_total");
            $table->double("invalid_rate_total");
            $table->boolean("has_dynamic_content");
            $table->boolean("has_delivery_report");
            $table->string("link_append_statement");
            $table->string("timezone", 50);
            $table->string("ftf_forwarded");
            $table->string("ftf_signups");
            $table->string("ftf_conversion_rate");
            $table->integer("optout_total");
            $table->double("optout_rate_total");
            $table->integer("opened_total");
            $table->integer("opened_unique");
            $table->double("opened_rate_unique");
            $table->double("opened_rate_aps");
            $table->integer("clicked_total");
            $table->integer("clicked_unique");
            $table->double("clicked_rate_unique");
            $table->double("clicked_rate_aps");
            $table->string("campaign_name", 100);
            $table->integer("campaign_id");
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
