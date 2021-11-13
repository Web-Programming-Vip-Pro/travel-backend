<?php
namespace App\Services;

require_once('app/models/notifyModel.php');
require_once('app/middleware/middleware.php');
require_once('core/http/Container.php');

use App\Models\NotifyModel;
use App\Middleware\Middleware;
use Core\Http\BaseController;

class NotifyService 
{  
    private $notify;
    private $middleware;
    private $controller;
    public function __construct()
    {   
        $this->controller   = new BaseController();
        $this->notify       = new NotifyModel();
        $this->middleware   = new Middleware();
        $this->user         = $this->middleware->handleUser();
    }
    /**
     * param @null
     * return @response
     */
    public function list(){
        if($this->user == false){
            return $this->controller->status(401,"Unauthorized");
        }
        $result= $this->notify->get($this->user->id);
        return $this->controller->status(200,$result);
    }
}
