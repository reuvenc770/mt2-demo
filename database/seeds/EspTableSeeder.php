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

        $maro = new \App\Models\Esp();
        $maro->name = "Maro";
        $maro->save();

        $m1esp = new EspAccount();
        $m1esp->account_name = "MAR1";
        $m1esp->key_1 ="113";
        $m1esp->key_2 = "rSyggC4QpzG3kHpfK7i3";
        $maro->espAccounts()->save($m1esp);

        $m2esp = new EspAccount();
        $m2esp->account_name ="MAR4";
        $m2esp->key_1 ="407";
        $m2esp->key_2 = "95e546d32ad42fb412d050097c40a1bbf86ae4da";
        $maro->espAccounts()->save($m2esp);

        $m3esp = new EspAccount();
        $m3esp->account_name ="MAR3";
        $m3esp->key_1 ="374";
        $m3esp->key_2 = "fc2cbbbb208aec6d6ab329603c22881837fc74f4";
        $maro->espAccounts()->save($m3esp);

        $m4esp = new EspAccount();
        $m4esp->account_name ="MAR2";
        $m4esp->key_1 ="406";
        $m4esp->key_2 = "39d834057ad7ab7ef64c192e4beaac5fd1811dc9";
        $maro->espAccounts()->save($m4esp);
    }
}
