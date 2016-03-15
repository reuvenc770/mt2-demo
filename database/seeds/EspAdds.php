<?php

use Illuminate\Database\Seeder;
use App\Models\EspAccount;
class EspAdds extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $aw = new \App\Models\Esp();
        $awesp = new EspAccount();
        $aw->name = "AWeber";
        $aw->save();
        $awesp->account_name = "AW001";
        $awesp->key_1 = "Agzar1qwGjUivLYrI80Ps613";
        $awesp->key_2 = "MzpvjmLY97o5eublmnse9qG0HvsYehRfijonUAwV";
        $aw->espAccounts()->save($awesp);

        $cp = new \App\Models\Esp();
        $cp->name = "GetResponse";
        $cp->save();
        $cpesp = new EspAccount();
        $cpesp->account_name = "GR001";
        $cpesp->key_1 = "497bca1349b1f02e8b256bf8fe62dfc5";
        $cp->espAccounts()->save($cpesp);
    }
}
