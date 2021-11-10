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
        $msg =  $result;
        return $this->status(200,$msg);
    }
    // add report with agency_id
    public function postAdd(){
        $agency_id = (int)$_REQUEST['id'];
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        $msgs = [];
        if(!$req['message'] || trim($req['message']) == ''){
            array_push($msgs,'Please fill out message');
        }
        if(count($msgs) > 0){
            $msg = 'Some fielt not filled in';
            return $this->status(422,$msg);
        } 
        $data = [
            'user_id'       => 1,
            'agency_id'      => $agency_id,
            'message'       => $req['message'],
        ];
        $result = $this->report->create($data);
        if($result == false){
            $msg= 'Add report to database fail';
            return $this->status(500,$msg);
        }
        $msg= 'Add report to database success';
        return $this->status(200,$msg);
    }  
}