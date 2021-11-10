<?php

namespace App\Controllers;

require_once('core/http/Container.php');
require_once('app/services/placeService.php');

use Core\Http\BaseController;
use App\Services\PlaceService;
class placeController extends BaseController{
    private $placeService;
    public function __construct(){
        $this->placeService = new PlaceService();
    }
    public function index()
    {
       return $this->placeService->list();
    }
    public function postAdd(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
       return $this->placeService->add($req);
    }  
    public function getEdit(){
        $id = (int)$_REQUEST['id'];
        return $this->placeService->getEdit($id);
        
    }
    public function postEdit(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        $id = (int)$_REQUEST['id'];
        return $this->placeService->postEdit($id,$req);
    }
    public function delete(){
        $id = (int)$_REQUEST['id'];
        return $this->placeService->delete($id);
    }
}
