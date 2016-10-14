<?php

use Illuminate\Database\Seeder;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class AdmiralRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Sentinel::getRoleRepository()->createModel()->create([
            'name' => 'Admiral',
            'slug' => 'admiral',
        ]);
    }
}
