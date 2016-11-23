<?php

use Illuminate\Database\Seeder;
use App\Models\EspDataExport;
use App\Facades\EspApiAccount;

class AddEspDataExports extends Seeder {

    public function run() {

        $espAccount = EspApiAccount::getAllAccountsByESPName('MAR2');

        $export = new EspDataExport();
        $export->feed_id = 2720;
        $export->esp_account_id = $espAccount->id;
        $export->target_list = 64409;
        $export->save();

        $export = new EspDataExport();
        $export->feed_id = 2721;
        $export->esp_account_id = $espAccount->id;
        $export->target_list = 64409;
        $export->save();

        $export = new EspDataExport();
        $export->feed_id = 2722;
        $export->esp_account_id = $espAccount->id;
        $export->target_list = 64409;
        $export->save();

        $export = new EspDataExport();
        $export->feed_id = 2723;
        $export->esp_account_id = $espAccount->id;
        $export->target_list = 64409;
        $export->save();

        $export = new EspDataExport();
        $export->feed_id = 2723;
        $export->esp_account_id = $espAccount->id;
        $export->target_list = 64409;
        $export->save();
    }
}