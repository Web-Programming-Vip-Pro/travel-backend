<?php

namespace App\Middleware;
require_once('app/services/authenticationService.php');
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
        $jwt = $this->authenticationService->decodeJWTToken($token);
        if($jwt->user[0]->role == '0'){
            return $jwt->user[0];
        }
        return false;
    }
    public function handleAgency(){
        $authHeader = apache_request_headers()['Authorization'];
        if(!isset($authHeader)){
            return null;
        }
        $arr = explode(" ", $authHeader);
        $token = $arr[1];
        $jwt = $this->authenticationService->decodeJWTToken($token);
        if($jwt->user[0]->role == '1'){
            return $jwt->user[0];
        }
        return false;
    }
    public function handleUser(){
        $authHeader = apache_request_headers()['Authorization'];
        if(!isset($authHeader)){
            return null;
        }
        $arr = explode(" ", $authHeader);
        $token = $arr[1];
        $jwt = $this->authenticationService->decodeJWTToken($token);
        if($jwt->user[0]->role == '2'){
            return $jwt->user[0];
        }
        return false;
    }
}