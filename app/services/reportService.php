<?php
namespace App\Services;

require_once('app/models/reportModel.php');
require_once('app/middleware/middleware.php');
require_once('core/http/Container.php');

use App\Models\ReportModel;
use App\Middleware\Middleware;
use Core\Http\BaseController;


class ReportService 
{  
    private $report;
    private $middleware;
    private $controller;
    public function __construct()
    {   
        $this->controller = new BaseController();
        $this->report    = new ReportModel();
        $this->middleware   = new Middleware();
        $this->user         = $this->middleware->handleUser();
        $this->admin         = $this->middleware->handleAdmin();
    }
    public function list($req){
        if($this->admin == false){
            return $this->controller->status(401,"Unauthorized");
        }
        $page = isset($req['page']) ? (int)($req['page']) : 0;
        $limit = isset($req['limit']) ? (int)($req['limit']) : 20;
        $result = $this->report->get(-1,$page,$limit);
        return $this->controller->status(200,$result);
    }
    public function add($agency_id,$req){
        if($this->user == false){
            return $this->controller->status(401,"Unauthorized");
        }
        $msgs = $this->handleValidator($req);
        if($msgs != false){
            return $this->container->status(422,$msgs);
        }
        $data = [
            'user_id'       => $this->user->id,
            'agency_id'      => $agency_id,
            'message'       => $req['message'],
        ];
        $result = $this->report->create($data);
        if($result == false){
            $msg= 'Add report to database fail';
            return $this->controller->status(500,$msg);
        }
        $msg= 'Add report to database success';
        return $this->controller->status(200,$msg);
    }
    public function handleValidator($req){
        $msgs = [];
        if(!$req['message'] || trim($req['message']) == ''){
            array_push($msgs,'Please fill out message');
        }
        if(count($msgs) > 0){
            return $msgs;
        } 
        return false;
    }
}
