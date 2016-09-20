<?php

use Illuminate\Database\Seeder;
use App\Models\EspAccountImageLinkFormat;
use App\Repositories\EspApiAccountRepo;

class EspAccountImageFormatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $removeFileExtension = ['PUB008', 'PUB009', 'PUB010', 'PUB014'];

        $espAccountRepo = App::make(EspApiAccountRepo::class);

        $accounts = $espAccountRepo->getAllAccounts();

        foreach($accounts as $account) {
            $linkFormat = new EspAccountImageLinkFormat();
            $linkFormat->esp_account_id = $account->id;

            if ($account->esp->name === 'Publicators') {
                if (!in_array($account->account_name, $removeFileExtension)) {
                    $linkFormat->remove_file_extension = 1;
                }
                else {
                    $linkFormat->remove_file_extension = 0;
                }
                $linkFormat->url_format = "http://{{CONTENT_DOMAIN}}/{{FILE_NAME}}";
            }
            elseif (preg_match('/BlueHornet|Maro/', $account->esp->name)) {
                $linkFormat->remove_file_extension = 0;
                $linkFormat->url_format = "http://{{CONTENT_DOMAIN}}/{{FILE_NAME}}";
            }
            else {
                $linkFormat->remove_file_extension = 0;
                $linkFormat->url_format = "./images/{{FILE_NAME}}";
            }

            $linkFormat->save();

        }
    }
}
