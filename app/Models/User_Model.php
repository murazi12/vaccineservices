<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helper\ResponseBuilder;

class User_Model extends Model
{
    protected $table = 'tbl_user';
    protected $primaryKey = 'user_id';
    protected $dateFormat = 'Y-m-d H:i:s.uO';

    const CREATED_AT = 'user_addon';
    const UPDATED_AT = 'user_chon';

    function __construct() {
        parent::__construct();
    }

    public static function get_profile($id) {
        $data = self::find($id);

        $return = array (
            'id'        => ResponseBuilder::ssl_crypt($data->user_id, 1),
            'email'     => $data->user_email,
            'username'  => $data->user_name,
            'profname'  => $data->user_profilename,
            'dob'       => $data->user_dob,
            'gender'    => $data->user_gender,
            'weight'    => $data->user_weight,
            'height'    => $data->user_height,
            'phone'     => $data->user_phno,
            'avatar'    => $data->user_avatar
        );

        return $return;
    }
}
