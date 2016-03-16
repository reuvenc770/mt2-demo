<?php

use Illuminate\Database\Seeder;
use App\Models\EspAccount;

class CsvDeliverablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pub = new \App\Models\Esp();
        $pubEsp = new EspAccount();
        $pub->name = 'Publicators';
        $pub->save();

        $pubEsp->account_name = 'PUB004';
        $pub->espAccounts()->save($pubEsp);

        $mapping = new \App\Models\DeliverableCsvMapping();
        $mapping->mapping = 'recipientcode,email_id,email,active,permission,counter,datetime';
        $pub->deliverableCsvMapping()->save($mapping);
    }
}
