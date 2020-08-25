<?php
namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Http\Helper\ResponseBuilder;
use App\Models\User_Model;
use Illuminate\Http\Request;
use DB;

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

        $username = strtolower($request->username);
        $sql = "SELECT * FROM tbl_user WHERE user_active = 1 AND (user_email = '".$username."' OR user_name = '".$username."')";
        $row = app('db')->select($sql);

        if(empty($row)) {
            return ResponseBuilder::response(401, 'Unauthorized', 'Username / Email not found !');
        }
        
        if(!password_verify($request->password, $row[0]->user_password)) {
            return ResponseBuilder::response(401, 'Unauthorized', 'Wrong password !');
        }

        $token = hash('sha1', time().md5('53cr3t'));
        $act = app('db')->table('tbl_user')->where('user_id', $row[0]->user_id)->update(['user_mobile_token' => $token]);
        if($act < 1) {
            return ResponseBuilder::response(500, 'Internal Server Error', 'Couldn`t generate token');
        }

        $msg = "";
        $data = array(
            'profile' => User_Model::get_profile($row[0]->user_id),
            'token' => $token
        );
        return responseBuilder::response(200, 'success', $msg, $data);
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
            $data = array('registered' => false);
            return ResponseBuilder::response(500, 'Internal Server Error', 'Email / username has been registered !', $data);
        }

        $email = strtolower($request->email);
        $uname = strtolower($request->username);
        $pass = password_hash($request->password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO tbl_user (user_email, user_name, user_password, user_active, user_addon) VALUES ('".$email."', '".$uname."', '".$pass."', 0, current_timestamp)";
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
        $param = responseBuilder::ssl_crypt($mailto, 1);
        $data = [
            'title' => 'Please follow the link below to activate your account',
            'link' => url('/activate?usr='.$param)
        ];

        Mail::send('emails.activation', $data, function($message) use ($mailto, $recipient) {
            $message->to($mailto, $recipient)->subject('User Activation');
        });
    }

    public function activate() {
        $email = responseBuilder::ssl_crypt($_REQUEST['usr'],0);

        try {
            $act = app('db')->table('tbl_user')->where('user_email', $email)->update(['user_active' => 1]);
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

    public function getProfile(Request $request) {
        $bearer = ($request->headers->all('authorization'));
        if(empty($bearer)) return ResponseBuilder::response('401', 'Unauthorized', 'Token is required !');

        $bearer = substr($bearer[0], (strpos($bearer[0], ' ')+1), strlen($bearer[0]));

        $row = User_Model::where('user_mobile_token', $bearer)->first();
        if(empty($row)) {
            return ResponseBuilder::response('401', 'Unauthorized', 'Token Expired !', '');
        }

        $code = 200;
        $status = 'success';
        $msg = '';
        $data = array(
            'profile' => User_Model::get_profile($row->user_id)
        );

        return ResponseBuilder::response($code, $status, $msg, $data);
    }

    public function updateProfile(Request $request) {
        $bearer = ($request->headers->all('authorization'));
        if(empty($bearer)) return ResponseBuilder::response('401', 'Unauthorized', 'Token is required !');

        $bearer = substr($bearer[0], (strpos($bearer[0], ' ')+1), strlen($bearer[0]));
        $row = User_Model::where('user_mobile_token', $bearer)->first();
        if(empty($row)) {
            return ResponseBuilder::response('401', 'Unauthorized', 'Token Expired !', '');
        }

        try{
            $gender = empty($request->gender) ? $row->user_gender : $request->gender;
            $dob = empty($request->dob) ? $row->user_dob : $request->dob;
            $profname = empty($request->profname) ? $row->user_profilename : $request->profname;
            $phno = empty($request->phno) ? $row->user_phno : $request->phno;
            $weight = empty($request->weight) ? $row->user_weight : $request->weight;
            $height = empty($request->height) ? $row->user_height : $request->height;

            $code = 200;
            $status = 'success';
            $act = User_Model::where('user_id', $row->user_id)->update([
                'user_gender' => $gender,
                'user_dob' => $dob,
                'user_profilename' => $profname,
                'user_phno' => $phno,
                'user_weight' => $weight,
                'user_height' => $height
            ]);
            
            if(!$act) {
                $msg = "Update profile failed, user not found";
            }
            else {
                $msg = "Profile updated !";
            }
        }
        catch(\Illuminate\Database\QueryException $ex) {
            $code = 500;
            $status = 'Internal server error';
            $msg = $ex->getMessage();
        }
        finally {
            $data = User_Model::get_profile($row->user_id);
            return ResponseBuilder::response($code, $status, $msg, $data);
        }
    }

    public function updateAvatar(Request $request) {
        $bearer = ($request->headers->all('authorization'));
        if(empty($bearer)) return ResponseBuilder::response('401', 'Unauthorized', 'Token is required !');

        $bearer = substr($bearer[0], (strpos($bearer[0], ' ')+1), strlen($bearer[0]));
        $row = User_Model::where('user_mobile_token', $bearer)->first();
        if(empty($row)) {
            return ResponseBuilder::response('401', 'Unauthorized', 'Token Expired !', '');
        }

        try {
            $avatar = empty($request->avatar) ? $row->user_avatar : $request->avatar;

            $code = 200;
            $status = 'success';
            $msg = '';
            $act = User_Model::where('user_id', $row->user_id)->update([
                'user_avatar' => $avatar
            ]);
        }
        catch(\Illuminate\Database\QueryException $ex) {
            $code = 500;
            $status = 'Internal server error';
            $msg = $ex->getMessage();
        }
        finally {
            $data = array(
                'avatar' => User_Model::where('user_id', $row->user_id)->first()->user_avatar
            );
            return ResponseBuilder::response($code, $status, $msg, $data);
        }
    }
}