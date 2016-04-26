<?php

use Illuminate\Database\Seeder;

class AddCSVStuff extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pub = new \App\Models\Esp();
        $pub->name = 'RelevantTools';
        $pub->save();

        $pubEsp = new App\Models\EspAccount();
        $pubEsp->account_name = 'RT001';
        $pub->espAccounts()->save($pubEsp);

        $pubEsp = new App\Models\EspAccount();
        $pubEsp->account_name = 'RT002';
        $pub->espAccounts()->save($pubEsp);

        $pubEsp = new App\Models\EspAccount();
        $pubEsp->account_name = 'RT003';
        $pub->espAccounts()->save($pubEsp);

        $mapping = new \App\Models\EspCampaignMapping();
        $mapping->mappings = 'campaign_name,total_sent,total_open';
        $pub->accountMapping()->save($mapping);


        $esp = \App\Models\Esp::where('name', "AWeber")->first();
        $mapping = new \App\Models\EspCampaignMapping();
        $mapping->mappings = 'campaign_name,total_sents,total_opens';
        $esp->accountMapping()->save($mapping);
    }
}
