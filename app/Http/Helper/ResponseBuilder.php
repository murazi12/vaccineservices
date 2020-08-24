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

    public static function ssl_crypt($str, $type) { // 1 -> encrypt, 0 -> decrypt
        $key = base64_encode("53cret");
        $method = 'AES-256-CBC';
        $iv_length = openssl_cipher_iv_length($method);
        $crypt_iv =  '1a2b3c4d5e6f7g8h';
        
        if($type == 1) {
            $crypt = openssl_encrypt($str, $method, $key, 0, $crypt_iv);
        }
        else {
            $crypt = openssl_decrypt($str, $method, $key, 0, $crypt_iv);
        }

        return $crypt;
    }
}
?>