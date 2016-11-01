<?php

use Illuminate\Database\Seeder;
use App\Models\SuppressionListType;

class SuppressionListTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $s1 = new SuppressionListType();
        $s1->name = 'offer';
        $s1->save();

        $s2 = new SuppressionListType();
        $s2->name = 'feed';
        $s2->save();
    }
}
