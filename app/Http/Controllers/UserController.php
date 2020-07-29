<?php
namespace App\Http\Controllers;

use App\User;
// use DB;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {

    }

    public function getOTP(Request $request)
    {
        if(!$request->has('phno')) {
            return parent::response(400, 'Bad Request');
        }

        $otp =  (string)mt_rand(100000, 999999);
        $key = str_random(1).''.md5(date('d-m-Y H:i:s')).''.str_random(1);

        $check = app('db')->select("SELECT COUNT(*) AS data FROM tbl_auth WHERE auth_phno = '".$request->phno."'");
        if($check[0]->data == 0) {
            $row = app('db')->insert("INSERT INTO tbl_auth (auth_phno, auth_token, auth_otp, auth_crdt) VALUES (?, ?, ?, ?)", array($request->phno, $key, $otp, date('Y-m-d H:i:s')));
            return $check;
        }
        /*
        else {
            $row = app('db')->update("UPDATE tbl_auth SET auth_token = '".$key."', auth_otp = '".$otp."', auth_chdt = '".date('Y-m-d H:i:s')."' WHERE auth_phno = ?", array($request->phno));
        }

        $data = array(
            'OTP' => $otp,
            'key' => $key
        );

        return parent::response(200,'success','',$data);
        */
    }

    public function verificationOTP(Request $request)
    {
        $header = apache_request_headers();
        if(!isset($header['token']) || empty($header['token'])) {
            return parent::response(400, 'Bad Request', 'Token is required !');
        }
        else if(!$request->has('otp')) {
            return parent::response(400, 'Bad Request', 'OTP is required !');
        }
        
        $data = app('db')->select("SELECT * FROM tbl_auth WHERE auth_OTP = '".$request->otp."' AND auth_token = '".$header['token']."' ");
        if(empty($data)) {
            return parent::response('200', 'success', 'Token or OTP is not valid !', array('phno' => '', 'verified' => false));
        }

        return parent::response('200', 'success', '', array('phno' => $data[0]->auth_phno, 'verified' => true));
    }
}