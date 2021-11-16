<?php

namespace App\Services;

require_once('storage/helper.php'); 
require_once('core/http/Container.php');
require_once('app/models/userModel.php');
require_once('app/services/authenticationService.php');
require_once('app/validators/userValidate.php');
require_once('app/middleware/middleware.php');
require_once('storage/sendMail.php');

use Storage\Helper;
use Storage\SendMail;
use App\Models\UserModel;
use Core\Http\BaseController;
use App\Middleware\Middleware;
use App\Services\AuthenticationService;
use App\Validator\UserValidate;
class UserService
{
    private $helper;
    private $sendMail;
    private $controller;
    private $user;
    private $middleware;
    private $authenticationService;
    private $validate;
    public function __construct()
    {
        $this->controller = new BaseController();
        $this->helper       = new Helper();
        $this->sendMail       = new SendMail();
        $this->user         = new UserModel();
        $this->validate     = new UserValidate();
        $this->authenticationService = new AuthenticationService();
        $this->middleware       = new Middleware();
        $this->userMiddle         = $this->middleware->handle();
        $this->adminMiddle      = $this->middleware->handleAdmin();
    }
    public function index(){
        $authHeader = apache_request_headers()['Authorization'];
        if(!isset($authHeader)){
            return null;
        }
        $arr = explode(" ", $authHeader);
        $token = $arr[1];
        $jwt = $this->authenticationService->decodeJWTToken($token);
        if($jwt == null){
            $msg =  'You are not loged in';
            return $this->controller->status(401,$msg);
        }  
        // when accessed,get data users
        return $this->controller->status(200,$jwt);
    }
    public function list(){
        $result = $this->user->get();
        return $this->controller->status(200,$result);
    }
    public function add($req)
    {
        if($this->adminMiddle == false){
            return $this->controller->status(401,"Unauthorized");
        }
        $msg = $this->handleValidator($req,'add');
        if($msg != false){
            return $this->controller->status(422,$msg);
        }
        $resultByEmail = $this->user->getByEmail($req['email']);
        if($resultByEmail != false){
            $msg='User existed';
            return $this->controller->status(500,$msg);
        }
        $hashed_password = password_hash($req["password"], PASSWORD_DEFAULT);
        $data =[
            "name"              => $req["name"],
            "email"             => $req["email"],
            "password"          => $hashed_password,
            "bio"               => $req["bio"],
            "avatar"            => $req['avatar'],
            "status_agency"     => 0,
            "image_cover"       => $req['image_cover'],
        ];
        $data['info'] = $this->helper->jsonEncodeInfo($req);
        $data['social'] = $this->helper->jsonEncodeSocial($req);
        $result = $this->user->create($data);
        if($result == true){
            $payload = $this->user->getByEmail($data['email']);
            $JWT = $this->authenticationService->generateJWTToken($payload);
            $msg = $JWT;
            return $this->controller->status(201,$msg);
        }
        $msg = 'Add user to database fail';
        return $this->controller->status(500,$msg);
    }
    public function getEdit($id){
        if($this->userMiddle == false){
            return $this->controller->status(401,"Unauthorized");
        }
        $msg = $this->handleId($id);
        if($msg != false){
            return $this->controller->status(500,$msg);
        }
        $result = $this->user->get($id);
        return $this->controller->status(200,$result);
    }
    public function postEdit($id,$req)
    {
        if($this->userMiddle == false){
            return $this->controller->status(401,"Unauthorized");
        }
        $msg = $this->handleId($id);
        if($msg != false){
            return $this->controller->status(500,$msg);
        }
        $msg = $this->handleValidator($req,'add');
        if($msg != false){
            return $this->controller->status(422,$msg);
        }
        $data =[
            "name"          => $req["name"],
            "bio"           => $req['bio'],
        ];
        if(isset($req['avatar'])){
            $data['avatar']  = $req['avatar'];
        }
        if(isset($req['image_cover'])){
            $data['image_cover']  = $req['image_cover'];
        }
        $data['info'] = $this->helper->jsonEncodeInfo($req);
        $data['social'] = $this->helper->jsonEncodeSocial($req);
        if($this->userMiddle->role == 2){
            if(isset($req['role'])){
                $data['role']           = $req['role'];
            }
            if(isset($req['status_agency'])){
                $data['status_agency']  = $req['status_agency'];
            }
            if(isset($req['blocked'])){
                $data['blocked']        = $req['blocked'];
            }
        }
        // nếu có password mới update
        if(isset($req['password']) && $req['password'] == $req['repassword']){
            $hashed_password = password_hash($req["password"], PASSWORD_DEFAULT);
            $data['password'] = $hashed_password;
        }
        $result = $this->user->update($id,$data);
        if($result == true){
            $msg = 'Update user success';
            return $this->controller->status(200,$msg);
        }
        $msg = 'Update user error';
        return $this->controller->status(500,$msg);
    }
    public function delete ($id){
        if($this->adminMiddle  == false){
            return $this->controller->status(401,"Unauthorized");
        }
        $msg = $this->handleId($id);
        if($msg != false){
            return $this->controller->status(500,$msg);
        }
        $this->user->delete($id);
        $msg = 'Delete user success';
        return $this->controller->status(200,$msg);
    }
    public function login($req){
        $msg = $this->handleValidator($req,'login');
        if($msg != false){
            return $this->controller->status(422,$msg);
        }
        $email = $req['email'];
        $password = $req['password'];
        $resultByEmail = $this->user->getByEmail($email);
        if($resultByEmail == false){
            $msg =  'User not existed';
            return $this->controller->status(500,$msg);
        }
        $passwordHash = $resultByEmail[0]->password;
        // $verify =password_verify($password, $passwordHash;
        if(!password_verify($password, $passwordHash)) {
            $msg = 'Password incorrect';
            return $this->controller->status(500,$msg);
        }
        /// Here we will transform this array into JWT:
        $jwt = $this->authenticationService->generateJWTToken($resultByEmail);
        $msg = $jwt;
        return $this->controller->status(200,$msg);
    }
    public function register($req){
        $msg = $this->handleValidator($req,'register');
        if($msg != false){
            return $this->controller->status(422,$msg);
        }
        $hashed_password = password_hash($req["password"], PASSWORD_DEFAULT);
        $data =[
            "name"              => $req["name"],
            "email"             => $req["email"],
            "password"          => $hashed_password,
        ];
        $resultByEmail = $this->user->getByEmail($data['email']);
        if($resultByEmail != false){
            $msg='User existed';
            return $this->controller->status(500,$msg);
        }
        $result = $this->user->create($data);
        if($result == true){
            $payload = $this->user->getByEmail($data['email']);
            $JWT = $this->authenticationService->generateJWTToken($payload);
            $msg = $JWT;
            return $this->controller->status(201,$msg);
        }
        $msg = 'Add user to database fail';
        return $this->controller->status(500,$msg);
    }
    public function forget($req){
        $msg = $this->handleValidator($req,'forget');
        if($msg != false){
            return $this->controller->status(422,$msg);
        }
        $result = $this->user->getByEmail($req['email']);
        if($result == false){
            return $this->controller->status(500,"User not exactly");
        }
        $passReset = $this->helper->generateRandomString();
        $toMail = $req['email'];
        $subject = "Forget Password with " .$req['email'];
        $body = '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Document</title>
        </head>
        <body>
            <h1>Your password reset.Please login with new password and change it soon.</h1>
            <p>New Password :' .$passReset. '</p>
        </body>
        </html>';
        $success = $this->sendMail->sendMail($toMail,$subject,$body);
        if(!$success){
            return $this->controller->status(500,"Send mail fail");
        }
        $data = null;
        $hashed_password = password_hash($passReset, PASSWORD_DEFAULT);
        $data['password'] = $hashed_password;
        $result = $this->user->update((int)$result['id'],$data);
        if($result == true){
            $msg = 'Password sent to email.Please check email to using new password';
            return $this->controller->status(200,$msg);
        }
        $msg = 'Reset password fail';
        return $this->controller->status(500,$msg);
    }
    public function changePassword($req){
        if($this->userMiddle == false){
            return $this->controller->status(401,"Unauthorized");
        }
        $msg = $this->handleValidator($req,'changePassword');
        if($msg != false){
            return $this->controller->status(422,$msg);
        }
        $oldPassword = $req['oldPassword'];
        // $verify =password_verify($password, $passwordHash;
        if(!password_verify($oldPassword, $this->userMiddle->password)) {
            $msg = 'Old password incorrect';
            return $this->controller->status(500,$msg);
        }
        $data['password'] = $req['newPassword'];
        $result = $this->user->update((int)$this->userMiddle->id,$data);
        if($result == true){
            $msg = 'Change password success';
            return $this->controller->status(200,$msg);
        }
        $msg = 'Change password fail';
        return $this->controller->status(500,$msg);
    }
    public function handleValidator($req,$action){
        $msgs = null;
        if($action == 'add'){
            $msgs = $this->validate->add($req); 
        }
        if($action == 'edit'){
            $msgs = $this->validate->edit($req); 
        }
        if($action == 'forget'){
            $msgs = $this->validate->forget($req); 
        }
        if($action == 'login'){
            $msgs = $this->validate->login($req); 
        }
        if($action == 'changePassword'){
            $msgs = $this->validate->changePassword($req); 
        }
        if($action == 'register'){
            $msgs = $this->validate->register($req); 
        }
        if(count($msgs) > 0){
            return $msgs;
        } 
        return false;
    }
    public function sendMail(){

    }
    public function handleId($id){
        if($id == 0){
            return 'Id not fill in';
        }
        $resultGetById = $this->user->get($id);
        if($resultGetById == null){
            return  'Id not exactly';
        }
        return false;
    }
}
