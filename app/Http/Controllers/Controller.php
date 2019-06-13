<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //    public function __construct()
//    {
//        $this->response =  Response::make();
//
//        $this->checkCookie($hasCookie,$cookie);
//    }
//
//    public function checkCookie($hasCookie,$cookie)
//    {
//        // If not has Cookie -> set Cookie and attach to response
//        if (! $hasCookie )
//        {
//            $this->response->withCookie(cookie()->forever('edemocracia', $cookie));
//        }
//
//    }
//
//    public function Redirect ($content)
//    {
//        return $this->response($content)->redirect();
//    }
//
//    public function View ($view) {
//
//        return $this->response->view();
//    }
}
