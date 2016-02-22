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
use Exception;
use Illuminate\Support\Facades\Request as RequestFacade;
/**
 * Class UserEventLogService
 * @package App\Services
 */
class UserEventLogService
{
    /**
     * @var UserEventLogRepo
     */
    protected $eventLogRepo;
    /**
     * @var
     */
    protected $authObject;

    /**
     * UserEventLogService constructor.
     * @param UserEventLogRepo $eventLogRepo
     */
    public function __construct(UserEventLogRepo $eventLogRepo)
    {
        $this->eventLogRepo = $eventLogRepo;
    }

    /**
     * @param $request
     * @param $response
     */
    public function trackRequest($request, $response){
            $eventFormatted = $this->decodeEvent($request, $response);
        if($eventFormatted) {
            $this->eventLogRepo->insertEvent($eventFormatted);
        }

    }

    /**
     * @param integer $userId
     * @param string $page
     * @param string $action
     * @param integer $status
     */
    public function insertCustomRequest($userId, $page, $action, $status){
        try{
            $this->eventLogRepo->insertEvent(array(
                "user_id" => $userId,
                "page"    => $page,
                "action"  => $this->decodeMethod($action),
                "ip_address" => RequestFacade::ip(),
                "status"  => $status,
            ));
        } catch (Exception $e){
            Log::error("Error inserting Custom Request:: ".$e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return array
     */
    private function decodeEvent($request, $response){
        $isJson = $request->wantsJson();
        $requestMethod = $request->getMethod();
        $statusCode = $response->getStatusCode();
        $isHtml = $request->acceptsHtml();
        $userId = Sentinel::check() ? Sentinel::getUser()->id : 0;
        $basicReturn = array(
            "user_id" => $userId,
            "page" => $request->decodedPath(),
            "ip_address" => $request->ip(),
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

    /**
     * @param $method
     * @return mixed
     */
    private function decodeMethod($method){
        $actionArray= array(
            "GET" => "Page View",
            "PUT" => "Update Record",
            "POST"=> "Create Record",

        );
        if (isset($actionArray[$method])){
            return $actionArray[$method];
        }

        return $method;
    }
}
