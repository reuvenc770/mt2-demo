<?php

use Illuminate\Database\Seeder;
use App\Models\EspFieldOption;
use App\Models\Esp;

class AddEspDeployFields extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $esp = new Esp();
        
        $bh = new EspFieldOption();
        $bh->esp_id = $esp->where('name', "BlueHornet")->first()->id;
        $bh->email_id_field = '%%&zwj;cf_EID%%';
        $bh->email_address_field = '%%to_email%%';
        $bh->save();

        $cmp = new EspFieldOption();
        $cmp->esp_id = $esp->where('name', "Campaigner")->first()->id;
        $cmp->email_id_field = '[Contact.EmailID]';
        $cmp->email_address_field = '';
        $cmp->save();

        $ed = new EspFieldOption();
        $ed->esp_id = $esp->where('name', "EmailDirect")->first()->id;
        $ed->email_id_field = '[Database!EID]';
        $ed->email_address_field = '[Database!Email]';
        $ed->save();

        $maro = new EspFieldOption();
        $maro->esp_id = $esp->where('name', "Maro")->first()->id;
        $maro->email_id_field = '{{contact.eid}}';
        $maro->email_address_field = '{{contact.email}}';
        $maro->save();

        $pub = new EspFieldOption();
        $pub->esp_id = $esp->where('name', "Publicators")->first()->id;
        $pub->email_id_field = '$user_name$';
        $pub->email_address_field = '$user_email$';
        $pub->save();

        $ymlp = new EspFieldOption();
        $ymlp->esp_id = $esp->where('name', "Ymlp")->first()->id;
        $ymlp->email_id_field = '[EID]';
        $ymlp->email_address_field = '[EMAIL]';
        $ymlp->save();
    }
}
