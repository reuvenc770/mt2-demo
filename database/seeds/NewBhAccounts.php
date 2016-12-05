<?php

use Illuminate\Database\Seeder;
use App\Models\EspAccount;
use App\Models\Esp;
use App\Models\EspDataExport;

class NewBhAccounts extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        $esp = new Esp();

        $espId = $esp->where('name', 'BlueHornet')->first()->id;

        $x = new EspAccount();
        $x->account_name = 'BHE1';
        $x->esp_id = $espId;
        $x->key_1 = 'e2376d61ef68525eb0e8147639877f49';
        $x->key_2 = 'd0084f9ec2b201c9cb440dd43ab39a77';
        $x->status = 1;
        $x->save();

        $x = new EspAccount();
        $x->account_name = 'BHE3';
        $x->esp_id = $espId;
        $x->key_1 = '17eb836c1bf8bbfd7417829ea9efa4cc';
        $x->key_2 = '7e8b70a0b353d6388e97651764710aee';
        $x->status = 1;
        $x->save();

        $x = new EspAccount();
        $x->account_name = 'BHE4';
        $x->esp_id = $espId;
        $x->key_1 = '7c429b1af545a71e2a03892ee837e1d8';
        $x->key_2 = '47a10f710d828a5d4993dbc0bfecc0db';
        $x->status = 1;
        $x->save();

        // Part 2 - setup of export information

        $y = new EspDataExport();
        $acc = new EspAccount();

        $espAccountId = $acc->where('account_name', 'BHE1')->first()->id;

        $y = new EspDataExport();
        $y->feed_id = 2430;
        $y->esp_account_id = $espAccountId;
        $y->target_list = '2122977';
        $y->save();

        $y = new EspDataExport();
        $y->feed_id = 2433;
        $y->esp_account_id = $espAccountId;
        $y->target_list = '6814234';
        $y->save();

        $y = new EspDataExport();
        $y->feed_id = 2957;
        $y->esp_account_id = $espAccountId;
        $y->target_list = '57687284';
        $y->save();

    }
}
