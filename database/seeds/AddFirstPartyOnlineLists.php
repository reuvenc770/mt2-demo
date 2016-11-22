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
        $list->suppression_list_id = 8084;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000ed8c0';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2983;
        $list->suppression_list_id = 9791;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000ed8bc';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2983;
        $list->suppression_list_id = ;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000ed8bf';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2983;
        $list->suppression_list_id = 9603;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000ed8bd';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2983;
        $list->suppression_list_id = 4123;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000ed8be';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2983;
        $list->suppression_list_id = 13723;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000edf3d';
        $list->save();


        /* RMP */

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2971;
        $list->suppression_list_id = 9208;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000d5e26';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2971;
        $list->suppression_list_id = 16991;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000e94a3';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2971;
        $list->suppression_list_id = 15929;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000e2a9b';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2971;
        $list->suppression_list_id = 15794;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000e34d1';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2971;
        $list->suppression_list_id = 15179;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000d6a38';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2971;
        $list->suppression_list_id = 10963;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000dfcd5';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2971;
        $list->suppression_list_id = 15832;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000d7889';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2971;
        $list->suppression_list_id = 12451;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000eb3b1';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2971;
        $list->suppression_list_id = 16473;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000eb3b2';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2971;
        $list->suppression_list_id = 10513;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000d5e28';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2971;
        $list->suppression_list_id = 16601;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000dd870';
        $list->save();

        // RMP2
        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2972;
        $list->suppression_list_id = 9208;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000d5e26';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2972;
        $list->suppression_list_id = 16991;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000e94a3';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2972;
        $list->suppression_list_id = 15929;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000e2a9b';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2972;
        $list->suppression_list_id = 15794;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000e34d1';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2972;
        $list->suppression_list_id = 15179;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000d6a38';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2972;
        $list->suppression_list_id = 10963;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000dfcd5';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2972;
        $list->suppression_list_id = 15832;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000d7889';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2972;
        $list->suppression_list_id = 12451;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000eb3b1';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2972;
        $list->suppression_list_id = 16473;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000eb3b2';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2972;
        $list->suppression_list_id = 10513;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000d5e28';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2972;
        $list->suppression_list_id = 16601;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000dd870';
        $list->save();

        // RMP3
        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2987;
        $list->suppression_list_id = 9208;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000d5e26';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2987;
        $list->suppression_list_id = 16991;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000e94a3';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2987;
        $list->suppression_list_id = 15929;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000e2a9b';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2987;
        $list->suppression_list_id = 15794;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000e34d1';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2987;
        $list->suppression_list_id = 15179;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000d6a38';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2987;
        $list->suppression_list_id = 10963;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000dfcd5';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2987;
        $list->suppression_list_id = 15832;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000d7889';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2987;
        $list->suppression_list_id = 12451;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000eb3b1';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2987;
        $list->suppression_list_id = 16473;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000eb3b2';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2987;
        $list->suppression_list_id = 10513;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000d5e28';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2987;
        $list->suppression_list_id = 16601;
        $list->esp_account_id = $br001;
        $list->target_list = '0bce03ec00000000000000000000000dd870';
        $list->save();


        /* AffiliateROI */

        $cy001 = EspApiAccount::getEspAccountDetailsByName('CY001')->id;

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 13566;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034726';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 12402;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034749';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 16260;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034774';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 9876;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034815';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 15230;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034770';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 15999;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034748';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 15929;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034728';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 16457;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034771';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 14182;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034773';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 15477;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034816';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 16259;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034746';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 16226;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034727';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 13842;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034729';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 13393;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034772';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 16834;
        $list->esp_account_id = $cy001;
        $list->target_list = '10166557';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 14427;
        $list->esp_account_id = $cy001;
        $list->target_list = '10167349';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 9208;
        $list->esp_account_id = $cy001;
        $list->target_list = '10167604';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 16848;
        $list->esp_account_id = $cy001;
        $list->target_list = '10177419';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 16634;
        $list->esp_account_id = $cy001;
        $list->target_list = '10178011';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 15213;
        $list->esp_account_id = $cy001;
        $list->target_list = '10178010';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2759;
        $list->suppression_list_id = 10390;
        $list->esp_account_id = $cy001;
        $list->target_list = '10178012';
        $list->save();

        // 2798
        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 13566;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034726';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 12402;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034749';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 16260;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034774';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 9876;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034815';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 15230;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034770';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 15999;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034748';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 15929;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034728';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 16457;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034771';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 14182;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034773';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 15477;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034816';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 16259;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034746';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 16226;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034727';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 13842;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034729';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 13393;
        $list->esp_account_id = $cy001;
        $list->target_list = '10034772';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 16834;
        $list->esp_account_id = $cy001;
        $list->target_list = '10166557';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 14427;
        $list->esp_account_id = $cy001;
        $list->target_list = '10167349';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 9208;
        $list->esp_account_id = $cy001;
        $list->target_list = '10167604';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 16848;
        $list->esp_account_id = $cy001;
        $list->target_list = '10177419';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 16634;
        $list->esp_account_id = $cy001;
        $list->target_list = '10178011';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 15213;
        $list->esp_account_id = $cy001;
        $list->target_list = '10178010';
        $list->save();

        $list = new FirstPartyOnlineSuppressionList();
        $list->feed_id = 2798;
        $list->suppression_list_id = 10390;
        $list->esp_account_id = $cy001;
        $list->target_list = '10178012';
        $list->save();
    }
}