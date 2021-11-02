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
        $cookie = $_COOKIE['login'];
        if(!isset($cookie)){
            return -1;
        }
        $value_cookie =JWT::decode(
            $cookie, //Data to be encoded in the JWT
            $secretKey, // The signing key
            array(ALGORITHM) 
        ); 
        return $value_cookie->data->role ;
    }
    public function handleUser(){

    }
}
$middleware = new Middleware();
$middleware->handleAdmin();