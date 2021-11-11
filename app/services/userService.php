<?php

namespace App\Services;

require_once('storage/helper.php'); 
require_once('core/http/Container.php');
use Storage\Helper;
use Core\Http\BaseController;
class UserService
{
    private $helper;
    public function __construct()
    {
        $this->container = new BaseController();
        $this->helper       = new Helper();
    }
    public function list(){
        
    }
    public function add($req)
    {
        $hashed_password = password_hash($req["password"], PASSWORD_DEFAULT);
        $data =[
            "name"              => $req["name"],
            "email"             => $req["email"],
            "password"          => $hashed_password,
            "bio"               => 'bio',
            "role"              => 0,
            "avatar"            => 'avatar',
            "status_agency"     => 0,
            "image_cover"       => 'image_cover',
            "blocked"           => 0,
        ];
        $data['info'] = $this->helper->jsonEncodeInfo($req);
        $data['social'] = $this->helper->jsonEncodeSocial($req);
        return $data;
    }
    public function getEdit($id){

    }
    public function postEdit($req)
    {
        $data =[
            "name"  => $req["name"],
            "email" => $req["email"],
            "bio"   => $req['bio'],
            "role"   => 0,
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
        return $data;
    }
    public function delete ($id){
        if($id == 0){
            $data=[
                'status'    => 'error',
                'msg'       =>  'Id not filled in',
                'data'      => null, 
            ];
            return $this->container->status(500,$data);
        }
    }
    public function handleValidator($req,$action){
        $msgs = null;
        if($action == 'add'){
            $msgs = $this->validate->add($req); 
        }else{
            $msgs = $this->validate->edit($req); 
        }
        if(count($msgs) > 0){
            return $msgs;
        } 
        return false;
    }
    public function handleId($id){
        if($id == 0){
            return 'Id not fill in';
        }
        $resultGetById = $this->category->get($id);
        if($resultGetById == null){
            return  'Id not exactly';
        }
        return false;
    }
}
