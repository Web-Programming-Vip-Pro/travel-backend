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
    public function index()
    {
        $authHeader = apache_request_headers()['Authorization'];
        if (!isset($authHeader)) {
            return null;
        }
        $arr = explode(" ", $authHeader);
        $token = $arr[1];
        $jwt = $this->authenticationService->decodeJWTToken($token);
        if ($jwt == null) {
            $msg =  'You are not loged in';
            return $this->controller->status(401, $msg);
        }
        // when accessed,get data users
        return $this->controller->status(200, $jwt);
    }

    public function getUser($req)
    {
        $id = (int)$req['id'];
        $role = isset($req['role']) ? (int)$req['role'] : 0;
        $user = $this->user->get($id, 0, $role, 1);
        if ($user == null) {
            $msg = 'User not found';
            return $this->controller->status(404, $msg);
        }
        return $this->controller->status(200, $user);
    }
    public function list($req)
    {
        $page = isset($req['page']) ? (int)($req['page']) : 0;
        $limit = isset($req['limit']) ? (int)($req['limit']) : 20;
        $role = isset($req['role']) ? $req['role'] : 0;
        $order = isset($req['order']) ? $req['order'] : 'DESC';
        $text = isset($req['text']) ? $req['text'] : null;
        $result = $this->user->get(-1, $page, $limit, $role, $order, $text);
        return $this->controller->status(200, $result);
    }
    public function page($req)
    {
        $limit = isset($req['limit']) ? (int)($req['limit']) : 20;
        $role = isset($req['role']) ? $req['role'] : 0;
        $pages = $this->user->getPages($limit, $role);
        return $this->controller->status(200, $pages);
    }
    public function add($req)
    {
        if ($this->adminMiddle == false) {
            return $this->controller->status(401, "Unauthorized");
        }
        $msg = $this->handleValidator($req, 'add');
        if ($msg != false) {
            return $this->controller->status(422, $msg);
        }
        $resultByEmail = $this->user->getByEmail($req['email']);
        if ($resultByEmail != null) {
            $msg = 'User existed';
            return $this->controller->status(500, $msg);
        }
        $hashed_password = password_hash($req["password"], PASSWORD_DEFAULT);
        $data = [
            "name"              => $req["name"],
            "email"             => $req["email"],
            "password"          => $hashed_password,
            "bio"               => $req["bio"],
            "avatar"            => $req['avatar'],
            "image_cover"       => $req['image_cover'],
            "blocked"       => $req['blocked'] ? $req['blocked'] : 0,
            "role"             => $req['role'] ? $req['role'] : 0,
        ];
        $data['social'] = $this->helper->jsonEncodeSocial($req);
        $result = $this->user->create($data);
        if ($result == true) {
            $payload = $this->user->getByEmail($data['email']);
            return $this->controller->status(201, $payload[0]);
        }
        $msg = 'Add user to database fail';
        return $this->controller->status(500, $msg);
    }

    public function update($req)
    {
        $msg = $this->handleValidator($req, 'add');
        if ($msg != false) {
            return $this->controller->status(422, $msg);
        }
        $id = (int)$req['id'];
        $data = [
            "name"              => $req["name"],
            "email"             => $req["email"],
            "bio"               => $req["bio"],
            "avatar"            => $req['avatar'],
            "image_cover"       => $req['image_cover'],
            "blocked"       => $req['blocked'] ? $req['blocked'] : 0,
            "role"             => $req['role'] ? $req['role'] : 0,
        ];
        $data['social'] = $this->helper->jsonEncodeSocial($req);
        $result = $this->user->update($id, $data);
        if ($result == true) {
            return $this->controller->status(201, "Update success!");
        }
        $msg = 'Update user to database fail';
        return $this->controller->status(500, $msg);
    }

    public function getEdit($id)
    {
        if ($this->userMiddle == false) {
            return $this->controller->status(401, "Unauthorized");
        }
        $msg = $this->handleId($id);
        if ($msg != false) {
            return $this->controller->status(500, $msg);
        }
        $result = $this->user->get($id);
        return $this->controller->status(200, $result);
    }
    public function postEdit($id, $req)
    {

        $msg = $this->handleId($id);
        if ($msg != false) {
            return $this->controller->status(500, $msg);
        }
        $msg = $this->handleValidator($req, 'edit');
        if ($msg != false) {
            return $this->controller->status(422, $msg);
        }
        $data = [
            "name"          => $req["name"],
            "bio"           => $req['bio'],
        ];
        if (isset($req['avatar'])) {
            $data['avatar']  = $req['avatar'];
        }
        if (isset($req['image_cover'])) {
            $data['image_cover']  = $req['image_cover'];
        }
        $data['info'] = $this->helper->jsonEncodeInfo($req);
        $data['social'] = $this->helper->jsonEncodeSocial($req);
        if ($this->userMiddle->role == 2) {
            if (isset($req['role'])) {
                $data['role']           = $req['role'];
            }
            if (isset($req['blocked'])) {
                $data['blocked']        = $req['blocked'];
            }
        }
        // nếu có password mới update
        if (isset($req['password']) && $req['password'] == $req['repassword']) {
            $hashed_password = password_hash($req["password"], PASSWORD_DEFAULT);
            $data['password'] = $hashed_password;
        }
        $result = $this->user->update($id, $data);
        if ($result == true) {
            $msg = 'Update user success';
            return $this->controller->status(200, $msg);
        }
        $msg = 'Update user error';
        return $this->controller->status(500, $msg);
    }
    public function updateInfo($id, $req)
    {
        $msg = $this->handleValidator($req, 'edit');
        if ($msg != false) {
            return $this->controller->status(422, $msg);
        }
        $data = [
            "name"          => $req["name"],
            "bio"           => $req['bio'],
        ];
        if (isset($req['avatar'])) {
            $data['avatar']  = $req['avatar'];
        }
        if (isset($req['image_cover'])) {
            $data['image_cover']  = $req['image_cover'];
        }
        if (isset($req['social'])) {
            $data['social'] = json_encode($req['social']);
        }
        $user = $this->user->get($id);
        // verify password
        if (!password_verify($req['password'], $user['password'])) {
            $msg = 'Password incorrect';
            return $this->controller->status(422, $msg);
        }
        // if user email changed, check email is exist
        if ($req['email'] != $user['email']) {
            $resultByEmail = $this->user->getByEmail($req['email']);
            if ($resultByEmail != null) {
                $msg = 'Email existed';
                return $this->controller->status(422, $msg);
            }
            $data['email'] = $req['email'];
        }
        $result = $this->user->update($id, $data);
        if ($result == true) {
            $msg = 'Update user success';
            return $this->controller->status(200, $msg);
        }
        $msg = 'Update user error';
        return $this->controller->status(500, $msg);
    }
    public function delete($id)
    {
        if ($this->adminMiddle  == false) {
            return $this->controller->status(401, "Unauthorized");
        }
        $msg = $this->handleId($id);
        if ($msg != false) {
            return $this->controller->status(500, $msg);
        }
        $this->user->delete($id);
        $msg = 'Delete user success';
        return $this->controller->status(200, $msg);
    }
    public function login($req)
    {
        $msg = $this->handleValidator($req, 'login');
        if ($msg != false) {
            return $this->controller->status(422, $msg);
        }
        $email = $req['email'];
        $password = $req['password'];
        $user = $this->user->getByEmail($email);
        if ($user == null) {
            $msg =  'User not existed';
            return $this->controller->status(500, $msg);
        }
        $user = $user[0];
        $passwordHash = $user->password;
        if (!password_verify($password, $passwordHash)) {
            $msg = 'Password incorrect';
            return $this->controller->status(500, $msg);
        }
        unset($user->password);
        if ($user->social) {
            $user->social = json_decode($user->social);
        }
        return $this->controller->status(200, $user);
    }
    public function register($req)
    {
        $msg = $this->handleValidator($req, 'register');
        if ($msg != false) {
            return $this->controller->status(422, $msg);
        }
        $hashed_password = password_hash($req["password"], PASSWORD_DEFAULT);
        $data = [
            "name"              => $req["name"],
            "email"             => $req["email"],
            "password"          => $hashed_password,
        ];
        $resultByEmail = $this->user->getByEmail($data['email']);
        if ($resultByEmail != null) {
            $msg = 'User existed';
            return $this->controller->status(500, $msg);
        }
        $result = $this->user->create($data);
        if ($result == true) {
            $payload = $this->user->getByEmail($data['email']);
            unset($payload[0]->password);
            // $JWT = $this->authenticationService->generateJWTToken($payload);
            // $msg = $JWT;
            return $this->controller->status(201, $payload[0]);
        }
        $msg = 'Add user to database fail';
        return $this->controller->status(500, $msg);
    }
    public function forget($req)
    {
        $msg = $this->handleValidator($req, 'forget');
        if ($msg != false) {
            return $this->controller->status(422, $msg);
        }
        $user = $this->user->getByEmail($req['email']);
        if ($user == null) {
            return $this->controller->status(500, "User not found!");
        }
        $user = $user[0];
        $passReset = $this->helper->generateRandomString();
        $toMail = $req['email'];
        $subject = "Request password reset from email $toMail - Fleety.space";
        $body = '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body>
            <h2>Your password has just been requested to reset. Here is your new password:</h2>
            <p>New Password:' . $passReset . '</p>
            <h5>Please login with new password and change it soon</h5>
        </body>
        </html>';
        try {
            $success = $this->sendMail->sendMail($toMail, $subject, $body);
            if (!$success) {
                return $this->controller->status(500, "Send mail fail");
            }
        } catch (\Exception $e) {
            return $this->controller->status(500, $e->getMessage());
        }
        $hashed_password = password_hash($passReset, PASSWORD_DEFAULT);
        $user = $this->user->update((int)$user->id, [
            "password" => $hashed_password
        ]);
        if ($user == true) {
            $msg = 'Please check email to using new password';
            return $this->controller->status(200, $msg);
        }
        $msg = 'Reset password fail';
        return $this->controller->status(500, $msg);
    }
    public function updatePassword($req)
    {
        $msg = $this->handleValidator($req, 'updatePassword');
        if ($msg != false) {
            return $this->controller->status(422, $msg);
        }
        // check if new password & confirm password is same
        $userId = (int)$req['id'];
        $oldPassword = $req['oldPassword'];
        // get user
        $user = $this->user->get($userId);
        if ($user == null) {
            $msg = 'User not existed';
            return $this->controller->status(500, $msg);
        }
        $passwordHash = $user['password'];
        if (!password_verify($oldPassword, $passwordHash)) {
            $msg = 'Password incorrect';
            return $this->controller->status(500, $msg);
        }
        $hashed_password = password_hash($req['newPassword'], PASSWORD_DEFAULT);
        $data = [
            "password"          => $hashed_password,
        ];
        $result = $this->user->update($userId, $data);
        if ($result == true) {
            $msg = 'Change password success';
            return $this->controller->status(200, $msg);
        }
        $msg = 'Change password fail';
        return $this->controller->status(500, $msg);
    }
    public function handleValidator($req, $action)
    {
        $msgs = null;
        if ($action == 'add') {
            $msgs = $this->validate->add($req);
        }
        if ($action == 'edit') {
            $msgs = $this->validate->edit($req);
        }
        if ($action == 'forget') {
            $msgs = $this->validate->forget($req);
        }
        if ($action == 'login') {
            $msgs = $this->validate->login($req);
        }
        if ($action == 'updatePassword') {
            $msgs = $this->validate->updatePassword($req);
        }
        if ($action == 'register') {
            $msgs = $this->validate->register($req);
        }
        if (count($msgs) > 0) {
            return $msgs;
        }
        return false;
    }
    public function sendMail()
    {
    }
    public function handleId($id)
    {
        if ($id == 0) {
            return 'Id not fill in';
        }
        $resultGetById = $this->user->get($id);
        if ($resultGetById == null) {
            return  'Id not exactly';
        }
        return false;
    }
}
