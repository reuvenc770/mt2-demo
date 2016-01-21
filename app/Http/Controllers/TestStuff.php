<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/13/16
 * Time: 3:41 PM
 */

namespace App\Http\Controllers;
use App\Facades\EspAccount;
use League\Csv\Reader;
use Illuminate\Support\Facades\Storage;
class TestStuff extends Controller{

    protected $apiFactory;

    public function __construct(){

    }

    public function index(){

        echo "Im in the TestStuff Controller\n\n";
        $mappings = EspAccount::grabCsvMappings("BH001");

        $reader = Reader::createFromPath(storage_path().'/app/test.csv');
        $keys = $mappings['row_headers'];
        $data = $reader->fetchAssoc($keys);
        foreach ($data as $row) {
            print_r($row);
        }
    }
}