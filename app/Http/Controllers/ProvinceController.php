<?php
namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Http\Helper\ResponseBuilder;
use App\Models\Province_Model;
use App\Models\User_Model;
use Illuminate\Http\Request;
use DB;

class ProvinceController extends Controller
{
    public function __construct() {

    }

    public function provinceList(Request $request) {
        $bearer = ($request->headers->all('authorization'));
        if(empty($bearer)) return ResponseBuilder::response(401, 'Unauthorized', 'Token is required !');

        $bearer = substr($bearer[0], (strpos($bearer[0], ' ')+1), strlen($bearer[0]));
        $auth = User_Model::where('user_mobile_token', $bearer)->first();
        if(empty($auth)) {
            return ResponseBuilder::response(401, 'Unauthorized', 'Token Expired !', '');
        }

        $data = array();
        $rows = Province_Model::where('prov_active', 1)->orderBy('prov_name')->get();
        $i = 0;
        foreach($rows as $row) {
            // $data["provinsi"][] = array('id' => $row->prov_id, 'name' => $row->prov_name);
            $data["provinsi"][$i]["id"] = $row->prov_id;
            $data["provinsi"][$i]["name"] = $row->prov_name;
            $i++;
        }

        return ResponseBuilder::response(200, 'success', '', $data);
    }
}