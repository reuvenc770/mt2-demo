<?php

use Illuminate\Database\Seeder;

class SeedNav extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $navItem = new \App\Models\NavigationParent();
        $navItem->name = "Data";
        $navItem->rank = "1";
        $navItem->glyth = "glyphicon-folder-open";
        $navItem->save();

        $navItem = new \App\Models\NavigationParent();
        $navItem->name = "Deploys";
        $navItem->rank = "2";
        $navItem->glyth = "glyphicon-plane";
        $navItem->save();

        $navItem = new \App\Models\NavigationParent();
        $navItem->name = "Reporting";
        $navItem->rank = "3";
        $navItem->glyth = "glyphicon-piggy-bank";
        $navItem->save();

        $navItem = new \App\Models\NavigationParent();
        $navItem->name = "Operations";
        $navItem->rank = "4";
        $navItem->glyth = "glyphicon-blackboard";
        $navItem->save();

        $navItem = new \App\Models\NavigationParent();
        $navItem->name = "Admin";
        $navItem->rank = "5";
        $navItem->glyth = "glyphicon-user";
        $navItem->save();
    }
}
