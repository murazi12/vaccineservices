<?php
namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Http\Helper\ResponseBuilder;
use App\Models\Layanan_Model;
use App\Models\User_Model;
use Illuminate\Http\Request;
// use DB;

class LayananController extends Controller
{
    public function __construct() {

    }

    public function layananList(Request $request) {
        $bearer = ($request->headers->all('authorization'));
        if(empty($bearer)) return ResponseBuilder::response(401, 'Unauthorized', 'Token is required !');

        $bearer = substr($bearer[0], (strpos($bearer[0], ' ')+1), strlen($bearer[0]));
        $auth = User_Model::where('user_mobile_token', $bearer)->first();
        if(empty($auth)) {
            return ResponseBuilder::response(401, 'Unauthorized', 'Token Expired !', '');
        }

        if(empty($request->hos_id)) return ResponseBuilder::response(400, 'Bad Request', 'Hospital is required !');

        $data = array();
        $rows = Layanan_Model::where('lay_active', 1)->where('lay_hos_id', $request->hos_id)->get();
        foreach($rows as $row) {
            $data['layanan'][] = array(
                'id'        => $row->lay_id,
                'code'      => $row->lay_code,
                'name'      => $row->lay_name,
                'tarif'     => $row->lay_tarif,
                'remark'    => $row->lay_remark
            );
        }
        return ResponseBuilder::response(200, 'success', '', $data);
    }
}