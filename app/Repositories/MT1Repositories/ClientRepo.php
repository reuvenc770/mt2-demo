<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/16/16
 * Time: 2:31 PM
 */

namespace App\Repositories\MT1Repositories;


use App\Models\MT1Models\Client;
use Illuminate\Database\DatabaseManager;
use DB;
class ClientRepo
{
    protected $client;
    protected $db;

    public function __construct(Client $client, DatabaseManager $db)
    {
        $this->client = $client;
        $this->db = $db;
    }


    public function getClientTypes(){

        $type = $this->db->connection('mt1mail')->select(DB::raw('SHOW COLUMNS FROM user WHERE Field = "client_type"'))[0]->Type;
        preg_match('/^enum\((.*)\)$/', $type, $matches);
        $values = array();
        foreach(explode(',', $matches[1]) as $value){
            $values[] = array("name" => trim($value, "'"), "value" => trim($value, "'"));
        }
        return $values;
    }

}