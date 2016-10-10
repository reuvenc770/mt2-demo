<?php

use Illuminate\Database\Seeder;
use App\Models\FeedType;
use App\Models\Country;

class FeedTypesAndCountries extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $country = new Country();
        $country->name = 'United States';
        $country->abbr = 'US';
        $country->save();

        $country = new Country();
        $country->name = 'United Kingdom';
        $country->abbr = 'UK';
        $country->save();

        $country = new Country();
        $country->name = 'Australia';
        $country->abbr = 'AUS';
        $country->save();

        $country = new Country();
        $country->name = 'Belgium';
        $country->abbr = 'BE';
        $country->save();

        $country = new Country();
        $country->name = 'Brazil';
        $country->abbr = 'BR';
        $country->save();

        $country = new Country();
        $country->name = 'Canada';
        $country->abbr = 'CA';
        $country->save();

        $country = new Country();
        $country->name = 'Germany';
        $country->abbr = 'DE';
        $country->save();

        $country = new Country();
        $country->name = 'Spain';
        $country->abbr = 'ES';
        $country->save();

        $country = new Country();
        $country->name = 'France';
        $country->abbr = 'FR';
        $country->save();

        $country = new Country();
        $country->name = 'Italy';
        $country->abbr = 'IT';
        $country->save();

        $country = new Country();
        $country->name = 'Netherlands';
        $country->abbr = 'NL';
        $country->save();


        $feedType = new FeedType();
        $feedType->name = 'Internal';
        $feedType->save();

        $feedType = new FeedType();
        $feedType->name = 'Direct Owner';
        $feedType->save();

        $feedType = new FeedType();
        $feedType->name = 'Broker';
        $feedType->save();
    }
}