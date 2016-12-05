<?php

use Illuminate\Database\Seeder;
use App\Models\FirstPartyOnlineSuppressionList;
use App\Facades\EspApiAccount;

class AddFirstPartyOnlineLists extends Seeder {

    public function run() {


        $br001 = EspApiAccount::getEspAccountDetailsByName('BR001')->id;

        /* SimplyJobs */

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2983;
        $list->suppression_list_id = 5124;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000ed8c0';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2983;
        $list->suppression_list_id = 9030;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000ed8bc';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2983;
        $list->suppression_list_id = 5262;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000ed8bf';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2983;
        $list->suppression_list_id = 8791;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000ed8bd';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2983;
        $list->suppression_list_id = 3434;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000ed8be';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2983;
        $list->suppression_list_id = 7480;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000edf3d';
        $list->save();


        /* RMP */

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2971;
        $list->suppression_list_id = 4682;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000d5e26';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2971;
        $list->suppression_list_id = 5345;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000e94a3';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2971;
        $list->suppression_list_id = 8445;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000e2a9b';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2971;
        $list->suppression_list_id = 8380;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000e34d1';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2971;
        $list->suppression_list_id = 8067;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000d6a38';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2971;
        $list->suppression_list_id = 5735;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000dfcd5';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2971;
        $list->suppression_list_id = 8399;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000d7889';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2971;
        $list->suppression_list_id = 2071;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000eb3b1';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2971;
        $list->suppression_list_id = 8800;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000eb3b2';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2971;
        $list->suppression_list_id = 5524;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000d5e28';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2971;
        $list->suppression_list_id = 8878;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000dd870';
        $list->save();

        // RMP2
        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2972;
        $list->suppression_list_id = 4682;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000d5e26';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2972;
        $list->suppression_list_id = 5345;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000e94a3';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2972;
        $list->suppression_list_id = 8445;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000e2a9b';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2972;
        $list->suppression_list_id = 8380;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000e34d1';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2972;
        $list->suppression_list_id = 8067;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000d6a38';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2972;
        $list->suppression_list_id = 5735;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000dfcd5';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2972;
        $list->suppression_list_id = 8399;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000d7889';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2972;
        $list->suppression_list_id = 2071;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000eb3b1';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2972;
        $list->suppression_list_id = 8800;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000eb3b2';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2972;
        $list->suppression_list_id = 5524;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000d5e28';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2972;
        $list->suppression_list_id = 8878;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000dd870';
        $list->save();

        // RMP3
        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2987;
        $list->suppression_list_id = 4682;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000d5e26';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2987;
        $list->suppression_list_id = 5345;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000e94a3';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2987;
        $list->suppression_list_id = 8445;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000e2a9b';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2987;
        $list->suppression_list_id = 8380;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000e34d1';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2987;
        $list->suppression_list_id = 8067;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000d6a38';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2987;
        $list->suppression_list_id = 5735;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000dfcd5';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2987;
        $list->suppression_list_id = 8399;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000d7889';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2987;
        $list->suppression_list_id = 2071;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000eb3b1';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2987;
        $list->suppression_list_id = 8800;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000eb3b2';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2987;
        $list->suppression_list_id = 5524;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000d5e28';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2987;
        $list->suppression_list_id = 8878;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000dd870';
        $list->save();


        /* AffiliateROI */

        $cy001 = EspApiAccount::getEspAccountDetailsByName('CY001')->id;

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 7399;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034726';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 6694;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034749';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 8668;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034774';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 5124;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034815';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 8086;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034770';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 8493;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034748';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 8445;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034728';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 7480;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034771';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 7661;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034773';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 8229;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034816';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 8667;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034746';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 8643;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034727';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 7528;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034729';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 7291;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034772';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 9044;
        $list->esp_account_id = $cy001;
        $list->target_list = '10166557';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 5611;
        $list->esp_account_id = $cy001;
        $list->target_list = '10167349';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 4682;
        $list->esp_account_id = $cy001;
        $list->target_list = '10167604';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 9053;
        $list->esp_account_id = $cy001;
        $list->target_list = '10177419';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 8901;
        $list->esp_account_id = $cy001;
        $list->target_list = '10178011';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 8079;
        $list->esp_account_id = $cy001;
        $list->target_list = '10178010';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 5459;
        $list->esp_account_id = $cy001;
        $list->target_list = '10178012';
        $list->save();

        // 2798
        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 7399;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034726';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 6694;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034749';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 8668;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034774';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 5124;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034815';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 8086;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034770';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 8493;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034748';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 8445;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034728';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 7480;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034771';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 7661;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034773';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 8229;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034816';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 8667;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034746';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 8643;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034727';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 7528;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034729';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 7291;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034772';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 9044;
        $list->esp_account_id = $cy001;
        $list->target_list = '10166557';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 5611;
        $list->esp_account_id = $cy001;
        $list->target_list = '10167349';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 4682;
        $list->esp_account_id = $cy001;
        $list->target_list = '10167604';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 9053;
        $list->esp_account_id = $cy001;
        $list->target_list = '10177419';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 8901;
        $list->esp_account_id = $cy001;
        $list->target_list = '10178011';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 8079;
        $list->esp_account_id = $cy001;
        $list->target_list = '10178010';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 5459;
        $list->esp_account_id = $cy001;
        $list->target_list = '10178012';
        $list->save();


        /* Autopilot */
        $mar2 = EspApiAccount::getEspAccountDetailsByName('MAR2')->id;

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2979;
        $list->suppression_list_id = 6694;
        $list->esp_account_id = $mar2;
        $list->target_list = '51348';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2979;
        $list->suppression_list_id = 5459;
        $list->esp_account_id = $mar2;
        $list->target_list = '61336';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2979;
        $list->suppression_list_id = 6758;
        $list->esp_account_id = $mar2;
        $list->target_list = '61333';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2979;
        $list->suppression_list_id = 6839;
        $list->esp_account_id = $mar2;
        $list->target_list = '61333';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2979;
        $list->suppression_list_id = 2071;
        $list->esp_account_id = $mar2;
        $list->target_list = '61335';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2979;
        $list->suppression_list_id = 6759;
        $list->esp_account_id = $mar2;
        $list->target_list = '61333';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2979;
        $list->suppression_list_id = 4682;
        $list->esp_account_id = $mar2;
        $list->target_list = '51346';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2979;
        $list->suppression_list_id = 7480;
        $list->esp_account_id = $mar2;
        $list->target_list = '51341';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2979;
        $list->suppression_list_id = 7026;
        $list->esp_account_id = $mar2;
        $list->target_list = '51345';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2979;
        $list->suppression_list_id = 7480;
        $list->esp_account_id = $mar2;
        $list->target_list = '51342';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2979;
        $list->suppression_list_id = 5524;
        $list->esp_account_id = $mar2;
        $list->target_list = '61337';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2979;
        $list->suppression_list_id = 9030;
        $list->esp_account_id = $mar2;
        $list->target_list = '61331';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2979;
        $list->suppression_list_id = 5611;
        $list->esp_account_id = $mar2;
        $list->target_list = '61334';
        $list->save();
    }
}