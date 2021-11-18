<?php
/**
 *  class validate user
 * 
 * 
*/
namespace App\Validator;

class UserValidate{
    public function __construct(){
        return;
    }
    public function add($req){
        $msg = [];
        if (!isset($req['name']) || trim($req['name']) == '') {
            array_push($msg, 'Please fill out name');
        }
        if(!isset($req['email']) || trim($req['email']) == ''){
            array_push($msg,'Please fill out email');
        }
        if(!isset($req['password']) || trim($req['password']) == ''){
            array_push($msg,'Please fill out password');
        }
        if(isset($req['password']) && $req['password'] != $req['repassword']){
            array_push($msg,'Password not matched');
        }
        return $msg;

    }        
    public function edit($req){
        $msg = [];
        if (!isset($req['name']) || trim($req['name']) == '') {
            array_push($msg, 'Please fill out name');
        }
        if(!isset($req['email']) || trim($req['email']) == ''){
            array_push($msg,'Please fill out email');
        }
        return $msg;
    }       
    public function changePassword($req){
        $msg = [];
        if(!isset($req['oldPassword']) || trim($req['oldPassword']) == ''){
            array_push($msg,'Please fill out oldPassword');
        }
        if(!isset($req['newPassword']) || trim($req['newPassword']) == ''){
            array_push($msg,'Please fill out newPassword');
        }
        if(isset($req['newPassword']) && $req['newPassword'] != $req['reNewPassword']){
            array_push($msg,'newPassword not matched');
        }
        return $msg;
    }       
    public function login($req){
        $msg = [];
        if(!isset($req['email']) || trim($req['email']) == ''){
            array_push($msg,'Please fill out email');
        }
        if(!isset($req['password']) || trim($req['password']) == ''){
            array_push($msg,'Please fill out password');
        }
        return $msg;
    }  
    public function forget($req){
        $msg = [];
        if(!isset($req['email']) || trim($req['email']) == '' ){
            array_push($msg,'Please fill out email');
        }
        return $msg;
    }  
    public function register($req){
        $msg = [];
        if (!isset($req['name']) || trim($req['name']) == '') {
            array_push($msg, 'Please fill out name');
        }
        if(!isset($req['email']) || trim($req['email']) == ''){
            array_push($msg,'Please fill out email');
        }
        if(!isset($req['password']) || trim($req['password']) == ''){
            array_push($msg,'Please fill out password');
        }
        if(isset($req['password']) && $req['password'] != $req['repassword']){
            array_push($msg,'Password not matched');
        }
        return $msg;

    }        

}
?>