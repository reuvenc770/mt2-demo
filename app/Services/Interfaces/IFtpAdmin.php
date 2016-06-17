<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\Interfaces;

interface IFtpAdmin {
    public function saveFtpUser ( $credentials );

    public function findNewFtpUsers ();
}
