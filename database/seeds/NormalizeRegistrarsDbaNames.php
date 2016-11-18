<?php

use Illuminate\Database\Seeder;

class NormalizeRegistrarsDbaNames extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Registrar::where( 'dba_names' , '' )->update( [ 'dba_names' => '[]' ] );
    }
}
