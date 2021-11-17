<?php

namespace App\Controllers;

require_once('core/http/Container.php');
require_once('app/services/categoryService.php');

use Core\Http\BaseController;
use App\Services\CategoryService;
class categoryController extends BaseController {
    private $cateService;
    public function __construct(){
        $this->cateService = new CategoryService();
    }
    public function index()
    {
       return $this->cateService->list();
    }
    public function postAdd(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        return $this->cateService->add($req);
    }  
    public function getEdit(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        $id = (int)$req['id'];
        return $this->cateService->getEdit($id);
    }
    public function postEdit(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        $id = (int)$req['id'];
        return $this->cateService->postEdit($id,$req);
        
    }
    public function delete(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        $id = (int)$req['id'];
        return $this->cateService->delete($id);
    }
}
