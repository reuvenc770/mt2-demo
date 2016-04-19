<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Seeder;

use App\Models\Esp;
use App\Models\EspAccount;

class PublicatorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $esp1 = new Esp();
        $esp1->name = 'Publicators';
        $esp1->save();

        $espAccount1 = new EspAccount();
        $espAccount1->account_name = 'PU001';
        $espAccount1->key_1 = '6CAA2228-A4FE-4CA0-B1F6-FB789E91952D';
        $espAccount1->key_2 = '5073C95B-C8F6-4C44-BE2D-B1FCDC95B71E';
        $espAccount1->esp_id = $esp1->id;
        $espAccount1->save();
        $esp1->espAccounts()->save( $espAccount1 );

        $espAccount2 = new EspAccount();
        $espAccount2->account_name = 'PU002';
        $espAccount2->key_1 = 'EDEE2318-30F5-4566-9618-8CA4CC1AEDC0';
        $espAccount2->key_2 = '32AA9359-4AD5-4EEC-A19E-4BAA72624678';
        $espAccount2->esp_id = $esp1->id;
        $espAccount2->save();
        $esp1->espAccounts()->save( $espAccount2 );
    }
}
