<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/11/17
 * Time: 2:30 PM
 */

namespace App\Repositories;


use App\Models\AWeberSubscriber;
use DB;
class AWeberSubscriberRepo
{

    protected $subscriber;

    public function __construct(AWeberSubscriber $subscriber)
    {
        $this->subscriber;
    }


    public function massUpsert($data)
    {
        DB::statement("
                    INSERT INTO a_weber_subscribers
                        ( email_address , internal_id )    
                    VALUES
                        " . join(' , ', $data) . "
                    ON DUPLICATE KEY UPDATE
                        email_address = email_address ,
                        internal_id = internal_id"
        );
    }
}
