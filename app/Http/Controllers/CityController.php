<?php
namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Http\Helper\ResponseBuilder;
use App\Models\City_Model;
use App\Models\User_Model;
use Illuminate\Http\Request;
use DB;

class CityController extends Controller
{
    public function __construct() {

    }

    public function cityList(Request $request) {
        $bearer = ($request->headers->all('authorization'));
        if(empty($bearer)) return ResponseBuilder::response(401, 'Unauthorized', 'Token is required !');

        $bearer = substr($bearer[0], (strpos($bearer[0], ' ')+1), strlen($bearer[0]));
        $auth = User_Model::where('user_mobile_token', $bearer)->first();
        if(empty($auth)) {
            return ResponseBuilder::response(401, 'Unauthorized', 'Token Expired !', '');
        }

        if(empty($request->prov_id)) {
            return ResponseBuilder::response(400, 'Bad Request', 'Province is required !', '');
        }

        $data = array();
        $rows = City_Model::where('cit_active', 1)->where('cit_prov_id', $request->prov_id)->orderBy('cit_name')->get();
        foreach($rows as $row) {
            $data["city"][] = array('id' => $row->cit_id, 'prov_id' => $row->cit_prov_id, 'name' => $row->cit_name);
        }

        return ResponseBuilder::response(200, 'success', '', $data);
    }
}