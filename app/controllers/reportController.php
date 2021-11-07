<?php

namespace App\Controllers;

include_once('app/models/reportModel.php');
include_once('core/http/Container.php');
use App\Models\reportModel;
use Core\Http\BaseController;
class reportController extends BaseController{
    private $report;
    public function __construct(){
        $this->report = new ReportModel();
    }
    // get all report 
    public function index()
    {
        $result= $this->report->get();
        $msgs = [
            'status'    =>  'success',
            'msg'       =>  'Get report',
            'data'      =>  $result
        ];
        return $this->status(200,$msgs);
    }
    // add report with agency_id
    public function postAdd(){
        $agency_id = (int)$_REQUEST['id'];
        $req = $_POST;
        $msgs = [];
        if(!$req['message'] || trim($req['message']) == ''){
            array_push($msgs,'Please fill out message');
        }
        if(count($msgs) > 0){
            $msg = [
                'status'    => 'error',
                'msg'       => 'Some fielt not fill in',
                'data'      => $msgs
            ];
            return $this->status(422,$msg);
        } 
        $data = [
            'user_id'       => 1,
            'agency_id'      => $agency_id,
            'message'       => $req['message'],
        ];
        $result = $this->report->create($data);
        if($result == false){
            $msg = [
                'status'    => 'error',
                'msg'       => 'Error add report',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $msg = [    
            'status'    => ' success',
            'msg'       => 'Add report success',
            'data'      => null
        ]; 
        return $this->status(200,$msg);
    }  
}