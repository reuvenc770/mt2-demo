<?php

use Illuminate\Database\Seeder;
class insertUsers extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    public function run()
    {
        $roleStandard = Sentinel::getRoleRepository()->createModel()->create([
            'name' => 'Standard',
            'slug' => 'standard',
        ]);

        $routeCollection = Route::getRoutes();
        $routlist = array();

        foreach ($routeCollection as $value) {
            $routlist[] = array("name" => $value->getName());
        }

        foreach($routlist as $route){
            //easier to remove permissions after seed.
            $roleStandard->addPermission($route['name']);
            $roleStandard->save();

        }
        $users = $this->getUsers();
        foreach($users as $entry) {
            $entry['password'] = str_random(6); // need to give random pw
            $user = Sentinel::registerAndActivate($entry);
            $roleStandard->users()->attach($user);
        }
    }

    private function getUsers(){
        return json_decode("[
  {
    \"mt1_user_id\": 269,
    \"username\": \"achin\",
    \"mt1_hash\": \"1c4db413f1544505529c45c8bd2a93d0\"
  },
  {
    \"mt1_user_id\": 256,
    \"username\": \"alondras\",
    \"mt1_hash\": \"d37903c360674d79d5ce33074972ce37\"
  },
  {
    \"mt1_user_id\": 60,
    \"username\": \"aperumal\",
    \"mt1_hash\": \"b09bd4bd525ea58e93e405af8ab9e33f\"
  },
  {
    \"mt1_user_id\": 258,
    \"username\": \"aperumal1\",
    \"mt1_hash\": \"bdbe13516e35a1c6d15bf0f9a4d65914\"
  },
  {
    \"mt1_user_id\": 74,
    \"username\": \"arapp\",
    \"mt1_hash\": \"3b1d93a5eaad040d33f9534f5a0bcf02\"
  },
  {
    \"mt1_user_id\": 203,
    \"username\": \"astern\",
    \"mt1_hash\": \"617b494b1794dd59992cf309f55edb34\"
  },
  {
    \"mt1_user_id\": 262,
    \"username\": \"blaffin\",
    \"mt1_hash\": \"c467d7fb8483b32f2b4f4cfc31213b39\"
  },
  {
    \"mt1_user_id\": 125,
    \"username\": \"despaillat\",
    \"mt1_hash\": \"1e1d63ba21b60b83140693031a902dd8\"
  },
  {
    \"mt1_user_id\": 265,
    \"username\": \"dpalumbo\",
    \"mt1_hash\": \"040ec0579fd6c82c1471df2c16edc89f\"
  },
  {
    \"mt1_user_id\": 17,
    \"username\": \"dpappas\",
    \"mt1_hash\": \"c3cdc4815096e62c3ac5c130d8060b2a\"
  },
  {
    \"mt1_user_id\": 133,
    \"username\": \"dpez\",
    \"mt1_hash\": \"3a6ba1dd865925d2267fb8d2add29b46\"
  },
  {
    \"mt1_user_id\": 85,
    \"username\": \"dpezas\",
    \"mt1_hash\": \"1b48b8914eaef3a496a566cdbc04a713\"
  },
  {
    \"mt1_user_id\": 264,
    \"username\": \"etsai\",
    \"mt1_hash\": \"b272af01b4c6487cda1e177041212d61\"
  },
  {
    \"mt1_user_id\": 250,
    \"username\": \"fbegum\",
    \"mt1_hash\": \"457ea192be3195da72f700816fce1ec2\"
  },
  {
    \"mt1_user_id\": 107,
    \"username\": \"jherlihy\",
    \"mt1_hash\": \"28fea47c087774081e104752c2195d38\"
  },
  {
    \"mt1_user_id\": 244,
    \"username\": \"jherlihydata\",
    \"mt1_hash\": \"28fea47c087774081e104752c2195d38\"
  },
  {
    \"mt1_user_id\": 2,
    \"username\": \"jsobeck\",
    \"mt1_hash\": \"38157eab2a90109f6b5e3ac7c938c539\"
  },
  {
    \"mt1_user_id\": 267,
    \"username\": \"kkim\",
    \"mt1_hash\": \"b79dcf98044e6844e95147a66488ff1c\"
  },
  {
    \"mt1_user_id\": 180,
    \"username\": \"klevarek\",
    \"mt1_hash\": \"5e2afc0688a240d3134980bc33c010ec\"
  },
  {
    \"mt1_user_id\": 232,
    \"username\": \"lbury\",
    \"mt1_hash\": \"f7c8045c2eec9c11e08389e646f9fce0\"
  },
  {
    \"mt1_user_id\": 245,
    \"username\": \"lunashin\",
    \"mt1_hash\": \"409ae6e8e6180ce019bbef52077100b2\"
  },
  {
    \"mt1_user_id\": 257,
    \"username\": \"pcunningham\",
    \"mt1_hash\": \"327c5d6c0d46531d0d0b7dd5f8ec88fb\"
  },
  {
    \"mt1_user_id\": 73,
    \"username\": \"pnamb\",
    \"mt1_hash\": \"8edd3cb7eb8961fdb0ee7cc0c467b068\"
  },
  {
    \"mt1_user_id\": 101,
    \"username\": \"ppareja\",
    \"mt1_hash\": \"89fb9808bd7fca32e8ef88ffb74b12ce\"
  },
  {
    \"mt1_user_id\": 28,
    \"username\": \"ptran\",
    \"mt1_hash\": \"240dcf2cd6c96ef4cc0f8ee2a8574437\"
  },
  {
    \"mt1_user_id\": 261,
    \"username\": \"rberscak\",
    \"mt1_hash\": \"4915eb06cb2fa626cc1871c51aa551cd\"
  },
  {
    \"mt1_user_id\": 270,
    \"username\": \"rbertorelli\",
    \"mt1_hash\": \"9b7830fbcbdd2c420d81ba51446a2bb5\"
  },
  {
    \"mt1_user_id\": 220,
    \"username\": \"sandrew\",
    \"mt1_hash\": \"e71e4360f497f5b0e2f456223b2fadcc\"
  },
  {
    \"mt1_user_id\": 194,
    \"username\": \"ssimon\",
    \"mt1_hash\": \"2d58ed154f676a9fecbd5fe6e7b580ce\"
  }
]", true);
    }

}
