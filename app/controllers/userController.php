<?php

namespace App\Controllers;

require_once('app/models/userModel.php');
require_once('core/http/Container.php');
require_once('app/middleware/middleware.php');
require_once('app/validators/userValidate.php');
require_once('vendor/autoload.php');
require_once('storage/helper.php');
use \Firebase\JWT\JWT; 
use App\Models\UserModel;
use Core\Http\BaseController;
use App\Middleware\Middleware;
use App\Validator\UserValidate;
use Storage\Helper;

class userController extends BaseController
{
    private $user;
    private $middleware;
    private $validate;
    private $helper;
    public function __construct()
    {
        $this->user         = new UserModel();
        $this->middleware   = new Middleware();
        $this->validate     = new UserValidate();
        $this->helper       = new Helper();
    }
    public function index()
    {   
        /**
         * middleware user
         */
        $role_login = $this->middleware->handleAdmin();
        if($role_login == -1){
            $msg = [
                'status'    => 'Unauthorized',
                'msg'       => 'You are not loged in',
                'data'      => null
            ];
            return $this->status(401,$msg);
        }  
        if($role_login == 2){
            $msg = [
                'status'    => 'Unauthorized',
                'msg'       => 'User  not permit to access and redirect to login',
                'data'      => null
            ];
            return $this->status(401,$msg);
        }  
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
        $hashed_password = password_hash($req["password"], PASSWORD_DEFAULT);
        $data =[
            "name"              => $req["name"],
            "email"             => $req["email"],
            "password"          => $hashed_password,
            "bio"               => 'bio',
            "role"              => 1,
            "avatar"            => 'avatar',
            "status_agency"     => 0,
            "image_cover"       => 'image_cover',
            "blocked"           => 0,
        ];
        $data['info'] = $this->helper->jsonEncodeInfo($req);
        $data['social'] = $this->helper->jsonEncodeSocial($req);
        $resultByEmail = $this->user->getByEmail($data['email']);
        if(count($resultByEmail)>0){
            $msg=[
                'status'    =>  'error',
                'msg'       =>  'User existed',
                'data'      =>  null
            ];
            return $this->status(500,$msg);
        }
        $result = $this->user->create($data);
        if($result == true){
            $msg=[
                'status'    =>'Created',
                'msg'       =>'Add user to database success',
                'data'      => null
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
        $data =[
            "name"  => $req["name"],
            "email" => $req["email"],
            "bio"   => $req['bio'],
            "role"   => 1,
            "avatar"   => 'avatar',
            "status_agency"   => 0,
            "image_cover"   => 'image_cover',
            "blocked" => 0,
        ];
        $data['info'] = $this->helper->jsonEncodeInfo($req);
        $data['social'] = $this->helper->jsonEncodeSocial($req);
        // nếu có password mới update
        if(isset($req['password']) && $req['password'] == $req['repassword']){
            $hashed_password = password_hash($req["password"], PASSWORD_DEFAULT);
            $data['password'] = $hashed_password;
        }
        // check email đã tồn tại chưa ?
        if($resultGetById['email'] != $data['email']){
            $resultByEmail = $this->user->getByEmail($data['email']);
            if(count($resultByEmail)>0){
                $msg = [
                    'status'    => 'error',
                    'msg'       =>  'User existed',
                    'data'      => null, 
                ];
                return $this->status(500,$msg);
            }
        }
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
        define('SECRET_KEY','Your-Secret-Key');  /// secret key can be a random string and keep in secret from anyone
        define('ALGORITHM','HS512');  
        $req = $_POST;
        $msg = $this->validate->login($req);
        if(count($msg) > 0 ){
            echo("mot vai truong chua dien");
            return;
        }
        $email = $req['email'];
        $password = $req['password'];
        $resultByEmail = $this->user->getByEmail($email);
        if($resultByEmail == null){
            echo "Tai khoan khong ton tai";
            return;
        }
        $passwordHash = $resultByEmail[0]->password;
        // $verify =password_verify($password, $passwordHash;
        if(!password_verify($password, $passwordHash)) {
            echo ("Mat khau khong chinh xac");
            return;
        }
        /**
         * 
         * 
         */
        $tokenId    = base64_encode(random_bytes(32));
        $issuedAt   = time();
        $notBefore  = $issuedAt + 10;  //Adding 10 seconds
        $expire     = $notBefore + 7200; // Adding 60 seconds
        $serverName = 'http://localhost/'; /// set your domain name    
        /*
            * Create the token as an array
            */
        $payload = [
            'iat'  => $issuedAt,         // Issued at: time when the token was generated
            'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
            'iss'  => $serverName,       // Issuer
            'nbf'  => $notBefore,        // Not before
            'exp'  => $expire,           // Expire
            'data' => [                  // Data related to the logged user you can set your required data
                'id'   => $resultByEmail[0]->id, // id from the users table
                'name' => $resultByEmail[0]->name, //  name
                'role' => $resultByEmail[0]->role, //  name
            ]
        ];
        $secretKey = base64_decode(SECRET_KEY);
        echo $secretKey;
        /// Here we will transform this array into JWT:
        $jwt = JWT::encode(
                $payload, //Data to be encoded in the JWT
                $secretKey, // The signing key
                ALGORITHM 
            ); 
        $role = $resultByEmail[0]->role;
        if($role == 0 || $role == 1){
            $msg = [
                'status'    =>'success',
                'msg'       =>"Return page admin",
                'data'       => $jwt,
            ];
            return $this->status(200,$msg);
        }
        $msg = [
            'status'    =>'success',
            'msg'       =>"Return page admin",
            'data'       => $jwt,
        ];
        return $this->status(200,$msg);
    }
    // public function logout(){
    //     unset($_COOKIE['login']);
    //     setcookie('login', '', time() - 3600, '/',$serverName); 
    //     echo "Logout success";
    //     return;
    // }
}
 