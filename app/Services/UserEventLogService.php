<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/22/16
 * Time: 10:17 AM
 */

namespace App\Services;


use Illuminate\Http\Request;
use App\Models\UserEventLog;
use App\Repositories\UserEventLogRepo;
use Illuminate\Http\Response;
use Sentinel;
use Log;
class UserEventLogService
{
    protected $eventLogRepo;
    protected $authObject;
    public function __construct(UserEventLogRepo $eventLogRepo)
    {
        $this->eventLogRepo = $eventLogRepo;
    }

    public function trackRequest($request,$response){
            $eventFormatted = $this->decodeEvent($request, $response);
        if($eventFormatted) {
            $this->eventLogRepo->insertEvent($eventFormatted);
        }

    }

    private function decodeEvent($request, $response){
        $isJson = $request->wantsJson();
        $requestMethod = $request->getMethod();
        $statusCode = $response->getStatusCode();
        $isHtml = $request->acceptsHtml();
        $userId = Sentinel::check() ? Sentinel::getUser()->id : 0;
        $basicReturn = array(
            "user_id" => $userId,
            "page" => $request->decodedPath(),
            "action" => $this->decodeMethod($requestMethod),
        );

        //I know this could be one huge if statement but rather break it up
        if($isJson && $statusCode == 200 && ($requestMethod == "POST" || $requestMethod == "PUT")){  //AJax update or create
            $basicReturn['status'] = UserEventLog::SUCCESS;
            return $basicReturn;
        } elseif ($isJson && $statusCode == 422){ //Validation Failed Form
            $basicReturn['status'] = UserEventLog::VALIDATION_FAILED;
           return $basicReturn;
        } elseif (!$isJson && $statusCode == 200 && $isHtml) {  //Normal Page view none-api
            $basicReturn['status'] = UserEventLog::SUCCESS;
            return $basicReturn;
        }
    }

    private function decodeMethod($method){
        $actionArray= array(
            "GET" => "Page View",
            "PUT" => "Update Record",
            "POST"=> "Create Record",

        );
        return $actionArray[$method];
    }
}
