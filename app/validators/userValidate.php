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
        if(!isset($req['role']) || trim($req['role']) == ''){
            array_push($msg,'Please fill out role');
        }
        return $msg;

    }        
    public function edit($req){
        $msg = [];
        if (!isset($req['name']) || trim($req['name']) == '') {
            array_push($msg, 'Please fill out name');
        }
        if(!isset($req['password']) || trim($req['password']) == ''){
            array_push($msg,'Please fill out password');
        }
        if(isset($req['password']) && $req['password'] != $req['repassword']){
            array_push($msg,'Password not matched');
        }
        if(!isset($req['role']) || trim($req['role']) == ''){
            array_push($msg,'Please fill out role');
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