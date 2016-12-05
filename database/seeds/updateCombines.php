<?php

use Illuminate\Database\Seeder;

class updateCombines extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $profiles = \App\Models\ListProfile::all();

        foreach($profiles as $profile){
            $combine = new \App\Models\ListProfileCombine();
            if($combine->where('list_profile_id',$profile->id)->first())
            if($combine){
                continue;
            }
            $nC = new \App\Models\ListProfileCombine();
            $nC->name = $profile->name;
            $nC->list_profile_id = $profile->id;
            $nC->save();
            $nC->listProfiles()->attach($profile->id);
        }

    }
}
