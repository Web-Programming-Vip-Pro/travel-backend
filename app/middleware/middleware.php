<?php

namespace App\Middleware;
require_once('vendor/autoload.php');
require_once('app/services/authenticationService.php');
use \Firebase\JWT\JWT; 
use App\Services\AuthenticationService;

class Middleware {
    private $authenticationService;
    public function __construct(){
        $this->authenticationService = new AuthenticationService();
        return;
    }
    public function handleAdmin(){
        $authHeader = apache_request_headers()['Authorization'];
        if(!isset($authHeader)){
            return null;
        }
        $arr = explode(" ", $authHeader);
        $token = $arr[1];
        return $this->authenticationService->decodeJWTToken($token);
    }
    public function handleUser(){
        $authHeader = apache_request_headers()['Authorization'];
        if(!isset($authHeader)){
            return null;
        }
        $arr = explode(" ", $authHeader);
        $token = $arr[1];
        return $this->authenticationService->decodeJWTToken($token);
    }
}