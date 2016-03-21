<?php

use Illuminate\Database\Seeder;
use App\Models\EspAccount;

class YmlpSeeder extends Seeder {

  public function run() {

    $ymlp = new \App\Models\Esp();
    $ymlpEsp = new EspAccount();
    $ymlp->name = "Ymlp";
    $ymlp->save();
    $ymlpEsp->account_name = 'YMLP1';
    $ymlpEsp->key_1 = 'ngn7';
    $ymlpEsp->key_2 = 'CY3MCAZSH7MS6U8K0Y82';
    $ymlp->espAccounts()->save($ymlpEsp);


    $ymlpCampaign1 = new \App\Models\YmlpCampaign();
    $ymlpCampaign1->esp_account_id = $ymlpEsp->id;
    $ymlpCampaign1->date = '2016-03-03';
    $ymlpCampaign1->sub_id = '1303097_YMLP1_GM_US_PDA_0303';
    $ymlpCampaign1->save();

    $ymlpCampaign2 = new \App\Models\YmlpCampaign();
    $ymlpCampaign2->esp_account_id = $ymlpEsp->id;
    $ymlpCampaign2->date = '2016-03-04';
    $ymlpCampaign2->sub_id = '1303202_YMLP1_GM_US_PDA_0304';
    $ymlpCampaign2->save();

    $ymlpCampaign3 = new \App\Models\YmlpCampaign();
    $ymlpCampaign3->esp_account_id = $ymlpEsp->id;
    $ymlpCampaign3->date = '2016-03-05';
    $ymlpCampaign3->sub_id = '1303786_YMLP1_GM_US_PDA_PRO_0307';
    $ymlpCampaign3->save();

    $ymlpCampaign4 = new \App\Models\YmlpCampaign();
    $ymlpCampaign4->esp_account_id = $ymlpEsp->id;
    $ymlpCampaign4->date = '2016-03-06';
    $ymlpCampaign4->sub_id = '1304039_YMLP1_GM_US_PDA_PRO_0308';
    $ymlpCampaign4->save();

    $ymlpCampaign5 = new \App\Models\YmlpCampaign();
    $ymlpCampaign5->esp_account_id = $ymlpEsp->id;
    $ymlpCampaign5->date = '2016-03-07';
    $ymlpCampaign5->sub_id = '1304517_YMLP1_GM_US_PDA_PRO_0309';
    $ymlpCampaign5->save();

    $ymlpCampaign6 = new \App\Models\YmlpCampaign();
    $ymlpCampaign6->esp_account_id = $ymlpEsp->id;
    $ymlpCampaign6->date = '2016-03-08';
    $ymlpCampaign6->sub_id = '1304519_YMLP1_GM_US_PDA_PRO_0310';
    $ymlpCampaign6->save();

    $ymlpCampaign7 = new \App\Models\YmlpCampaign();
    $ymlpCampaign7->esp_account_id = $ymlp->id;
    $ymlpCampaign7->date = '2016-03-09';
    $ymlpCampaign7->sub_id = '1304521_YMLP1_GM_US_PDA_PRO_0311';
    $ymlpCampaign7->save();

  }
}