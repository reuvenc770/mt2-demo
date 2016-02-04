<?php

namespace App\Repositories;

use App\Models\Esp;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

/**
 * Class EspApiRepo
 * @package App\Repositories
 */
class EspApiRepo
{
    /**
     * @var Esp
     */
    protected $esp;

    /**
     * EspApiRepo constructor.
     * @param Esp $esp
     */
    public function __construct( Esp $esp )
    {
        $this->esp = $esp;
    }

    /**
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAllEsps () {
        return $this->esp->all();
    }
}
