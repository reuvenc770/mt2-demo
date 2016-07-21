<?php

use Illuminate\Database\Seeder;
use App\Models\PublicatorsSuppressionList;

class PublicatorsSuppressionListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $acct1 = new PublicatorsSuppressionList();
        $acct1->account_name = 'PUB001';
        $acct1->suppression_list_id = 82019;
        $acct1->save();

        $acct2 = new PublicatorsSuppressionList();
        $acct2->account_name = 'PUB002';
        $acct2->suppression_list_id = 82020;
        $acct2->save();

        $acct3 = new PublicatorsSuppressionList();
        $acct3->account_name = 'PUB003';
        $acct3->suppression_list_id = 82021;
        $acct3->save();

        $acct4 = new PublicatorsSuppressionList();
        $acct4->account_name = 'PUB004';
        $acct4->suppression_list_id = 82022;
        $acct4->save();

        $acct5 = new PublicatorsSuppressionList();
        $acct5->account_name = 'PUB005';
        $acct5->suppression_list_id = 82023;
        $acct5->save();

        $acct6 = new PublicatorsSuppressionList();
        $acct6->account_name = 'PUB006';
        $acct6->suppression_list_id = 82024;
        $acct6->save();

        $acct7 = new PublicatorsSuppressionList();
        $acct7->account_name = 'PUB007';
        $acct7->suppression_list_id = 82025;
        $acct7->save();

        $acct8 = new PublicatorsSuppressionList();
        $acct8->account_name = 'PUB008';
        $acct8->suppression_list_id = 82026;
        $acct8->save();

        $acct9 = new PublicatorsSuppressionList();
        $acct9->account_name = 'PUB009';
        $acct9->suppression_list_id = 82027;
        $acct9->save();

        $acct10 = new PublicatorsSuppressionList();
        $acct10->account_name = 'PUB010';
        $acct10->suppression_list_id = 82028;
        $acct10->save();

        $acct11 = new PublicatorsSuppressionList();
        $acct11->account_name = 'PUB011';
        $acct11->suppression_list_id = 82029;
        $acct11->save();

        $acct12 = new PublicatorsSuppressionList();
        $acct12->account_name = 'PUB012';
        $acct12->suppression_list_id = 82030;
        $acct12->save();

        $acct13 = new PublicatorsSuppressionList();
        $acct13->account_name = 'PUB013';
        $acct13->suppression_list_id = 82031;
        $acct13->save();

        $acct14 = new PublicatorsSuppressionList();
        $acct14->account_name = 'PUB014';
        $acct14->suppression_list_id = 82032;
        $acct14->save();

    }
}
