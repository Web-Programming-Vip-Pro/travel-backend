<?php

namespace App\Controllers;

require_once('core/http/Container.php');
require_once('app/services/blogService.php');

use Core\Http\BaseController;
use App\Services\blogService;
class blogController extends BaseController {
    private $blogService;
    public function __construct(){
        $this->blogService = new blogService();
    }
    public function index()
    {
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        return $this->blogService->list($req);
    }
    public function postAdd(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        return $this->blogService->add($req);
    }  
    public function getEdit(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        $id = $req['id'];
        return $this->blogService->getEdit($id);
    }
    public function postEdit(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        $id = $req['id'];
        return $this->blogService->postEdit($id,$req);
    }
    public function delete(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        $id = $req['id'];
        return $this->blogService->delete($id); 
    }
}