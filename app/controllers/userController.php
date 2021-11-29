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
    public function page()
    {
        $req = $_REQUEST;
        return $this->userService->page($req);
    }
    public function getUser()
    {
        $req = $_REQUEST;
        return $this->userService->getUser($req);
    }

    public function update()
    {
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);
        return $this->userService->update($input);
    }
    public function list()
    {
        $req = $_REQUEST;
        return $this->userService->list($req);
    }
    public function postAdd()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        return $this->userService->add($req);
    }
    public function getEdit()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        $id = (int)$req['id'];
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
        $req = json_decode($inputJSON, true);
        $id = (int)$req['id'];
        return $this->userService->postEdit($id, $req);
    }
    public function updateInfo()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        $id = (int)$req['id'];
        return $this->userService->updateInfo($id, $req);
    }
    public function updatePassword()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        return $this->userService->updatePassword($req);
    }
    public function delete()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        $id = (int)$req['id'];
        return $this->userService->delete($id);
    }
    public function login()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        return $this->userService->login($req);
    }
    public function register()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        return $this->userService->register($req);
    }
    public function forget()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        return $this->userService->forget($req);
    }
}
