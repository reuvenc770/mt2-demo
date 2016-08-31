<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = Carbon::now();

        DB::table( 'pages' )->insert( [
            [ 'name' => 'home' , 'created_at' => $date , 'updated_at' => $date ] ,
            [ 'name' => 'espapi.list' , 'created_at' => $date , 'updated_at' => $date ] ,
            [ 'name' => 'feed.list' , 'created_at' => $date , 'updated_at' => $date ] ,
            [ 'name' => 'role.list' , 'created_at' => $date , 'updated_at' => $date ] ,
            [ 'name' => 'user.list' , 'created_at' => $date , 'updated_at' => $date ] ,
            [ 'name' => 'clientgroup.list' , 'created_at' => $date , 'updated_at' => $date ] ,
            [ 'name' => 'tools.recordlookup' , 'created_at' => $date , 'updated_at' => $date ] ,
            [ 'name' => 'listprofile.list' , 'created_at' => $date , 'updated_at' => $date ] ,
            [ 'name' => 'dataexport.list' , 'created_at' => $date , 'updated_at' => $date ] ,
            [ 'name' => 'ymlpcampaign.list' , 'created_at' => $date , 'updated_at' => $date ] ,
            [ 'name' => 'devtools.jobs' , 'created_at' => $date , 'updated_at' => $date ] ,
            [ 'name' => 'tools.bulksuppression' , 'created_at' => $date , 'updated_at' => $date ] ,
            [ 'name' => 'client.attribution' , 'created_at' => $date , 'updated_at' => $date ] ,
            [ 'name' => 'datacleanse.list' , 'created_at' => $date , 'updated_at' => $date ]
        ] );
    }
}
