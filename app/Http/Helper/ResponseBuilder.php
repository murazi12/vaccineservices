<?php
namespace App\Http\Helper;

class ResponseBuilder {

    public static function response($code, $status, $msg='', $data='') {
        $response = array(
            'code' => $code,
            'status' => $status,
            'message' => $msg,
            'data' => $data
        );

        return $response;
    }
}
?>