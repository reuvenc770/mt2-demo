<?php

use Illuminate\Database\Seeder;
use App\Models\OfferPayoutType;

class AddPayoutTypes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $type1 = new OfferPayoutType();
        $type1->name = 'CPM';
        $type1->save();

        $type2 = new OfferPayoutType();
        $type2->name = 'CPC';
        $type2->save();

        $type3 = new OfferPayoutType();
        $type3->name = 'CPA';
        $type3->save();

        $type4 = new OfferPayoutType();
        $type4->name = 'Unknown'; // To support legacy data/unfilled
        $type4->save();
    }
}
