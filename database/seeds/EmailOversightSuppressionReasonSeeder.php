<?php

use Illuminate\Database\Seeder;

class EmailOversightSuppressionReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::insert( "
            INSERT INTO
                suppression_reasons (
                    id ,
                    display_status ,
                    suppression_type ,
                    display
                )
            VALUES
                ( 56 , 'Suppression because of Email Oversight - Undeliverable' , 2 , 1 ) ,
                ( 57 , 'Suppression because of Email Oversight - Catch All' , 2 , 1 ) ,
                ( 58 , 'Suppression because of Email Oversight - Role' , 2 , 1 ) ,
                ( 59 , 'Suppression because of Email Oversight - Malformed' , 2 , 1 ) ,
                ( 60 , 'Suppression because of Email Oversight - SpamTrap' , 0 , 1 ) ,
                ( 61 , 'Suppression because of Email Oversight - Complainer' , 3 , 1 ) ,
                ( 62 , 'Suppression because of Email Oversight - Bot' , 2 , 1 ) ,
                ( 63 , 'Suppression because of Email Oversight - Seed Account' , 2 , 1 ) ,
                ( 64 , 'Suppression because of Email Oversight - Disposable Email' , 2 , 1 ) ,
                ( 65 , 'Suppression because of Email Oversight - Suppressed' , 1 , 1 );
        " );
    }
}
