<?php
namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {

    }

    public function getOTP(Request $request)
    {
        $otp =  (string)mt_rand(100000, 999999);
        $key = str_random(32);
        $res = array(
            'code' => 200,
            'status' => 'success',
            'message' => '',
            'data' => array(
                'OTP' => $otp,
                'key' => $key
            )
        );
        return $res;
    }
}