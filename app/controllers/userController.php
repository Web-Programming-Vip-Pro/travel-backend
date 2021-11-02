<?php

namespace App\Controllers;

include_once('app/models/userModel.php');
include_once('core/http/Container.php');
include_once('app/middleware/middleware.php');
require_once('vendor/autoload.php');
use \Firebase\JWT\JWT; 
use App\Models\UserModel;
use Core\Http\BaseController;
use App\Middleware\Middleware;

class userController extends BaseController
{
    private $user;
    private $middleware;
    public function __construct()
    {
        $this->user = new UserModel();
        $this-> middleware = new Middleware();
       
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
        if($role_login != 0){
            echo "User  not permit to access and redirect to login";
            $msg = ['User  not permit to access and redirect to login'];
            return;
        }  
        // when accessed,get data users
        $result = $this->user->get();
        $result = json_encode($result);
        print_r($result);
    }
    public function postAdd()
    {   
        if(!isset($this->cookie)){
            echo("Return login");
            return;
        }
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
            echo "Tài khoản đã tồn tại";
            return;
        }
        $result = $this->user->create($data);
        if($result != null){
            print_r("Add user success");
            return ;
        }
        echo "Add user error";
        return ;
    }
    public function getEdit()
    {   
        if(!isset($this->cookie)){
            echo("Return login");
            return;
        }
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
        if(!isset($this->cookie)){
            echo("Return login");
            return;
        }
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
        if($result == null){
            print_r("Add user success");
            return ;
        }
        echo "Add user error";
        return ;
    }
    public function delete()
    {
        if(!isset($this->cookie)){
            echo("Return login");
            return;
        }
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
        $msg = [];
        if(!isset($req['email'])){
            array_push($msg,'Vui long nhap email');
        }
        if(!isset($req['password'])){
            array_push($msg,'Vui long nhap password');
        }
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
        // echo (random_bytes(32));
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
        // set token 
        setcookie('login',$jwt,$expire,'/');
        /**
         * 
         * 
         */
       
        // define role
        $role = $resultByEmail[0]->role;
        if($role == 0 || $role == 1){
            echo "Return page admin";
            return;
        }
        echo "Return home page";
        return;
    }
    public function logout(){
        // if(!isset($this->cookie)){
        //     echo("Return login");
        //     return;
        // }
        unset($_COOKIE['login']);
        setcookie('login', '', time() - 3600, '/',$serverName); 
        echo "Logout success";
        return;
    }
}
 