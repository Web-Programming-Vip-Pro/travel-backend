<?php

namespace App\Controllers;

include_once('app/models/notifyModel.php');
include_once('core/http/Container.php');
use App\Models\NotifyModel;
use Core\Http\BaseController;
class notifyController extends BaseController{
    private $notify;
    public function __construct(){
        $this->notify = new NotifyModel();
    }
    // get all notify with user_id
    public function index()
    {
        $user_id = 1;
        $result= $this->notify->get($user_id);
        $msg = $result;
        return $this->status(200,$msg);
    }
}