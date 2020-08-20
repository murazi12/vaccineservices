<?php
namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Http\Helper\ResponseBuilder;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {

    }

    public function login(Request $request) {
        if(!$request->has('username') || empty($request->username)) {
            return ResponseBuilder::response(400, 'Bad Request', 'Email / Username is required !');
        }
        elseif(!$request->has('password') || empty($request->password)) {
            return ResponseBuilder::response(400, 'Bad Request', 'Password is required !');
        }
    
        $sql = "SELECT * FROM tbl_user WHERE user_email = '".$request->username."' OR user_name = '".$request->username."'";
        echo $sql;
        // Break 20200821 0140
    }

    public function register(Request $request)
    {
        $data = null;

        if(!$request->has('email') || empty($request->email)) {
            return ResponseBuilder::response(400, 'Bad Request', 'Email is required !');
        }
        elseif(!$request->has('username') || empty($request->username)) {
            return ResponseBuilder::response(400, 'Bad Request', 'Username is required !');
        }
        elseif(!$request->has('password') || empty($request->password)) {
            return ResponseBuilder::response(400, 'Bad Request', 'Password is required !');
        }

        $check = app('db')->select("SELECT * FROM tbl_user WHERE user_email = '".$request->email."' OR user_name = '".$request->username."'");
        if(count($check) > 0) {
            return ResponseBuilder::response(500, 'Internal Server Error', 'Email / username has been registered !');
        }

        $pass = password_hash($request->password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO tbl_user (user_email, user_name, user_password, user_active, user_addon) VALUES ('".$request->email."', '".$request->username."', '".$pass."', 0, current_timestamp)";
        try {
            $reg = app('db')->insert($sql);
            if(!$reg) {
                throw new Exception("Error in SQL syntax");
            }
            $code = 200;
            $status = "success";
            $msg = "Please check your email for account activation";
            $data = array(
                'registered' => true
            );

            self::sendMail($request->email, $request->username);
        }
        catch(Exception $e) {
            $code = 500;
            $status = 'Internal Server Error';
            $msg = $e->getMessage();
        }
        finally {
            return ResponseBuilder::response($code, $status, $msg, $data);
        }
    }

    public function sendMail($mailto,$recipient) {
        $data = [
            'title' => 'Please follow the link below to activate your account',
            'link' => url('/activate/'.$mailto)
        ];

        Mail::send('emails.activation', $data, function($message) use ($mailto, $recipient) {
            $message->to($mailto, $recipient)->subject('User Activation');
        });
    }

    public function activate(Request $request) {
        try {
            $act = app('db')->table('tbl_user')->where('user_email', $request->user)->update(['user_active' => 1]);
            if($act < 1) {
                $msg = "User Not Found !";
            }
            else {
                $msg = "Activate Success !";
            }
        }
        catch(\Illuminate\Database\QueryException $ex) {
            $msg = $ex->getMessage();
        }
        finally {
            echo $msg;
        }
    }

    /*
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
        }
        else {
            $row = app('db')->update("UPDATE tbl_auth SET auth_token = '".$key."', auth_otp = '".$otp."', auth_chdt = '".date('Y-m-d H:i:s')."' WHERE auth_phno = ?", array($request->phno));
        }

        $data = array(
            'OTP' => $otp,
            'key' => $key
        );

        return parent::response(200,'success','',$data);
    }

    public function verificationOTP(Request $request)
    {
        $header = apache_request_headers();
        if(!isset($header['Token']) || empty($header['Token'])) {
            return parent::response(400, 'Bad Request', 'Token is required !');
        }
        else if(!$request->has('otp')) {
            return parent::response(400, 'Bad Request', 'OTP is required !');
        }
        
        $data = app('db')->select("SELECT * FROM tbl_auth WHERE auth_OTP = '".$request->otp."' AND auth_token = '".$header['Token']."' ");
        if(empty($data)) {
            return parent::response('200', 'success', 'Token or OTP is not valid !', array('phno' => '', 'verified' => false));
        }

        return parent::response('200', 'success', '', array('phno' => $data[0]->auth_phno, 'verified' => true));
    }
    */
}