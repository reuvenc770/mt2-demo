<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/13/16
 * Time: 3:41 PM
 */

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Jobs\RetrieveReports;
class TestStuff extends Controller{

    protected $apiFactory;

    public function __construct(){

    }

    public function index(){

        echo "Im in the TestStuff Controller\n\n";
        $espDetails = DB::table('esp_accounts')
            ->where('account_number',"BH001")
            ->first();
        $this->dispatch(new RetrieveReports("BlueHornet", "BH001", '2015-01-05'));
        dd("fuck");
    }
}