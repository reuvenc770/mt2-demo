<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\Interfaces;

interface IConversion {
    public function getByDate( $dateRange = null );
}
