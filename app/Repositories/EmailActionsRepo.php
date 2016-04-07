<?php

namespace App\Repositories;

use App\Models\EmailAction;
use DB;
use PDO;
use Illuminate\Database\Query\Builder;
use Carbon\Carbon;;

/**
 *
 */
class EmailActionsRepo {
  
    private $actions;

    public function __construct(EmailAction $actions) {
        $this->actions = $actions;
    } 

    public function insertAction($data) {
        $this->actions->insert($data);
    }

    public function pullActionsInLast($lookback) {
        // In line with expectations, this got enormous
        // running an unbuffered query (and returning the resource)
        $pdo = DB::connection('reporting_data')->getPdo();
        /*
        $statement = $pdo->prepare(
            "SELECT * FROM email_actions WHERE id > :id", 
            array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false )
        );
        $statement->bindParam(':id', $lookback, PDO::PARAM_INT);
        $statement->execute();
        return $statement;
        */
        
        // Testing a slightly different implementation
        // in the perhaps naive hope that it works
        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        $result = $pdo->query("SELECT * FROM email_actions WHERE id > $lookback");
        return $result;
        
    }
}