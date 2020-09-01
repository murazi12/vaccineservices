<?php
namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Http\Helper\ResponseBuilder;
use App\Models\Hospital_Model;
use App\Models\User_Model;
use Illuminate\Http\Request;
use DB;

class HospitalController extends Controller
{
    public function __construct() {

    }

    public function hospitalList(Request $request) {
        $bearer = ($request->headers->all('authorization'));
        if(empty($bearer)) return ResponseBuilder::response(401, 'Unauthorized', 'Token is required !');

        $bearer = substr($bearer[0], (strpos($bearer[0], ' ')+1), strlen($bearer[0]));
        $auth = User_Model::where('user_mobile_token', $bearer)->first();
        if(empty($auth)) {
            return ResponseBuilder::response(401, 'Unauthorized', 'Token Expired !', '');
        }

        if(empty($request->prov_id)) return ResponseBuilder::response(400, 'Bad Request', 'Province is required !');
        if(empty($request->cit_id)) return ResponseBuilder::response(400, 'Bad Request', 'City is required !');

        $data = array();
        $rows = Hospital_Model::where('hos_active', 1)->get();
        foreach($rows as $row) {
            $data['hospital'][] = array(
                'id'        => $row->hos_id,
                'name'      => $row->hos_name,
                'addess'    => $row->hos_address,
                'pict'      => $row->hos_pict,
                'remark'     => $row->hos_remark
            );
        }
        return ResponseBuilder::response(200, 'success', '', $data);
    }
}