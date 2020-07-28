<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function __construct()
    {

    }

    protected function response($code, $status, $msg = '', $data = null)
    {
        $response = array(
            'code' => $code,
            'status' => $status,
            'message' => $msg,
            'data' => $data
        );

        return $response;
    }
}
