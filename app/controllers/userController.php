<?php

namespace App\Controllers;

require_once('app/services/authenticationService.php');
require_once('app/services/userService.php');
use Core\Http\BaseController;
use App\Services\UserService;

class userController extends BaseController
{
    private $userService;
    public function __construct()
    {
        $this->userService = new UserService();
    }
    public function index()
    {   
        /**
         * middleware user
         */
       return $this->userService->index();
    }
    public function list()
    {   
        // when accessed,get data users
        return $this->userService->list();
    }
    public function postAdd()
    {
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        return $this->userService->add($req);
    }
    public function getEdit()
    {   
        $id = (int)$_REQUEST['id'];
        return $this->userService->getEdit($id);
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
        return $this->userService->postEdit($id,$req);
    }
    public function delete()
    {
        $id = (int)$_REQUEST['id'];
        return $this->userService->delete($id);
    }
    public function login(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        return $this->userService->login($req);
    }
    public function register()
    {
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        return $this->userService->register($req);
    }
    public function forget()
    {
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        return $this->userService->forget($req);
    }
    public function changePassword()
    {
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        return $this->userService->changePassword($req);
    }
}
 