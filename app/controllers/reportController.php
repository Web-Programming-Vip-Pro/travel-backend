<?php

namespace App\Controllers;

include_once('app/services/reportService.php');
include_once('core/http/Container.php');

use App\Services\ReportService;
use Core\Http\BaseController;
class reportController extends BaseController{
    private $reportService;
    public function __construct(){
        $this->reportService = new ReportService();
    }
    // get all report 
    public function index()
    {
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
       return $this->reportService->list($req);
    }
    // add report with agency_id
    public function postAdd(){
        $agency_id = $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        $agency_id = (int)$req['agency_id'];
        return $this->reportService->add($agency_id,$req);
    }  
}