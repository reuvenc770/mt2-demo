<?php

use Illuminate\Database\Seeder;
use App\Models\EspAccount;
use App\Models\SuppressionReason;
class NewEsp extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bronto = new \App\Models\Esp();
        $brontoA = new EspAccount();
        $bronto->name = "Bronto";
        $bronto->save();
        $brontoA->account_name = "BR001";
        $brontoA->key_1 = "18CC1C1A-6F9A-40D9-81D2-3B2527C98A33";
        $bronto->espAccounts()->save($brontoA);

        $brontoB = new EspAccount();
        $brontoB->account_name = "BR002";
        $brontoB->key_1 = "0A2B8917-74FD-455D-BA2A-A34F0E7EC684";
        $bronto->espAccounts()->save($brontoB);

        $reason = new SuppressionReason();
        $reason->display_status = "Suppression because of ESP Unsub - {$bronto->name}";
        $reason->esp_id = $bronto->id;
        $reason->display = 1;
        $reason->suppression_type = \App\Models\Suppression::TYPE_UNSUB;
        $reason->save();

        $reason = new SuppressionReason();
        $reason->display_status = "Suppression because of ESP Hardbounce - {$bronto->name}";
        $reason->esp_id = $bronto->id;
        $reason->display = 1;
        $reason->suppression_type = \App\Models\Suppression::TYPE_HARD_BOUNCE;
        $reason->save();

        $reason = new SuppressionReason();
        $reason->display_status = "Suppression because of ESP Complaint - {$bronto->name}";
        $reason->esp_id = $bronto->id;
        $reason->display = 1;
        $reason->suppression_type = \App\Models\Suppression::TYPE_COMPLAINT;
        $reason->save();
    }
}
