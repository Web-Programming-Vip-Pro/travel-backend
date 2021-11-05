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
        if (!isset($req['name'])) {
            array_push($msg, 'Vui lòng điền tên đầy đủ');
        }
        if(!isset($req['email'])){
            array_push($msg,'Vui lòng điền email');
        }
        if(!isset($req['password'])){
            array_push($msg,'Vui lòng điền password');
        }
        if(isset($req['password']) && $req['password'] != $req['repassword']){
            array_push($msg,'Mật khẩu nhập lại không chính xác');
        }
        if(!isset($req['role'])){
            array_push($msg,'Vui lòng chọn role');
        }
        return $msg;

    }        
    public function edit($req){
        $msg = [];
        if (!isset($req['name'])) {
            array_push($msg, 'Vui lòng điền tên đầy đủ');
        }
        if(!isset($req['email'])){
            array_push($msg,'Vui lòng điền email');
        }
        if(isset($req['password']) && $req['password'] != $req['repassword']){
            array_push($msg,'Mật khẩu nhập lại không chính xác');
        }
        if(!isset($req['role'])){
            array_push($msg,'Vui lòng chọn role');
        }
        return $msg;
    }       
}
?>