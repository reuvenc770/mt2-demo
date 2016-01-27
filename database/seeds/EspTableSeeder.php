<?php

use Illuminate\Database\Seeder;
use App\Models\EspAccount;
class EspTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bh = new \App\Models\Esp();
        $bhesp = new EspAccount();
        $bh->name = "BlueHornet";
        $bh->save();
        $bhesp->account_name = "BH001";
        $bhesp->key_1 = "ced21d9cfb0655eccf3946585d6b0fde";
        $bhesp->key_2 = "bdc925fe6cbd7596dc2a5e71bc211caa";
        $bh->espAccounts()->save($bhesp);

        $cp = new \App\Models\Esp();
        $cp->name = "Campaigner";
        $cp->save();
        $cpesp = new EspAccount();
        $cpesp->account_name = "CA009";
        $cpesp->key_1 = "api@poptasticnow.com";
        $cpesp->key_2 = "#caapi6#";
        $cp->espAccounts()->save($cpesp);

        $ed = new \App\Models\Esp();
        $ed->name = "EmailDirect";
        $ed->save();
        $edesp = new EspAccount();
        $edesp->account_name ="ED001";
        $edesp->key_1 = "a81cf56a-37bf-4abf-83b2-f7eed4e10380";
        $ed->espAccounts()->save($edesp);


    }
}
