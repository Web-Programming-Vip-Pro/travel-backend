<?php

namespace App\Controllers;

require_once('core/http/Container.php');
require_once('app/services/countryService.php');

use Core\Http\BaseController;
use App\Services\CountryService;
class countryController extends BaseController{
    private $countryService;
    public function __construct(){
        $this->countryService = new CountryService();
    }
    public function index()
    {
        return $this->countryService->list();
    }
    public function postAdd(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        return $this->countryService->add($req);
    }  
    public function getEdit(){
        $id = (int)$_REQUEST['id'];
        return $this->countryService->getEdit($id);
    }
    public function postEdit(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        $id = (int)$_REQUEST['id'];
        return $this->countryService->postEdit($id,$req);
    }
    public function delete(){
        $id = (int)$_REQUEST['id'];
        return $this->countryService->delete($id);
    }
}
