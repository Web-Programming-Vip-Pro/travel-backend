<?php

namespace App\Controllers;

require_once('app/models/userModel.php');
require_once('core/http/Container.php');
require_once('app/middleware/middleware.php');
require_once('app/validators/userValidate.php');
require_once('app/services/authenticationService.php');
require_once('app/services/userService.php');
require_once('vendor/autoload.php');
require_once('storage/helper.php'); 
use App\Models\UserModel;
use Core\Http\BaseController;
use App\Middleware\Middleware;
use App\Validator\UserValidate;
use App\Services\AuthenticationService;
use App\Services\UserService;
use Storage\Helper;

class userController extends BaseController
{
    private $user;
    private $middleware;
    private $validate;
    private $helper;
    private $authenticationService;
    private $userService;
    public function __construct()
    {
        $this->user         = new UserModel();
        $this->middleware   = new Middleware();
        $this->validate     = new UserValidate();
        $this->helper       = new Helper();
        $this->authenticationService = new AuthenticationService();
        $this->userService = new UserService();
    }
    public function index()
    {   
        /**
         * middleware user
         */
        $authHeader = apache_request_headers()['Authorization'];
        if(!isset($authHeader)){
            return null;
        }
        $arr = explode(" ", $authHeader);
        $token = $arr[1];
        $jwt = $this->authenticationService->decodeJWTToken($token);
        if($jwt == null){
            $msg = [
                'status'    => 'Unauthorized',
                'msg'       => 'You are not loged in',
                'data'      => null
            ];
            return $this->status(401,$msg);
        }  
        // when accessed,get data users
        $msg = [
            'status'    => 'success',
            'msg'       => 'Get jwt user',
            'data'      => $jwt
        ];
        return $this->status(200,$msg);
    }
    public function list()
    {   
        // when accessed,get data users
        $result = $this->user->get();
        $msg = [
            'status'    => 'success',
            'msg'       => 'Get list users',
            'data'      => $result
        ];
        return $this->status(200,$msg);
    }
    
    public function postAdd()
    {
        $req = $_POST;
        $msgs = $this->validate->add($req);
        if(count($msgs) >0){
            $msg=[
                'status'    => 'error',
                'msg'       => 'Some field not fill in',
                'data'      => $msgs
            ];
            return $this->status(422,$msg);
        }
        $data = $this->userService->add($req);
        $resultByEmail = $this->user->getByEmail($data['email']);
        if($resultByEmail != false){
            $msg=[
                'status'    =>  'error',
                'msg'       =>  'User existed',
                'data'      =>  null
            ];
            return $this->status(500,$msg);
        }
        $result = $this->user->create($data);
        if($result == true){
            $JWT = $this->authenticationService->generateJWTToken($result);
            $msg=[
                'status'    =>'Created',
                'msg'       =>'Add user to database success',
                'data'      => $JWT
            ];
            return $this->status(201,$msg);
        }
        $msg=[
            'status'    => 'error',
            'msg'       =>'Add user to database fail',
            'data'      => null
        ];
        return $this->status(500,$msg);
    }
    public function getEdit()
    {   
        $id = (int)$_REQUEST['id'];
        if($id == 0){
            $msg = [
                'status'    =>  'error',
                'msg'       =>  'Id not fill in',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $result = $this->user->get($id);
        if($result == null){
            $msg = [
                'status'    =>  'error',
                'msg'       =>  'Id not existed',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $msg = [
            'status'    =>  'success',
            'msg'       =>  'Get user by id',
            'data'      => $result
        ];
        return $this->status(200,$msg);
    }
    /*
    ***
    *error : update sql with email đã tồn tại;
    ***
    */
    public function postEdit()
    {
        $req = $_POST;
        $id = (int)$_REQUEST['id'];
        // check param có id không
        if($id == 0){
            $msg =[
                'status'    => 'error',
                'msg'       =>  'Id not filled in',
                'data'      => null, 
            ];
            return $this->status(500,$msg);
        }
        $resultGetById = $this->user->get($id);
        // check user co ton tai khong ?
        if($resultGetById == null){
            $msg=[
                'status'    => 'error',
                'msg'       =>  'Id not exactly',
                'data'      => null, 
            ];
            return $this->status(500,$msg);
        }
        // check email đã tồn tại chưa ?
        if($resultGetById['email'] != $req['email']){
            $resultByEmail = $this->user->getByEmail($req['email']);
            if($resultByEmail != false){
                $msg = [
                    'status'    => 'error',
                    'msg'       =>  'User existed',
                    'data'      => null, 
                ];
                return $this->status(500,$msg);
            }
        }
        // validator
        $msgs = $this->validate->edit($req);
        if(count($msgs) > 0){
            $msg=[
                'status'    => 'error',
                'msg'       => 'Some field not fill in',
                'data'      => $msgs
            ];
            return $this->status(422,$msg);
        }
        // data req
        $data = $this->userService->edit($req);
        $result = $this->user->update($id,$data);
        if($result == true){
            $msg = [
                'status'    => 'success', 
                'msg'       => 'Update user success',
                'data'      => null
            ];
            return $this->status(200,$msg);
        }
        $msg = [
            'status'    => 'error', 
            'msg'       => 'Update user error',
            'data'      => null
        ];
        return $this->status(500,$msg);
    }
    public function delete()
    {
        $id = (int)$_REQUEST['id'];
        if($id == 0){
            $data=[
                'status'    => 'error',
                'msg'       =>  'Id not filled in',
                'data'      => null, 
            ];
            return $this->status(500,$data);
        }
        $resultGetById = $this->user->get($id);
        if($resultGetById == null){
            $msg = [
                'status'    => 'error',
                'msg'       =>  'Id not exactly',
                'data'      => null, 
            ];
            return $this->status(500,$msg);
        }
        $this->user->delete($id);
        $msg = [
            'status'    => 'success',
            'msg'       =>  'Delete user success',
            'data'      => null, 
        ];
        return $this->status(200,$msg);
    }
    public function login(){
        $req = $_POST;
        $msgs = $this->validate->login($req);
        if(count($msgs) > 0 ){
            echo("mot vai truong chua dien");
            return;
        }
        $email = $req['email'];
        $password = $req['password'];
        $resultByEmail = $this->user->getByEmail($email);
        if($resultByEmail == false){
            $msg = [
                'status'    =>'error',
                'msg'       => 'User not existed',
                'data'      => null,
            ];
            return $this->status(500,$msg);
        }
        $passwordHash = $resultByEmail[0]->password;
        // $verify =password_verify($password, $passwordHash;
        if(!password_verify($password, $passwordHash)) {
            $msg = [
                'status'    =>'error',
                'msg'       => 'Password incorrect',
                'data'       => null,
            ];
            return $this->status(200,$msg);
        }
        /// Here we will transform this array into JWT:
        $jwt = $this->authenticationService->generateJWTToken($resultByEmail);
        $role = $resultByEmail[0]->role;
        if($role == 0 || $role == 1){
            $msg = [
                'status'    =>'success',
                'msg'       => 'Return page admin',
                'data'      => $jwt,
            ];
            return $this->status(200,$msg);
        }
        $msg = [
            'status'    =>'success',
            'msg'       =>'Return page admin',
            'data'      => $jwt,
        ];
        return $this->status(200,$msg);
    }
}
 