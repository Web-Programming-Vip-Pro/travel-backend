<?php

namespace App\Controllers;

require_once('core/http/Container.php');
require_once('app/services/notifyService.php');
use Core\Http\BaseController;
use App\Services\NotifyService;
class notifyController extends BaseController{
    private $notifyService;
    public function __construct(){
        $this->notifyService = new NotifyService();
    }
    // get all notify with user_id
    public function index()
    {
        return $this->notifyService->list();
    }
}