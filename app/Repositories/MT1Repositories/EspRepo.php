<?php

namespace App\Repositories\MT1Repositories;


use App\Models\MT1Models\Esp;

class EspRepo
{
    protected $esp;
    public function __construct(Esp $esp)
    {
        $this->esp = $esp;
    }

    public function getEspIdAndName(){
        return $this->esp->select('espID as id', "espLabel as name")->get();
    }
}