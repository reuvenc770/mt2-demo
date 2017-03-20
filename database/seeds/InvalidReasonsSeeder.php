<?php

use Illuminate\Database\Seeder;

class InvalidReasonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::statement("INSERT INTO invalid_reasons (id, name) 

            VALUES 
            
            (1, 'No email address'), 
            (2, 'No IP address'), 
            (3, 'No capture date'), 
            (4, 'No password'), 
            (5, 'Bad source url'), 
            (6, 'Bad IP address'), 
            (7, 'Bad email domain'), 
            (8, 'Canada'),
            (9, 'Invalid Email'),
            (10, 'Other')");
    }
}
