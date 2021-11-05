<?php

namespace App\Middleware;
require_once('vendor/autoload.php');
use \Firebase\JWT\JWT; 

class Middleware {
    public function __construct(){
        return;
    }
    public function handleAdmin(){
        define('SECRET_KEY','Your-Secret-Key');  /// secret key can be a random string and keep in secret from anyone
        define('ALGORITHM','HS512');  
        $secretKey = base64_decode(SECRET_KEY);
        $authHeader = apache_request_headers()['Authorization'];
        if(!isset($authHeader)){
            return -1;
        }
        $arr = explode(" ", $authHeader);
        $jwt = $arr[1];
        $result = JWT::decode(
            $jwt, //Data to be encoded in the JWT
            $secretKey, // The signing key
            array(ALGORITHM) 
        ); 
        if(!isset($result)){
            return -1;
        }
        return $result->data->role ;
    }
    public function handleUser(){

    }
}