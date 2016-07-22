<?php

use Illuminate\Database\Seeder;
use App\Models\ClientPayoutType;

class AddPayoutTypes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $type1 = new ClientPayoutType();
        $type1->name = 'CPM';
        $type1->save();

        $type2 = new ClientPayoutType();
        $type2->name = 'Revshare';
        $type2->save();

        $type3 = new ClientPayoutType();
        $type3->name = 'CPA';
        $type3->save();
    }
}
