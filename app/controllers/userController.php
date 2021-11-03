<?php

namespace App\Controllers;

include_once('app/models/userModel.php');
include_once('core/http/Container.php');
include_once('app/middleware/middleware.php');
require_once('app/validators/userValidate.php');
require_once('vendor/autoload.php');
use \Firebase\JWT\JWT; 
use App\Models\UserModel;
use Core\Http\BaseController;
use App\Middleware\Middleware;
use App\Validator\UserValidate;

class userController extends BaseController
{
    private $user;
    private $middleware;
    private $validate;
    public function __construct()
    {
        $this->user = new UserModel();
        $this->middleware = new Middleware();
        $this->validate = new UserValidate();
       
    }
    public function index()
    {   
        /**
         * middleware user
         */
        $role_login = $this->middleware->handleAdmin();
        if($role_login == -1){
            echo "Not Login Redirect to page Login";
            $msg = ['Not Login Redirect to page Login'];
            return;
        }  
        if($role_login == 2){
            echo "User  not permit to access and redirect to login";
            $msg = ['User  not permit to access and redirect to login'];
            return;
        }  
        // when accessed,get data users
        $result = $this->user->get();
        return $this->status(200,$result);
    }
    public function postAdd()
    {   
        $req = $_POST;
        $msg = $this->validate->add($req);
        if(count($msg) >0){
            $data=[
                'msg'       =>'Some field not fill in'
            ];
            return $this->status(422,$data);
        }
        $hashed_password = password_hash($req["password"], PASSWORD_DEFAULT);
        $data =[
            "name"  => $req["name"],
            "email" => $req["email"],
            "password" => $hashed_password,
            "bio"   => 'bio',
            "role"   => 1,
            "info"   => 'info',
            "avatar"   => 'avatar',
            "status_agency"   => 0,
            "image_cover"   => 'image_cover',
            "social" => "social",
            "blocked" => 0,
        ];
        $resultByEmail = $this->user->getByEmail($data['email']);
        if(count($resultByEmail)>0){
            $data=[
                'localtion' => 'redirect to add user page',
                'msg'       =>'Tài khoản đã tồn tại'
            ];
            return $this->status(301,$data);
        }
        $result = $this->user->create($data);
        if($result != null){
            $data=[
                'localtion' => 'redirect to list user page',
                'msg'       =>'add user to database success'
            ];
            return $this->status(301,$data);
        }
        $data=[
            'msg'       =>'add user to database fail'
        ];
        return $this->status(500,$data);
    }
    public function getEdit()
    {   
        $id = (int)$_REQUEST['id'];
        if($id == 0){
            $data=[
                'localtion' => 'Not Foung page',
                'msg'       =>'id not fill in'
            ];
            return $this->status(404,$data);
        }
        $result = $this->user->get($id);
        if($result == null){
            $data=[
                'localtion' => 'Not Foung page',
                'msg'       =>'id not exactly'
            ];
            return $this->status(404,$data);
        }
        return $this->status(200,$result);
    }
    /*
    ***
    *error : update sql with email đã tồn tại;
    ***
    */
    public function postEdit()
    {
        $req = $_POST;
        $msg = $this->validate->edit(req);
        // validator
        if(count($msg) >0){
            $data=[
                'msg'       =>'Some field not fill in'
            ];
            return $this->status(422,$data);
        }
        // data req
        $data =[
            "name"  => $req["name"],
            "email" => $req["email"],
            "password" => $hashed_password,
            "bio"   => $req['bio'],
            "role"   => 1,
            "info"   => $req['info'],
            "avatar"   => 'avatar',
            "status_agency"   => 0,
            "image_cover"   => 'image_cover',
            "social" => "social",
            "blocked" => 0,
        ];
        // nếu có password mới update
        if(isset($req['password'])){
            $data['password'] = $req['password'];
        }
        $id = (int)$_REQUEST['id'];
        // check param có id không
        if($id == 0){
            $data=[
                'localtion' => 'Not Foung page',
                'msg'       =>'id not fill in'
            ];
            return $this->status(404,$data);
        }
        $resultGetById = $this->user->get($id);
        // check user co ton tai khong ?
        if($resultGetById == null){
            $data=[
                'localtion' => 'Not Foung page',
                'msg'       =>'id not exactly'
            ];
            return $this->status(404,$data);
        }
        // check email đã tồn tại chưa ?
        if($resultGetById['email' != $data['email']]){
            $resultByEmail = $this->user->getByEmail($data['email']);
            echo $resultByEmail;
            if(count($resultByEmail)>0){
                $data=[
                    'msg'       =>'user existed'
                ];
                return $this->status(200,$data);
            }
        }
        $result = $this->user->update($id,$data);
        if($result != null){
            $data=[
                'msg'       =>'update user success'
            ];
            return $this->status(200,$data);
        }
        $data=[
            'msg'       =>'update user error'
        ];
        return $this->status(500,$data);
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
        /// Here we will transform this array into JWT:
        $jwt = JWT::encode(
                $payload, //Data to be encoded in the JWT
                $secretKey, // The signing key
                ALGORITHM 
            ); 
        $role = $resultByEmail[0]->role;
        if($role == 0 || $role == 1){
            $data=[
                'status'    =>'success',
                'jwt'       => $jwt,
                'msg'       =>"Return page admin"
            ];
            return $this->status(200,$data);
        }
        $data=[
            'status'    =>'success',
            'jwt'       => $jwt,
            'msg'       =>"Return page admin"
        ];
        return $this->status(200,$data);
    }
    // public function logout(){
    //     unset($_COOKIE['login']);
    //     setcookie('login', '', time() - 3600, '/',$serverName); 
    //     echo "Logout success";
    //     return;
    // }
}
 