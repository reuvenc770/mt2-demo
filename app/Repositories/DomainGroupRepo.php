<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 9/28/16
 * Time: 3:12 PM
 */

namespace App\Repositories;


use App\Models\DomainGroup;
use DB;
class DomainGroupRepo
{
    protected $domainGroup;

    public function __construct(DomainGroup $domainGroup)
    {
        $this->domainGroup = $domainGroup;
    }

    public function getModel(){
        return $this->domainGroup
            ->join("email_domains", 'domain_groups.id', '=', 'email_domains.domain_group_id')
            ->select(DB::raw("domain_groups.name, count(email_domains.id) as domainCount"))
            ->groupBy("domain_groups.name");
    }

}