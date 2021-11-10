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
    private $validate;
    private $authenticationService;
    private $userService;
    public function __construct()
    {
        $this->user         = new UserModel();
        $this->validate     = new UserValidate();
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
        var_dump($jwt);
        if($jwt == null){
            $msg =  'You are not loged in';
            return $this->status(401,$msg);
        }  
        // when accessed,get data users
        $msg = $jwt;
        return $this->status(200,$msg);
    }
    public function list()
    {   
        // when accessed,get data users
        $result = $this->user->get();
        $msg = $result;
        return $this->status(200,$msg);
    }
    public function postAdd()
    {
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        $msgs = $this->validate->add($req);
        if(count($msgs) >0){
            $msg= $msgs;
            return $this->status(422,$msg);
        }
        $data = $this->userService->add($req);
        $resultByEmail = $this->user->getByEmail($data['email']);
        if($resultByEmail != false){
            $msg='User existed';
            return $this->status(500,$msg);
        }
        $result = $this->user->create($data);
        if($result == true){
            $payload = $this->user->getByEmail($data['email']);
            $JWT = $this->authenticationService->generateJWTToken($payload);
            $msg = $JWT;
            return $this->status(201,$msg);
        }
        $msg = 'Add user to database fail';
        return $this->status(500,$msg);
    }
    public function getEdit()
    {   
        $id = (int)$_REQUEST['id'];
        if($id == 0){
            $msg = 'Id not fill in';
            return $this->status(500,$msg);
        }
        $result = $this->user->get($id);
        if($result == null){
            $msg = 'Id not existed';
            return $this->status(500,$msg);
        }
        $msg = $result;
        return $this->status(200,$msg);
    }
    /*
    ***
    *error : update sql with email đã tồn tại;
    ***
    */
    public function postEdit()
    {
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        $id = (int)$_REQUEST['id'];
        // check param có id không
        if($id == 0){
            $msg = 'Id not filled in';
            return $this->status(500,$msg);
        }
        $resultGetById = $this->user->get($id);
        // check user co ton tai khong ?
        if($resultGetById == null){
            $msg= 'Id not exactly';
            return $this->status(500,$msg);
        }
        // check email đã tồn tại chưa ?
        if($resultGetById['email'] != $req['email']){
            $resultByEmail = $this->user->getByEmail($req['email']);
            if($resultByEmail != false){
                $msg =  'User existed';
                return $this->status(500,$msg);
            }
        }
        // validator
        $msgs = $this->validate->edit($req);
        if(count($msgs) > 0){
            $msg= $msgs;
            return $this->status(422,$msg);
        }
        // data req
        $data = $this->userService->edit($req);
        $result = $this->user->update($id,$data);
        if($result == true){
            $msg = 'Update user success';
            return $this->status(200,$msg);
        }
        $msg = 'Update user error';
        return $this->status(500,$msg);
    }
    public function delete()
    {
        $id = (int)$_REQUEST['id'];
        if($id == 0){
            $data = 'Id not filled in';
            return $this->status(500,$data);
        }
        $resultGetById = $this->user->get($id);
        if($resultGetById == null){
            $msg = 'Id not exactly';
            return $this->status(500,$msg);
        }
        $this->user->delete($id);
        $msg = 'Delete user success';
        return $this->status(200,$msg);
    }
    public function login(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        $msgs = $this->validate->login($req);
        if(count($msgs) > 0 ){
            $msg = $msgs;
            return $this->status(422,$msg);
        }
        $email = $req['email'];
        $password = $req['password'];
        $resultByEmail = $this->user->getByEmail($email);
        if($resultByEmail == false){
            $msg =  'User not existed';
            return $this->status(500,$msg);
        }
        $passwordHash = $resultByEmail[0]->password;
        // $verify =password_verify($password, $passwordHash;
        if(!password_verify($password, $passwordHash)) {
            $msg = 'Password incorrect';
            return $this->status(200,$msg);
        }
        /// Here we will transform this array into JWT:
        $jwt = $this->authenticationService->generateJWTToken($resultByEmail);
        $msg = $jwt;
        return $this->status(200,$msg);
    }
}
 