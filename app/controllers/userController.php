<?php

namespace App\Controllers;

include_once('app/models/userModel.php');
include_once('core/http/Container.php');

use App\Models\UserModel;
use Core\Http\BaseController;

class userController extends BaseController
{
    private $user;
    public function __construct()
    {
        $this->user = new UserModel();
    }
    public function index()
    {
        $result = $this->user->get();
        $result = json_encode($result);
        print_r($result);
    }
    public function postAdd()
    {
        $req = $_POST;
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
        if(count($msg) >0){
            echo "Một vài trường chưa được điền đầy đủ";
            return;
        }
        $data =[
            "name"  => $req["name"],
            "email" => $req["email"],
            "password" => $req["password"],
            "role" => $req["role"],
        ];
        $resultByEmail = $this->user->getByEmail($data['email']);
        if(count($resultByEmail)>0){
            echo "Tài khoản đã tồn tại";
            return;
        }
        $result = $this->user->create($data);
        if($result ==1){
            print_r("Add user success");
            return ;
        }
        echo "Add user error";
        return ;
    }
    public function getEdit()
    {
        $id = (int)$_REQUEST['id'];
        if($id == 0){
            echo "Id khong co";
            return;
        }
        $result = $this->user->get($id);
        if($result == null){
            echo "Id khong chinh xac";
            return;
        }
        print_r($result);
        return;
    }
    /*
    ***
    *error : update sql with email đã tồn tại;
    ***
    */
    public function postEdit()
    {
        $req = $_POST;
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
        // validator
        if(count($msg) >0){
            echo "Một vài trường chưa được điền đầy đủ";
            return;
        }
        // data req
        $data =[
            "name"  => $req["name"],
            "email" => $req["email"],
            "role" => $req["role"],
        ];
        // nếu có password mới update
        if(isset($req['password'])){
            $data['password'] = $req['password'];
        }
        $id = (int)$_REQUEST['id'];
        // check param có id không
        if($id == 0){
            echo "Vui lòng nhập Id";
            return;
        }
        $resultGetById = $this->user->get($id);
        // check user co ton tai khong ?
        if($resultGetById == null){
            echo "Id Không tồn tại";
            return;
        }
        // check email đã tồn tại chưa ?
        if($resultGetById['email' != $data['email']]){
            $resultByEmail = $this->user->getByEmail($data['email']);
            echo $resultByEmail;
            if(count($resultByEmail)>0){
                echo "Tài khoản đã tồn tại";
                return;
            }
        }
        $result = $this->user->update($id,$data);
        if($result == 1){
            print_r("Add user success");
            return ;
        }
        echo "Add user error";
        return ;
    }
    public function delete()
    {
        $id = (int)$_REQUEST['id'];
        if($id == 0){
            echo "Vui lòng nhập Id";
            return;
        }
        $resultGetById = $this->user->get($id);
        if($resultGetById == null){
            echo "Id khong tồn tại";
            return;
        }
        $this->user->delete($id);
        print_r("Delete user success");
        return ;
    }
}
