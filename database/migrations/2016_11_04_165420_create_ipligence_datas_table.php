<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateIpligenceDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        // Need raw statement for the zerofill
        DB::statement("CREATE TABLE `ipligence_data` (
          `ip_from` int(10) unsigned zerofill NOT NULL DEFAULT '0000000000',
          `ip_to` int(10) unsigned zerofill NOT NULL DEFAULT '0000000000',
          `country_code` varchar(10) NOT NULL,
          `country_name` varchar(255) NOT NULL,
          `continent_code` varchar(10) NOT NULL,
          `continent_name` varchar(255) NOT NULL,
          `time_zone` varchar(10) NOT NULL,
          `region_code` varchar(10) NOT NULL,
          `region_name` varchar(255) NOT NULL,
          `owner` varchar(255) NOT NULL,
          `city_name` varchar(255) NOT NULL,
          `county_name` varchar(255) NOT NULL,
          `post_code` varchar(10) NOT NULL,
          `metro_code` varchar(10) NOT NULL,
          `area_code` varchar(10) NOT NULL,
          `latitude` double NOT NULL,
          `longitude` double NOT NULL,
          PRIMARY KEY (`ip_to`)
        )");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('ipligence_data');
    }
}
