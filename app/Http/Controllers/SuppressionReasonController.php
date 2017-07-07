<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Services\SuppressionService;

/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/11/16
 * Time: 2:25 PM
 */
class SuppressionReasonController extends Controller
{
    protected $suppressionService;

    public function __construct(SuppressionService $suppressionService)
    {
        $this->suppressionService = $suppressionService;
    }

    public function index()
    {
        return $this->suppressionService->listAllReasons();
    }

    public function create()
    {
        return response('Unauthorized.', 401);
    }

    public function store()
    {
        return response('Unauthorized.', 401);

    }

    public function show($id)
    {
        return response('Unauthorized.', 401);
    }


    public function edit()
    {
        return response('Unauthorized.', 401);
    }


    public function update($id)
    {
        return response('Unauthorized.', 401);
    }

}
