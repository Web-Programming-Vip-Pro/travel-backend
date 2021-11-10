<?php

namespace App\Controllers;

require_once('core/http/Container.php');
require_once('app/services/cityService.php');

use Core\Http\BaseController;
use App\Services\CityService;
class cityController extends BaseController{
    private $cityService;
    public function __construct(){
        $this->cityService = new CityService();
    }
    public function index()
    {
        return $this->cityService->list();
    }
    public function postAdd(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
       return $this->cityService->add($req);
    }  
    public function getEdit(){
        $id = (int)$_REQUEST['id'];
        return $this->cityService->getEdit($id);
    }
    public function postEdit(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        $id = (int)$_REQUEST['id'];
       return $this->cityService->postEdit($id,$req);
    }
    public function delete(){
        $id = (int)$_REQUEST['id'];
       return $this->cityService->delete($id);
    }
}