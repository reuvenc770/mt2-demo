<?php

use Illuminate\Database\Seeder;
use App\Models\EspDataExport;
use App\Facades\EspApiAccount;

class AddEspDataExports extends Seeder {

    public function run() {

        // Third party

        $espAccount = EspApiAccount::getEspAccountDetailsByName('MAR2');

        $export = new EspDataExport();
        $export->feed_id = 2720;
        $export->esp_account_id = $espAccount->id;
        $export->target_list = '64409';
        $export->save();

        $export = new EspDataExport();
        $export->feed_id = 2721;
        $export->esp_account_id = $espAccount->id;
        $export->target_list = '64409';
        $export->save();

        $export = new EspDataExport();
        $export->feed_id = 2722;
        $export->esp_account_id = $espAccount->id;
        $export->target_list = '64409';
        $export->save();

        $export = new EspDataExport();
        $export->feed_id = 2723;
        $export->esp_account_id = $espAccount->id;
        $export->target_list = '64409';
        $export->save();

        $export = new EspDataExport();
        $export->feed_id = 2724;
        $export->esp_account_id = $espAccount->id;
        $export->target_list = '64409';
        $export->save();


        // First Party
        // AffiliateROI && Genesis

        $espAccount = EspApiAccount::getEspAccountDetailsByName('CY001');

        $export = new EspDataExport();
        $export->feed_id = 2759;
        $export->esp_account_id = $espAccount->id;
        $export->target_list = '10013806';
        $export->save();

        $export = new EspDataExport();
        $export->feed_id = 2798;
        $export->esp_account_id = $espAccount->id;
        $export->target_list = '10013962';
        $export->save();

        $export = new EspDataExport();
        $export->feed_id = 2757;
        $export->esp_account_id = $espAccount->id;
        $export->target_list = '10567246';
        $export->save();

        // RMP
        $espAccount = EspApiAccount::getEspAccountDetailsByName('BR001');

        $export = new EspDataExport();
        $export->feed_id = 2972;
        $export->esp_account_id = $espAccount->id;
        $export->target_list = '0bce03ec00000000000000000000000d5821';
        $export->save();

        $export = new EspDataExport();
        $export->feed_id = 2971;
        $export->esp_account_id = $espAccount->id;
        $export->target_list = '0bce03ec00000000000000000000000d5822';
        $export->save();

        $export = new EspDataExport();
        $export->feed_id = 2987;
        $export->esp_account_id = $espAccount->id;
        $export->target_list = '0bce03ec00000000000000000000000d5822';
        $export->save();

        // Simply Jobs (same esp account)

        $export = new EspDataExport();
        $export->feed_id = 2983;
        $export->esp_account_id = $espAccount->id;
        $export->target_list = '0bce03ec00000000000000000000000ec137';
        $export->save();


        // Autopilot

        /*
        $espAccount = EspApiAccount::getEspAccountDetailsByName('???');

        $export = new EspDataExport();
        $export->feed_id = 2979;
        $export->esp_account_id = $espAccount->id;
        $export->target_list = 'ADC';
        $export->save();
        */

        // YREG

    }
}