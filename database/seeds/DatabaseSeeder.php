<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $this->call(EspTableSeeder::class);
        $this->call(UserRoles::class);
        $this->call(YmlpSeeder::class);
        $this->call(EmailTablesSeeder::class);
        $this->call(InsertUsers::class);
        $this->call(EspAdds::class);
        $this->call(CsvDeliverablesSeeder::class);
    }
}
