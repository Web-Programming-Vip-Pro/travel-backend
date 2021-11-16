<?php

namespace App\Middleware;
require_once('app/services/authenticationService.php');
use App\Services\AuthenticationService;

class Middleware {
    private $authenticationService;
    public function __construct(){
        $this->authenticationService = new AuthenticationService();
    }
    public function handle(){
        $authHeader = apache_request_headers()['Authorization'];
        if(!isset($authHeader)){
            return false;
        }
        $arr = explode(" ", $authHeader);
        $token = $arr[1];
        $jwt = $this->authenticationService->decodeJWTToken($token);
        if($jwt->user == null){
            return false;
        }
        return $jwt->user;
    }
    public function handleAdmin(){
        $authHeader = apache_request_headers()['Authorization'];
        if(!isset($authHeader)){
            return false;
        }
        $arr = explode(" ", $authHeader);
        $token = $arr[1];
        $jwt = $this->authenticationService->decodeJWTToken($token);
        if($jwt->user->role == 2){
            return $jwt->user;
        }
        return false;
    }
    public function handleAgency(){
        $authHeader = apache_request_headers()['Authorization'];
        if(!isset($authHeader)){
            return false;
        }
        $arr = explode(" ", $authHeader);
        $token = $arr[1];
        $jwt = $this->authenticationService->decodeJWTToken($token);
        if($jwt->user->role != 0){
            return $jwt->user;
        }
        return false;
    }
    public function handleUser(){
        $authHeader = apache_request_headers()['Authorization'];
        if(!isset($authHeader)){
            return false;
        }
        $arr = explode(" ", $authHeader);
        $token = $arr[1];
        $jwt = $this->authenticationService->decodeJWTToken($token);
        if($jwt->user->role == 0){
            return $jwt->user;
        }
        return false;
    }
}