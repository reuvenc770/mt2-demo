<?php

use Illuminate\Database\Seeder;
use App\Models\CakeRedirectDomain;
use App\Models\OfferPayoutType;

class CakeRedirectLinkSeeder extends Seeder {
    
    public function run() {

        // offer payout types: 
        // 1: CPM
        // 2: CPC
        // 3: CPA

        $p = new OfferPayoutType(); // This will soon change to OfferPayoutType
        $p->name = 'CPS';
        $p->save();

        $p = new OfferPayoutType();
        $p->name = 'REV';
        $p->save();

        // 4: CPS
        // 5: REV

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1391;
        $r1->offer_payout_type_id = 1;
        $r1->redirect_domain = 'transposi.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1502;
        $r1->offer_payout_type_id = 1;
        $r1->redirect_domain = 'transposi.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1391;
        $r1->offer_payout_type_id = 2;
        $r1->redirect_domain = 'transposi.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1391;
        $r1->offer_payout_type_id = 3;
        $r1->redirect_domain = 'transposi.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1502;
        $r1->offer_payout_type_id = 2;
        $r1->redirect_domain = 'transposi.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1502;
        $r1->offer_payout_type_id = 3;
        $r1->redirect_domain = 'transposi.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1010;
        $r1->offer_payout_type_id = 1;
        $r1->redirect_domain = 'skilledapparatus.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1010;
        $r1->offer_payout_type_id = 2;
        $r1->redirect_domain = 'drivenobjective.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1010;
        $r1->offer_payout_type_id = 3;
        $r1->redirect_domain = 'drivenobjective.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1010;
        $r1->offer_payout_type_id = 4;
        $r1->redirect_domain = 'drivenobjective.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1010;
        $r1->offer_payout_type_id = 5;
        $r1->redirect_domain = 'drivenobjective.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 609;
        $r1->offer_payout_type_id = 1;
        $r1->redirect_domain = 'brghtburst.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 609;
        $r1->offer_payout_type_id = 2;
        $r1->redirect_domain = 'hitgoalscore.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 609;
        $r1->offer_payout_type_id = 3;
        $r1->redirect_domain = 'hitgoalscore.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 609;
        $r1->offer_payout_type_id = 4;
        $r1->redirect_domain = 'hitgoalscore.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 609;
        $r1->offer_payout_type_id = 5;
        $r1->redirect_domain = 'hitgoalscore.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1454;
        $r1->offer_payout_type_id = 1;
        $r1->redirect_domain = 'routineship.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1454;
        $r1->offer_payout_type_id = 2;
        $r1->redirect_domain = 'phoneticroad.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1454;
        $r1->offer_payout_type_id = 3;
        $r1->redirect_domain = 'phoneticroad.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1454;
        $r1->offer_payout_type_id = 4;
        $r1->redirect_domain = 'phoneticroad.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1454;
        $r1->offer_payout_type_id = 5;
        $r1->redirect_domain = 'phoneticroad.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1455;
        $r1->offer_payout_type_id = 1;
        $r1->redirect_domain = 'learnrtech.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1455;
        $r1->offer_payout_type_id = 2;
        $r1->redirect_domain = 'prequelskys.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1455;
        $r1->offer_payout_type_id = 3;
        $r1->redirect_domain = 'prequelskys.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1455;
        $r1->offer_payout_type_id = 4;
        $r1->redirect_domain = 'prequelskys.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1455;
        $r1->offer_payout_type_id = 5;
        $r1->redirect_domain = 'prequelskys.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 211;
        $r1->offer_payout_type_id = 1;
        $r1->redirect_domain = 'rhythmictek.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 211;
        $r1->offer_payout_type_id = 2;
        $r1->redirect_domain = 'cloudskier.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 211;
        $r1->offer_payout_type_id = 3;
        $r1->redirect_domain = 'cloudskier.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 211;
        $r1->offer_payout_type_id = 4;
        $r1->redirect_domain = 'cloudskier.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 211;
        $r1->offer_payout_type_id = 5;
        $r1->redirect_domain = 'cloudskier.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1479;
        $r1->offer_payout_type_id = 1;
        $r1->redirect_domain = 'routineship.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1479;
        $r1->offer_payout_type_id = 2;
        $r1->redirect_domain = 'phoneticroad.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1479;
        $r1->offer_payout_type_id = 3;
        $r1->redirect_domain = 'phoneticroad.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1479;
        $r1->offer_payout_type_id = 4;
        $r1->redirect_domain = 'phoneticroad.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1479;
        $r1->offer_payout_type_id = 5;
        $r1->redirect_domain = 'phoneticroad.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1029;
        $r1->offer_payout_type_id = 1;
        $r1->redirect_domain = 'accuracydata.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1029;
        $r1->offer_payout_type_id = 2;
        $r1->redirect_domain = 'precisionfigure.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1029;
        $r1->offer_payout_type_id = 3;
        $r1->redirect_domain = 'precisionfigure.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1029;
        $r1->offer_payout_type_id = 4;
        $r1->redirect_domain = 'precisionfigure.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 1029;
        $r1->offer_payout_type_id = 5;
        $r1->redirect_domain = 'precisionfigure.com';
        $r1->save();

        $r1 = new CakeRedirectDomain();
        $r1->cake_affiliate_id = 309;
        $r1->offer_payout_type_id = 1;
        $r1->redirect_domain = 'aztechking.com';
        $r1->save();

    }
}