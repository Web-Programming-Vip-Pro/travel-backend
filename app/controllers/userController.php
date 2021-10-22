<?php
namespace App\Controllers;
include_once('app/models/userModel.php');
include_once('core/http/Container.php');
use App\Models\UserModel;
use Core\Http\BaseController;

class userController extends BaseController
{
    private $user;
    public function __construct(){
        $this->user = new UserModel();
    }
    public function index()
    {
          echo 'home';
    }
    public function home(){
        $result = $this->user->get();
        return $this->render('users/index',$result);
    }
}
